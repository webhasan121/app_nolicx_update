<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Product;
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
        $q = $request->query('q');
        $location = $request->query('location');
        $state = $request->query('state', '');
        $get = $request->query('get');

        $query = vendor::query()->where('status', 'Active');

        if (Auth::check()) {
            $query->where('country', auth()->user()?->country);
        }

        if ($q) {
            $keyword = Str::ucfirst($q);
            $query->where(function ($builder) use ($keyword) {
                $builder->where('shop_name_en', 'like', '%' . $keyword . '%')
                    ->orWhere('shop_name_bn', 'like', '%' . $keyword . '%');
            });
        }

        if ($location) {
            $keyword = Str::ucfirst($location);
            $query->where(function ($builder) use ($keyword) {
                $builder->where('district', 'like', '%' . $keyword . '%')
                    ->orWhere('upozila', 'like', '%' . $keyword . '%')
                    ->orWhere('village', 'like', '%' . $keyword . '%')
                    ->orWhere('country', 'like', '%' . $keyword . '%');
            });
        }

        $shops = $query->paginate(config('app.paginate'))->withQueryString();

        $selectedShop = null;
        $products = null;

        if ($get) {
            $shop = vendor::with('user')->findOrFail($get);
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
                ->where('user_id', $shop->user?->id)
                ->with(['attr:id,product_id,name,value'])
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
                'links' => $productList->linkCollection()->toArray(),
            ];
        }

        return Inertia::render('Reseller/Resel/Shops', [
            'filters' => [
                'q' => $q,
                'location' => $location,
                'state' => $state,
                'get' => $get,
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
                'links' => $shops->linkCollection()->toArray(),
            ],
            'selectedShop' => $selectedShop,
            'products' => $products,
        ]);
    }
}
