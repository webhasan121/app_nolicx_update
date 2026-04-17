<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Package_pays;
use App\Models\Packages;
use App\Models\User;
use App\Models\vip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class VipController extends Controller
{
    public function userEditReact($vip): Response
    {
        $vipData = $this->findVipUser($vip);

        return Inertia::render('Auth/system/vip/Edit', [
            'vipData' => [
                'id' => $vipData->id,
                'user_id' => $vipData->user_id,
                'name' => $vipData->name ?? 'N/A',
                'created_at_formatted' => $vipData->created_at?->toFormattedDateString(),
                'package_name' => $vipData->package?->name ?? 'N/A',
                'package_id' => $vipData->package_id,
                'status' => (int) ($vipData->status ?? 0),
                'deleted_at' => $vipData->deleted_at?->toDateTimeString(),
                'payment_by' => $vipData->payment_by ?? 'N/A',
                'trx' => $vipData->trx ?? 'N/A',
                'nid' => $vipData->nid ?? 'N/A',
                'phone' => $vipData->phone ?? 'N/A',
                'comission' => $vipData->comission ?? 'N/A',
                'reference' => $vipData->reference ?? 'N/A',
                'task_type' => $vipData->task_type ?? 'daily',
                'valid_till' => $vipData->valid_till,
                'valid_till_formatted' => $vipData->valid_till ? Carbon::parse($vipData->valid_till)->toFormattedDateString() : null,
                'valid_till_human' => $vipData->valid_till ? Carbon::parse($vipData->valid_till)->diffForHumans() : null,
                'expired' => !empty($vipData->valid_till) && Carbon::parse($vipData->valid_till)->lt(now()) && (bool) $vipData->status,
                'refer_by_name' => $vipData->referBy?->name ?? 'N/A',
                'refer_by_email' => $vipData->referBy?->email ?? 'N/A',
                'nid_front_url' => !empty($vipData->nid_front) ? asset('storage/' . $vipData->nid_front) : null,
                'nid_back_url' => !empty($vipData->nid_back) ? asset('storage/' . $vipData->nid_back) : null,
            ],
            'vips' => Packages::query()->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                ];
            })->values()->all(),
        ]);
    }

    public function indexReact(Request $request)
    {
        $nav = $request->input('nav', 'Active');
        $find = trim((string) $request->input('find', ''));
        $query = Packages::query()->withCount('user')->latest('id');

        if ($nav === 'Trash') {
            $query->onlyTrashed();
        }

        if ($find !== '') {
            $query->where(function ($subQuery) use ($find) {
                $subQuery
                    ->where('id', 'like', '%' . $find . '%')
                    ->orWhere('name', 'like', '%' . $find . '%')
                    ->orWhere('slug', 'like', '%' . $find . '%')
                    ->orWhere('price', 'like', '%' . $find . '%')
                    ->orWhere('coin', 'like', '%' . $find . '%')
                    ->orWhere('m_coin', 'like', '%' . $find . '%')
                    ->orWhere('countdown', 'like', '%' . $find . '%');
            });
        }

        $packages = $query->paginate(config('app.paginate'))->withQueryString();

        return Inertia::render('Auth/system/vip/package/index', [
            'nav' => $nav,
            'filters' => [
                'nav' => $nav,
                'find' => $find,
            ],
            'packages' => [
                'data' => $packages->getCollection()->values()->map(function ($item) use ($packages) {
                    $usersCount = $item->user_count ?? 0;

                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'price' => $item->price,
                        'countdown' => $item->countdown,
                        'coin' => $item->coin,
                        'm_coin' => $item->m_coin ?? '0',
                        'ref_owner_get_coin' => $item->ref_owner_get_coin,
                        'users_count' => $usersCount,
                        'earn' => $item->price * $usersCount,
                        'created_at_human' => $item->created_at?->diffForHumans(),
                        'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                        'trashed' => method_exists($item, 'trashed') ? $item->trashed() : false,
                    ];
                })->all(),
                'links' => collect($packages->linkCollection())->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $packages->firstItem(),
                'to' => $packages->lastItem(),
                'total' => $packages->total(),
            ],
            'printUrl' => route('system.vip.package.print-summery', [
                'nav' => $nav,
                'find' => $find,
            ]),
        ]);
    }

    public function printPackageReact(Request $request)
    {
        $nav = $request->input('nav', 'Active');
        $find = trim((string) $request->input('find', ''));
        $query = Packages::query()->withCount('user')->latest('id');

        if ($nav === 'Trash') {
            $query->onlyTrashed();
        }

        if ($find !== '') {
            $query->where(function ($subQuery) use ($find) {
                $subQuery
                    ->where('id', 'like', '%' . $find . '%')
                    ->orWhere('name', 'like', '%' . $find . '%')
                    ->orWhere('slug', 'like', '%' . $find . '%')
                    ->orWhere('price', 'like', '%' . $find . '%')
                    ->orWhere('coin', 'like', '%' . $find . '%')
                    ->orWhere('m_coin', 'like', '%' . $find . '%')
                    ->orWhere('countdown', 'like', '%' . $find . '%');
            });
        }

        $packages = $query->get()->map(function ($item) {
            $usersCount = $item->user_count ?? 0;

            return [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'countdown' => $item->countdown,
                'coin' => $item->coin,
                'm_coin' => $item->m_coin ?? '0',
                'ref_owner_get_coin' => $item->ref_owner_get_coin,
                'users_count' => $usersCount,
                'earn' => $item->price * $usersCount,
                'created_at_formatted' => $item->created_at?->toFormattedDateString(),
            ];
        })->values()->all();

        return Inertia::render('Auth/system/vip/package/PrintSummery', [
            'packages' => $packages,
            'filters' => [
                'nav' => $nav,
                'find' => $find,
            ],
        ]);
    }

    public function createReact()
    {
        return Inertia::render('Auth/system/vip/package/Create', [
            'paymentOptions' => [
                ['pay_type' => '', 'pay_to' => ''],
            ],
        ]);
    }

    public function usersReact(Request $request)
    {
        $nav = $request->input('nav', 'All');
        $search = (string) $request->string('search');
        $sdate = $request->input('sdate');
        $edate = $request->input('edate');
        $type = $request->input('type', 'All');
        $validity = $request->input('validity', 'All');
        $query = vip::query()
            ->with(['user', 'package']);

        if ($nav === 'Trash') {
            $query->onlyTrashed();
        } elseif ($nav === 'Pending') {
            $query->where(['status' => false]);
        } elseif ($nav === 'Confirmed' || $nav === 'Active') {
            $query->where(['status' => true]);
        }

        if ($type !== 'All') {
            $query->where(['task_type' => $type]);
        }

        if ($validity !== 'All') {
            $query->whereDate(
                'valid_till',
                $validity === 'valid' ? '>' : '<',
                now()->format('Y-m-d')
            );
        }

        $this->applyDateFilter($query, $sdate, $edate);

        if (!empty($search)) {
            $query = vip::query()
                ->with(['user', 'package'])
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%')
                ->latest('id');

            if ($nav === 'Trash') {
                $query->onlyTrashed();
            } elseif ($nav === 'Pending') {
                $query->where(['status' => false]);
            } elseif ($nav === 'Confirmed' || $nav === 'Active') {
                $query->where(['status' => true]);
            }

            if ($type !== 'All') {
                $query->where(['task_type' => $type]);
            }

            if ($validity !== 'All') {
                $query->whereDate(
                    'valid_till',
                    $validity === 'valid' ? '>' : '<',
                    now()->format('Y-m-d')
                );
            }

            $this->applyDateFilter($query, $sdate, $edate);
        }

        $vipUsers = $query->paginate(config('app.paginate'))->withQueryString();

        return Inertia::render('Auth/system/vip/users', [
            'filters' => [
                'nav' => $nav,
                'search' => $search,
                'sdate' => $sdate,
                'edate' => $edate,
                'type' => $type,
                'validity' => $validity,
            ],
            'printUrl' => route('system.vip.print-summery', [
                'nav' => $nav,
                'search' => $search,
                'sdate' => $sdate,
                'edate' => $edate,
                'type' => $type,
                'validity' => $validity,
            ]),
            'vip' => [
                'data' => $vipUsers->getCollection()->values()->map(function ($item, $index) use ($vipUsers) {
                    $status = 'Pending';

                    if ($item->status) {
                        $status = 'Active';
                    } elseif (($item->stauts ?? null) == -1 || $item->deleted_at) {
                        $status = 'Trash';
                    }

                    return [
                        'sl' => (($vipUsers->currentPage() - 1) * $vipUsers->perPage()) + $index + 1,
                        'id' => $item->id,
                        'name' => $item->name ?? 'N/A',
                        'user_email' => $item->user?->email ?? 'N/A',
                        'package_name' => $item->package?->name ?? 'N/A',
                        'task_type' => $item->task_type ?? 'N/A',
                        'user_coin' => $item->user?->coin ?? '0',
                        'status' => $status,
                        'deleted_at_formatted' => $item->deleted_at?->toFormattedDateString(),
                        'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                        'valid_till_formatted' => $item->valid_till ? Carbon::parse($item->valid_till)->toFormattedDateString() : '',
                        'valid_till_human' => $item->valid_till ? Carbon::parse($item->valid_till)->diffForHumans() : '',
                    ];
                })->all(),
                'links' => collect($vipUsers->linkCollection())->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $vipUsers->firstItem(),
                'to' => $vipUsers->lastItem(),
                'total' => $vipUsers->total(),
            ],
        ]);
    }

    public function printReact(Request $request)
    {
        $nav = $request->input('nav', 'All');
        $search = (string) $request->string('search');
        $sdate = $request->input('sdate');
        $edate = $request->input('edate');
        $type = $request->input('type');
        $validity = $request->input('validity');

        $query = vip::query()
            ->with(['user', 'package']);

        if ($nav == 'Trash') {
            $query->onlyTrashed();
        } elseif ($nav === 'Pending') {
            $query->where(['status' => false]);
        } elseif ($nav === 'Confirmed' || $nav === 'Active') {
            $query->where(['status' => true]);
        }

        if ($type != 'All') {
            $query->where(['task_type' => $type]);
        }

        if ($validity != 'All') {
            $query->whereDate('valid_till', $validity == 'valid' ? '>' : '<', now()->format('Y-m-d'));
        }

        $this->applyDateFilter($query, $sdate, $edate);

        if (isset($search) && !empty($search)) {
            $query = vip::query()
                ->with(['user', 'package'])
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%');

            if ($nav == 'Trash') {
                $query->onlyTrashed();
            } elseif ($nav === 'Pending') {
                $query->where(['status' => false]);
            } elseif ($nav === 'Confirmed' || $nav === 'Active') {
                $query->where(['status' => true]);
            }

            if ($type != 'All') {
                $query->where(['task_type' => $type]);
            }

            if ($validity != 'All') {
                $query->whereDate('valid_till', $validity == 'valid' ? '>' : '<', now()->format('Y-m-d'));
            }

            $this->applyDateFilter($query, $sdate, $edate);
        }

        $vipUsers = $query->paginate(config('app.paginate'));

        $items = $vipUsers->getCollection()->values()->map(function ($item, $index) {
            $status = 'Pending';

            if ($item?->status) {
                $status = 'Active';
            } elseif (($item?->stauts ?? null) == -1 || $item?->deleted_at) {
                $status = 'Trash';
            }

            return [
                'sl' => $index + 1,
                'name' => $item?->name ?? 'N/A',
                'user_email' => $item?->user?->email ?? 'N/A',
                'package_name' => $item?->package?->name ?? 'N/A',
                'task_type' => $item?->task_type ?? 'N/A',
                'amount' => $item?->package?->price ?? 0,
                'comission' => $item?->comission ?? 0,
                'status' => $status,
                'deleted_at_formatted' => $item?->deleted_at?->toFormattedDateString(),
                'created_at_formatted' => $item?->created_at?->toFormattedDateString(),
                'valid_till_formatted' => $item?->valid_till ? Carbon::parse($item->valid_till)->toFormattedDateString() : '',
                'valid_till_human' => $item?->valid_till ? Carbon::parse($item->valid_till)->diffForHumans() : '',
            ];
        })->all();

        return Inertia::render('Auth/system/vip/PrintSummery', [
            'sdate' => $sdate,
            'edate' => $edate,
            'vip' => $items,
            'totals' => [
                'count' => count($items),
                'package_price' => collect($items)->sum('amount'),
                'comission' => collect($items)->sum('comission'),
            ],
        ]);
    }

    public function editReact(Packages $packages)
    {
        return Inertia::render('Auth/system/vip/package/Edit', [
            'package' => [
                'id' => $packages->id,
                'name' => $packages->name,
                'price' => $packages->price,
                'countdown' => $packages->countdown,
                'coin' => $packages->coin,
                'm_coin' => $packages->m_coin,
                'ref_owner_get_coin' => $packages->ref_owner_get_coin,
                'description' => $packages->description,
            ],
            'paymentOptions' => $packages->payOption->map(function ($option) {
                return [
                    'pay_type' => $option->pay_type,
                    'pay_to' => $option->pay_to,
                ];
            })->values()->all(),
        ]);
    }

    public function updateUserStatus(Request $request, $vip)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:active,pending,reject'],
            'valid_days' => ['nullable', 'numeric', 'min:1'],
        ]);

        $vipData = $this->findVipUser($vip);

        if ($vipData->deleted_at) {
            return redirect()->back()->with('warning', 'Trashed !');
        }

        if ($validated['status'] === 'active') {
            $vipData->status = 1;

            if (empty($vipData->valid_till)) {
                $days = (int) ($validated['valid_days'] ?? 360);
                $vipData->valid_from = now();
                $vipData->valid_till = now()->addDays($days);
            }

            $vipData->save();

            $vipUser = $vipData->user;
            $ref = User::find($vipData->refer);

            if ($vipUser && $ref) {
                $ref->coin += $vipData->comission ?? 100;
                $ref->save();
            }

            return redirect()->back()->with('success', 'Status Updated !');
        }

        if ($validated['status'] === 'pending') {
            $vipData->status = 0;
            $vipData->save();

            return redirect()->back()->with('success', 'Status Updated !');
        }

        $vipData->status = -1;
        $vipData->delete();

        return redirect()->route('system.vip.users')->with('success', 'Moved to trash !');
    }

    public function updateUserTask(Request $request, $vip)
    {
        $validated = $request->validate([
            'task' => ['required', 'in:daily,monthly,disabled'],
        ]);

        $vipData = $this->findVipUser($vip);
        $vipData->task_type = $validated['task'];
        $vipData->save();

        return redirect()->back()->with('success', 'Task Type Updated !');
    }

    public function updateUserValidity(Request $request, $vip)
    {
        $validated = $request->validate([
            'valid_days' => ['required', 'numeric', 'min:1'],
        ]);

        $vipData = $this->findVipUser($vip);
        $vipData->valid_from = now();
        $vipData->valid_till = now()->addDays((int) $validated['valid_days']);
        $vipData->save();

        return redirect()->back()->with('success', 'Validation Updated!');
    }

    public function reCalculateRefComission($vip)
    {
        $vipData = $this->findVipUser($vip);

        if (!$vipData->refer) {
            return redirect()->back()->with('error', 'No refer associated with this vip !');
        }

        $refUser = User::find($vipData->refer);

        if (!$refUser) {
            return redirect()->back()->with('error', 'Refer user not found !');
        }

        $refUser->coin += $vipData->comission ?? 100;
        $refUser->save();

        return redirect()->back()->with('success', 'Comission added to refer user !');
    }

    public function pushBackRefComission($vip)
    {
        $vipData = $this->findVipUser($vip);

        if (!$vipData->refer) {
            return redirect()->back()->with('error', 'No refer associated with this vip !');
        }

        $refUser = User::find($vipData->refer);

        if (!$refUser) {
            return redirect()->back()->with('error', 'Refer user not found !');
        }

        $refUser->coin -= $vipData->comission ?? 100;

        if ($refUser->coin < 0) {
            $refUser->coin = 0;
        }

        $refUser->save();

        return redirect()->back()->with('success', 'Comission removed from refer user !');
    }

    public function restoreUser($vip)
    {
        $vipData = $this->findVipUser($vip);
        $vipData->restore();

        return redirect()->back()->with('success', 'Succfully restored !');
    }

    public function deleteUser($vip)
    {
        $vipData = $this->findVipUser($vip);
        $vipData->forceDelete();

        return redirect()->route('system.vip.users')->with('success', 'Deleted permanently !');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'price' => ['required', 'numeric'],
            'coin' => ['required', 'numeric'],
            'm_coin' => ['required', 'numeric'],
            'countdown' => ['required', 'numeric'],
            'ref_owner_get_coin' => ['required', 'numeric'],
            'owner_get_coin' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
            'paymentOptions' => ['array'],
            'paymentOptions.*.pay_type' => ['nullable', 'string'],
            'paymentOptions.*.pay_to' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $package = Packages::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'price' => $validated['price'],
                'countdown' => $validated['countdown'],
                'status' => 1,
                'coin' => $validated['coin'],
                'm_coin' => $validated['m_coin'],
                'ref_owner_get_coin' => $validated['ref_owner_get_coin'],
                'owner_get_coin' => $validated['owner_get_coin'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            foreach ($validated['paymentOptions'] ?? [] as $value) {
                Package_pays::create([
                    'package_id' => $package->id,
                    'pay_type' => $value['pay_type'] ?? '',
                    'pay_to' => $value['pay_to'] ?? '',
                ]);
            }
        });

        return redirect()->route('system.vip.index')->with('success', 'VIP package created');
    }

    public function update(Request $request, Packages $packages)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'price' => ['required', 'numeric'],
            'coin' => ['required', 'numeric'],
            'm_coin' => ['nullable', 'numeric'],
            'countdown' => ['required', 'numeric'],
            'ref_owner_get_coin' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
            'paymentOptions' => ['array'],
            'paymentOptions.*.pay_type' => ['nullable', 'string'],
            'paymentOptions.*.pay_to' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $packages) {
            $packages->update([
                'name' => $validated['name'],
                'price' => $validated['price'],
                'countdown' => $validated['countdown'],
                'coin' => $validated['coin'],
                'm_coin' => $validated['m_coin'] ?? null,
                'ref_owner_get_coin' => $validated['ref_owner_get_coin'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            Package_pays::where(['package_id' => $packages->id])->delete();

            foreach ($validated['paymentOptions'] ?? [] as $value) {
                if (!empty($value['pay_type']) && !empty($value['pay_to'])) {
                    Package_pays::create([
                        'package_id' => $packages->id,
                        'pay_type' => $value['pay_type'],
                        'pay_to' => $value['pay_to'],
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Updated !');
    }

    public function trash($id)
    {
        Packages::destroy($id);

        return redirect()->back()->with('success', 'Packages now in Trash');
    }

    public function restore($id)
    {
        Packages::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->back()->with('success', 'Packages restored');
    }

    private function applyDateFilter($query, ?string $sdate, ?string $edate): void
    {
        if (!empty($sdate) && !empty($edate)) {
            $start = Carbon::parse($sdate)->startOfDay();
            $end = Carbon::parse($edate)->endOfDay();

            if ($start->gt($end)) {
                [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
            }

            $query->whereBetween('created_at', [$start, $end]);

            return;
        }

        if (!empty($sdate)) {
            $query->whereBetween('created_at', [
                Carbon::parse($sdate)->startOfDay(),
                Carbon::parse($sdate)->endOfDay(),
            ]);

            return;
        }

        if (!empty($edate)) {
            $query->whereBetween('created_at', [
                Carbon::parse($edate)->startOfDay(),
                Carbon::parse($edate)->endOfDay(),
            ]);
        }
    }

    private function findVipUser($vip): vip
    {
        return vip::query()
            ->withTrashed()
            ->with(['user', 'package', 'referBy'])
            ->findOrFail($vip);
    }
}
