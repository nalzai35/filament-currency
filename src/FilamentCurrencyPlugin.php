<?php

namespace Nalzai35\FilamentCurrency;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Nalzai35\FilamentCurrency\Resources\CurrencyResource;

class FilamentCurrencyPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-currency';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
           CurrencyResource::class
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
