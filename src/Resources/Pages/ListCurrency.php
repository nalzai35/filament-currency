<?php

namespace Nalzai35\FilamentCurrency\Resources\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Nalzai35\FilamentCurrency\Facades\ExchangeRate;
use Nalzai35\FilamentCurrency\Facades\FilamentCurrency;
use Nalzai35\FilamentCurrency\Models\Currency;
use Nalzai35\FilamentCurrency\Resources\CurrencyResource;
use Filament\Actions;
class ListCurrency extends ListRecords
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('xl')
                ->form([
                    self::$resource::getCodeInput()
                ])
                ->using(function (array $data, string $model) {
                    $defaultCode = Currency::getDefault()->code;
                    $currency = FilamentCurrency::getCurrency($data['code']);
                    $rate = Arr::get(ExchangeRate::latest($defaultCode, [$data['code']]), 'data.'.$data['code']);
                    $currency['exchange_rate'] = $rate;

                    $record = new $model;
                    DB::beginTransaction();
                    $currency = $record->create([
                        ...$currency,
                        'code' => $data['code']
                    ]);
                    DB::commit();

                    return $currency;
                }),
        ];
    }
}
