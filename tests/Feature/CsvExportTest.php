<?php

namespace Abolfazrastegar\LaravelExcelExporter\Tests\Feature;

use Abolfazrastegar\LaravelExcelExporter\ExcelExportService;
use Abolfazrastegar\LaravelExcelExporter\Tests\TestCase;

class CsvExportTest extends TestCase
{
    public function test_csv_file_can_be_created(): void
    {
        $path = storage_path(
            'app/excel-exporter-tests/users.csv'
        );

        if (file_exists($path)) {
            unlink($path);
        }

        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir(
                $directory,
                0755,
                true
            );
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
                'id' => 'شناسه',
                'name' => 'نام',
            ])
            ->csv()
            ->filename('users.csv');

        $builder->store(
            'users.csv',
            'local'
        );

        $this->assertFileExists($path);

        $content = file_get_contents($path);

        $this->assertStringContainsString(
            'شناسه',
            $content
        );

        $this->assertStringContainsString(
            'Abolfazl',
            $content
        );
    }
}