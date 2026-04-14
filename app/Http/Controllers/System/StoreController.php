<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\DistributeComissions;
use App\Models\Store;
use App\Models\TakeComissions;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class StoreController extends Controller
{
    public function indexReact(Request $request): Response
    {
        $activeTab = (string) $request->query('tab', 'commissions');
        $tabs = ['commissions', 'withdrawals'];

        $store = $this->resolveStore();
        $targetStore = $this->resolveStore(Carbon::now()->subMonth());

        $widgets = [
            ['label' => 'Total Earnings', 'value' => $this->sumStoreColumn('total_balance')],
            ['label' => 'Final Remaining', 'value' => $this->sumBalanceColumn('current')],
            ['label' => 'Monthly Total', 'value' => $store['total_balance'] ?? 0],
            ['label' => 'Current Balance', 'value' => $store['current_balance'] ?? 0],
            ['label' => 'Total Distributed', 'value' => $store['distribute_balance'] ?? 0],
            ['label' => 'Previous Total', 'value' => $targetStore['total_balance'] ?? 0],
            ['label' => 'Previous Balance', 'value' => $targetStore['current_balance'] ?? 0],
            ['label' => 'Last Distributed', 'value' => $targetStore['distribute_balance'] ?? 0],
        ];

        $columns1 = ['SL', 'Name of User', 'Store Info', 'Given', 'Range', 'Purpose', 'A/C'];
        $columns2 = ['SL', 'Name of User', 'Store', 'Server Cost', 'Donation', 'Method', 'Status', 'Requested At', 'A/C'];

        $commissions = DistributeComissions::query()
            ->with('user')
            ->whereIn('info', ['Store Commission', 'Developer Commission', 'Management Commission'])
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $withdrawals = Withdraw::query()
            ->with('user')
            ->where('type', 'debit')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $commissionStoreMap = $this->buildStoreMap($commissions);

        return Inertia::render('Auth/system/store/index', [
            'pageTitle' => 'Coin Store - ' . now()->format('F Y'),
            'widgets' => $widgets,
            'tabs' => $tabs,
            'activeTab' => in_array($activeTab, $tabs, true) ? $activeTab : 'commissions',
            'columns1' => $columns1,
            'columns2' => $columns2,
            'storeMeta' => [
                'current' => $store,
                'target' => $targetStore,
            ],
            'coinStore' => [
                'store' => $this->sumBalanceColumn('current'),
                'take' => TakeComissions::where(['confirmed' => true])->sum('take_comission'),
                'give' => TakeComissions::where(['confirmed' => true])->sum('distribute_comission'),
            ],
            'coastStore' => [
                'store' => $this->withdrawDiff('maintenance_fee'),
            ],
            'donationStore' => [
                'store' => $this->withdrawDiff('server_fee'),
            ],
            'commissions' => [
                'data' => $commissions->getCollection()->values()->map(function (DistributeComissions $item, int $index) use ($commissionStoreMap) {
                    $store = $this->formatStoreLabel($commissionStoreMap[$item->id] ?? null);

                    return [
                        'sl' => $index + 1,
                        'user_name' => $item->user?->name ?? 'N/A',
                        'store' => $store,
                        'amount' => number_format((float) $item->amount, 2) . '/-',
                        'range' => number_format((float) $item->range, 2) . '%',
                        'info' => $item->info ?? '',
                    ];
                })->all(),
                'links' => collect($commissions->linkCollection())->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'total' => $commissions->total(),
            ],
            'withdrawals' => [
                'data' => $withdrawals->getCollection()->values()->map(function (Withdraw $withdraw, int $index) {
                    return [
                        'sl' => $index + 1,
                        'user_name' => $withdraw->user?->name ?? 'N/A',
                        'store_req' => number_format((float) $withdraw->store_req, 2) . '/-',
                        'maintenance_fee' => number_format((float) $withdraw->maintenance_fee, 2) . '/-',
                        'server_fee' => number_format((float) $withdraw->server_fee, 2) . '/-',
                        'pay_by' => $withdraw->pay_by ?? '',
                        'status' => $withdraw->status === 1 ? 'Confirm' : 'Pending',
                        'requested_at' => $withdraw->created_at?->format('M d, Y'),
                    ];
                })->all(),
                'links' => collect($withdrawals->linkCollection())->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'total' => $withdrawals->total(),
            ],
        ]);
    }

    public function distribute(Request $request): RedirectResponse
    {
        $targetStore = $this->resolveStore(Carbon::now()->subMonth());

        if (!$targetStore || empty($targetStore['id'])) {
            return back()->with('error', 'Previous month store not found');
        }

        if (!empty($targetStore['generate'])) {
            return back()->with('error', 'Distribution already generated');
        }

        $balance = (float) ($targetStore['total_balance'] ?? 0);
        if ($balance <= 0) {
            return back()->with('error', 'No balance available for distribution');
        }

        $developerPool = $balance * 0.05;
        $managementPool = $balance * 0.10;
        $levelPool = $balance * 0.85;

        $developers = User::with('developerAccess')
            ->whereHas('developerAccess')
            ->get();

        $managers = User::with('managementAccess')
            ->whereHas('managementAccess')
            ->get();

        $levelUsers = User::with('currentLevel')
            ->where('current_level_id', '!=', 1)
            ->whereHas('currentLevel')
            ->get();

        DB::transaction(function () use (
            $developers,
            $managers,
            $levelUsers,
            $developerPool,
            $managementPool,
            $levelPool,
            $targetStore
        ) {
            $totalDistributed = 0;

            if ($developers->count() > 0) {
                $share = $developerPool / $developers->count();
                foreach ($developers as $user) {
                    $this->insertCommission($user->id, null, 'Developer Commission', $targetStore['id'], $share);
                    $totalDistributed += $share;
                }
            }

            if ($managers->count() > 0) {
                $share = $managementPool / $managers->count();
                foreach ($managers as $user) {
                    $this->insertCommission($user->id, null, 'Management Commission', $targetStore['id'], $share);
                    $totalDistributed += $share;
                }
            }

            $totalBonus = $levelUsers->sum(fn ($u) => $u->currentLevel->bonus ?? 0);
            if ($totalBonus > 0) {
                foreach ($levelUsers as $user) {
                    $percent = (float) ($user->currentLevel->bonus ?? 0);
                    $share = ($percent / $totalBonus) * $levelPool;
                    $this->insertCommission($user->id, $percent, 'Store Commission', $targetStore['id'], $share);
                    $totalDistributed += $share;
                }
            }

            if ($this->storeHasColumns(['current_balance', 'distribute_balance', 'generate'])) {
                DB::table('stores')
                    ->where('id', $targetStore['id'])
                    ->update([
                        'current_balance' => DB::raw('current_balance - ' . $totalDistributed),
                        'distribute_balance' => DB::raw('distribute_balance + ' . $totalDistributed),
                        'generate' => true,
                    ]);
            }
        });

        return back()->with('success', 'Previous month balance distributed successfully');
    }

    public function withdrawCoin(Request $request): RedirectResponse
    {
        $payload = $this->validateWithdraw($request);

        DB::transaction(function () use ($payload) {
            $balance = $this->lockBalance();

            if ($balance['current'] < $payload['amount']) {
                throw new \Exception('Insufficient system balance');
            }

            Withdraw::create($this->buildWithdrawPayload($payload, [
                'store_req' => $payload['amount'],
                'server_fee' => 0,
                'maintenance_fee' => 0,
            ]));

            if ($this->balanceHasColumn('current')) {
                DB::table('balances')->decrement('current', $payload['amount']);
            }
            if ($this->balanceHasColumn('withdraw')) {
                DB::table('balances')->increment('withdraw', $payload['amount']);
            }
        });

        return back()->with('success', 'Withdraw resquest successfull');
    }

    public function withdrawCoast(Request $request): RedirectResponse
    {
        $payload = $this->validateWithdraw($request);

        Withdraw::create($this->buildWithdrawPayload($payload, [
            'store_req' => 0,
            'server_fee' => 0,
            'maintenance_fee' => $payload['amount'],
        ]));

        return back()->with('success', 'Withdraw resquest successfull');
    }

    public function withdrawDonation(Request $request): RedirectResponse
    {
        $payload = $this->validateWithdraw($request);

        Withdraw::create($this->buildWithdrawPayload($payload, [
            'store_req' => 0,
            'server_fee' => $payload['amount'],
            'maintenance_fee' => 0,
        ]));

        return back()->with('success', 'Withdraw resquest successfull');
    }

    private function validateWithdraw(Request $request): array
    {
        return $request->validate([
            'amount' => ['required', 'numeric'],
            'method' => ['required'],
            'phone' => ['nullable'],
            'bankAccount' => ['nullable', 'string'],
            'accountholder' => ['nullable', 'string'],
            'bankBranch' => ['nullable', 'string'],
            'swiftCode' => ['nullable', 'string'],
            'accountNumber' => ['nullable', 'string'],
            'remarks' => ['nullable', 'string'],
        ]);
    }

    private function buildWithdrawPayload(array $payload, array $overrides = []): array
    {
        $payTo = $payload['accountNumber'] ?: ($payload['phone'] ?: null);

        return [
            'user_id' => Auth::user()->id,
            'phone' => $payload['phone'] ?? null,
            'pay_by' => $payload['method'],
            'pay_to' => $payTo,
            'amount' => $payload['amount'],
            'type' => 'debit',
            'status' => 0,
            'payable_amount' => $payload['amount'],
            'total_fee' => $payload['amount'],
            'fee_range' => 100,
            'payment_method' => $payload['method'],
            'bank_account' => $payload['bankAccount'] ?? null,
            'account_holder_name' => $payload['accountholder'] ?? null,
            'bank_branch' => $payload['bankBranch'] ?? null,
            'swift_code' => $payload['swiftCode'] ?? null,
            'account_number' => $payload['accountNumber'] ?? null,
            'remarks' => $payload['remarks'] ?? null,
            ...$overrides,
        ];
    }

    private function resolveStore(?Carbon $date = null): array
    {
        $date = $date ?: Carbon::now();
        $year = $date->year;
        $month = $date->month;

        if ($this->storeHasColumns(['year', 'month'])) {
            $store = DB::table('stores')
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            if (!$store) {
                $insert = [
                    'year' => $year,
                    'month' => $month,
                    'total_balance' => 0,
                    'current_balance' => 0,
                    'distribute_balance' => 0,
                    'generate' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                DB::table('stores')->insert($insert);
                $store = DB::table('stores')
                    ->where('year', $year)
                    ->where('month', $month)
                    ->first();
            }

            return $store ? (array) $store : [];
        }

        $fallback = Store::query()->first();
        return $fallback ? $fallback->toArray() : [];
    }

    private function storeHasColumns(array $columns): bool
    {
        foreach ($columns as $column) {
            if (!Schema::hasColumn('stores', $column)) {
                return false;
            }
        }
        return true;
    }

    private function balanceHasColumn(string $column): bool
    {
        return Schema::hasTable('balances') && Schema::hasColumn('balances', $column);
    }

    private function sumBalanceColumn(string $column): float
    {
        if (!$this->balanceHasColumn($column)) {
            return 0;
        }

        return (float) DB::table('balances')->sum($column);
    }

    private function sumStoreColumn(string $column): float
    {
        if (!Schema::hasTable('stores') || !Schema::hasColumn('stores', $column)) {
            return 0;
        }

        return (float) DB::table('stores')->sum($column);
    }

    private function withdrawDiff(string $column): float
    {
        $credit = Withdraw::where(['type' => 'credit', 'status' => true])->sum($column);
        $debit = Withdraw::where(['type' => 'debit', 'status' => true])->sum($column);
        return (float) $credit - (float) $debit;
    }

    private function lockBalance(): array
    {
        if (!$this->balanceHasColumn('current')) {
            throw new \Exception('System balance not found');
        }

        $balance = DB::table('balances')->lockForUpdate()->first();
        if (!$balance) {
            throw new \Exception('System balance not found');
        }

        return (array) $balance;
    }

    private function insertCommission(int $userId, ?float $percentage, string $info, int $storeId, float $balance): void
    {
        $data = [
            'user_id' => $userId,
            'confirmed' => 1,
            'amount' => $percentage === null ? $balance : ($percentage / 100) * $balance,
            'range' => $percentage ?? 0,
            'info' => $info,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('distribute_comissions', 'store_id')) {
            $data['store_id'] = $storeId;
        }

        DB::table('distribute_comissions')->insert($data);
    }

    private function buildStoreMap($commissions): array
    {
        if (!Schema::hasColumn('distribute_comissions', 'store_id')) {
            return [];
        }

        $storeIds = $commissions->getCollection()->pluck('store_id')->filter()->unique()->values();
        if ($storeIds->isEmpty()) {
            return [];
        }

        $stores = DB::table('stores')->whereIn('id', $storeIds)->get()->keyBy('id');
        $map = [];

        foreach ($commissions as $commission) {
            $map[$commission->id] = $stores[$commission->store_id] ?? null;
        }

        return $map;
    }

    private function formatStoreLabel($store): string
    {
        if (!$store) {
            return 'N/A';
        }

        if (isset($store->year, $store->month)) {
            return Carbon::create($store->year, $store->month)->format('M-Y');
        }

        if (isset($store->created_at)) {
            return Carbon::parse($store->created_at)->format('M-Y');
        }

        return 'N/A';
    }
}
