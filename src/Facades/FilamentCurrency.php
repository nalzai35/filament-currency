<?php

namespace Nalzai35\FilamentCurrency\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Nalzai35\FilamentCurrency\FilamentCurrency
 */
class FilamentCurrency extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Nalzai35\FilamentCurrency\FilamentCurrency::class;
    }
}
