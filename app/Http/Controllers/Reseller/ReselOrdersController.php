<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class ReselOrdersController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = [
            'nav' => $request->query('nav', 'Pending'),
            'delivery' => $request->query('delivery', 'all'),
            'create' => $request->query('create', 'all'),
            'type' => $request->query('type', 'All'),
            'start_date' => $request->query('start_date', ''),
            'end_date' => $request->query('end_date', ''),
            'area' => $request->query('area', 'all'),
        ];

        $query = Order::query()->where([
            'user_id' => auth()->id(),
            'user_type' => 'reseller',
        ]);

        if ($filters['nav'] === 'Trashed') {
            $query->onlyTrashed();
        } elseif ($filters['nav'] !== 'All') {
            $query->where('status', $filters['nav']);
        }

        if ($filters['type'] !== 'All') {
            $query->where('name', $filters['type']);
        }

        if ($filters['create'] === 'day') {
            $query->whereDate('created_at', Carbon::parse($filters['start_date'] ?: now()->toDateString())->endOfDay());
        } elseif ($filters['create'] === 'between' && $filters['start_date'] && $filters['end_date']) {
            $query->whereBetween('created_at', [
                Carbon::parse($filters['start_date'])->endOfDay(),
                Carbon::parse($filters['end_date'])->endOfDay(),
            ]);
        }

        $data = $query
            ->with([
                'seller:id,name,phone',
                'seller.requestsToBeVendor:id,user_id,shop_name_en,status',
                'syncDetails:id,reseller_order_id,user_order_id,user_cart_order_id',
                'resellerProfit:id,order_id,profit',
            ])
            ->orderByDesc('id')
            ->paginate(100)
            ->withQueryString();

        $summaryBase = auth()->user()->myOrdersAsReseller();

        return Inertia::render('Reseller/Resel/Orders/Index', [
            'activeNav' => auth()->user()->active_nav,
            'filters' => $filters,
            'summary' => [
                'orders' => (clone $summaryBase)->count(),
                'pending' => (clone $summaryBase)->where('status', 'Pending')->count(),
                'cancel' => (clone $summaryBase)->where('status', 'Cancel')->count(),
                'cancelled' => (clone $summaryBase)->where('status', 'Cancelled')->count(),
                'accept' => (clone $summaryBase)->where('status', 'Accept')->count(),
            ],
            'list' => [
                'data' => $data->getCollection()->map(function (Order $item) {
                    $vendorShop = $item->seller?->requestsToBeVendor?->firstWhere('status', 'Active');
                    $synced = $item->syncDetails;
                    $isSynced = (bool) $synced;
                    $profit = $isSynced || ($item->name === 'Sync')
                        ? ($item->resellerProfit?->sum('profit') ?? 0)
                        : 0;

                    return [
                        'id' => $item->id,
                        'shop_name_en' => $vendorShop->shop_name_en ?? '',
                        'shop_id' => $vendorShop->id ?? null,
                        'seller_phone' => $item->seller?->phone ?? '',
                        'sync' => $isSynced ? [
                            'user_order_id' => $synced->user_order_id,
                            'user_cart_order_id' => $synced->user_cart_order_id,
                            'view_url' => route('vendor.orders.view', ['order' => $synced->user_order_id]),
                        ] : null,
                        'order_name' => $item->name,
                        'total' => $item->total ?? 0,
                        'shipping' => $item->shipping ?? 0,
                        'profit' => $profit,
                        'delevery' => $item->delevery,
                        'location' => $item->location,
                        'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                        'status' => $item->status,
                        'view_url' => route('reseller.order.view', ['order' => $item->id]),
                        'print_url' => route('vendor.orders.print', ['order' => $item->id]),
                    ];
                })->values()->all(),
            ],
        ]);
    }
}
