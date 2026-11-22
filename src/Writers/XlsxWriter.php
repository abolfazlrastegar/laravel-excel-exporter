<?php

namespace Abolfazrastegar\LaravelExcelExporter\Writers;

use Abolfazrastegar\LaravelExcelExporter\ExcelColumn;
use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

final class XlsxWriter
{
    public function write(
        iterable $data,
        array $columns,
        string $path,
        string $sheetName = 'Sheet1',
        bool $rtl = false,
        bool $freezeHeader = false,
        bool $filter = false,
        bool $autoSize = false,
    ): string {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle(
            mb_substr($sheetName, 0, 31)
        );

        if ($rtl) {
            $sheet->setRightToLeft(true);
        }

        $this->writeHeader(
            $sheet,
            $columns
        );

        $row = 2;

        foreach ($data as $item) {
            $this->writeRow(
                $sheet,
                $row,
                $item,
                $columns
            );

            $row++;
        }

        $this->styleHeader(
            $sheet,
            count($columns)
        );

        if ($freezeHeader) {
            $sheet->freezePane('A2');
        }

        if ($filter && count($columns) > 0) {
            $lastColumn = $this->columnLetter(
                count($columns)
            );

            $sheet->setAutoFilter(
                "A1:{$lastColumn}" . max(1, $row - 1)
            );
        }

        $this->setWidths(
            $sheet,
            $columns,
            $autoSize
        );

        $writer = new Xlsx($spreadsheet);

        $writer->save($path);

        $spreadsheet->disconnectWorksheets();

        unset($spreadsheet);

        return $path;
    }

    protected function writeHeader(
        $sheet,
        array $columns
    ): void {
        foreach ($columns as $index => $column) {
            $sheet->setCellValue(
                $this->columnLetter($index + 1) . '1',
                $column->title
            );
        }
    }

    protected function writeRow(
        $sheet,
        int $row,
        mixed $item,
        array $columns
    ): void {
        foreach ($columns as $index => $column) {
            /** @var ExcelColumn $column */
            $value = $column->resolve($item);

            $value = $this->formatValue(
                $value,
                $column->format
            );

            $sheet->setCellValue(
                $this->columnLetter($index + 1) . $row,
                $value
            );
        }
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

        if ($format === 'boolean') {
            return $value ? 'بله' : 'خیر';
        }

        if (is_bool($value)) {
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

    protected function styleHeader(
        $sheet,
        int $columnCount
    ): void {
        if ($columnCount === 0) {
            return;
        }

        $lastColumn = $this->columnLetter(
            $columnCount
        );

        $range = "A1:{$lastColumn}1";

        $style = $sheet->getStyle($range);

        $style->getFont()->setBold(true);

        $style->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );

        $style->getFill()
            ->setFillType(Fill::FILL_SOLID);

        $style->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                Border::BORDER_THIN
            );
    }

    protected function setWidths(
        $sheet,
        array $columns,
        bool $autoSize
    ): void {
        foreach ($columns as $index => $column) {
            /** @var ExcelColumn $column */
            $letter = $this->columnLetter(
                $index + 1
            );

            if ($column->width !== null) {
                $sheet
                    ->getColumnDimension($letter)
                    ->setWidth($column->width);

                continue;
            }

            if ($autoSize) {
                $sheet
                    ->getColumnDimension($letter)
                    ->setAutoSize(true);
            }
        }
    }

    protected function columnLetter(int $column): string
    {
        $letter = '';

        while ($column > 0) {
            $mod = ($column - 1) % 26;

            $letter = chr(65 + $mod) . $letter;

            $column = intdiv(
                $column - $mod,
                26
            );
        }

        return $letter;
    }
}