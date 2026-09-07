<?php
namespace Abolfazrastegar\LaravelExcelExporter;
class ExcelExportService
{
    public function make(): ExcelBuilder
    {
        return new ExcelBuilder();
    }
}