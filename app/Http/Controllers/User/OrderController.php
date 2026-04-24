<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $nav = $request->query('nav', 'Pending');
        $find = trim((string) $request->query('find', ''));

        $query = Order::with('cartOrders')
            ->where([
                'user_id' => $request->user()->id,
                'user_type' => 'user',
            ])
            ->latest('id');

        if ($find !== '') {
            $query->where(function ($subQuery) use ($find) {
                $subQuery
                    ->where('id', 'like', '%' . $find . '%')
                    ->orWhere('status', 'like', '%' . $find . '%')
                    ->orWhere('total', 'like', '%' . $find . '%')
                    ->orWhere('number', 'like', '%' . $find . '%')
                    ->orWhereHas('cartOrders.product', function ($productQuery) use ($find) {
                        $productQuery
                            ->where('name', 'like', '%' . $find . '%')
                            ->orWhere('slug', 'like', '%' . $find . '%');
                    });
            });
        }

        $orders = $query->paginate(config('app.paginate'))->withQueryString();

        return Inertia::render('User/Orders', [
            'filters' => [
                'nav' => $nav,
                'find' => $find,
            ],
            'orders' => [
                'data' => $orders->getCollection()->map(function ($order) {
                    $shop = $order->shop()->first();

                    return [
                        'id' => $order->id,
                        'status' => $order->status,
                        'quantity' => $order->quantity,
                        'total' => $order->total,
                        'cart_orders_count' => $order->cartOrders?->count() ?? 0,
                        'shop' => [
                            'shop_name_en' => $shop?->shop_name_en,
                            'shop_name_bn' => $shop?->shop_name_bn,
                            'village' => $shop?->village ?? 'n/a',
                            'upozila' => $shop?->upozila ?? 'n/a',
                            'district' => $shop?->district ?? 'n/a',
                        ],
                    ];
                })->values()->all(),
                'links' => collect($orders->linkCollection())->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
                'total' => $orders->total(),
            ],
            'nav' => $nav,
            'printUrl' => route('user.orders.print', [
                'find' => $find,
            ]),
            // 'roleNames' => auth()->user()->getRoleNames(),
        ]);
    }

    public function print(Request $request)
    {
        $find = trim((string) $request->query('find', ''));

        $query = Order::with('cartOrders')
            ->where([
                'user_id' => $request->user()->id,
                'user_type' => 'user',
            ])
            ->latest('id');

        if ($find !== '') {
            $query->where(function ($subQuery) use ($find) {
                $subQuery
                    ->where('id', 'like', '%' . $find . '%')
                    ->orWhere('status', 'like', '%' . $find . '%')
                    ->orWhere('total', 'like', '%' . $find . '%')
                    ->orWhere('number', 'like', '%' . $find . '%')
                    ->orWhereHas('cartOrders.product', function ($productQuery) use ($find) {
                        $productQuery
                            ->where('name', 'like', '%' . $find . '%')
                            ->orWhere('slug', 'like', '%' . $find . '%');
                    });
            });
        }

        $orders = $query->get()->map(function ($order) {
            $shop = $order->shop()->first();

            return [
                'id' => $order->id,
                'status' => $order->status,
                'quantity' => $order->quantity,
                'total' => $order->total,
                'cart_orders_count' => $order->cartOrders?->count() ?? 0,
                'shop' => [
                    'shop_name_en' => $shop?->shop_name_en,
                    'shop_name_bn' => $shop?->shop_name_bn,
                    'village' => $shop?->village ?? 'n/a',
                    'upozila' => $shop?->upozila ?? 'n/a',
                    'district' => $shop?->district ?? 'n/a',
                ],
            ];
        })->values()->all();

        return Inertia::render('User/Orders/Print', [
            'filters' => [
                'find' => $find,
            ],
            'orders' => $orders,
        ]);
    }

    public function destroy(Order $order)
    {
        if ($order->user_id === auth()->id()) {
            $order->delete();
        }

        return back();
    }

    public function cancel(Order $order)
    {
        if ($order->user_id === auth()->id()) {
            $order->update([
                'status' => 'Cancelled',
            ]);
        }

        return back();
    }

    public function details(Request $request, $id)
    {
        $order = Order::with(['cartOrders.product', 'hasRider.rider'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        $riderAssignment = $order->hasRider()->latest()->first();

        return Inertia::render('User/Orders/Details', [
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'created_at' => Carbon::parse($order->created_at)->toFormattedDateString(),
                'created_time' => Carbon::parse($order->created_at)->format('H:i a'),
                'received_at' => $order->received_at,
                'total' => (float) ($order->total ?? 0),
                'shipping' => (float) ($order->shipping ?? 0),
                'delevery' => $order->delevery,
                'area_condition' => $order->area_condition,
                'location' => $order->location,
                'number' => $order->number,
                'cart_orders' => $order->cartOrders->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'quantity' => (int) ($item->quantity ?? 0),
                        'size' => $item->size,
                        'price' => (float) ($item->price ?? 0),
                        'total' => (float) ($item->total ?? 0),
                        'product' => [
                            'id' => $item->product?->id,
                            'name' => $item->product?->name ?? 'N/A',
                            'slug' => $item->product?->slug ?? '',
                            'thumbnail' => $item->product?->thumbnail,
                        ],
                    ];
                })->values(),
                'assigned_rider' => $riderAssignment ? [
                    'name' => $riderAssignment?->rider?->name,
                    'phone' => $riderAssignment?->phone ?? $riderAssignment?->rider?->phone,
                ] : null,
            ],
        ]);
    }

    public function markReceived(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)->findOrFail($id);

        if (!$order->received_at) {
            $order->update([
                'received_at' => Carbon::now(),
            ]);
        }

        return back()->with('success', 'Order successfully marked as received.');
    }
}
