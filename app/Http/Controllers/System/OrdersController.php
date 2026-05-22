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
        $date = $request->query('date', '');
        $search = $request->query('search', '');
        $sd = $request->query('sd');
        $ed = $request->query('ed');
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
            $comission = $this->money($item->comissionsInfo()->sum('take_comission'));
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
                'total' => $this->money($item->total),
                'comission' => $comission,
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
                'sum_total' => $this->money($orders->sum('total')),
                'sum_comission' => $this->money($totalCom),
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
        $sd = $request->query('sd');
        $ed = $request->query('ed');
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
                'sd_formatted' => $sd ? Carbon::parse($sd)->format('d/m/Y') : '',
                'ed_formatted' => $ed ? Carbon::parse($ed)->format('d/m/Y') : '',
            ],
            'orders' => $orders->map(function (Order $item) use (&$totalCom) {
                $comission = $this->money($item->comissionsInfo()->sum('take_comission'));
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
                    'total' => $this->money($item->total),
                    'comission' => $comission,
                    'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                ];
            })->values()->all(),
            'summary' => [
                'count' => $orders->count(),
                'sum_total' => $this->money($orders->sum('total')),
                'sum_comission' => $this->money($totalCom),
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
                'shipping' => $this->money($order->shipping),
                'user' => [
                    'name' => $order->user?->name ?? 'Not Found !',
                ],
                'cart_orders' => $order->cartOrders->map(function ($item, $key) use ($order) {
                    $quantity = max(1, (int) ($item->quantity ?? 1));
                    $buyingPrice = is_numeric($item->buying_price)
                        ? (float) $item->buying_price
                        : (float) ($item->product?->buying_price ?? 0);

                    return [
                        'id' => $item->id ?? 'N/A',
                        'product_title' => $item->product?->title ?? 'N/A',
                        'product_thumbnail' => $item->product?->thumbnail,
                        'is_resel' => (bool) ($item->product?->isResel ?? false),
                        'price' => $this->money($item->price),
                        'quantity' => $quantity,
                        'total' => $this->money($item->total),
                        'size' => $item->size ?? 'N/A',
                        'buying_price' => $this->money($buyingPrice),
                        'profit' => $this->money(((float) ($item->price ?? 0) - $buyingPrice) * $quantity),
                        'comission' => $this->money($item->order?->comissionsInfo[$key]?->take_comission ?? 0),
                    ];
                })->values()->all(),
                'cart_sum_total' => $this->money($order->cartOrders->sum('total')),
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
                'buying_price' => $this->money($item->buying_price),
                'selling_price' => $this->money($item->selling_price),
                'profit' => $this->money($item->profit),
                'comission_range' => $this->money($item->comission_range),
                'take_comission' => $this->money($item->take_comission),
                'distribute_comission' => $this->money($item->distribute_comission),
                'store' => $this->money($item->store),
                'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                'confirmed' => (bool) $item->confirmed,
            ])->values()->all(),
            'resellerProfit' => $resellerProfit->map(fn ($item) => [
                'id' => $item->id,
                'buy' => $this->money($item->buy),
                'sel' => $this->money($item->sel),
                'profit' => $this->money($item->profit),
                'confirmed' => (bool) $item->confirmed,
                'created_at_formatted' => $item->created_at?->toFormattedDateString(),
            ])->values()->all(),
            'reseller_profit_sum' => $this->money($resellerProfit->sum('profit')),
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

    private function money($value): float
    {
        return round((float) ($value ?? 0), 2);
    }
}
