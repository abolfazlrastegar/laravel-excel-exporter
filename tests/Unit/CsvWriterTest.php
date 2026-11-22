<?php

namespace Abolfazrastegar\LaravelExcelExporter\Tests\Unit;

use Abolfazrastegar\LaravelExcelExporter\ExcelColumn;
use Abolfazrastegar\LaravelExcelExporter\Writers\CsvWriter;
use PHPUnit\Framework\TestCase;

final class CsvWriterTest extends TestCase
{
    public function test_it_prefixes_formula_like_values_to_prevent_csv_injection(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'excel-exporter-csv-test-');

        self::assertNotFalse($path);

        try {
            (new CsvWriter())->write(
                data: [['value' => '=HYPERLINK("https://example.com")']],
                columns: [
                    new ExcelColumn(key: 'value', title: 'Value'),
                ],
                path: $path,
            );

            $contents = file_get_contents($path);

            self::assertIsString($contents);
            self::assertStringContainsString("'=HYPERLINK", $contents);
            self::assertStringStartsWith("\xEF\xBB\xBF", $contents);
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
