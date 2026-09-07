<?php
namespace Abolfazrastegar\LaravelExcelExporter;

use Illuminate\Support\ServiceProvider;

class ExcelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/excel-exporter.php',
            'excel-exporter'
        );

        $this->app->singleton(
            ExcelExportService::class,
            fn () => new ExcelExportService()
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/excel-exporter.php' =>
                config_path('excel-exporter.php'),
        ], 'excel-exporter-config');
    }
}