<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\CartOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class EarnBySellController extends Controller
{
    public function index(Request $request): Response
    {
        $nav = $request->query('nav', 'sold');
        $fd = $request->query('fd');
        $lastDate = $request->query('lastDate', '');
        $search = trim((string) $request->query('search', ''));
        $account = auth()->user()->account_type();

        $baseQuery = CartOrder::query()
            ->where('belongs_to_type', $account);

        if ($nav === 'sold') {
            $baseQuery->where('status', 'Confirm');
        } elseif ($nav === 'selling') {
            $baseQuery->whereIn('status', ['Pending', 'Picked', 'Delivery', 'Delivered']);
        }

        if (!empty($fd) && !empty($lastDate)) {
            $startDate = Carbon::parse($fd)->startOfDay();
            $endDate = Carbon::parse($lastDate)->endOfDay();
            $baseQuery->whereBetween('created_at', [$startDate, $endDate]);
        } elseif (!empty($fd)) {
            $baseQuery->whereDate('created_at', Carbon::parse($fd)->toDateString());
        } elseif (!empty($lastDate)) {
            $baseQuery->whereDate('created_at', Carbon::parse($lastDate)->toDateString());
        }

        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $query
                    ->whereHas('product', function ($productQuery) use ($search) {
                        $productQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('title', 'like', '%' . $search . '%');
                    })
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('user_type', 'like', '%' . $search . '%')
                    ->orWhere('belongs_to_type', 'like', '%' . $search . '%');
            });
        }

        $totalSell = (clone $baseQuery)->sum('price');
        $tn = (clone $baseQuery)->sum('buying_price');
        $tp = $totalSell - $tn;
        $shop = (clone $baseQuery)->count();

        $productsQuery = CartOrder::query()
            ->where('belongs_to_type', $account)
            ->with([
                'product' => function ($query) {
                    $query->with(['owner', 'isResel', 'resel']);
                },
            ]);

        if ($nav === 'sold') {
            $productsQuery->where('status', 'Confirm');
        } elseif ($nav === 'selling') {
            $productsQuery->whereIn('status', ['Pending', 'Picked', 'Delivery', 'Delivered']);
        }

        if (!empty($fd) && !empty($lastDate)) {
            $startDate = Carbon::parse($fd)->startOfDay();
            $endDate = Carbon::parse($lastDate)->endOfDay();
            $productsQuery->whereBetween('created_at', [$startDate, $endDate]);
        } elseif (!empty($fd)) {
            $productsQuery->whereDate('created_at', Carbon::parse($fd)->toDateString());
        } elseif (!empty($lastDate)) {
            $productsQuery->whereDate('created_at', Carbon::parse($lastDate)->toDateString());
        }

        if ($search !== '') {
            $productsQuery->where(function ($query) use ($search) {
                $query
                    ->whereHas('product', function ($productQuery) use ($search) {
                        $productQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('title', 'like', '%' . $search . '%');
                    })
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('user_type', 'like', '%' . $search . '%')
                    ->orWhere('belongs_to_type', 'like', '%' . $search . '%');
            });
        }

        $products = $productsQuery->orderByDesc('id')->paginate(config('app.paginate'))->withQueryString();

        return Inertia::render('Reseller/EarnBySell/Index', [
            'filters' => [
                'nav' => $nav,
                'fd' => $fd,
                'lastDate' => $lastDate,
                'search' => $search,
            ],
            'overview' => [
                'totalSell' => $totalSell,
                'tp' => $tp,
                'tn' => $tn,
                'shop' => $shop,
            ],
            'products' => [
                'data' => $products->getCollection()->map(function (CartOrder $item) {
                    $product = $item->product;
                    $owner = $product?->owner;
                    $ownerName = $owner?->name;

                    if ($product?->belongs_to_type === 'reseller') {
                        $ownerName = $owner?->resellerShop()?->shop_name_en ?? $owner?->name;
                    } elseif ($product?->belongs_to_type === 'vendor') {
                        $ownerName = $owner?->vendorShop()?->shop_name_en ?? $owner?->name;
                    }

                    return [
                        'id' => $item->id,
                        'product_id' => $product?->id,
                        'product_slug' => $product?->slug,
                        'product_name' => $product?->name ?? 'N/A',
                        'product_thumbnail' => $product?->thumbnail ? asset('storage/' . $product->thumbnail) : null,
                        'product_status' => $product?->status ?? 'N/A',
                        'user_type' => $item->user_type,
                        'belongs_to_type' => $item->belongs_to_type,
                        'owner_name' => $ownerName,
                        'is_resel_count' => $product?->isResel()->count() ?? 0,
                        'resel_count' => $product?->resel()->count() ?? 0,
                        'product_price' => $product?->price ?? 0,
                        'offer_type' => (bool) ($product?->offer_type ?? false),
                        'discount' => $product?->discount,
                        'discount_percent' => ($product?->offer_type && $product?->price)
                            ? round((100 * (($product->price ?? 0) - ($product->discount ?? 0))) / $product->price, 0)
                            : null,
                        'product_created_at' => $product?->created_at?->toFormattedDateString(),
                        'status' => $item->status,
                    ];
                })->values()->all(),
                'links' => $products->linkCollection()->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => $link['label'],
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'total' => $products->total(),
            ],
            'counts' => [
                'items' => $products->count(),
                'unique' => $products->getCollection()->groupBy('product_id')->count(),
            ],
            'printUrl' => route('reseller.sel.print', [
                'nav' => $nav,
                'fd' => $fd,
                'lastDate' => $lastDate,
                'search' => $search,
            ]),
        ]);
    }

    public function print(Request $request): Response
    {
        $nav = $request->query('nav', 'sold');
        $fd = $request->query('fd');
        $lastDate = $request->query('lastDate', '');
        $search = trim((string) $request->query('search', ''));
        $account = auth()->user()->account_type();

        $productsQuery = CartOrder::query()
            ->where('belongs_to_type', $account)
            ->with([
                'product' => function ($query) {
                    $query->with(['owner', 'isResel', 'resel']);
                },
            ]);

        if ($nav === 'sold') {
            $productsQuery->where('status', 'Confirm');
        } elseif ($nav === 'selling') {
            $productsQuery->whereIn('status', ['Pending', 'Picked', 'Delivery', 'Delivered']);
        }

        if (!empty($fd) && !empty($lastDate)) {
            $startDate = Carbon::parse($fd)->startOfDay();
            $endDate = Carbon::parse($lastDate)->endOfDay();
            $productsQuery->whereBetween('created_at', [$startDate, $endDate]);
        } elseif (!empty($fd)) {
            $productsQuery->whereDate('created_at', Carbon::parse($fd)->toDateString());
        } elseif (!empty($lastDate)) {
            $productsQuery->whereDate('created_at', Carbon::parse($lastDate)->toDateString());
        }

        if ($search !== '') {
            $productsQuery->where(function ($query) use ($search) {
                $query
                    ->whereHas('product', function ($productQuery) use ($search) {
                        $productQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('title', 'like', '%' . $search . '%');
                    })
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('user_type', 'like', '%' . $search . '%')
                    ->orWhere('belongs_to_type', 'like', '%' . $search . '%');
            });
        }

        $products = $productsQuery->orderByDesc('id')->get();

        return Inertia::render('Reseller/EarnBySell/Print', [
            'filters' => [
                'nav' => $nav,
                'fd' => $fd,
                'lastDate' => $lastDate,
                'search' => $search,
            ],
            'products' => $products->values()->map(function (CartOrder $item, int $index) {
                $product = $item->product;
                $owner = $product?->owner;
                $ownerName = $owner?->name;

                if ($product?->belongs_to_type === 'reseller') {
                    $ownerName = $owner?->resellerShop()?->shop_name_en ?? $owner?->name;
                } elseif ($product?->belongs_to_type === 'vendor') {
                    $ownerName = $owner?->vendorShop()?->shop_name_en ?? $owner?->name;
                }

                return [
                    'sl' => $index + 1,
                    'id' => $item->id,
                    'product_name' => $product?->name ?? 'N/A',
                    'owner_name' => $ownerName,
                    'product_price' => $product?->price ?? 0,
                    'product_created_at' => $product?->created_at?->toFormattedDateString(),
                    'status' => $item->status,
                    'user_type' => $item->user_type,
                    'belongs_to_type' => $item->belongs_to_type,
                ];
            })->all(),
        ]);
    }
}
