<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\rider;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RiderController extends Controller
{
    public function indexReact(Request $request)
    {
        $condition = $request->input('condition', 'Active');
        $find = $request->input('find');
        $sd = $request->input('sd');
        $ed = $request->input('ed');

        $query = rider::query()
            ->with('user')
            ->orderBy('created_at', 'desc');

        if ($condition !== 'all') {
            $query->where(['status' => $condition]);
        }

        if (!empty($find)) {
            $query->where(function ($subQuery) use ($find) {
                $subQuery
                    ->where('id', 'like', '%' . $find . '%')
                    ->orWhere('phone', 'like', '%' . $find . '%')
                    ->orWhere('email', 'like', '%' . $find . '%')
                    ->orWhere('nid', 'like', '%' . $find . '%')
                    ->orWhereHas('user', function ($userQuery) use ($find) {
                        $userQuery
                            ->where('name', 'like', '%' . $find . '%')
                            ->orWhere('email', 'like', '%' . $find . '%')
                            ->orWhere('phone', 'like', '%' . $find . '%');
                    });
            });
        }

        $this->applyDateFilter($query, $sd, $ed);

        $riders = $query->paginate(config('app.paginate'))->withQueryString();

        return Inertia::render('Auth/system/rider/index', [
            'filters' => [
                'condition' => $condition,
                'find' => $find,
                'sd' => $sd,
                'ed' => $ed,
            ],
            'widgets' => [
                ['title' => 'Total Rider', 'content' => rider::query()->count()],
                ['title' => 'Active Rider', 'content' => rider::query()->active()->count()],
                ['title' => 'Pending Rider', 'content' => rider::query()->pending()->count()],
                ['title' => 'Suspended Rider', 'content' => rider::query()->suspended()->count()],
                ['title' => 'Disabled Rider', 'content' => rider::query()->disabled()->count()],
            ],
            'riders' => [
                'data' => $riders->getCollection()->values()->map(function ($item, $index) use ($riders) {
                    return [
                        'sl' => (($riders->currentPage() - 1) * $riders->perPage()) + $index + 1,
                        'id' => $item->id,
                        'user_name' => $item->user?->name ?? 'N/A',
                        'status' => $item->status ?? 'N/A',
                        'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                        'created_at_human' => $item->created_at?->diffForHumans(),
                    ];
                })->all(),
                'links' => collect($riders->linkCollection())->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $riders->firstItem(),
                'to' => $riders->lastItem(),
                'total' => $riders->total(),
                'count' => $riders->count(),
            ],
            'printUrl' => route('system.rider.print-summery', [
                'condition' => $condition,
                'find' => $find,
                'sd' => $sd,
                'ed' => $ed,
            ]),
        ]);
    }

    public function printReact(Request $request)
    {
        $condition = $request->input('condition', 'Active');
        $find = $request->input('find');
        $sd = $request->input('sd');
        $ed = $request->input('ed');

        $query = rider::query()
            ->with('user')
            ->orderBy('created_at', 'desc');

        if ($condition !== 'all') {
            $query->where(['status' => $condition]);
        }

        if (!empty($find)) {
            $query->where(function ($subQuery) use ($find) {
                $subQuery
                    ->where('id', 'like', '%' . $find . '%')
                    ->orWhere('phone', 'like', '%' . $find . '%')
                    ->orWhere('email', 'like', '%' . $find . '%')
                    ->orWhere('nid', 'like', '%' . $find . '%')
                    ->orWhereHas('user', function ($userQuery) use ($find) {
                        $userQuery
                            ->where('name', 'like', '%' . $find . '%')
                            ->orWhere('email', 'like', '%' . $find . '%')
                            ->orWhere('phone', 'like', '%' . $find . '%');
                    });
            });
        }

        $this->applyDateFilter($query, $sd, $ed);

        $riders = $query->get()->values()->map(function ($item, $index) {
            return [
                'sl' => $index + 1,
                'id' => $item->id,
                'user_name' => $item->user?->name ?? 'N/A',
                'status' => $item->status ?? 'N/A',
                'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                'created_at_human' => $item->created_at?->diffForHumans(),
            ];
        })->all();

        return Inertia::render('Auth/system/rider/PrintSummery', [
            'riders' => $riders,
            'filters' => [
                'condition' => $condition,
                'find' => $find,
                'sd' => $sd,
                'ed' => $ed,
            ],
        ]);
    }

    public function editReact(Request $request, $id)
    {
        $rider = rider::query()->with('user')->findOrFail($id);
        $user = $rider->user;
        $nav = $request->input('nav', 'user');

        return Inertia::render('Auth/system/rider/Edit', [
            'nav' => $nav,
            'rider' => [
                'id' => $rider->id,
                'status' => $rider->status ?? 'N/A',
                'rejected_for' => $rider->rejected_for,
                'area_condition' => $rider->area_condition ?? '',
                'targeted_area' => $rider->targeted_area ?? '',
                'updated_at_human' => $rider->updated_at?->diffForHumans(),
                'comission' => $rider->comission ?? '',
                'phone' => $rider->phone ?? '',
                'email' => $rider->email ?? '',
                'nid' => $rider->nid ?? '',
                'nid_photo_front_url' => $rider->nid_photo_front ? asset('storage/' . $rider->nid_photo_front) : null,
                'nid_photo_back_url' => $rider->nid_photo_back ? asset('storage/' . $rider->nid_photo_back) : null,
                'current_address' => $rider->current_address ?? '',
                'fixed_address' => $rider->fixed_address ?? '',
                'user' => [
                    'id' => $user?->id,
                    'name' => $user?->name ?? 'N/A',
                    'email' => $user?->email ?? 'N/A',
                ],
            ],
            'editUser' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'coin' => $user->coin,
                'reference' => $user->reference,
                'reference_owner_name' => $user->getReffOwner?->owner?->name,
                'roles' => $user->getRoleNames()->values()->all(),
                'permissions' => $user->getPermissionNames()->values()->all(),
                'permissions_via_role' => $user->getPermissionsViaRoles()->pluck('name')->values()->all(),
            ] : null,
            'roles' => Role::query()->get(['id', 'name'])->toArray(),
            'permissions' => Permission::query()->get(['id', 'name'])->toArray(),
            'defaultAdminRef' => config('app.ref'),
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:Active,Pending,Disabled,Suspended'],
            'comission' => ['nullable', 'numeric'],
        ]);

        $rider = rider::query()->findOrFail($id);
        $rider->status = $request->status;
        $rider->comission = $request->input('comission', $rider->comission);
        $rider->save();

        return redirect()->back()->with('success', 'Status Updated !');
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
