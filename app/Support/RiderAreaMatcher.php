<?php

namespace App\Support;

use App\Models\Order;
use App\Models\rider;
use Illuminate\Database\Eloquent\Builder;

class RiderAreaMatcher
{
    public static function termsForRider(?rider $rider): array
    {
        if (!$rider) {
            return [];
        }

        $terms = [
            $rider->targeted_area,
            $rider->targetedArea?->name,
            $rider->upozila,
        ];

        return self::normalizeTerms($terms);
    }

    public static function termsForOrder(Order $order): array
    {
        return self::normalizeTerms([
            $order->target_area,
            $order->upozila,
        ]);
    }

    public static function applyOrderAreaScope(Builder $query, array $terms): Builder
    {
        $terms = self::normalizeTerms($terms);

        if (empty($terms)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $builder) use ($terms) {
            foreach ($terms as $term) {
                $like = '%' . self::escapeLike($term) . '%';

                $builder
                    ->orWhereRaw('LOWER(TRIM(target_area)) = ?', [$term])
                    ->orWhereRaw('LOWER(TRIM(upozila)) = ?', [$term])
                    ->orWhereRaw('LOWER(TRIM(target_area)) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(TRIM(upozila)) LIKE ?', [$like]);
            }
        });
    }

    public static function applyRiderAreaScope(Builder $query, array $terms): Builder
    {
        $terms = self::normalizeTerms($terms);

        if (empty($terms)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $builder) use ($terms) {
            foreach ($terms as $term) {
                $like = '%' . self::escapeLike($term) . '%';

                $builder
                    ->orWhereRaw('LOWER(TRIM(targeted_area)) = ?', [$term])
                    ->orWhereRaw('LOWER(TRIM(targeted_area)) LIKE ?', [$like]);
            }
        });
    }

    public static function riderMatchesOrder(?rider $rider, Order $order): bool
    {
        $riderTerms = self::termsForRider($rider);
        $orderTerms = self::termsForOrder($order);

        foreach ($riderTerms as $riderTerm) {
            foreach ($orderTerms as $orderTerm) {
                if ($riderTerm === $orderTerm || str_contains($riderTerm, $orderTerm) || str_contains($orderTerm, $riderTerm)) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function normalizeTerms(array $terms): array
    {
        $normalized = [];

        foreach ($terms as $term) {
            foreach (preg_split('/[,|]+/', (string) $term) ?: [] as $part) {
                $value = mb_strtolower(trim($part));

                if ($value === '' || $value === 'n/a' || $value === 'not defined') {
                    continue;
                }

                $normalized[] = $value;
            }
        }

        return array_values(array_unique($normalized));
    }

    private static function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
