<?php

namespace Abolfazrastegar\LaravelExcelExporter\Tests\Unit;

use Abolfazrastegar\LaravelExcelExporter\ExcelColumn;
use Abolfazrastegar\LaravelExcelExporter\Writers\XlsxWriter;
use PHPUnit\Framework\TestCase;

final class XlsxWriterTest extends TestCase
{
    public function test_it_generates_an_xlsx_file_using_the_autoloaded_writer_class(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'excel-exporter-test-');

        self::assertNotFalse($path);

        try {
            $result = (new XlsxWriter())->write(
                data: [['name' => 'Abolfazl']],
                columns: [
                    new ExcelColumn(key: 'name', title: 'Name'),
                ],
                path: $path,
            );

            self::assertSame($path, $result);
            self::assertFileExists($path);
            self::assertStringStartsWith('PK', (string) file_get_contents($path));
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
