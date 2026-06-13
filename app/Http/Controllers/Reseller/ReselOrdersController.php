<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\TableDateFilter;
use Illuminate\Database\Eloquent\Builder;
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
            'find' => trim((string) $request->query('find', '')),
        ];

        $data = $this->buildOrderQuery($filters, TableDateFilter::hasOnlyDefaultFilters($request, [
            'nav' => 'Pending',
            'delivery' => 'all',
            'create' => 'all',
            'type' => 'All',
            'area' => 'all',
        ]))
            ->with([
                'seller:id,name,phone',
                'seller.requestsToBeVendor:id,user_id,shop_name_en,status',
                'syncDetails:id,reseller_order_id,user_order_id,user_cart_order_id',
                'resellerProfit:id,order_id,profit',
            ])
            ->orderByDesc('id')
            ->paginate(config('app.paginate'))
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
            'printUrl' => route('reseller.resel-order.print', $filters),
            'list' => [
                'data' => $data->getCollection()->map(fn (Order $item, int $index) => $this->serializeOrder($item, ($data->firstItem() ?? 1) + $index))->values()->all(),
                'links' => $data->linkCollection()->map(fn ($link) => [
                    'url' => $link['url'],
                    'label' => $link['label'],
                    'active' => $link['active'],
                ])->values()->all(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'total' => $data->total(),
            ],
        ]);
    }

    public function print(Request $request): Response
    {
        $filters = [
            'nav' => $request->query('nav', 'Pending'),
            'delivery' => $request->query('delivery', 'all'),
            'create' => $request->query('create', 'all'),
            'type' => $request->query('type', 'All'),
            'start_date' => $request->query('start_date', ''),
            'end_date' => $request->query('end_date', ''),
            'area' => $request->query('area', 'all'),
            'find' => trim((string) $request->query('find', '')),
        ];

        $orders = $this->buildOrderQuery($filters, TableDateFilter::hasOnlyDefaultFilters($request, [
            'nav' => 'Pending',
            'delivery' => 'all',
            'create' => 'all',
            'type' => 'All',
            'area' => 'all',
        ]))
            ->with([
                'seller:id,name,phone',
                'seller.requestsToBeVendor:id,user_id,shop_name_en,status',
                'syncDetails:id,reseller_order_id,user_order_id,user_cart_order_id',
                'resellerProfit:id,order_id,profit',
            ])
            ->orderByDesc('id')
            ->get()
            ->map(fn (Order $item, int $index) => $this->serializeOrder($item, $index + 1))
            ->values()
            ->all();

        return Inertia::render('Reseller/Resel/Orders/Print', [
            'filters' => $filters,
            'orders' => $orders,
        ]);
    }

    private function buildOrderQuery(array $filters, bool $defaultToday = false): Builder
    {
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

        if ($filters['delivery'] !== 'all') {
            $query->where('delevery', $filters['delivery']);
        }

        if ($filters['area'] !== 'all') {
            $query->where('target_area', $filters['area']);
        }

        if ($filters['create'] === 'day') {
            $query->whereDate('created_at', Carbon::parse($filters['start_date'] ?: now()->toDateString())->toDateString());
        } elseif ($filters['create'] === 'between' && $filters['start_date'] && $filters['end_date']) {
            $query->whereBetween('created_at', [
                Carbon::parse($filters['start_date'])->startOfDay(),
                Carbon::parse($filters['end_date'])->endOfDay(),
            ]);
        } elseif ($defaultToday) {
            $query->whereDate('created_at', Carbon::today());
        }

        if ($filters['find'] !== '') {
            $search = $filters['find'];

            $query->where(function (Builder $orderQuery) use ($search) {
                $orderQuery
                    ->where('id', $search)
                    ->orWhere('number', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%')
                    ->orWhere('district', 'like', '%' . $search . '%')
                    ->orWhere('upozila', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhereHas('seller', function (Builder $sellerQuery) use ($search) {
                        $sellerQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%')
                            ->orWhereHas('requestsToBeVendor', function (Builder $shopQuery) use ($search) {
                                $shopQuery->where('shop_name_en', 'like', '%' . $search . '%');
                            });
                    });
            });
        }

        return $query;
    }

    private function serializeOrder(Order $item, int $serial): array
    {
        $vendorShop = $item->seller?->requestsToBeVendor?->firstWhere('status', 'Active');
        $synced = $item->syncDetails;
        $isSynced = (bool) $synced;
        $profit = $isSynced || ($item->name === 'Sync')
            ? ($item->resellerProfit?->sum('profit') ?? 0)
            : 0;

        return [
            'sl' => $serial,
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
    }
}
