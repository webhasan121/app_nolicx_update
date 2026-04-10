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
        $lastDate = $request->query('lastDate', now()->toDateString());
        $account = auth()->user()->account_type();

        $startDate = $fd ? Carbon::parse($fd)->startOfDay() : Carbon::create(1970, 1, 1)->startOfDay();
        $endDate = Carbon::parse($lastDate)->endOfDay();

        $baseQuery = CartOrder::query()
            ->where('belongs_to_type', $account)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($nav === 'sold') {
            $baseQuery->where('status', 'Confirm');
        } elseif ($nav === 'selling') {
            $baseQuery->whereIn('status', ['Pending', 'Picked', 'Delivery', 'Delivered']);
        }

        $totalSell = (clone $baseQuery)->sum('price');
        $tn = (clone $baseQuery)->sum('buying_price');
        $tp = $totalSell - $tn;
        $shop = (clone $baseQuery)->count();

        $productsQuery = CartOrder::query()
            ->where('belongs_to_type', $account)
            ->whereBetween('created_at', [$startDate, $endDate])
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

        $products = $productsQuery->orderByDesc('id')->paginate(20)->withQueryString();

        return Inertia::render('Reseller/EarnBySell/Index', [
            'filters' => [
                'nav' => $nav,
                'fd' => $fd,
                'lastDate' => $lastDate,
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
                'links' => $products->linkCollection()->toArray(),
            ],
            'counts' => [
                'items' => $products->count(),
                'unique' => $products->getCollection()->groupBy('product_id')->count(),
            ],
        ]);
    }
}
