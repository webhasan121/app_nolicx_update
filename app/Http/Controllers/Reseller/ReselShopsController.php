<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\country;
use App\Models\vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ReselShopsController extends Controller
{
    public function index(Request $request): Response
    {
        $q = trim((string) $request->query('q', ''));
        $location = trim((string) $request->query('location', ''));
        $state = $request->query('state', '');
        $get = $request->query('get');
        $slug = $request->query('slug');

        $query = vendor::query()->where('status', 'Active');

        if (Auth::check()) {
            $country = trim((string) Auth::user()?->country);
            if ($country !== '') {
                $countryName = ctype_digit($country)
                    ? trim((string) country::query()->find((int) $country)?->name)
                    : $country;

                if ($countryName !== '') {
                    $query->whereRaw('LOWER(country) = ?', [mb_strtolower($countryName)]);
                }
            }
        }

        if ($q) {
            $keyword = mb_strtolower($q);
            $query->where(function ($builder) use ($keyword) {
                $builder->whereRaw('LOWER(shop_name_en) LIKE ?', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(shop_name_bn) LIKE ?', ["%{$keyword}%"]);
            });
        }

        if ($location) {
            $keyword = mb_strtolower($location);
            $query->where(function ($builder) use ($keyword) {
                $builder->whereRaw('LOWER(district) LIKE ?', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(upozila) LIKE ?', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(village) LIKE ?', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(country) LIKE ?', ["%{$keyword}%"]);
            });
        }

        $shops = $query->paginate(config('app.paginate'))->withQueryString();

        $selectedShop = null;
        $products = null;

        if ($get || $slug) {
            $shopQuery = vendor::with('user');
            if ($get) {
                $shop = $shopQuery->findOrFail($get);
            } elseif ($slug) {
                $shop = $shopQuery->whereRaw('LOWER(shop_name_en) = ?', [mb_strtolower($slug)])->firstOrFail();
            }
            $selectedShop = [
                'id' => $shop->id,
                'shop_name_en' => $shop->shop_name_en,
                'banner_url' => $shop->banner ? asset('storage/' . $shop->banner) : null,
                'logo_url' => $shop->logo ? asset('storage/' . $shop->logo) : null,
                'village' => $shop->village,
                'upozila' => $shop->upozila,
                'district' => $shop->district,
                'email' => $shop->email,
                'phone' => $shop->phone,
                'user' => [
                    'name' => $shop->user?->name,
                    'village' => $shop->user?->village,
                    'upozila' => $shop->user?->upozila,
                    'district' => $shop->user?->district,
                ],
            ];

            $productList = Product::query()
                ->active()
                ->vendor()
                ->where('user_id', $shop->user?->id);

            // Product search
            if ($q) {
                $keyword = mb_strtolower($q);
                $productList->where(function ($builder) use ($keyword) {
                    $builder->whereRaw('LOWER(name) LIKE ?', ["%{$keyword}%"])
                        ->orWhereRaw('LOWER(title) LIKE ?', ["%{$keyword}%"]);
                });
            }

            $productList = $productList->with(['attr:id,product_id,name,value'])
                ->paginate(config('app.paginate'))
                ->withQueryString();

            $products = [
                'data' => $productList->getCollection()->map(function (Product $product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'title' => $product->title,
                        'thumbnail_url' => $product->thumbnail ? asset('storage/' . $product->thumbnail) : null,
                        'price' => $product->price,
                        'discount' => $product->discount,
                        'offer_type' => (bool) $product->offer_type,
                        'unit' => $product->unit,
                        'total_price' => $product->totalPrice(),
                        'attr' => $product->attr ? [
                            'name' => $product->attr->name,
                            'value' => $product->attr->value,
                        ] : null,
                        'shipping_in_dhaka' => $product->shipping_in_dhaka,
                        'shipping_out_dhaka' => $product->shipping_out_dhaka,
                    ];
                })->values()->all(),
                'links' => $productList->linkCollection()->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => $link['label'],
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $productList->firstItem(),
                'to' => $productList->lastItem(),
                'total' => $productList->total(),
            ];
        }

        return Inertia::render('Reseller/Resel/Shops', [
            'filters' => [
                'q' => $q,
                'location' => $location,
                'state' => $state,
                'get' => $get,
                'slug' => $slug,
            ],
            'shops' => [
                'data' => $shops->getCollection()->map(function ($shop) {
                    return [
                        'id' => $shop->id,
                        'shop_name_en' => $shop->shop_name_en,
                        'banner_url' => $shop->banner ? asset('storage/' . $shop->banner) : null,
                        'logo_url' => $shop->logo ? asset('storage/' . $shop->logo) : null,
                        'village' => $shop->village,
                        'upozila' => $shop->upozila,
                        'district' => $shop->district,
                    ];
                })->values()->all(),
                'links' => $shops->linkCollection()->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => $link['label'],
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $shops->firstItem(),
                'to' => $shops->lastItem(),
                'total' => $shops->total(),
            ],
            'selectedShop' => $selectedShop,
            'products' => $products,
            'printUrl' => route('shops.print', [
                'q' => $q,
                'location' => $location,
                'state' => $state,
                'get' => $get,
                'slug' => $slug,
            ]),
        ]);
    }

    public function print(Request $request): Response
    {
        $q = trim((string) $request->query('q', ''));
        $location = trim((string) $request->query('location', ''));
        $state = $request->query('state', '');
        $get = $request->query('get');

        $query = vendor::query()->where('status', 'Active');

        if (Auth::check()) {
            $country = trim((string) Auth::user()?->country);
            if ($country !== '') {
                $countryName = ctype_digit($country)
                    ? trim((string) country::query()->find((int) $country)?->name)
                    : $country;

                if ($countryName !== '') {
                    $query->whereRaw('LOWER(country) = ?', [mb_strtolower($countryName)]);
                }
            }
        }

        if ($q !== '') {
            $keyword = Str::ucfirst($q);
            $query->where(function ($builder) use ($keyword) {
                $builder->where('shop_name_en', 'like', '%' . $keyword . '%')
                    ->orWhere('shop_name_bn', 'like', '%' . $keyword . '%');
            });
        }

        if ($location !== '') {
            $keyword = Str::ucfirst($location);
            $query->where(function ($builder) use ($keyword) {
                $builder->where('district', 'like', '%' . $keyword . '%')
                    ->orWhere('upozila', 'like', '%' . $keyword . '%')
                    ->orWhere('village', 'like', '%' . $keyword . '%')
                    ->orWhere('country', 'like', '%' . $keyword . '%');
            });
        }

        $shops = $query->get();

        $selectedShop = null;
        $products = [];

        if ($get) {
            $shop = vendor::with('user')->findOrFail($get);
            $selectedShop = [
                'id' => $shop->id,
                'shop_name_en' => $shop->shop_name_en,
            ];

            $products = Product::query()
                ->active()
                ->vendor()
                ->where('user_id', $shop->user?->id)
                ->with(['attr:id,product_id,name,value'])
                ->get()
                ->values()
                ->map(function (Product $product, int $index) {
                    return [
                        'sl' => $index + 1,
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'discount' => $product->discount,
                        'offer_type' => (bool) $product->offer_type,
                        'unit' => $product->unit,
                        'total_price' => $product->totalPrice(),
                    ];
                })
                ->all();
        }

        return Inertia::render('Reseller/Resel/ShopsPrint', [
            'filters' => [
                'q' => $q,
                'location' => $location,
                'state' => $state,
                'get' => $get,
            ],
            'shops' => $shops->values()->map(function ($shop, int $index) {
                return [
                    'sl' => $index + 1,
                    'id' => $shop->id,
                    'shop_name_en' => $shop->shop_name_en,
                    'village' => $shop->village,
                    'upozila' => $shop->upozila,
                    'district' => $shop->district,
                ];
            })->all(),
            'selectedShop' => $selectedShop,
            'products' => $products,
        ]);
    }
}
