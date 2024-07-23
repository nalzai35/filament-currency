<?php

namespace Nalzai35\FilamentCurrency\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Nalzai35\FilamentCurrency\Facades\ExchangeRate;
use Nalzai35\FilamentCurrency\Facades\FilamentCurrency;
use Nalzai35\FilamentCurrency\Models\Currency;
use Nalzai35\FilamentCurrency\Resources\Pages;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
                self::getCodeInput(),
                Forms\Components\TextInput::make('symbol'),
                Forms\Components\TextInput::make('format'),
                Forms\Components\TextInput::make('exchange_rate')
                    ->numeric(),
                Forms\Components\Group::make([
                    Forms\Components\Toggle::make('default'),
                    Forms\Components\Toggle::make('active'),
                ])->columns(1)->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('symbol'),
                Tables\Columns\TextColumn::make('format'),
                Tables\Columns\TextColumn::make('exchange_rate'),
                Tables\Columns\ToggleColumn::make('default'),
                Tables\Columns\ToggleColumn::make('active'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('update_rate')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->color('info')
                    ->action(function (Currency $record) {
                        $defaultCode = Currency::getDefault()->code;
                        $rate = Arr::get(ExchangeRate::latest($defaultCode, [$record->code]), 'data.'.$record->code);
                        $record->update([
                            'exchange_rate' => $rate,
                        ]);
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrency::route('/'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }

    public static function getCodeInput(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('code')
            ->placeholder('USD')
            ->mask(RawJs::make(<<<'JS'
                            $input.toUpperCase()
                        JS
            ))
            ->in(fn(): array => FilamentCurrency::getCurrencyList())
            ->unique(ignoreRecord: true)
            ->required();
    }
}
