<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\CartOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class EarnBySellController extends Controller
{
    public function indexReact(Request $request): Response
    {
        $nav = (string) $request->query('nav', 'sold');
        $fd = $request->query('fd');
        $lastDate = (string) $request->query('lastDate', now()->toDateString());
        $userType = (string) $request->query('user_type', 'user');

        $baseQuery = CartOrder::query()
            ->whereBetween('created_at', [$fd, Carbon::parse($lastDate)->endOfDay()]);

        if ($userType !== 'all') {
            $baseQuery->where('user_type', $userType);
        }

        if ($nav === 'sold') {
            $baseQuery->where('status', 'Confirmed');
        } elseif ($nav === 'selling') {
            $baseQuery->whereIn('status', ['Pending', 'Picked', 'Delivery', 'Delivered']);
        }

        $productsQuery = CartOrder::query()
            ->with(['product.owner', 'product.isResel', 'product.resel'])
            ->where(function ($query) use ($userType, $nav) {
                if ($userType !== 'all') {
                    $query->where('user_type', $userType);
                }

                if ($nav === 'sold') {
                    $query->where('status', 'Confirmed');
                } elseif ($nav === 'selling') {
                    $query->whereIn('status', ['Pending', 'Picked', 'Delivery', 'Delivered']);
                }
            })
            ->whereBetween('created_at', [$fd, Carbon::parse($lastDate)->endOfDay()]);

        $products = $productsQuery->orderByDesc('id')->paginate(20)->withQueryString();
        $mapped = $products->getCollection()->map(function (CartOrder $item) {
            $product = $item->product;
            $owner = $product?->owner;
            $ownerName = 'N/A';

            if (($product?->belongs_to_type ?? '') === 'reseller') {
                $ownerName = $owner?->resellerShop()?->shop_name_en ?? $owner?->name ?? 'N/A';
            } elseif (($product?->belongs_to_type ?? '') === 'vendor') {
                $ownerName = $owner?->vendorShop()?->shop_name_en ?? $owner?->name ?? 'N/A';
            }

            $discountPercent = null;
            if ($product?->offer_type && ($product?->price ?? 0) > 0) {
                $discountPercent = round((100 * (($product?->price ?? 0) - ($product?->discount ?? 0))) / ($product?->price ?? 1), 0);
            }

            return [
                'id' => $item->id,
                'product_id' => $product?->id ?? null,
                'product_slug' => $product?->slug ?? '',
                'product_name' => $product?->name ?? 'N/A',
                'product_thumbnail' => !empty($product?->thumbnail) ? asset('storage/' . $product?->thumbnail) : null,
                'product_status' => $product?->status ?? 'N/A',
                'user_type' => $item->user_type,
                'belongs_to_type' => $item->belongs_to_type,
                'owner_name' => $ownerName,
                'is_resel_count' => $product?->isResel()->count() ?? 0,
                'resel_count' => $product?->resel()->count() ?? 0,
                'product_price' => $product?->price ?? 0,
                'offer_type' => (bool) ($product?->offer_type ?? false),
                'discount' => $product?->discount ?? 0,
                'discount_percent' => $discountPercent,
                'product_created_at' => $product?->created_at?->toFormattedDateString(),
                'status' => $item->status ?? 'Unknown',
            ];
        })->values();

        return Inertia::render('Auth/system/earn-by-sell/index', [
            'filters' => [
                'nav' => $nav,
                'fd' => !empty($fd) ? Carbon::parse($fd)->format('d, M Y') : '',
                'lastDate' => !empty($lastDate) ? Carbon::parse($lastDate)->format('d, M Y') : '',
                'fd_value' => $fd,
                'lastDate_value' => $lastDate,
                'user_type' => $userType,
            ],
            'overview' => [
                'totalSell' => (float) (clone $baseQuery)->sum('price'),
                'tn' => (float) (clone $baseQuery)->sum('buying_price'),
                'tp' => (float) ((clone $baseQuery)->sum('price') - (clone $baseQuery)->sum('buying_price')),
                'shop' => (int) (clone $baseQuery)->count(),
                'tpr' => CartOrder::query()->where('user_type', 'reseller')->distinct('product_id')->count('product_id'),
                'tprr' => CartOrder::query()->where('user_type', 'user')->distinct('product_id')->count('product_id'),
            ],
            'products' => [
                'data' => $mapped->all(),
                'links' => collect($products->linkCollection())->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'total' => $products->total(),
                'count' => $mapped->count(),
                'unique_count' => $mapped->groupBy('product_id')->count(),
            ],
        ]);
    }
}
