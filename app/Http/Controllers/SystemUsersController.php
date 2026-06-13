<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserHasRefs;
use App\Models\Vip;
use App\Models\Order;
use App\Support\TableDateFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SystemUsersController extends Controller
{
    // users view to system by permission
    public function admin_view()
    {
        $users = User::withoutRole('system')->orderBy('id', 'desc')->get();
        // return $users[0]->role;
        return view('auth.system.users.index', compact('users'));
    }


    /**
     * users edit form to system by permissions
     * 
     * @return view
     */
    public function admin_edit()
    {
        $user = User::withoutRole('system')->where('email', request('email'))->first();
        return view('auth.system.users.edit', compact('user'));
    }

    public function editReact($id)
    {
        $user = User::findOrFail($id);

        return Inertia::render('Auth/system/users/Edit', [
            'editUser' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'coin' => $user->coin,
                'reference' => $user->reference,
                'reference_owner_name' => $user->getReffOwner?->owner?->name,
                'roles' => $user->getRoleNames()->values()->all(),
                'permissions' => $user->getPermissionNames()->values()->all(),
                'permissions_via_role' => $user->getPermissionsViaRoles()->pluck('name')->values()->all(),
            ],
            'roles' => Role::query()->get(['id', 'name'])->toArray(),
            'permissions' => Permission::query()->get(['id', 'name'])->toArray(),
            'defaultAdminRef' => config('app.ref'),
        ]);
    }

    public function detailsReact($id)
    {
        $user = User::query()
            ->withoutAdmin()
            ->with([
                'myRef',
                'getReffOwner.owner',
                'subscription.package',
                'roles',
                'permissions',
                'currentLevel',
                'requestsToBeVendor',
                'requestsToBeReseller',
                'requestsToBeRider',
            ])
            ->withCount(['myOrderAsUser', 'myOrdersAsReseller', 'myDeposit', 'myWithdraw'])
            ->findOrFail($id);

        $subscription = $user->subscription;

        return Inertia::render('Auth/system/users/Details', [
            'userDetails' => [
                'id' => $user->id,
                'name' => $user->name ?? 'N/A',
                'email' => $user->email ?? 'N/A',
                'phone' => $user->phone ?? 'N/A',
                'gender' => $user->gender ?? 'N/A',
                'dob' => $user->dob ?? 'N/A',
                'bio' => $user->bio ?? '',
                'coin' => $user->coin ?? 0,
                'available_coin' => method_exists($user, 'abailCoin') ? $user->abailCoin() : ($user->coin ?? 0),
                'currency' => $user->currency ?? 'N/A',
                'currency_sing' => $user->currency_sing ?? 'TK',
                'language' => $user->language ?? $user->site_language ?? 'N/A',
                'active_nav' => $user->active_nav ?? 'N/A',
                'is_active' => (bool) ($user->is_active ?? true),
                'kyc_status' => $user->kyc_status ?? 'N/A',
                'created_at_formatted' => $user->created_at?->toDayDateTimeString() ?? 'N/A',
                'updated_at_formatted' => $user->updated_at?->diffForHumans() ?? 'N/A',
                'email_verified_at' => $user->email_verified_at?->toDayDateTimeString() ?? 'Not verified',
                'location' => collect([$user->line1, $user->line2, $user->city, $user->state, $user->country, $user->zip])
                    ->filter(fn ($item) => filled($item))
                    ->join(', ') ?: 'N/A',
                'ref' => $user->myRef?->ref ?? 'N/A',
                'reference' => $user->reference ?? 'N/A',
                'reference_owner_name' => $user->getReffOwner?->owner?->name ?? 'N/A',
                'roles' => $user->getRoleNames()->values()->all(),
                'permissions' => $user->getPermissionNames()->values()->all(),
                'permissions_via_role' => $user->getPermissionsViaRoles()->pluck('name')->values()->all(),
                'level' => $user->currentLevel?->name ?? 'N/A',
                'vip' => [
                    'package' => $subscription?->package?->name ?? 'No active package',
                    'status' => $subscription ? ($subscription->status ? 'Active' : 'Pending') : 'No',
                    'task_type' => $subscription?->task_type ?? 'N/A',
                    'valid_till' => $subscription?->valid_till ? Carbon::parse($subscription->valid_till)->toFormattedDateString() : 'N/A',
                ],
                'counts' => [
                    'orders' => $user->my_order_as_user_count ?? 0,
                    'reseller_orders' => $user->my_orders_as_reseller_count ?? 0,
                    'deposits' => $user->my_deposit_count ?? 0,
                    'withdraws' => $user->my_withdraw_count ?? 0,
                    'roles' => $user->roles->count(),
                    'permissions' => $user->permissions->count(),
                ],
                'shops' => [
                    'vendor' => $user->requestsToBeVendor->map(fn ($shop) => [
                        'id' => $shop->id,
                        'name' => $shop->shop_name_en ?? $shop->shop_name_bn ?? 'N/A',
                        'status' => $shop->status ?? 'N/A',
                    ])->values()->all(),
                    'reseller' => $user->requestsToBeReseller->map(fn ($shop) => [
                        'id' => $shop->id,
                        'name' => $shop->shop_name_en ?? $shop->shop_name_bn ?? 'N/A',
                        'status' => $shop->status ?? 'N/A',
                    ])->values()->all(),
                    'rider' => $user->requestsToBeRider->map(fn ($item) => [
                        'id' => $item->id,
                        'status' => $item->status ?? 'N/A',
                        'area_condition' => $item->area_condition ?? 'N/A',
                    ])->values()->all(),
                ],
                'recent_orders' => Order::query()
                    ->where('user_id', $user->id)
                    ->latest('id')
                    ->limit(5)
                    ->get()
                    ->map(fn ($order) => [
                        'id' => $order->id,
                        'status' => $order->status ?? 'N/A',
                        'total' => $order->total ?? 0,
                        'created_at' => $order->created_at?->toFormattedDateString() ?? 'N/A',
                    ])->values()->all(),
                'recent_deposits' => $user->myDeposit()
                    ->latest('id')
                    ->limit(5)
                    ->get()
                    ->map(fn ($deposit) => [
                        'id' => $deposit->id,
                        'amount' => $deposit->amount ?? 0,
                        'method' => $deposit->paymentMethod ?? 'N/A',
                        'confirmed' => (bool) $deposit->confirmed,
                        'created_at' => $deposit->created_at?->toFormattedDateString() ?? 'N/A',
                    ])->values()->all(),
                'recent_withdraws' => $user->myWithdraw()
                    ->latest('id')
                    ->limit(5)
                    ->get()
                    ->map(fn ($withdraw) => [
                        'id' => $withdraw->id,
                        'amount' => $withdraw->amount ?? 0,
                        'status' => $withdraw->is_rejected ? 'Rejected' : ($withdraw->status ? 'Paid' : 'Pending'),
                        'created_at' => $withdraw->created_at?->toFormattedDateString() ?? 'N/A',
                    ])->values()->all(),
            ],
        ]);
    }

    public function indexReact(Request $request)
    {
        $search = (string) $request->string('search');
        $sd = $request->input('sd');
        $ed = $request->input('ed');
        $status = $request->input('status', 'All');
        $defaultToday = TableDateFilter::hasOnlyDefaultFilters($request, ['status' => 'All']);

        $query = User::query()
            ->withoutAdmin()
            ->orderBy('id', 'desc')
            ->with(['myRef', 'getReffOwner.owner', 'subscription.package', 'roles', 'permissions'])
            ->withCount('myOrderAsUser');

        if (!empty($search)) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('reference', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhereHas('subscription.package', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('myRef', function ($q) use ($search) {
                        $q->where('ref', 'like', '%' . $search . '%');
                    });
            });
        }

        $this->applyStatusFilter($query, $status);
        $this->applyDateFilter($query, $sd, $ed, $defaultToday);

        $users = $query->paginate(config('app.paginate'))->withQueryString();
        $totalUsers = User::query()->withoutAdmin()->count();
        $todayUsers = User::query()->withoutAdmin()->whereDate('created_at', today())->count();
        $totalVipAccounts = Vip::query()->count();
        $todayVipAccounts = Vip::query()->whereDate('created_at', today())->count();

        return Inertia::render('Auth/system/users/index', [
            'filters' => [
                'search' => $search,
                'status' => $status,
                'sd' => $sd,
                'ed' => $ed,
            ],
            'widgets' => [
                ['head' => 'Today Vip account', 'data' => $todayVipAccounts],
                ['head' => 'Total Vip account', 'data' => $totalVipAccounts],
                ['head' => 'Today user', 'data' => $todayUsers],
                ['head' => 'Total user', 'data' => $totalUsers],
            ],
            'users' => [
                'data' => $users->getCollection()->map(function ($user) {
                    $subscription = $user->subscription;
                    $vipStatus = [
                        'label' => 'NO',
                        'className' => 'px-1 rounded inline-flex bg-red-200 text-xs',
                    ];

                    if ($subscription) {
                        if ($subscription->valid_till > now() && $subscription->status) {
                            $vipStatus = [
                                'label' => $subscription?->package?->name ?? 'N/A',
                                'className' => 'px-1 rounded inline-flex bg-green-200 text-xs',
                            ];
                        } elseif ($subscription->valid_till < now() && $subscription->status) {
                            $vipStatus = [
                                'label' => 'Expired',
                                'className' => 'px-1 rounded inline-flex bg-yellow-200 text-xs',
                            ];
                        } elseif (!$subscription->status) {
                            $vipStatus = [
                                'label' => 'Pending',
                                'className' => 'px-1 rounded inline-flex bg-blue-200 text-xs',
                            ];
                        }
                    }

                    return [
                        'id' => $user->id,
                        'name' => $user->name ?? 'N/A',
                        'email' => $user->email ?? 'N/A',
                        'ref' => $user->myRef?->ref ?? 'N/A',
                        'reference' => $user->reference ?? 'Not Found',
                        'reference_owner_name' => $user->getReffOwner?->owner?->name,
                        'roles' => $user->getRoleNames()->values()->all(),
                        'permissions_count' => $user->permissions?->count() ?? 0,
                        'vip_status' => $vipStatus,
                        'orders_count' => $user->my_order_as_user_count ?? 0,
                        'coin' => $user->coin ?? 0,
                        'created_at_formatted' => $user->created_at?->toFormattedDateString() ?? '',
                    ];
                })->values()->all(),
                'links' => collect($users->linkCollection())->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
                'total' => $users->total(),
            ],
            'printUrl' => route('system.users.print-summery', [
                'search' => $search,
                'status' => $status,
                'sd' => $sd,
                'ed' => $ed,
            ]),
        ]);
    }

    public function printReact(Request $request)
    {
        $search = (string) $request->string('search');
        $sd = $request->input('sd');
        $ed = $request->input('ed');
        $status = $request->input('status', 'All');
        $defaultToday = TableDateFilter::hasOnlyDefaultFilters($request, ['status' => 'All']);

        $query = User::query()
            ->withoutAdmin()
            ->orderBy('id', 'desc')
            ->with(['myRef', 'getReffOwner.owner', 'subscription.package', 'roles', 'permissions'])
            ->withCount('myOrderAsUser');

        if (!empty($search)) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('reference', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhereHas('subscription.package', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('myRef', function ($q) use ($search) {
                        $q->where('ref', 'like', '%' . $search . '%');
                    });
            });
        }

        $this->applyStatusFilter($query, $status);
        $this->applyDateFilter($query, $sd, $ed, $defaultToday);

        $users = $query->get()->values()->map(function ($user) {
            $subscription = $user->subscription;
            $vipStatus = [
                'label' => 'NO',
                'className' => 'px-1 rounded inline-flex bg-red-200 text-xs',
            ];

            if ($subscription) {
                if ($subscription->valid_till > now() && $subscription->status) {
                    $vipStatus = [
                        'label' => $subscription?->package?->name ?? 'N/A',
                        'className' => 'px-1 rounded inline-flex bg-green-200 text-xs',
                    ];
                } elseif ($subscription->valid_till < now() && $subscription->status) {
                    $vipStatus = [
                        'label' => 'Expired',
                        'className' => 'px-1 rounded inline-flex bg-yellow-200 text-xs',
                    ];
                } elseif (!$subscription->status) {
                    $vipStatus = [
                        'label' => 'Pending',
                        'className' => 'px-1 rounded inline-flex bg-blue-200 text-xs',
                    ];
                }
            }

            return [
                'id' => $user->id,
                'name' => $user->name ?? 'N/A',
                'email' => $user->email ?? 'N/A',
                'ref' => $user->myRef?->ref ?? 'N/A',
                'reference' => $user->reference ?? 'Not Found',
                'reference_owner_name' => $user->getReffOwner?->owner?->name,
                'roles' => $user->getRoleNames()->values()->all(),
                'permissions_count' => $user->permissions?->count() ?? 0,
                'vip_status' => $vipStatus,
                'orders_count' => $user->my_order_as_user_count ?? 0,
                'coin' => $user->coin ?? 0,
                'created_at_formatted' => $user->created_at?->toFormattedDateString() ?? '',
            ];
        })->all();

        return Inertia::render('Auth/system/users/PrintSummery', [
            'sd' => $sd,
            'ed' => $ed,
            'status' => $status,
            'users' => $users,
        ]);
    }

    public function admin_update(Request $request, $id)
    {
        // $user->update(request()->validate([
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = User::query()->withoutAdmin()->findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('reference')) {
            $reffArray = UserHasRefs::all('ref', 'user_id');
            $reference = $request->reference;
            $reff = $reffArray->where('ref', $reference)->first();

            if ($reff) {
                # code...
                $user->reference_accepted_at = Carbon::now();
                $user->reference = $request->reference;
            }
        }

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function update_roles(Request $request, User $user)
    {
        $request->validate([
            'role' => ['array'],
            'role.*' => ['string', 'exists:roles,name'],
        ]);

        $user->syncRoles($request->input('role', []));

        return redirect()->back()->with('success', 'Role Attached');
    }

    public function update_permissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $user->syncPermissions($request->input('permissions', []));

        return redirect()->back()->with('success', 'Permission Synced !');
    }

    private function applyDateFilter($query, ?string $sd, ?string $ed, bool $defaultToday = false): void
    {
        TableDateFilter::apply($query, $sd, $ed, $defaultToday);
    }

    private function applyStatusFilter($query, string $status): void
    {
        if ($status === 'Active') {
            $query->where('is_active', true);
            return;
        }

        if ($status === 'Disabled') {
            $query->where('is_active', false);
        }
    }
}
