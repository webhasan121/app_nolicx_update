<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TableDateFilter
{
    public static function hasOnlyDefaultFilters(Request $request, array $defaults = []): bool
    {
        foreach ($request->query() as $key => $value) {
            if ($key === 'page' || $value === null || $value === '') {
                continue;
            }

            if (array_key_exists($key, $defaults) && self::matchesDefault($value, $defaults[$key])) {
                continue;
            }

            return false;
        }

        return true;
    }

    public static function apply($query, ?string $startDate, ?string $endDate, bool $defaultToday = false, string $column = 'created_at'): void
    {
        if (!empty($startDate) && !empty($endDate)) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            if ($start->gt($end)) {
                [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
            }

            $query->whereBetween($column, [$start, $end]);

            return;
        }

        if (!empty($startDate)) {
            $query->whereBetween($column, [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($startDate)->endOfDay(),
            ]);

            return;
        }

        if (!empty($endDate)) {
            $query->whereBetween($column, [
                Carbon::parse($endDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);

            return;
        }

        if ($defaultToday) {
            $query->whereBetween($column, [
                Carbon::today()->startOfDay(),
                Carbon::today()->endOfDay(),
            ]);
        }
    }

    private static function matchesDefault($value, $default): bool
    {
        if (is_bool($default)) {
            return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) === $default;
        }

        return (string) $value === (string) $default;
    }
}
