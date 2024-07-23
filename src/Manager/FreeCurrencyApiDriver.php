<?php

namespace Nalzai35\FilamentCurrency\Manager;

use Exception;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FreeCurrencyApiDriver
{
    const BASE_URL = 'https://api.freecurrencyapi.com/v1/';
    const REQUEST_TIMEOUT_DEFAULT = 15; // seconds

    /**
     * @throws ExchangeRateException
     */
    private function call(string $endpoint, ?array $query = [])
    {
        $url = self::BASE_URL . $endpoint;

        try {
            $query = [
                'apikey' => config('filament-currency.exchange_rate.drivers.freecurrencyapi.api_key'),
                ...$query
            ];

            $response = Http::withOptions([
                'timeout' => self::REQUEST_TIMEOUT_DEFAULT,
            ])->get($url, $query);
        } catch (HttpClientException|Exception $e) {
            throw new ExchangeRateException($e->getMessage());
        }

        return $response->json();
    }

    public function status()
    {
        return Cache::remember('status', $this->getCacheExpired(), function() {
            return $this->call('status');
        });
    }

    public function latest(string $baseCurrency = 'USD', ?array $currencies = [])
    {
        $key = 'latest.'.$baseCurrency.implode(',', $currencies);

        return Cache::remember($key, $this->getCacheExpired(), function() use ($baseCurrency, $currencies) {
            return $this->call('latest', [
                'base_currency' => $baseCurrency,
                'currencies' => implode(',', $currencies)
            ]);
        });
    }

    public function currencies(?array $currencies = [])
    {
        return collect(Arr::get($this->latest(), 'data'))->only($currencies)->toArray();
    }

    public function historical(string $date = '', string $baseCurrency = 'USD', ?array $currencies = [])
    {
        $date = empty($date) ? date('Y-m-d') : $date;
        $key = 'historical.'.$date.$baseCurrency.implode(',', $currencies);

        return Cache::remember($key, $this->getCacheExpired(), function() use ($date, $baseCurrency, $currencies) {
            return $this->call('historical', [
                'date' => $date,
                'base_currency' => $baseCurrency,
                'currencies' => implode(',', $currencies)
            ]);
        });
    }

    private function buildHeaders($apiKey): array
    {
        return [
            'user-agent' => 'Freecurrencyapi/PHP/0.1',
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'apikey' => $apiKey,
        ];
    }

    private function getCacheExpired()
    {
        return config('filament-currency.exchange_rate.drivers.freecurrencyapi.cache_exp');
    }
}
