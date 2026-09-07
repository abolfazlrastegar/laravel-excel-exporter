<?php
namespace Abolfazrastegar\LaravelExcelExporter\tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Abolfazrastegar\LaravelExcelExporter\ExcelServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ExcelServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set(
            'excel-exporter.chunk_size',
            100
        );

        $app['config']->set(
            'excel-exporter.temporary_directory',
            'excel-exporter-tests'
        );
    }

}