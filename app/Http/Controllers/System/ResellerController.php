<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\reseller;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ResellerController extends Controller
{
    public function indexReact(Request $request)
    {
        $filter = $request->input('filter', 'Active');
        $find = $request->input('find');
        $sd = $request->input('sd');
        $ed = $request->input('ed');

        $query = reseller::query()->with('user')->latest('id');

        if (!empty($find)) {
            $query->where(function ($subQuery) use ($find) {
                $subQuery
                    ->where('id', 'like', '%' . $find . '%')
                    ->orWhere('shop_name_en', 'like', '%' . $find . '%')
                    ->orWhere('shop_name_bn', 'like', '%' . $find . '%')
                    ->orWhere('email', 'like', '%' . $find . '%')
                    ->orWhere('phone', 'like', '%' . $find . '%')
                    ->orWhereHas('user', function ($userQuery) use ($find) {
                        $userQuery
                            ->where('name', 'like', '%' . $find . '%')
                            ->orWhere('email', 'like', '%' . $find . '%')
                            ->orWhere('phone', 'like', '%' . $find . '%');
                    });
            });
        }

        if ($filter !== '*') {
            $query->where(['status' => $filter]);
        }

        $this->applyDateFilter($query, $sd, $ed);

        $resellers = $query->paginate(config('app.paginate'))->withQueryString();

        $resellers->through(function ($item) {
            return [
                'id' => $item->id,
                'user_name' => $item->user?->name ?? 'N/A',
                'shop_name_bn' => $item->shop_name_bn ?? 'N/A',
                'status' => $item->status ?? 'N/A',
                'system_get_comission' => $item->system_get_comission ?? 'N/A',
                'categories_count' => 0,
                'products_count' => 0,
                'created_at_human' => $item->created_at?->diffForHumans(),
                'created_at_formatted' => $item->created_at?->toFormattedDateString(),
            ];
        });

        return Inertia::render('Auth/system/reseller/index', [
            'filters' => [
                'filter' => $filter,
                'find' => $find,
                'sd' => $sd,
                'ed' => $ed,
            ],
            'widgets' => [
                ['title' => 'Resellers', 'content' => reseller::query()->count()],
                ['title' => 'Active', 'content' => reseller::query()->active()->count()],
                ['title' => 'Pending', 'content' => reseller::query()->pending()->count()],
                ['title' => 'Disabled', 'content' => reseller::query()->disabled()->count()],
                ['title' => 'Suspended', 'content' => reseller::query()->suspended()->count()],
            ],
            'resellers' => [
                'data' => $resellers->items(),
                'links' => collect($resellers->linkCollection())->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $resellers->firstItem(),
                'to' => $resellers->lastItem(),
                'total' => $resellers->total(),
            ],
            'printUrl' => route('system.reseller.print-summery', [
                'filter' => $filter,
                'find' => $find,
                'sd' => $sd,
                'ed' => $ed,
            ]),
        ]);
    }

    public function printReact(Request $request)
    {
        $filter = $request->input('filter', 'Active');
        $find = $request->input('find');
        $sd = $request->input('sd');
        $ed = $request->input('ed');

        $query = reseller::query()->with('user')->latest('id');

        if (!empty($find)) {
            $query->where(function ($subQuery) use ($find) {
                $subQuery
                    ->where('id', 'like', '%' . $find . '%')
                    ->orWhere('shop_name_en', 'like', '%' . $find . '%')
                    ->orWhere('shop_name_bn', 'like', '%' . $find . '%')
                    ->orWhere('email', 'like', '%' . $find . '%')
                    ->orWhere('phone', 'like', '%' . $find . '%')
                    ->orWhereHas('user', function ($userQuery) use ($find) {
                        $userQuery
                            ->where('name', 'like', '%' . $find . '%')
                            ->orWhere('email', 'like', '%' . $find . '%')
                            ->orWhere('phone', 'like', '%' . $find . '%');
                    });
            });
        }

        if ($filter !== '*') {
            $query->where(['status' => $filter]);
        }

        $this->applyDateFilter($query, $sd, $ed);

        $resellers = $query->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'user_name' => $item->user?->name ?? 'N/A',
                'shop_name_bn' => $item->shop_name_bn ?? 'N/A',
                'status' => $item->status ?? 'N/A',
                'system_get_comission' => $item->system_get_comission ?? 'N/A',
                'categories_count' => 0,
                'products_count' => 0,
                'created_at_human' => $item->created_at?->diffForHumans(),
                'created_at_formatted' => $item->created_at?->toFormattedDateString(),
            ];
        })->values()->all();

        return Inertia::render('Auth/system/reseller/PrintSummery', [
            'resellers' => $resellers,
            'filters' => [
                'filter' => $filter,
                'find' => $find,
                'sd' => $sd,
                'ed' => $ed,
            ],
        ]);
    }

    public function editReact(Request $request, $id)
    {
        $reseller = reseller::query()
            ->with(['user', 'documents'])
            ->findOrFail($id);

        $user = $reseller->user;
        $nav = $request->input('nav', 'documents');
        $document = $reseller->documents;

        return Inertia::render('Auth/system/reseller/Edit', [
            'nav' => $nav,
            'reseller' => [
                'id' => $reseller->id,
                'status' => $reseller->status ?? 'N/A',
                'shop_name_bn' => $reseller->shop_name_bn ?? 'N/A',
                'shop_name_en' => $reseller->shop_name_en ?? 'N/A',
                'email' => $reseller->email ?? 'N/A',
                'phone' => $reseller->phone ?? 'N/A',
                'address' => $reseller->address ?? 'N/A',
                'upazila' => $reseller->upazila ?? 'N/A',
                'district' => $reseller->district ?? 'N/A',
                'country' => $reseller->country ?? 'N/A',
                'fixed_amount' => $reseller->fixed_amount ?? 0,
                'system_get_comission' => $reseller->system_get_comission ?? 0,
                'allow_max_product_upload' => (string) ($reseller->allow_max_product_upload ?? 0),
                'allow_max_resell_product' => (string) ($reseller->allow_max_resell_product ?? 0),
                'max_product_upload' => $reseller->max_product_upload ?? 0,
                'max_resell_product' => $reseller->max_resell_product ?? 0,
                'updated_at_human' => $reseller->updated_at?->diffForHumans(),
                'user' => [
                    'id' => $user?->id,
                    'name' => $user?->name ?? 'N/A',
                    'email' => $user?->email ?? 'N/A',
                    'phone' => $user?->phone ?? 'N/A',
                ],
                'documents' => [
                    'deatline' => $document?->deatline,
                    'deatline_formatted' => $document?->deatline
                        ? Carbon::parse($document->deatline)->toFormattedDateString()
                        : 'N/A',
                    'deatline_human' => $document?->deatline
                        ? Carbon::parse($document->deatline)->diffForHumans()
                        : '',
                    'nid' => $document?->nid,
                    'nid_front_url' => $document?->nid_front ? asset('storage/' . $document->nid_front) : null,
                    'nid_back_url' => $document?->nid_back ? asset('storage/' . $document->nid_back) : null,
                    'shop_tin' => $document?->shop_tin,
                    'shop_tin_image_url' => $document?->shop_tin_image ? asset('storage/' . $document->shop_tin_image) : null,
                    'shop_trade' => $document?->shop_trade,
                    'shop_trade_image_url' => $document?->shop_trade_image ? asset('storage/' . $document->shop_trade_image) : null,
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
            'status' => ['required', 'in:Pending,Disabled,Suspended,Active'],
        ]);

        $reseller = reseller::query()->findOrFail($id);
        $reseller->status = $request->status;
        $reseller->save();

        return redirect()->back()->with('success', 'Status Updated !');
    }

    public function updateComission(Request $request, $id)
    {
        $request->validate([
            'comission' => ['nullable', 'numeric'],
            'allow_max_product_upload' => ['nullable', 'in:0,1'],
            'allow_max_resell_product' => ['nullable', 'in:0,1'],
            'max_product_upload' => ['nullable', 'numeric'],
            'max_resell_product' => ['nullable', 'numeric'],
            'fixed_amount' => ['nullable', 'numeric'],
        ]);

        $reseller = reseller::query()->findOrFail($id);
        $reseller->system_get_comission = $request->input('comission', 0);
        $reseller->allow_max_product_upload = $request->input('allow_max_product_upload', 0);
        $reseller->allow_max_resell_product = $request->input('allow_max_resell_product', 0);
        $reseller->max_product_upload = $request->input('max_product_upload', 0);
        $reseller->max_resell_product = $request->input('max_resell_product', 0);
        $reseller->fixed_amount = $request->input('fixed_amount', 0);
        $reseller->save();

        return redirect()->back()->with('success', 'Comission Set !');
    }

    public function updateDocumentDeadline(Request $request, $id)
    {
        $request->validate([
            'deatline' => ['nullable', 'date'],
        ]);

        $reseller = reseller::query()->with('documents')->findOrFail($id);
        $reseller->documents?->update([
            'deatline' => $request->deatline,
        ]);

        return redirect()->back()->with('success', 'Updated');
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
