<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductComissionController;
use App\Models\cod;
use App\Models\Order;
use App\Models\rider;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class OrdersController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = [
            'nav' => $request->query('nav', 'Pending'),
            'delivery' => $request->query('delivery', 'all'),
            'create' => $request->query('create', 'all'),
            'start_date' => $request->query('start_date', ''),
            'end_date' => $request->query('end_date', ''),
            'area' => $request->query('area', 'all'),
            'find' => trim((string) $request->query('find', '')),
        ];

        $account = auth()->user()->account_type();
        $query = auth()->user()->orderToMe()->where(['belongs_to_type' => $account]);

        if (in_array($filters['nav'], ['Trash', 'Trashed'], true)) {
            $query->onlyTrashed();
        } elseif ($filters['nav'] !== 'All') {
            $query->where('status', $filters['nav']);
        }

        if ($filters['delivery'] !== 'all') {
            $query->where('delevery', $filters['delivery']);
        }

        if ($filters['create'] === 'day' && !empty($filters['start_date'])) {
            $query->whereDate('created_at', Carbon::parse($filters['start_date'])->toDateString());
        } elseif ($filters['create'] === 'between' && !empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($filters['start_date'])->startOfDay(),
                Carbon::parse($filters['end_date'])->endOfDay(),
            ]);
        }

        if ($filters['area'] !== 'all') {
            $query->where('area_condition', $filters['area']);
        }

        if ($filters['find'] !== '') {
            $query->where(function ($builder) use ($filters) {
                $builder
                    ->where('id', 'like', '%' . $filters['find'] . '%')
                    ->orWhere('number', 'like', '%' . $filters['find'] . '%')
                    ->orWhere('location', 'like', '%' . $filters['find'] . '%')
                    ->orWhere('status', 'like', '%' . $filters['find'] . '%')
                    ->orWhereHas('user', function ($userQuery) use ($filters) {
                        $userQuery
                            ->where('name', 'like', '%' . $filters['find'] . '%')
                            ->orWhere('email', 'like', '%' . $filters['find'] . '%');
                    })
                    ->orWhereHas('cartOrders.product', function ($productQuery) use ($filters) {
                        $productQuery
                            ->where('title', 'like', '%' . $filters['find'] . '%')
                            ->orWhere('name', 'like', '%' . $filters['find'] . '%');
                    });
            });
        }

        $data = $query
            ->with(['user:id,name', 'comissionsInfo'])
            ->withCount('cartOrders')
            ->latest('id')
            ->paginate(config('app.paginate'))
            ->withQueryString();

        $summaryQuery = auth()->user()->orderToMe()->where(['belongs_to_type' => $account]);

        return Inertia::render('Vendor/Orders/Index', [
            'activeNav' => auth()->user()->active_nav,
            'filters' => $filters,
            'summary' => [
                'orders' => (clone $summaryQuery)->count(),
                'pending' => (clone $summaryQuery)->where('status', 'Pending')->count(),
                'cancel' => (clone $summaryQuery)->where('status', 'Cancel')->count(),
                'cancelled' => (clone $summaryQuery)->where('status', 'Cancelled')->count(),
                'accept' => (clone $summaryQuery)->where('status', 'Accept')->count(),
            ],
            'list' => [
                'data' => $data->getCollection()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'cart_orders_count' => $item->cart_orders_count ?? 0,
                        'quantity' => $item->quantity,
                        'total' => $item->total,
                        'shipping' => $item->shipping,
                        'status' => $item->status,
                        'created_at_human' => $item->created_at?->diffForHumans(),
                        'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                        'delevery' => $item->delevery,
                        'location' => $item->location,
                        'user_name' => $item->user?->name,
                        'number' => $item->number,
                        'comission' => $item->comissionsInfo?->sum('take_comission') ?? 0,
                    ];
                })->values()->all(),
                'sum_total' => $data->getCollection()->sum('total'),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'total' => $data->total(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'prev_page_url' => $data->previousPageUrl(),
                'next_page_url' => $data->nextPageUrl(),
                'links' => $data->linkCollection()->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags((string) $link['label']),
                        'active' => (bool) $link['active'],
                    ];
                })->values()->all(),
            ],
            'printUrl' => route('vendor.orders.summary.print', [
                'nav' => $filters['nav'],
                'delivery' => $filters['delivery'],
                'create' => $filters['create'],
                'start_date' => $filters['start_date'],
                'end_date' => $filters['end_date'],
                'area' => $filters['area'],
                'find' => $filters['find'],
            ]),
        ]);
    }

    public function view(Request $request, int $order): Response
    {
        $data = auth()->user()
            ->orderToMe()
            ->where(['belongs_to_type' => auth()->user()->account_type()])
            ->with(['user', 'cartOrders.product', 'comissionsInfo.product', 'hasRider.rider', 'resellerProfit'])
            ->findOrFail($order);

        $actor = auth()->user();
        $systemComissionRate = $actor->account_type() === 'reseller'
            ? ($actor->resellerShop()?->system_get_comission ?? 0)
            : ($actor->vendorShop()?->system_get_comission ?? 0);

        return Inertia::render('Vendor/Orders/View', [
            'order' => [
                'id' => $data->id,
                'account_type' => $actor->account_type(),
                'status' => $data->status,
                'name' => $data->name,
                'user_type' => $data->user_type,
                'belongs_to_type' => $data->belongs_to_type,
                'delevery' => $data->delevery,
                'area_condition' => $data->area_condition,
                'shipping' => $data->shipping,
                'total' => $data->total,
                'received_at' => $data->received_at,
                'created_at_daytime' => $data->created_at?->toDayDateTimeString(),
                'location' => $data->location,
                'house_no' => $data->house_no ?? 'Not Defined !',
                'road_no' => $data->road_no ?? 'Not Defined !',
                'number' => $data->number,
                'user' => [
                    'name' => $data->user?->name ?? 'Not Found !',
                ],
                'cart_orders' => $data->cartOrders->map(function ($item) use ($data) {
                    $profit = 0;
                    if ($item->order?->name === 'Resel') {
                        $profit = intval($item->buying_price) - intval($item->product?->buying_price);
                    } else {
                        $profit = intval($item->price) - intval($item->buying_price);
                    }

                    return [
                        'id' => $item->id ?? 'N/A',
                        'product_title' => $item->product?->title ?? 'N/A',
                        'product_thumbnail' => $item->product?->thumbnail ? asset('storage/' . $item->product?->thumbnail) : null,
                        'is_resel' => (bool) ($item->product?->isResel ?? false),
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'total' => $item->total,
                        'size' => $item->size ?? 'N/A',
                        'buying_price' => $item->buying_price ?? 'N/A',
                        'main_buying_price' => $item->product?->buying_price ?? 'N/A',
                        'profit_unit' => $profit,
                        'profit_total' => $profit * intval($item->quantity),
                    ];
                })->values()->all(),
                'cart_sum_total' => $data->cartOrders->sum('total'),
                'cart_count' => $data->cartOrders->count(),
                'cart_quantity_sum' => $data->cartOrders->sum('quantity'),
                'comission_sum' => $data->comissionsInfo?->sum('take_comission') ?? 0,
                'reseller_profit_sum' => $data->resellerProfit?->sum('profit') ?? 0,
                'has_rider_count' => $data->hasRider?->count() ?? 0,
                'system_comission_rate' => $systemComissionRate,
                'riders' => ($data->hasRider ?? collect())->map(function ($item) {
                    $rider = $item->rider;
                    $riderInfo = $rider?->isRider();

                    return [
                        'id' => $item->id,
                        'name' => $rider?->name ?? 'N/A',
                        'phone' => $rider?->phone ?? 'N/A',
                        'current_address' => $riderInfo?->current_address ?? 'N/A',
                        'targeted_area' => $riderInfo?->targeted_area ?? 'N/A',
                        'status' => $item->status ?? 'N/A',
                    ];
                })->values()->all(),
                'rider_candidates' => rider::query()
                    ->where(function ($query) use ($data) {
                        $query->where('targeted_area', 'like', '%' . ($data->district ?? '') . '%')
                            ->orWhere('targeted_area', 'like', '%' . ($data->upozila ?? '') . '%')
                            ->orWhere('targeted_area', 'like', '%' . ($data->location ?? '') . '%');
                    })
                    ->with('user:id,name')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'user_name' => $item->user?->name ?? 'N/A',
                            'phone' => $item->phone ?? 'N/A',
                        ];
                    })
                    ->values()
                    ->all(),
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
            'shipping' => ['nullable', 'numeric'],
        ]);

        $data = auth()->user()
            ->orderToMe()
            ->where(['belongs_to_type' => auth()->user()->account_type()])
            ->with(['comissionsInfo', 'resellerProfit'])
            ->findOrFail($order);

        if ($data->status === 'Confirm') {
            return redirect()->back()->with('error', 'Order Confirmed !');
        }

        $status = $payload['status'];
        $allow = ['Pending', 'Accept', 'Picked', 'Delivery', 'Delivered', 'Confirm', 'Hold', 'Cancel', 'Cancelled', 'Reject'];
        if (!in_array($status, $allow, true)) {
            return redirect()->back()->with('error', 'Invalid status');
        }

        if ($status === 'Accept' && array_key_exists('shipping', $payload) && $payload['shipping'] !== null) {
            $data->shipping = $payload['shipping'];
        }

        if ($data->status === 'Pending' && $status === 'Accept') {
            $ensureBalance = ($data->comissionsInfo?->sum('take_comission') ?? 0) + ($data->resellerProfit?->sum('profit') ?? 0);
            if (auth()->user()->abailCoin() < $ensureBalance) {
                return redirect()->back()->with('error', "You don't have required balance. Minimum {$ensureBalance} needed.");
            }
        }

        $data->status = $status;
        $data->save();

        if ($status === 'Confirm') {
            $ct = new ProductComissionController();
            $ct->confirmTakeComissions($data->id);
        }

        return redirect()->back()->with('success', 'Order status updated');
    }

    public function assignRider(Request $request, int $order): RedirectResponse
    {
        $payload = $request->validate([
            'rider_id' => ['required', 'integer', 'exists:riders,id'],
        ]);

        $data = auth()->user()
            ->orderToMe()
            ->where(['belongs_to_type' => auth()->user()->account_type()])
            ->findOrFail($order);

        $rdr = rider::find($payload['rider_id']);
        if (!$rdr) {
            return redirect()->back()->with('error', 'Rider not found');
        }

        cod::create([
            'order_id' => $data->id,
            'rider_id' => $rdr->user_id,
            'seller_id' => $data->belongs_to,
            'user_id' => $data->user_id,
            'amount' => $data->total,
            'comission' => $rdr->comission,
            'rider_amount' => $data->shipping,
            'system_comission' => ($data->shipping * $rdr->comission) / 100,
            'total_amount' => ($data->total + (($data->shipping * $rdr->comission) / 100)),
            'due_amount' => $data->total,
            'status' => 'Pending',
        ]);

        $data->status = 'Picked';
        $data->save();

        return redirect()->back()->with('success', 'Rider assigned successfully');
    }

    public function removeRider(int $order, int $cod): RedirectResponse
    {
        $data = auth()->user()
            ->orderToMe()
            ->where(['belongs_to_type' => auth()->user()->account_type()])
            ->findOrFail($order);

        $codRow = $data->hasRider()->findOrFail($cod);
        $codRow->delete();

        return redirect()->back()->with('success', 'Rider removed successfully');
    }

    public function cprint(int $order): Response
    {
        $data = auth()->user()
            ->orderToMe()
            ->where(['belongs_to_type' => auth()->user()->account_type()])
            ->with(['user', 'cartOrders.product'])
            ->findOrFail($order);

        return Inertia::render('Vendor/Orders/CPrint', [
            'order' => [
                'id' => $data->id,
                'created_at_daytime' => $data->created_at?->toDayDateTimeString(),
                'printed_at_daytime' => now()->toDayDateTimeString(),
                'location' => $data->location,
                'house_no' => $data->house_no ?? 'Not Defined !',
                'road_no' => $data->road_no ?? 'N/A !',
                'total' => $data->total ?? 0,
                'shipping' => $data->shipping ?? 0,
                'user' => [
                    'name' => $data->user?->name ?? 'Not Found !',
                ],
                'cart_orders' => $data->cartOrders->map(function ($item) {
                    return [
                        'id' => $item->id ?? 'N/A',
                        'product_title' => $item->product?->title ?? 'N/A',
                        'product_thumbnail' => $item->product?->thumbnail ? asset('storage/' . $item->product?->thumbnail) : null,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'line_total' => $item->total,
                        'size' => $item->size ?? 'N/A',
                    ];
                })->values()->all(),
            ],
        ]);
    }

    public function vprint(int $order): Response
    {
        $data = auth()->user()
            ->orderToMe()
            ->where(['belongs_to_type' => auth()->user()->account_type()])
            ->with(['user', 'cartOrders.product'])
            ->findOrFail($order);

        return Inertia::render('Vendor/Orders/VPrint', [
            'order' => [
                'id' => $data->id,
                'created_at_daytime' => $data->created_at?->toDayDateTimeString(),
                'printed_at_daytime' => now()->toDayDateTimeString(),
                'location' => $data->location,
                'house_no' => $data->house_no ?? 'Not Defined !',
                'road_no' => $data->road_no ?? 'N/A !',
                'total' => $data->total ?? 0,
                'shipping' => $data->shipping ?? 0,
                'user' => [
                    'name' => $data->user?->name ?? 'Not Found !',
                ],
                'cart_orders' => $data->cartOrders->map(function ($item) {
                    return [
                        'id' => $item->id ?? 'N/A',
                        'product_title' => $item->product?->title ?? 'N/A',
                        'product_thumbnail' => $item->product?->thumbnail ? asset('storage/' . $item->product?->thumbnail) : null,
                        'quantity' => $item->quantity,
                        'size' => $item->size ?? 'N/A',
                        'line_total' => $item->total,
                    ];
                })->values()->all(),
            ],
        ]);
    }

    public function summaryPrint(Request $request): Response
    {
        $filters = [
            'nav' => $request->query('nav', 'Pending'),
            'delivery' => $request->query('delivery', 'all'),
            'create' => $request->query('create', 'all'),
            'start_date' => $request->query('start_date', ''),
            'end_date' => $request->query('end_date', ''),
            'area' => $request->query('area', 'all'),
            'find' => trim((string) $request->query('find', '')),
        ];

        $account = auth()->user()->account_type();
        $query = auth()->user()->orderToMe()->where(['belongs_to_type' => $account]);

        if (in_array($filters['nav'], ['Trash', 'Trashed'], true)) {
            $query->onlyTrashed();
        } elseif ($filters['nav'] !== 'All') {
            $query->where('status', $filters['nav']);
        }

        if ($filters['delivery'] !== 'all') {
            $query->where('delevery', $filters['delivery']);
        }

        if ($filters['create'] === 'day' && !empty($filters['start_date'])) {
            $query->whereDate('created_at', Carbon::parse($filters['start_date'])->toDateString());
        } elseif ($filters['create'] === 'between' && !empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($filters['start_date'])->startOfDay(),
                Carbon::parse($filters['end_date'])->endOfDay(),
            ]);
        }

        if ($filters['area'] !== 'all') {
            $query->where('area_condition', $filters['area']);
        }

        if ($filters['find'] !== '') {
            $query->where(function ($builder) use ($filters) {
                $builder
                    ->where('id', 'like', '%' . $filters['find'] . '%')
                    ->orWhere('number', 'like', '%' . $filters['find'] . '%')
                    ->orWhere('location', 'like', '%' . $filters['find'] . '%')
                    ->orWhere('status', 'like', '%' . $filters['find'] . '%')
                    ->orWhereHas('user', function ($userQuery) use ($filters) {
                        $userQuery
                            ->where('name', 'like', '%' . $filters['find'] . '%')
                            ->orWhere('email', 'like', '%' . $filters['find'] . '%');
                    })
                    ->orWhereHas('cartOrders.product', function ($productQuery) use ($filters) {
                        $productQuery
                            ->where('title', 'like', '%' . $filters['find'] . '%')
                            ->orWhere('name', 'like', '%' . $filters['find'] . '%');
                    });
            });
        }

        $orders = $query
            ->with(['user:id,name', 'comissionsInfo'])
            ->withCount('cartOrders')
            ->latest('id')
            ->get();

        return Inertia::render('Vendor/Orders/PrintSummery', [
            'filters' => $filters,
            'orders' => $orders->values()->map(function ($item, int $index) {
                return [
                    'sl' => $index + 1,
                    'id' => $item->id,
                    'cart_orders_count' => $item->cart_orders_count ?? 0,
                    'quantity' => $item->quantity,
                    'total' => $item->total,
                    'shipping' => $item->shipping,
                    'status' => $item->status,
                    'created_at_human' => $item->created_at?->diffForHumans(),
                    'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                    'delevery' => $item->delevery,
                    'location' => $item->location,
                    'user_name' => $item->user?->name,
                    'number' => $item->number,
                    'comission' => $item->comissionsInfo?->sum('take_comission') ?? 0,
                ];
            })->all(),
        ]);
    }
}
