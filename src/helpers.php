<?php

if (!function_exists('currency_format')) {
    function currency_format($amount = null, $currency = null, $include_symbol = true)
    {
        return app('currency')->format($amount, $currency, $include_symbol);
    }
}
