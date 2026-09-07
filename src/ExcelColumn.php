<?php
namespace Abolfazrastegar\LaravelExcelExporter;
use Closure;
class ExcelColumn
{
    public function __construct(
        public readonly string $key,
        public readonly string $title,
        public readonly ?int $width = null,
        public readonly ?string $format = null,
        public readonly ?Closure $value = null,
    ) {
    }

    public function resolve(mixed $item): mixed
    {
        if ($this->value !== null) {
            return ($this->value)($item);
        }

        return data_get($item, $this->key);
    }
}