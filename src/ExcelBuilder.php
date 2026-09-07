<?php
namespace Abolfazrastegar\LaravelExcelExporter;

use Abolfazrastegar\LaravelExcelExporter\Writers\CsvWriter;
use Abolfazrastegar\LaravelExcelExporter\Writers\XlsxWriter;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class ExcelBuilder
{
    protected mixed $data = null;

    /** @var ExcelColumn[] */
    protected array $columns = [];

    protected ?\Closure $mapper = null;

    protected string $filename = 'export.xlsx';

    protected string $sheetName = 'Sheet1';

    protected int $chunkSize = 2000;

    protected bool $rtl = false;

    protected bool $freezeHeader = false;

    protected bool $filter = false;

    protected bool $autoSize = false;

    protected string $type = 'xlsx';

    public function from(
        mixed $data
    ): static {
        $this->data = $data;

        return $this;
    }

    public function columns(array $columns): static
    {
        $this->columns = [];

        foreach ($columns as $key => $config) {

            if (is_string($config)) {
                $this->columns[] = new ExcelColumn(
                    key: $key,
                    title: $config
                );

                continue;
            }

            $this->columns[] = new ExcelColumn(
                key: $key,
                title: $config['title'] ?? $key,
                width: $config['width'] ?? null,
                format: $config['format'] ?? null,
                value: $config['value'] ?? null,
            );
        }

        return $this;
    }

    public function map(\Closure $mapper): static
    {
        $this->mapper = $mapper;

        return $this;
    }

    public function filename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function sheet(string $name): static
    {
        $this->sheetName = mb_substr(
            $name,
            0,
            31
        );

        return $this;
    }

    public function chunk(int $size = 2000): static
    {
        $this->chunkSize = max(1, $size);

        return $this;
    }

    public function rtl(bool $value = true): static
    {
        $this->rtl = $value;

        return $this;
    }

    public function freezeHeader(bool $value = true): static
    {
        $this->freezeHeader = $value;

        return $this;
    }

    public function filter(bool $value = true): static
    {
        $this->filter = $value;

        return $this;
    }

    public function autoSize(bool $value = true): static
    {
        $this->autoSize = $value;

        return $this;
    }

    public function csv(): static
    {
        $this->type = 'csv';

        if (! str_ends_with(
            strtolower($this->filename),
            '.csv'
        )) {
            $this->filename = pathinfo(
                    $this->filename,
                    PATHINFO_FILENAME
                ) . '.csv';
        }

        return $this;
    }

    public function xlsx(): static
    {
        $this->type = 'xlsx';

        if (! str_ends_with(
            strtolower($this->filename),
            '.xlsx'
        )) {
            $this->filename = pathinfo(
                    $this->filename,
                    PATHINFO_FILENAME
                ) . '.xlsx';
        }

        return $this;
    }

    public function download(): BinaryFileResponse
    {
        $this->validate();

        $path = $this->makeTemporaryPath();

        $data = $this->data();

        if ($this->type === 'csv') {

            app(CsvWriter::class)->write(
                data: $data,
                columns: $this->columns,
                path: $path,
                delimiter: config(
                    'excel-exporter.csv.delimiter',
                    ','
                ),
                enclosure: config(
                    'excel-exporter.csv.enclosure',
                    '"'
                ),
                escape: config(
                    'excel-exporter.csv.escape',
                    '\\'
                ),
            );
        } else {

            app(XlsxWriter::class)->write(
                data: $data,
                columns: $this->columns,
                path: $path,
                sheetName: $this->sheetName,
                rtl: $this->rtl,
                freezeHeader: $this->freezeHeader,
                filter: $this->filter,
                autoSize: $this->autoSize,
            );
        }

        return response()
            ->download(
                $path,
                $this->filename
            )
            ->deleteFileAfterSend(true);
    }

    protected function data(): iterable
    {
        if ($this->mapper === null) {
            return $this->resolveData();
        }

        return $this->mapData(
            $this->resolveData()
        );
    }

    protected function resolveData(): iterable
    {
        if (
            $this->data instanceof EloquentBuilder ||
            $this->data instanceof QueryBuilder
        ) {
            return $this->data->cursor();
        }

        if ($this->data instanceof \Illuminate\Support\Collection) {
            return $this->data;
        }

        if (is_array($this->data)) {
            return $this->data;
        }

        if ($this->data instanceof \Traversable) {
            return $this->data;
        }

        throw new \InvalidArgumentException(
            'Unsupported export data type.'
        );
    }

    protected function mapData(iterable $data): \Generator
    {
        foreach ($data as $item) {
            yield ($this->mapper)($item);
        }
    }

    protected function validate(): void
    {
        if ($this->data === null) {
            throw new \InvalidArgumentException(
                'Export data is required.'
            );
        }

        if ($this->columns === []) {
            throw new \InvalidArgumentException(
                'At least one export column is required.'
            );
        }
    }

    protected function makeTemporaryPath(): string
    {
        $directory = storage_path(
            'app/' .
            config(
                'excel-exporter.temporary_directory',
                'excel-exporter'
            )
        );

        if (! is_dir($directory)) {
            mkdir(
                $directory,
                0755,
                true
            );
        }

        return $directory . '/' . uniqid(
                'export_',
                true
            ) . '_' . $this->filename;
    }
}