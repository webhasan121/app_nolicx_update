<?php

namespace App\Support;

use App\Models\country;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CountrySelection
{
    public static function fromRequest(Request $request): ?string
    {
        $requestedCountry = trim((string) $request->query('country', ''));

        if ($requestedCountry !== '') {
            $country = self::resolveName($requestedCountry);

            $request->session()->put('selected_country', $country);

            return $country;
        }

        $sessionCountry = trim((string) $request->session()->get('selected_country', ''));

        if ($sessionCountry !== '') {
            return self::resolveName($sessionCountry);
        }

        $userCountry = trim((string) ($request->user()?->country ?? ''));

        if ($userCountry !== '') {
            return self::resolveName($userCountry);
        }

        return 'Bangladesh';
    }

    public static function applyToProducts(Builder $query, ?string $country): Builder
    {
        return self::applyCountryColumn($query, $country);
    }

    public static function applyToShops(Builder $query, ?string $country): Builder
    {
        return self::applyCountryColumn($query, $country);
    }

    public static function resolveName(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        return country::query()
            ->where('id', $value)
            ->orWhereRaw('LOWER(name) = ?', [mb_strtolower($value)])
            ->orWhereRaw('LOWER(iso2) = ?', [mb_strtolower($value)])
            ->orWhereRaw('LOWER(iso3) = ?', [mb_strtolower($value)])
            ->value('name') ?? $value;
    }

    private static function applyCountryColumn(Builder $query, ?string $country): Builder
    {
        $country = trim((string) $country);

        if ($country === '') {
            return $query;
        }

        return $query->whereRaw('LOWER(country) = ?', [mb_strtolower($country)]);
    }
}
