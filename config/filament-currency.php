<?php

// config for Nalzai35/FilamentCurrency
return [
    'exchange_rate' => [
        'default' => 'freecurrencyapi',
        'drivers' => [
            'freecurrencyapi' => [
                'api_key' => env('FREE_CURRENCY_API_KEY'),
                'cache_exp' => env('FREE_CURRENCY_API_CACHE_EXP', now()->addDay()),
            ]
        ]
    ]
];
