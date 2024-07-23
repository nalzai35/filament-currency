<?php

namespace Nalzai35\FilamentCurrency\Manager;

use Illuminate\Support\Manager;

class ExchangeRateManager extends Manager
{
    public function createFreeCurrencyApiDriver()
    {
        return $this->buildProvider(FreeCurrencyApiDriver::class);
    }

    public function buildProvider($provider)
    {
        return $this->container->make($provider);
    }

    public function getDefaultDriver()
    {
        return $this->config->get('filament-currency.exchange_rate.default');
    }
}
