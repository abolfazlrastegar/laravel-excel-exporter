<?php

namespace Abolfazrastegar\LaravelExcelExporter\Tests\Feature;

use Abolfazrastegar\LaravelExcelExporter\ExcelExportService;
use Abolfazrastegar\LaravelExcelExporter\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ZipArchive;

class XlsxExportTest extends TestCase
{
    public function test_xlsx_file_can_be_created(): void
    {
        Storage::fake('local');

        $builder = app(ExcelExportService::class)->make();

        $builder
            ->from([
                ['id' => 1, 'name' => 'Abolfazl'],
                ['id' => 2, 'name' => 'Ali'],
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

        $builder->store('users.xlsx', 'local');

        Storage::disk('local')->assertExists('users.xlsx');

        $spreadsheet = IOFactory::load(Storage::disk('local')->path('users.xlsx'));
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertSame('شناسه', $sheet->getCell('A1')->getValue());
        $this->assertSame('Abolfazl', $sheet->getCell('B2')->getValue());
        $spreadsheet->disconnectWorksheets();

        $archive = new ZipArchive();
        $this->assertSame(true, $archive->open(Storage::disk('local')->path('users.xlsx')));

        $worksheetXml = $archive->getFromName('xl/worksheets/sheet1.xml');
        $archive->close();

        $this->assertIsString($worksheetXml);
        $this->assertStringContainsString('rightToLeft="true"', $worksheetXml);
    }
}
