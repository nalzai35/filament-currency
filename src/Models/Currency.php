<?php

namespace Nalzai35\FilamentCurrency\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    public static function booted(): void
    {
        static::created(function (Currency $currency) {
            $currency->ensureOnlyOneDefault($currency);
        });

        static::updated(function (Currency $currency) {
            $currency->ensureOnlyOneDefault($currency);
        });
    }
    public function scopeGetDefault($query)
    {
        return $query->where('default', true)->first();
    }

    public function scopeGetEnabled($query)
    {
        return $query->where('enabled', true)->get();
    }

    protected function ensureOnlyOneDefault($savedLanguage): void
    {
        // Wrap here so we avoid a query if it's not been set to default.
        if ($savedLanguage->default) {
            self::withoutEvents(function () use ($savedLanguage) {
                self::whereDefault(true)->where('id', '!=', $savedLanguage->id)->update([
                    'default' => false,
                ]);
            });
        }
    }
}
