<?php
declare(strict_types=1);
namespace Weijiajia\ProvinceCityChina;

trait Makeable
{
    /**
     * Instantiate a new class with the arguments.
     */
    public static function make(mixed ...$arguments): static
    {
        return new static(...$arguments);
    }

    
}