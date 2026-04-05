<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\rider;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RiderController extends Controller
{
    public function indexReact(Request $request)
    {
        $condition = $request->input('condition', 'Active');
        $query = rider::query()
            ->with('user')
            ->orderBy('created_at', 'desc');

        if ($condition !== 'all') {
            $query->where(['status' => $condition]);
        }

        $riders = $query->paginate(200)->withQueryString();

        return Inertia::render('Auth/system/rider/index', [
            'condition' => $condition,
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
                'count' => $riders->count(),
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
}
