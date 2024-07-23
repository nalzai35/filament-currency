<?php

namespace Nalzai35\FilamentCurrency;

use Illuminate\Support\Arr;
use Nalzai35\FilamentCurrency\Models\Currency;

class FilamentCurrency
{
    protected array $currencies;

    public function __construct()
    {
        $this->currencies = include(__DIR__ . '/../resources/currencies.php');
        return 'asd';
    }

    public function getCurrency($currency): array
    {
        return Arr::get($this->currencies, $currency);
    }

    public function getCurrencyList(): array
    {
        return array_keys($this->currencies);
    }

    public function format($value, $code = null, $include_symbol = true)
    {
        // Get default currency if one is not set
        $code = $code ?: Currency::getDefault()->code;

        // Remove unnecessary characters
        $value = preg_replace('/[\s\',!]/', '', $value);

        // Get the measurement format
        $format = Currency::whereCode($code)->first()?->format ?? $this->getCurrencyProp($code, 'format');

        // Value Regex
        $valRegex = '/([0-9].*|)[0-9]/';

        // Match decimal and thousand separators
        preg_match_all('/[\s\',.!]/', $format, $separators);

        if ($thousand = Arr::get($separators, '0.0', null)) {
            if ($thousand == '!') {
                $thousand = '';
            }
        }

        $decimal = Arr::get($separators, '0.1', null);

        // Match format for decimals count
        preg_match($valRegex, $format, $valFormat);

        $valFormat = Arr::get($valFormat, 0, 0);

        // Count decimals length
        $decimals = $decimal ? strlen(substr(strrchr($valFormat, $decimal), 1)) : 0;

        // Do we have a negative value?
        if ($negative = $value < 0 ? '-' : '') {
            $value = $value * -1;
        }

        // Format the value
        $value = number_format($value, $decimals, $decimal, $thousand);

        // Apply the formatted measurement
        if ($include_symbol) {
            $value = preg_replace($valRegex, $value, $format);
        }

        // Return value
        return $negative . $value;
    }

    protected function getCurrencyProp($code, $key, $default = null)
    {
        return Arr::get($this->getCurrency($code), $key, $default);
    }
}
