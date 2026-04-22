<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\CartOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class EarnBySellController extends Controller
{
    public function indexReact(Request $request): Response
    {
        $filters = $this->resolveFilters($request);

        $baseQuery = CartOrder::query();
        $this->applyEarnFilters($baseQuery, $filters);

        $productsQuery = CartOrder::query()->with(['product.owner', 'product.isResel', 'product.resel']);
        $this->applyEarnFilters($productsQuery, $filters);

        $products = $productsQuery->orderByDesc('id')->paginate(20)->withQueryString();
        $mapped = $this->mapProducts($products->getCollection());

        return Inertia::render('Auth/system/earn-by-sell/index', [
            'filters' => [
                'nav' => $filters['nav'],
                'fd' => $filters['fd'] !== '' ? Carbon::parse($filters['fd'])->format('d, M Y') : '',
                'lastDate' => $filters['lastDate'] !== '' ? Carbon::parse($filters['lastDate'])->format('d, M Y') : '',
                'fd_value' => $filters['fd'],
                'lastDate_value' => $filters['lastDate'],
                'user_type' => $filters['user_type'],
                'find' => $filters['find'],
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
            'printUrl' => route('system.earn.print', [
                'nav' => $filters['nav'],
                'fd' => $filters['fd'],
                'lastDate' => $filters['lastDate'],
                'user_type' => $filters['user_type'],
                'find' => $filters['find'],
            ]),
        ]);
    }

    public function printReact(Request $request): Response
    {
        $filters = $this->resolveFilters($request);

        $productsQuery = CartOrder::query()->with(['product.owner', 'product.isResel', 'product.resel']);
        $this->applyEarnFilters($productsQuery, $filters);

        return Inertia::render('Auth/system/earn-by-sell/PrintSummery', [
            'filters' => [
                'nav' => $filters['nav'],
                'fd' => $filters['fd'],
                'lastDate' => $filters['lastDate'],
                'user_type' => $filters['user_type'],
                'find' => $filters['find'],
            ],
            'products' => $this->mapProducts($productsQuery->orderByDesc('id')->get())->values()->all(),
        ]);
    }

    private function resolveFilters(Request $request): array
    {
        return [
            'nav' => (string) $request->query('nav', 'sold'),
            'fd' => trim((string) $request->query('fd', '')),
            'lastDate' => trim((string) $request->query('lastDate', '')),
            'user_type' => (string) $request->query('user_type', 'user'),
            'find' => trim((string) $request->query('find', '')),
        ];
    }

    private function applyEarnFilters(Builder $query, array $filters): void
    {
        if ($filters['user_type'] !== 'all') {
            $query->where('user_type', $filters['user_type']);
        }

        if ($filters['nav'] === 'sold') {
            $query->where('status', 'Confirmed');
        } elseif ($filters['nav'] === 'selling') {
            $query->whereIn('status', ['Pending', 'Picked', 'Delivery', 'Delivered']);
        }

        if ($filters['fd'] !== '') {
            $query->where('created_at', '>=', Carbon::parse($filters['fd'])->startOfDay());
        }

        if ($filters['lastDate'] !== '') {
            $query->where('created_at', '<=', Carbon::parse($filters['lastDate'])->endOfDay());
        }

        if ($filters['find'] !== '') {
            $search = $filters['find'];

            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->where('id', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('user_type', 'like', '%' . $search . '%')
                    ->orWhere('belongs_to_type', 'like', '%' . $search . '%')
                    ->orWhereHas('product', function (Builder $productQuery) use ($search) {
                        $productQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('title', 'like', '%' . $search . '%')
                            ->orWhere('slug', 'like', '%' . $search . '%')
                            ->orWhereHas('owner', function (Builder $ownerQuery) use ($search) {
                                $ownerQuery
                                    ->where('name', 'like', '%' . $search . '%')
                                    ->orWhereHas('vendorShop', function (Builder $shopQuery) use ($search) {
                                        $shopQuery->where('shop_name_en', 'like', '%' . $search . '%');
                                    })
                                    ->orWhereHas('resellerShop', function (Builder $shopQuery) use ($search) {
                                        $shopQuery->where('shop_name_en', 'like', '%' . $search . '%');
                                    });
                            });
                    });
            });
        }
    }

    private function mapProducts($items)
    {
        return collect($items)->map(function (CartOrder $item) {
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
        });
    }
}
