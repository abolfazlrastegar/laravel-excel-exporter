<?php

namespace Abolfazrastegar\LaravelExcelExporter\tests\Feature;

use Abolfazrastegar\LaravelExcelExporter\ExcelExportService;
use Abolfazrastegar\LaravelExcelExporter\tests\TestCase;
use PhpOffice\PhpSpreadsheet\IOFactory;

class XlsxExportTest extends TestCase
{
    public function test_xlsx_file_can_be_created(): void
    {
        $path = storage_path(
            'app/excel-exporter-tests/users.xlsx'
        );

        if (file_exists($path)) {
            unlink($path);
        }

        $builder = app(
            ExcelExportService::class
        )->make();

        $builder
            ->from([
                [
                    'id' => 1,
                    'name' => 'Abolfazl',
                ],
                [
                    'id' => 2,
                    'name' => 'Ali',
                ],
            ])
            ->columns([
                'id' => [
                    'title' => 'شناسه',
                    'width' => 10,
                ],

                'name' => [
                    'title' => 'نام',
                    'width' => 30,
                ],
            ])
            ->rtl()
            ->freezeHeader()
            ->filter()
            ->xlsx();

        $builder->store(
            'users.xlsx',
            'local'
        );

        $this->assertFileExists($path);

        $spreadsheet = IOFactory::load($path);

        $sheet = $spreadsheet->getActiveSheet();

        $this->assertSame(
            'شناسه',
            $sheet->getCell('A1')->getValue()
        );

        $this->assertSame(
            'Abolfazl',
            $sheet->getCell('B2')->getValue()
        );

        $this->assertTrue(
            $sheet->isRightToLeft()
        );

        $spreadsheet->disconnectWorksheets();
    }
}