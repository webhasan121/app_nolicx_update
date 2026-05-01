<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProductComissionController;
use App\Models\CartOrder;
use App\Models\city;
use App\Models\country;
use App\Models\Order;
use App\Models\Product;
use App\Models\state;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductOrderController extends Controller
{
    public function create($id, $slug)
    {
        $product = Product::query()
            ->with([
                'category:id,name,slug',
                'attr:id,product_id,name,value',
                'showcase:id,product_id,image',
                'comments.user:id,name',
                'owner:id,name',
            ])
            ->where('id', $id)
            ->active()
            ->reseller()
            ->firstOrFail();

        $ownerShop = $product->owner?->resellerShop();
        $country = country::where('name', 'Bangladesh')->firstOrFail();
        $states = state::where('country_id', $country->id)->orderBy('name')->get();
        $price = $product->offer_type ? $product->discount : $product->price;

        return Inertia::render('Products/Order', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'title' => $product->title,
                'slug' => $product->slug,
                'description' => $product->description,
                'thumbnail' => $product->thumbnail,
                'video' => $product->video,
                'video_url' => $product->video ? asset('storage/' . $product->video) : null,
                'offer_type' => $product->offer_type,
                'discount' => $product->discount,
                'price' => $product->price,
                'unit' => $product->unit,
                'shipping_note' => $product->shipping_note,
                'shipping_in_dhaka' => $product->shipping_in_dhaka,
                'shipping_out_dhaka' => $product->shipping_out_dhaka,
                'cod' => (bool) $product->cod,
                'courier' => (bool) $product->courier,
                'hand' => (bool) $product->hand,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                    'slug' => $product->category->slug,
                ] : null,
                'attr' => $product->attr ? [
                    'name' => $product->attr->name,
                    'value' => $product->attr->value,
                ] : null,
                'showcase' => $product->showcase->map(fn($image) => [
                    'id' => $image->id,
                    'image' => $image->image,
                ])->values(),
                'owner' => [
                    'id' => $product->owner?->id,
                    'name' => $product->owner?->name,
                    'shop' => $ownerShop ? [
                        'id' => $ownerShop->id,
                        'shop_name_en' => $ownerShop->shop_name_en,
                        'address' => $ownerShop->address,
                        'phone' => $ownerShop->phone,
                    ] : null,
                ],
                'comments' => $product->comments
                    ->sortByDesc('created_at')
                    ->values()
                    ->map(fn($comment) => [
                        'id' => $comment->id,
                        'user_id' => $comment->user_id,
                        'comments' => $comment->comments,
                        'created_at_human' => $comment->created_at?->diffForHumans(),
                        'user' => [
                            'name' => $comment->user?->name,
                        ],
                    ]),
            ],
            'states' => $states,
            'initialPrice' => $price,
        ]);
    }

    public function cities($state)
    {
        return city::where('state_id', $state)->orderBy('name')->get(['id', 'name']);
    }

    public function store(Request $request, $id, $slug)
    {
        $product = Product::query()->where('id', $id)->active()->reseller()->firstOrFail();

        $rules = [
            'quantity' => ['required', 'integer', 'min:1'],
            'phone' => ['required'],
            'district' => ['required'],
            'upozila' => ['required'],
            'location' => ['required'],
            'delevery' => ['required'],
            'area_condition' => ['nullable'],
            'house_no' => ['nullable'],
            'road_no' => ['nullable'],
        ];

        if (!empty($product->attr?->value)) {
            $rules['size'] = ['required'];
        }

        $data = $request->validate($rules);

        if (auth()->id() === $product->user_id) {
            return back()->with('warning', "You can't purchase your own product");
        }

        $price = $product->offer_type ? $product->discount : $product->price;
        $shipping = $data['delevery'] === 'hand'
            ? 0
            : (($data['area_condition'] ?? 'Dhaka') === 'Dhaka'
                ? $product->shipping_in_dhaka
                : $product->shipping_out_dhaka);
        $total = $price * (int) $data['quantity'];

        $order = Order::create([
            'user_id' => auth()->id(),
            'user_type' => 'user',
            'belongs_to' => $product->user_id,
            'belongs_to_type' => 'reseller',
            'status' => 'Pending',
            'quantity' => $data['quantity'],
            'total' => $total,
            'delevery' => $data['delevery'],
            'number' => $data['phone'],
            'area_condition' => $data['area_condition'] ?? 'Dhaka',
            'district' => $data['district'],
            'upozila' => $data['upozila'],
            'location' => $data['location'],
            'phone' => $data['phone'],
            'road_no' => $data['road_no'] ?? null,
            'house_no' => $data['house_no'] ?? null,
            'shipping' => $shipping,
            'target_area' => $data['upozila'],
        ]);

        CartOrder::create([
            'user_id' => auth()->id(),
            'user_type' => 'user',
            'belongs_to' => $product->user_id,
            'belongs_to_type' => 'reseller',
            'order_id' => $order->id,
            'product_id' => $product->id,
            'size' => $data['size'] ?? null,
            'price' => $price,
            'total' => $total,
            'quantity' => $data['quantity'],
            'buying_price' => $product->buying_price ?? '0',
        ]);

        ProductComissionController::dispatchProductComissionsListeners($order->id);

        return redirect()->route('user.orders.view');
    }
}
