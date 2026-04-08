<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductComissionController;
use App\Models\Order;
use App\Models\ResellerResellProfits;
use App\Models\TakeComissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class OrdersController extends Controller
{
    public function indexReact(Request $request): Response
    {
        $date = $request->query('date', 'today');
        $search = $request->query('search', '');
        $sd = $request->query('sd', now()->format('Y-m-d'));
        $ed = $request->query('ed', now()->format('Y-m-d'));
        $qf = $request->query('qf', 'id');
        $type = $request->query('type');
        $status = $request->query('status');
        $pagn = (int) config('app.paginate');

        $query = Order::query()->with(['user', 'seller', 'comissionsInfo']);

        if ($search) {
            $query->where([$qf => $search]);
        }

        if ($date) {
            switch ($date) {
                case 'today':
                    $sd = '';
                    $ed = '';
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;
                case 'between':
                    if (!empty($sd) && !empty($ed)) {
                        $query->whereBetween('created_at', [$sd, Carbon::parse($ed)->endOfDay()]);
                    }
                    break;
            }
        }

        if ($type) {
            $query->where(['user_type' => $type]);
        }

        if ($status) {
            $query->where(['status' => $status]);
        }

        $orders = $query->orderBy('id', 'desc')->paginate($pagn)->withQueryString();
        $or = Order::all();
        $totalCom = 0;

        $items = $orders->getCollection()->map(function (Order $item) use (&$totalCom) {
            $comission = $item->comissionsInfo()->sum('take_comission');
            $totalCom += $comission;

            return [
                'id' => $item->id,
                'user' => [
                    'id' => $item->user?->id ?? '',
                    'name' => $item->user?->name ?? 'N/A',
                    'phone' => $item->user?->phone ?? 'N/A',
                    'email' => $item->user?->email ?? 'N/A',
                ],
                'seller' => [
                    'id' => $item->seller?->id ?? '',
                    'name' => $item->seller?->name ?? 'N/A',
                    'phone' => $item->seller?->phone ?? 'N/A',
                    'email' => $item->seller?->email ?? 'N/A',
                ],
                'user_type' => $item->user_type,
                'belongs_to_type' => $item->belongs_to_type,
                'status' => $item->status,
                'total' => $item->total ?? 0,
                'comission' => $comission ?? 0,
                'created_at_formatted' => $item->created_at?->toFormattedDateString(),
            ];
        })->values()->all();

        return Inertia::render('Auth/system/orders/index', [
            'filters' => compact('date', 'search', 'sd', 'ed', 'qf', 'type', 'status'),
            'stats' => [
                'orders' => $or->count(),
                'amount' => $or->sum('total'),
                'user_to_reseller' => $or->where('belongs_to_type', 'reseller')->count(),
                'reseller_to_vendor' => $or->where('belongs_to_type', 'vendor')->count(),
            ],
            'orders' => [
                'data' => $items,
                'links' => $orders->linkCollection()->toArray(),
                'sum_total' => $orders->sum('total'),
                'sum_comission' => $totalCom,
                'count' => count($items),
            ],
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        Order::destroy($id);

        return redirect()->back()->with('success', 'Deleted !');
    }

    public function printReact(Request $request): Response
    {
        $date = $request->query('date');
        $search = $request->query('search', '');
        $sd = $request->query('sd', now()->format('Y-m-d'));
        $ed = $request->query('ed', now()->format('Y-m-d'));
        $qf = $request->query('qf', 'id');
        $type = $request->query('type');
        $status = $request->query('status');

        $query = Order::query()->with(['user', 'seller', 'comissionsInfo']);

        if ($search) {
            $query->where([$qf => $search]);
        }

        if ($date) {
            switch ($date) {
                case 'today':
                    $sd = '';
                    $ed = '';
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;
                case 'between':
                    if ($sd && $ed) {
                        $query->whereBetween('created_at', [$sd, Carbon::parse($ed)->endOfDay()]);
                    }
                    break;
            }
        }

        if ($type) {
            $query->where(['user_type' => $type]);
        }

        if ($status) {
            $query->where(['status' => $status]);
        }

        $orders = $query->get();
        $totalCom = 0;

        return Inertia::render('Auth/system/orders/PrintSummery', [
            'filters' => [
                'sd_formatted' => Carbon::parse($sd)->format('d/m/Y'),
                'ed_formatted' => Carbon::parse($ed)->format('d/m/Y'),
            ],
            'orders' => $orders->map(function (Order $item) use (&$totalCom) {
                $comission = $item->comissionsInfo()->sum('take_comission');
                $totalCom += $comission;

                return [
                    'id' => $item->id,
                    'user' => [
                        'name' => $item->user?->name ?? 'N/A',
                        'phone' => $item->user?->phone ?? 'N/A',
                        'email' => $item->user?->email ?? 'N/A',
                    ],
                    'seller' => [
                        'name' => $item->seller?->name ?? 'N/A',
                        'phone' => $item->seller?->phone ?? 'N/A',
                        'email' => $item->seller?->email ?? 'N/A',
                    ],
                    'user_type' => $item->user_type,
                    'belongs_to_type' => $item->belongs_to_type,
                    'status' => $item->status,
                    'total' => $item->total ?? 0,
                    'comission' => $comission,
                    'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                ];
            })->values()->all(),
            'summary' => [
                'count' => $orders->count(),
                'sum_total' => $orders->sum('total'),
                'sum_comission' => $totalCom,
            ],
        ]);
    }

    public function detailsReact(Request $request, int $id): Response
    {
        $nav = $request->query('nav', 'tab');
        $order = Order::with(['user', 'cartOrders.product', 'comissionsInfo', 'seller'])->findOrFail($id);
        $resellerProfit = ResellerResellProfits::where(['order_id' => $id])->get();
        $earnComissions = TakeComissions::where(['order_id' => $id])->get();

        return Inertia::render('Auth/system/orders/Details', [
            'nav' => $nav,
            'order' => [
                'id' => $order->id,
                'user_type' => $order->user_type,
                'belongs_to_type' => $order->belongs_to_type,
                'created_at_daytime' => $order->created_at?->toDayDateTimeString(),
                'location' => $order->location,
                'house_no' => $order->house_no ?? 'Not Defined !',
                'road_no' => $order->road_no ?? 'Not Defined !',
                'number' => $order->number,
                'shipping' => $order->shipping ?? 0,
                'user' => [
                    'name' => $order->user?->name ?? 'Not Found !',
                ],
                'cart_orders' => $order->cartOrders->map(function ($item, $key) use ($order) {
                    return [
                        'id' => $item->id ?? 'N/A',
                        'product_title' => $item->product?->title ?? 'N/A',
                        'product_thumbnail' => $item->product?->thumbnail,
                        'is_resel' => (bool) ($item->product?->isResel ?? false),
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'total' => $item->total,
                        'size' => $item->size ?? 'N/A',
                        'buying_price' => $item->product?->buying_price ?? 'N/A',
                        'profit' => ($item->price - $item->buying_price) * $item->quantity,
                        'comission' => $item->order?->comissionsInfo[$key]?->take_comission ?? 0,
                    ];
                })->values()->all(),
                'cart_sum_total' => $order->cartOrders->sum('total'),
            ],
            'earnFilters' => [
                'where' => 'order_id',
                'wid' => $order->id,
                'from_formatted' => Carbon::parse(now())->format('d/m/Y'),
                'to_formatted' => Carbon::parse(now())->format('d/m/Y'),
            ],
            'earnComissions' => $earnComissions->map(fn (TakeComissions $item) => [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'order_id' => $item->order_id ?? 0,
                'product_id' => $item->product_id ?? 0,
                'buying_price' => $item->buying_price ?? 0,
                'selling_price' => $item->selling_price ?? 0,
                'profit' => $item->profit ?? 0,
                'comission_range' => $item->comission_range ?? 0,
                'take_comission' => $item->take_comission ?? 0,
                'distribute_comission' => $item->distribute_comission ?? 0,
                'store' => $item->store ?? 0,
                'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                'confirmed' => (bool) $item->confirmed,
            ])->values()->all(),
            'resellerProfit' => $resellerProfit->map(fn ($item) => [
                'id' => $item->id,
                'buy' => $item->buy,
                'sel' => $item->sel,
                'profit' => $item->profit,
                'confirmed' => (bool) $item->confirmed,
                'created_at_formatted' => $item->created_at?->toFormattedDateString(),
            ])->values()->all(),
            'reseller_profit_sum' => $resellerProfit->sum('profit'),
        ]);
    }

    public function confirmResellerProfit(int $id): RedirectResponse
    {
        $pc = new ProductComissionController();
        $pc->transferResellerResellProfit($id);

        return redirect()->back()->with('success', 'Profit Rounded !');
    }

    public function refundResellerProfit(int $id): RedirectResponse
    {
        $pc = new ProductComissionController();
        $pc->refundResellerResellProfit($id);

        return redirect()->back()->with('success', 'Profit Rounded !');
    }
}
