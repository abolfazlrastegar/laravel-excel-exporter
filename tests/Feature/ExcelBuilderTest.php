<?php

namespace Abolfazrastegar\LaravelExcelExporter\tests\Feature;

use Abolfazrastegar\LaravelExcelExporter\ExcelBuilder;
use Abolfazrastegar\LaravelExcelExporter\ExcelExportService;
use Abolfazrastegar\LaravelExcelExporter\tests\TestCase;

class ExcelBuilderTest extends TestCase
{
    public function test_make_returns_builder(): void
    {
        $builder = app(
            ExcelExportService::class
        )->make();

        $this->assertInstanceOf(
            ExcelBuilder::class,
            $builder
        );
    }

    public function test_columns_can_be_defined_using_string(): void
    {
        $builder = app(
            ExcelExportService::class
        )->make();

        $result = $builder->columns([
            'id' => 'شناسه',
            'name' => 'نام',
        ]);

        $this->assertSame(
            $builder,
            $result
        );
    }

    public function test_builder_supports_fluent_api(): void
    {
        $builder = app(
            ExcelExportService::class
        )->make();

        $result = $builder
            ->from([
                [
                    'id' => 1,
                    'name' => 'Abolfazl',
                ],
            ])
            ->columns([
                'id' => 'شناسه',
                'name' => 'نام',
            ])
            ->rtl()
            ->freezeHeader()
            ->filter()
            ->autoSize()
            ->xlsx();

        $this->assertSame(
            $builder,
            $result
        );
    }
}