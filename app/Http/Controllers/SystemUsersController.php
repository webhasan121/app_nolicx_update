<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\user_has_refs;
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

    public function indexReact(Request $request)
    {
        $search = (string) $request->string('search');
        $sd = $request->input('sd');
        $ed = $request->input('ed');

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

        $this->applyDateFilter($query, $sd, $ed);

        $users = $query->paginate(config('app.paginate'))->withQueryString();
        $allUsers = User::get();

        return Inertia::render('Auth/system/users/index', [
            'filters' => [
                'search' => $search,
                'sd' => $sd,
                'ed' => $ed,
            ],
            'widgets' => [
                ['head' => 'Total', 'data' => $allUsers->count()],
                ['head' => 'Today', 'data' => $allUsers->where('created_at', today())->count()],
                ['head' => 'VIP', 'data' => $allUsers->where('vip', '!=', '0')->count()],
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

        $this->applyDateFilter($query, $sd, $ed);

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
            $reffArray = user_has_refs::all('ref', 'user_id');
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

    private function applyDateFilter($query, ?string $sd, ?string $ed): void
    {
        if (!empty($sd) && !empty($ed)) {
            $start = Carbon::parse($sd)->startOfDay();
            $end = Carbon::parse($ed)->endOfDay();

            if ($start->gt($end)) {
                [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
            }

            $query->whereBetween('created_at', [$start, $end]);

            return;
        }

        if (!empty($sd)) {
            $query->whereBetween('created_at', [
                Carbon::parse($sd)->startOfDay(),
                Carbon::parse($sd)->endOfDay(),
            ]);

            return;
        }

        if (!empty($ed)) {
            $query->whereBetween('created_at', [
                Carbon::parse($ed)->startOfDay(),
                Carbon::parse($ed)->endOfDay(),
            ]);
        }
    }
}
