<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $nav = $request->query('nav', 'Pending');

        $orders = Order::with('cartOrders')
            ->where([
                'user_id' => $request->user()->id,
                'user_type' => 'user',
            ])
            ->latest('id')
            ->get();

        $orders->each(function ($order) {
            $order->shop = $order->shop()->first();
        });

        return Inertia::render('User/Orders', [
            'orders' => $orders,
            'nav' => $nav,
            // 'roleNames' => auth()->user()->getRoleNames(),
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
                'total' => $order->total,
                'shipping' => $order->shipping,
                'delevery' => $order->delevery,
                'area_condition' => $order->area_condition,
                'location' => $order->location,
                'number' => $order->number,
                'cart_orders' => $order->cartOrders->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'quantity' => $item->quantity,
                        'size' => $item->size,
                        'price' => $item->price,
                        'total' => $item->total,
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
