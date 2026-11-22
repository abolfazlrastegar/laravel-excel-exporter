<?php

namespace Abolfazrastegar\LaravelExcelExporter\Tests\Unit;

use Abolfazrastegar\LaravelExcelExporter\Tests\TestCase;
use Abolfazrastegar\LaravelExcelExporter\ExcelColumn;
class ExcelColumnTest extends TestCase
{
    public function test_it_resolves_simple_value(): void
    {
        $column = new ExcelColumn(
            key: 'name',
            title: 'نام'
        );

        $result = $column->resolve([
            'name' => 'Abolfazl',
        ]);

        $this->assertSame(
            'Abolfazl',
            $result
        );
    }

    public function test_it_resolves_nested_value(): void
    {
        $column = new ExcelColumn(
            key: 'user.name',
            title: 'نام'
        );

        $result = $column->resolve([
            'user' => [
                'name' => 'Abolfazl',
            ],
        ]);

        $this->assertSame(
            'Abolfazl',
            $result
        );
    }

    public function test_it_resolves_custom_callback(): void
    {
        $column = new ExcelColumn(
            key: 'name',
            title: 'نام',
            value: fn ($item) => strtoupper($item['name'])
        );

        $result = $column->resolve([
            'name' => 'abolfazl',
        ]);

        $this->assertSame(
            'ABOLFAZL',
            $result
        );
    }
}