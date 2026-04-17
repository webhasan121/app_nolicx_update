<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductComissionController;
use App\Jobs\UpdateProductSalesIndex;
use App\Models\Order;
use App\Models\syncOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrdersController extends Controller
{
    public function index(Request $request): Response
    {
        $nav = $request->query('nav', 'Pending');
        $account = auth()->user()->account_type();
        $baseQuery = auth()->user()->orderToMe()->where(['belongs_to_type' => $account]);

        if ($nav === 'Trashed') {
            $query = (clone $baseQuery)->onlyTrashed();
        } else {
            $query = (clone $baseQuery)->where(['status' => $nav]);
        }

        $orders = $query
            ->with(['comissionsInfo:id,order_id,take_comission'])
            ->withCount('cartOrders')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Reseller/Orders/Index', [
            'activeNav' => auth()->user()->active_nav,
            'account' => $account,
            'filters' => [
                'nav' => $nav,
            ],
            'summary' => [
                'orders' => (clone $baseQuery)->count(),
                'pending' => (clone $baseQuery)->where(['status' => 'Pending'])->count(),
                'cancel' => (clone $baseQuery)->where(['status' => 'Cancel'])->count(),
                'cancelled' => (clone $baseQuery)->where(['status' => 'Cancelled'])->count(),
                'accept' => (clone $baseQuery)->where(['status' => 'Accept'])->count(),
            ],
            'orders' => [
                'data' => $orders->getCollection()->map(function (Order $item, int $index) use ($orders) {
                    return [
                        'sl' => (($orders->currentPage() - 1) * $orders->perPage()) + $index + 1,
                        'id' => $item->id,
                        'cart_orders_count' => $item->cart_orders_count ?? 0,
                        'quantity' => $item->quantity ?? 'N/A',
                        'total' => $item->total ?? 'N/A',
                        'shipping' => $item->shipping ?? 'N/A',
                        'status' => $item->status ?? 'Pending',
                        'created_at_human' => $item->created_at?->diffForHumans(),
                        'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                        'delevery' => $item->delevery,
                        'area_condition' => $item->area_condition,
                        'number' => $item->number ?? 'N/A',
                        'take_comission_sum' => $item->comissionsInfo?->sum('take_comission') ?? 0,
                        'view_url' => route('reseller.order.view', ['order' => $item->id]),
                        'print_url' => route('vendor.orders.cprint', ['order' => $item->id]),
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
        ]);
    }

    public function view(int $order): Response
    {
        $data = Order::query()
            ->with([
                'user',
                'seller',
                'cartOrders.product',
                'cartOrders.order.seller',
                'comissionsInfo.product',
                'hasRider.rider',
                'syncDetails',
                'resellerProfit',
            ])
            ->findOrFail($order);

        $rider = $data->hasRider?->sortByDesc('id')->first();
        $synced = $data->name === 'Resel'
            ? syncOrder::query()->where(['reseller_order_id' => $data->id])->first()
            : null;

        return Inertia::render('Reseller/Orders/View', [
            'order' => [
                'id' => $data->id,
                'status' => $data->status,
                'name' => $data->name,
                'user_type' => $data->user_type,
                'belongs_to_type' => $data->belongs_to_type,
                'delevery' => $data->delevery,
                'area_condition' => $data->area_condition,
                'shipping' => $data->shipping ?? 0,
                'total' => $data->total ?? 0,
                'created_at_daytime' => $data->created_at?->toDayDateTimeString(),
                'created_at_formatted' => $data->created_at?->toFormattedDateString(),
                'location' => $data->location,
                'house_no' => $data->house_no ?? 'Not Defined !',
                'road_no' => $data->road_no ?? 'Not Defined !',
                'number' => $data->number,
                'user' => [
                    'name' => $data->user?->name ?? 'Not Found !',
                ],
                'cart_orders' => $data->cartOrders->map(function ($item) {
                    $vendorShop = $item->order?->seller?->vendorShop();

                    return [
                        'id' => $item->id ?? 'N/A',
                        'product_title' => $item->product?->title ?? 'N/A',
                        'product_thumbnail' => $item->product?->thumbnail ? asset('storage/' . $item->product?->thumbnail) : null,
                        'owner_shop_name' => $vendorShop?->shop_name_en ?? '',
                        'owner_phone' => $item->order?->seller?->phone ?? '',
                        'owner_shop_url' => route('shops', [
                            'get' => $vendorShop?->id,
                            'slug' => $vendorShop?->shop_name_en ?? 'not_found',
                        ]),
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'total' => $item->total,
                        'size' => $item->size ?? 'N/A',
                        'buying_price' => $item->buying_price ?? 'N/A',
                        'profit' => $item->order?->name === 'Resel'
                            ? ((float) $item->price - (float) $item->buying_price) * (int) $item->quantity
                            : null,
                    ];
                })->values()->all(),
                'cart_sum_total' => $data->cartOrders->sum('total'),
                'cart_count' => $data->cartOrders->count(),
                'cart_quantity_sum' => $data->cartOrders->sum('quantity'),
                'reseller_profit_sum' => $data->name === 'Resel'
                    ? ($data->resellerProfit?->sum('profit') ?? 0)
                    : 0,
                'rider' => $rider ? [
                    'name' => $rider?->rider?->name,
                    'phone' => $rider?->phone ?? $rider?->rider?->phone,
                ] : null,
                'timeline' => $this->resellerTimeline($data, $rider),
                'synced' => $synced ? [
                    'user_order_id' => $synced->user_order_id,
                    'user_cart_order_id' => $synced->user_cart_order_id,
                    'url' => route('vendor.orders.view', ['order' => $synced->user_order_id]),
                ] : null,
                'comissions' => ($data->comissionsInfo ?? collect())->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'take_comission' => $item->take_comission ?? 0,
                        'product_name' => $item->product?->name ?? 'N/A',
                    ];
                })->values()->all(),
            ],
        ]);
    }

    public function updateStatus(Request $request, int $order): RedirectResponse
    {
        $payload = $request->validate([
            'status' => ['required', 'string'],
        ]);

        $data = Order::query()
            ->with('comissionsInfo')
            ->findOrFail($order);

        if ($data->status === 'Pending') {
            $requiredBalance = $data->comissionsInfo->sum('take_comission');

            if (auth()->user()->abailCoin() > $requiredBalance) {
                $data->status = $payload['status'];
                $data->save();

                $ct = new ProductComissionController();
                $ct->confirmTakeComissions($data->id);
                UpdateProductSalesIndex::dispatch();

                return redirect()->back();
            }

            return redirect()
                ->back()
                ->with('warning', "You Don't have requried balance to accept the order. You need ensure minimum" . $requiredBalance . " balance to procces the order ");
        }

        return redirect()->back();
    }

    private function resellerTimeline(Order $order, $rider): array
    {
        $placed = [
            'title' => 'Placed the order',
            'description' => $order->created_at?->toFormattedDateString(),
        ];

        $accepted = [
            'title' => 'Order has been accepted by seller.',
            'description' => null,
        ];

        $packed = [
            'title' => 'Order Packed.',
            'description' => 'Order product has been packed and ready for shipment.',
        ];

        $sent = [
            'title' => 'Order Send',
            'description' => $order->delevery === 'cash'
                ? 'Order has been send to ' . $order->location
                : 'Order has beed send to ' . $order->location . '.',
        ];

        $assigned = [
            'title' => in_array($order->status, ['Delivered', 'Confirmed'], true)
                ? 'Order Assigned'
                : 'Order Assignedd to Rider',
            'description' => $order->delevery === 'cash' && $rider
                ? 'Assigned to rider ' . ($rider?->rider?->name ?? 'N/A')
                : null,
        ];

        return match ($order->status) {
            'Pending' => [$placed],
            'Accept' => [$accepted, $placed],
            'Picked' => [$packed, $accepted, $placed],
            'Delivery' => $order->hasRider()->exists()
                ? [$assigned, $sent, $packed, $accepted, $placed]
                : [$sent, $packed, $accepted, $placed],
            'Delivered' => $order->hasRider()->exists()
                ? [[
                    'title' => 'Delivered',
                    'description' => 'Order has been marked as delivered to you by rider at.',
                ], $assigned, $sent, $packed, $accepted, $placed]
                : [],
            'Confirmed' => [[
                'title' => 'Success and Finished',
                'description' => null,
            ], $assigned, $sent, $packed, $accepted, $placed],
            default => [],
        };
    }
}
