<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Models\CartOrder;
use App\Models\cod;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ConsignmentController extends Controller
{
    public function indexReact(Request $request)
    {
        $user = $request->user();
        $riderInfo = [];
        $orders = [];

        if ($user?->requestsToBeRider() && $user?->isRider()) {
            $rider = $user->isRider()->load('targetedArea');
            $riderInfo = [
                'id' => $rider?->id,
                'targeted_area' => $rider?->targeted_area,
                'targeted_area_name' => $rider?->targetedArea?->name ?? 'N/A',
                'comission' => $rider?->comission ?? 0,
            ];
        }

        if (!empty($riderInfo)) {
            $orders = Order::query()
                ->with(['cartOrders.product'])
                ->where(function ($itm) use ($riderInfo) {
                    $itm->where('target_area', 'like', '%' . ($riderInfo['targeted_area'] ?? '') . '%')
                        ->whereIn('status', ['Accept']);
                })
                ->whereDoesntHave('hasRider', function ($query) {
                    $query->where('rider_id', auth()->id());
                })
                ->get()
                ->map(function ($order) use ($riderInfo) {
                    $totalForNotResel = 0;
                    $thumbnails = [];

                    foreach ($order->cartOrders as $item) {
                        if (!$item->product?->isResel) {
                            $totalForNotResel += $item->total;
                            if (!empty($item->product?->thumbnail)) {
                                $thumbnails[] = $item->product->thumbnail;
                            }
                        }
                    }

                    $firstCartOrder = $order->cartOrders->first();
                    $systemComission = ($order->shipping * ($riderInfo['comission'] ?? 0)) / 100;

                    return [
                        'id' => $order->id,
                        'location' => $order->location,
                        'shipping' => $order->shipping,
                        'displayable' => $order->cartOrders->count() === 1 && !$firstCartOrder?->product?->isResel,
                        'thumbnails' => $thumbnails,
                        'total_for_not_resel' => $totalForNotResel,
                        'system_comission' => $systemComission,
                        'display_total' => $totalForNotResel + $systemComission,
                    ];
                })
                ->values()
                ->all();
        }

        return Inertia::render('Rider/Consignment/Index', [
            'riderInfo' => $riderInfo,
            'orders' => $orders,
        ]);
    }

    public function confirmOrder(Request $request, Order $order)
    {
        if (!auth()?->user()?->isRider()) {
            return back()->with('error', 'Your are not a Rider !');
        }

        if (
            cod::where('order_id', '=', $order->id)->accept()->exists() ||
            cod::where('order_id', '=', $order->id)->complete()->exists() ||
            cod::where('order_id', '=', $order->id)->pending()->exists()
        ) {
            return back()->with('error', 'Already Picked !');
        }

        if ($order->delevery == 'cash' && $order->status == 'Accept') {
            $riderCmRange = auth()->user()?->isRider()?->comission;
            $systemCm = ($order->shipping * $riderCmRange) / 100;
            $totalNotResel = $order->cartOrders->each(function ($item) {
                return !$item->product?->isResel;
            })->sum('total');

            cod::create([
                'order_id' => $order->id,
                'seller_id' => $order->belongs_to,
                'seller_type' => $order->belongs_to_type,
                'user_id' => $order->user_id,
                'rider_id' => Auth::id(),
                'amount' => $totalNotResel,
                'due_amount' => $totalNotResel,
                'total_amount' => $totalNotResel + $systemCm,
                'rider_amount' => $order->shipping,
                'comission' => $riderCmRange,
                'system_comission' => $systemCm,
            ]);

            $order->update([
                'status' => 'Picked',
            ]);

            return back()->with('success', 'Order confirmed successfully.');
        }

        return back()->with('error', 'Order not found or already confirmed.');
    }

    public function show($id)
    {
        $cod = cod::query()
            ->with(['order', 'seller', 'user'])
            ->findOrFail($id);

        $seller = User::findOrFail($cod->seller_id);
        $buyer = User::findOrFail($cod->user_id);
        $shop = $seller->account_type() === 'reseller'
            ? $seller->resellerShop()
            : $seller->vendorShop();

        $cartOrders = CartOrder::query()
            ->with('product')
            ->where('order_id', $cod->order_id)
            ->get()
            ->map(function (CartOrder $item) {
                return [
                    'id' => $item->id,
                    'product' => [
                        'id' => $item->product?->id,
                        'title' => $item->product?->title ?? 'N/A',
                        'thumbnail' => $item->product?->thumbnail,
                    ],
                ];
            })
            ->values();

        return Inertia::render('Rider/Consignment/View', [
            'id' => $id,
            'cod' => [
                'id' => $cod->id,
                'amount' => $cod->amount,
                'paid_amount' => $cod->paid_amount,
                'due_amount' => $cod->due_amount,
                'system_comission' => $cod->system_comission,
                'total_amount' => $cod->total_amount,
                'status' => $cod->status,
            ],
            'order' => [
                'id' => $cod->order?->id,
                'location' => $cod->order?->location,
                'number' => $cod->order?->number,
            ],
            'cartOrders' => $cartOrders,
            'seller' => [
                'id' => $seller->id,
                'name' => $seller->name,
            ],
            'buyer' => [
                'id' => $buyer->id,
                'name' => $buyer->name,
            ],
            'shop' => [
                'shop_name_en' => $shop?->shop_name_en,
                'address' => $shop?->address,
                'district' => $shop?->district,
                'upozila' => $shop?->upozila,
                'village' => $shop?->village,
                'phone' => $shop?->phone,
            ],
        ]);
    }
}
