<?php

namespace Abolfazrastegar\LaravelExcelExporter\Writers;

use Abolfazrastegar\LaravelExcelExporter\ExcelColumn;
use DateTimeInterface;

final class CsvWriter
{
    public function write(
        iterable $data,
        array $columns,
        string $path,
        string $delimiter = ',',
        string $enclosure = '"',
        string $escape = '\\',
    ): string {
        $handle = fopen($path, 'wb');

        if ($handle === false) {
            throw new \RuntimeException(
                "Unable to create CSV file: {$path}"
            );
        }

        try {
            // UTF-8 BOM برای Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv(
                $handle,
                array_map(
                    fn (ExcelColumn $column) => $column->title,
                    $columns
                ),
                $delimiter,
                $enclosure,
                $escape
            );

            foreach ($data as $item) {
                $row = [];

                foreach ($columns as $column) {
                    $value = $column->resolve($item);

                    $row[] = $this->formatValue(
                        $value,
                        $column->format
                    );
                }

                fputcsv(
                    $handle,
                    $row,
                    $delimiter,
                    $enclosure,
                    $escape
                );
            }
        } finally {
            fclose($handle);
        }

        return $path;
    }

    protected function formatValue(
        mixed $value,
        ?string $format
    ): mixed {
        if ($value === null) {
            return '';
        }

        if ($value instanceof DateTimeInterface) {
            return match ($format) {
                'date' => $value->format('Y-m-d'),
                default => $value->format('Y-m-d H:i:s'),
            };
        }

        if ($format === 'boolean' || is_bool($value)) {
            return $value ? 'بله' : 'خیر';
        }

        if (is_array($value)) {
            return json_encode(
                $value,
                JSON_UNESCAPED_UNICODE
            );
        }

        return $value;
    }
}