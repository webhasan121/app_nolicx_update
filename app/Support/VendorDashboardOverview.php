<?php

namespace App\Support;

use App\Models\CartOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class VendorDashboardOverview
{
    public static function get(User $user): array
    {
        $account = $user->account_type();
        $productsQuery = $user->myProducts()->where(['belongs_to_type' => $account]);
        $confirmedOrdersQuery = $user->orderToMe()->where([
            'belongs_to_type' => $account,
            'status' => 'Confirm',
        ]);
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $yearStart = Carbon::now()->startOfYear();

        return [
            'products' => (clone $productsQuery)->count(),
            'sales' => (clone $confirmedOrdersQuery)->sum('total'),
            'today_sell' => (clone $confirmedOrdersQuery)
                ->whereDate('created_at', $today)
                ->sum('total'),
            'monthly_sell' => (clone $confirmedOrdersQuery)
                ->whereBetween('created_at', [$monthStart, Carbon::now()])
                ->sum('total'),
            'product_stock' => (clone $productsQuery)->sum('unit'),
            'total_product_stock_price' => (clone $productsQuery)->get()->sum(function ($product) {
                return (float) ($product->unit ?? 0) * (float) $product->totalPrice();
            }),
            'yearly_sell_amount' => (clone $confirmedOrdersQuery)
                ->whereBetween('created_at', [$yearStart, Carbon::now()])
                ->sum('total'),
            'total_amount' => (clone $confirmedOrdersQuery)->sum('total'),
            'monthly_profit' => self::profitFor($user, $account, function (Builder $query) use ($monthStart) {
                $query->whereBetween('orders.created_at', [$monthStart, Carbon::now()]);
            }),
            'daily_profit' => self::profitFor($user, $account, function (Builder $query) use ($today) {
                $query->whereDate('orders.created_at', $today);
            }),
        ];
    }

    private static function profitFor(User $user, string $account, callable $dateFilter): float
    {
        $query = CartOrder::query()
            ->with(['order:id,name', 'product:id,price,discount,offer_type,buying_price'])
            ->whereHas('order', function (Builder $orderQuery) use ($user, $account, $dateFilter) {
                $orderQuery
                    ->where('belongs_to', $user->id)
                    ->where('belongs_to_type', $account)
                    ->where('status', 'Confirm');

                $dateFilter($orderQuery);
            });

        return round((float) $query->get()->sum(function (CartOrder $cartOrder) {
            $quantity = (int) ($cartOrder->quantity ?? 0);
            $buyingPrice = (float) ($cartOrder->buying_price ?? 0);

            if ($cartOrder->order?->name === 'Resel') {
                $sellingPrice = (float) ($cartOrder->product?->totalPrice() ?? 0);
            } else {
                $sellingPrice = (float) ($cartOrder->price ?? 0);
            }

            return ($sellingPrice - $buyingPrice) * $quantity;
        }), 2);
    }
}
