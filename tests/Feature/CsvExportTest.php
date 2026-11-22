<?php

namespace Abolfazrastegar\LaravelExcelExporter\Tests\Feature;

use Abolfazrastegar\LaravelExcelExporter\ExcelExportService;
use Abolfazrastegar\LaravelExcelExporter\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class CsvExportTest extends TestCase
{
    public function test_csv_file_can_be_created(): void
    {
        Storage::fake('local');

        $builder = app(ExcelExportService::class)->make();

        $builder
            ->from([
                ['id' => 1, 'name' => 'Abolfazl'],
                ['id' => 2, 'name' => 'Ali'],
            ])
            ->columns([
                'id' => 'شناسه',
                'name' => 'نام',
            ])
            ->csv()
            ->filename('users.csv');

        $builder->store('users.csv', 'local');

        Storage::disk('local')->assertExists('users.csv');

        $content = Storage::disk('local')->get('users.csv');

        $this->assertStringContainsString('شناسه', $content);
        $this->assertStringContainsString('Abolfazl', $content);
    }
}
