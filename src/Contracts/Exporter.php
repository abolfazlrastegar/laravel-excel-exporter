<?php
namespace Abolfazrastegar\LaravelExcelExporter;
interface Exporter
{
    public function write(iterable $data, array $columns): string;
}