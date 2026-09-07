<?php

namespace Abolfazrastegar\LaravelExcelExporter\tests\Feature;

use Abolfazrastegar\LaravelExcelExporter\ExcelExportService;
use Abolfazrastegar\LaravelExcelExporter\tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_excel_service_is_registered(): void
    {
        $service = app(
            ExcelExportService::class
        );

        $this->assertInstanceOf(
            ExcelExportService::class,
            $service
        );
    }
}