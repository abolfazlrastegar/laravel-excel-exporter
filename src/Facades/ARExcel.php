<?php

namespace Abolfazrastegar\LaravelExcelExporter\Facades;

use Abolfazrastegar\LaravelExcelExporter\ExcelExportService;
use Illuminate\Support\Facades\Facade;

class ARExcel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ExcelExportService::class;
    }
}