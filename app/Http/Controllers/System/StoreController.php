<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UserWalletController;
use App\Models\DeveloperAccess;
use App\Models\DistributeComissions;
use App\Models\Level;
use App\Models\LevelHistory;
use App\Models\ManagementAccess;
use App\Models\ManagementTeam;
use App\Models\Store;
use App\Models\TakeComissions;
use App\Models\User;
use App\Models\Withdraw;
use App\Support\SystemSettings;
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
        $search = trim((string) $request->query('search', ''));
        $startDate = $this->dateQueryValue($request, 'start_date');
        $endDate = $this->dateQueryValue($request, 'end_date');
        $tabs = ['commissions', 'withdrawals'];

        $metrics = $this->buildStoreMetrics();
        $store = $metrics['current'];
        $targetStore = $metrics['previous'];
        $targetStore['can_distribute'] = $this->canDistributeStore($targetStore);

        $widgets = [
            ['label' => 'Total Earnings', 'value' => $metrics['totals']['earnings']],
            ['label' => 'Final Remaining', 'value' => $metrics['totals']['remaining']],
            ['label' => 'Monthly Total', 'value' => $store['total_balance'] ?? 0],
            ['label' => 'Current Balance', 'value' => $store['current_balance'] ?? 0],
            ['label' => 'Developer Share', 'value' => $store['developer_balance'] ?? 0],
            ['label' => 'Management Share', 'value' => $store['management_balance'] ?? 0],
            ['label' => 'Management TM Share', 'value' => $store['management_team_balance'] ?? 0],
            ['label' => 'Star System Share', 'value' => $store['star_system_balance'] ?? 0],
            ['label' => 'Total Share', 'value' => $store['total_share'] ?? 0],
            ['label' => 'Previous Total', 'value' => $targetStore['total_balance'] ?? 0],
            ['label' => 'Last Distributed', 'value' => $targetStore['distribute_balance'] ?? 0],
        ];

        $columns1 = ['SL', 'Name of User', 'Store Info', 'Given', 'Range', 'Purpose', 'Distributed At', 'A/C'];
        $columns2 = ['SL', 'Name of User', 'Store', 'Server Cost', 'Donation', 'Method', 'Status', 'Requested At', 'Remarks', 'A/C'];

        $commissions = DistributeComissions::query()
            ->with('user')
            ->when($startDate !== '', fn ($query) => $query->whereRaw('DATE(created_at) >= ?', [$startDate]))
            ->when($endDate !== '', fn ($query) => $query->whereRaw('DATE(created_at) <= ?', [$endDate]))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('info', 'like', '%' . $search . '%')
                        ->orWhere('amount', 'like', '%' . $search . '%')
                        ->orWhere('range', 'like', '%' . $search . '%')
                        ->orWhere('created_at', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery
                                ->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $withdrawals = Withdraw::query()
            ->with('user')
            ->where('type', 'debit')
            ->when($startDate !== '', fn ($query) => $query->whereRaw('DATE(created_at) >= ?', [$startDate]))
            ->when($endDate !== '', fn ($query) => $query->whereRaw('DATE(created_at) <= ?', [$endDate]))
            ->when($search !== '', fn ($query) => $this->applyWithdrawSearch($query, $search))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $commissionStoreMap = $this->buildStoreMap($commissions);

        return Inertia::render('Auth/system/store/index', [
            'pageTitle' => 'Coin Store - ' . ($store['label'] ?? now()->format('F Y')),
            'widgets' => $widgets,
            'tabs' => $tabs,
            'activeTab' => in_array($activeTab, $tabs, true) ? $activeTab : 'commissions',
            'filters' => [
                'tab' => in_array($activeTab, $tabs, true) ? $activeTab : 'commissions',
                'search' => $search,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'columns1' => $columns1,
            'columns2' => $columns2,
            'storeMeta' => [
                'current' => $store,
                'target' => $targetStore,
                'totals' => $metrics['totals'],
                'percentages' => $metrics['percentages'],
            ],
            'coinStore' => [
                'store' => $metrics['totals']['remaining'],
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
                        'created_at' => $item->created_at?->format('M d, Y'),
                    ];
                })->all(),
                'links' => collect($commissions->linkCollection())->map(function (array $link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $commissions->firstItem(),
                'to' => $commissions->lastItem(),
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
                        'remarks' => $withdraw->remarks ?? '-',
                    ];
                })->all(),
                'links' => collect($withdrawals->linkCollection())->map(function (array $link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags($link['label']),
                        'active' => $link['active'],
                    ];
                })->values()->all(),
                'from' => $withdrawals->firstItem(),
                'to' => $withdrawals->lastItem(),
                'total' => $withdrawals->total(),
            ],
            'printUrl' => route('system.store.print', [
                'tab' => in_array($activeTab, $tabs, true) ? $activeTab : 'commissions',
                'search' => $search,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]),
        ]);
    }

    public function printReact(Request $request): Response
    {
        $activeTab = (string) $request->query('tab', 'commissions');
        $search = trim((string) $request->query('search', ''));
        $startDate = $this->dateQueryValue($request, 'start_date');
        $endDate = $this->dateQueryValue($request, 'end_date');
        $tabs = ['commissions', 'withdrawals'];
        $activeTab = in_array($activeTab, $tabs, true) ? $activeTab : 'commissions';

        $commissions = DistributeComissions::query()
            ->with('user')
            ->when($startDate !== '', fn ($query) => $query->whereRaw('DATE(created_at) >= ?', [$startDate]))
            ->when($endDate !== '', fn ($query) => $query->whereRaw('DATE(created_at) <= ?', [$endDate]))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('info', 'like', '%' . $search . '%')
                        ->orWhere('amount', 'like', '%' . $search . '%')
                        ->orWhere('range', 'like', '%' . $search . '%')
                        ->orWhere('created_at', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery
                                ->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest('id')
            ->get();

        $withdrawals = Withdraw::query()
            ->with('user')
            ->where('type', 'debit')
            ->when($startDate !== '', fn ($query) => $query->whereRaw('DATE(created_at) >= ?', [$startDate]))
            ->when($endDate !== '', fn ($query) => $query->whereRaw('DATE(created_at) <= ?', [$endDate]))
            ->when($search !== '', fn ($query) => $this->applyWithdrawSearch($query, $search))
            ->latest('id')
            ->get();

        $commissionStoreMap = $this->buildStoreMap($commissions);

        return Inertia::render('Auth/system/store/PrintSummery', [
            'activeTab' => $activeTab,
            'filters' => [
                'tab' => $activeTab,
                'search' => $search,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'commissions' => $commissions->values()->map(function (DistributeComissions $item, int $index) use ($commissionStoreMap) {
                $store = $this->formatStoreLabel($commissionStoreMap[$item->id] ?? null);

                return [
                    'sl' => $index + 1,
                    'user_name' => $item->user?->name ?? 'N/A',
                    'store' => $store,
                    'amount' => number_format((float) $item->amount, 2) . '/-',
                    'range' => number_format((float) $item->range, 2) . '%',
                    'info' => $item->info ?? '',
                    'created_at' => $item->created_at?->format('M d, Y'),
                ];
            })->all(),
            'withdrawals' => $withdrawals->values()->map(function (Withdraw $withdraw, int $index) {
                return [
                    'sl' => $index + 1,
                    'user_name' => $withdraw->user?->name ?? 'N/A',
                    'store_req' => number_format((float) $withdraw->store_req, 2) . '/-',
                    'maintenance_fee' => number_format((float) $withdraw->maintenance_fee, 2) . '/-',
                    'server_fee' => number_format((float) $withdraw->server_fee, 2) . '/-',
                    'pay_by' => $withdraw->pay_by ?? '',
                    'status' => $withdraw->status === 1 ? 'Confirm' : 'Pending',
                    'requested_at' => $withdraw->created_at?->format('M d, Y'),
                    'remarks' => $withdraw->remarks ?? '-',
                ];
            })->all(),
        ]);
    }

    public function distribute(Request $request): RedirectResponse
    {
        $metrics = $this->buildStoreMetrics();
        $targetStore = $metrics['previous'];

        if (!$targetStore || empty($targetStore['id'])) {
            return back()->with('error', 'Previous month store not found');
        }

        if (!empty($targetStore['generate'])) {
            return back()->with('error', 'Distribution already generated');
        }

        if (now()->day !== 5) {
            return back()->with('error', 'Distribution is available only on the 5th of each month');
        }

        $balance = (float) ($targetStore['total_balance'] ?? 0);
        if ($balance <= 0) {
            return back()->with('error', 'No balance available for distribution');
        }

        $developerPercentage = (float) ($metrics['percentages']['developer'] ?? 0);
        $managementPercentage = (float) ($metrics['percentages']['management'] ?? 0);
        $managementTeamPercentage = (float) ($metrics['percentages']['management_team'] ?? 0);
        $developerPool = round(($balance * $developerPercentage) / 100, 8);
        $managementPool = round(($balance * $managementPercentage) / 100, 8);
        $managementTeamPool = round(($balance * $managementTeamPercentage) / 100, 8);
        $levelCap = max(0, round($balance - $developerPool - $managementPool - $managementTeamPool, 8));

        $developers = $this->approvedPartnershipUsers(DeveloperAccess::class);
        $managers = $this->approvedPartnershipUsers(ManagementAccess::class);
        $managementTeams = $this->approvedPartnershipUsers(ManagementTeam::class);
        $levelUsers = $this->refreshQualifiedLevelUsers();

        DB::transaction(function () use (
            $developers,
            $managers,
            $levelUsers,
            $developerPool,
            $managementPool,
            $managementTeamPool,
            $levelCap,
            $balance,
            $targetStore,
            $developerPercentage,
            $managementPercentage,
            $managementTeamPercentage
        ) {
            $totalDistributed = 0;

            if ($developers->count() > 0) {
                $share = $developerPool / $developers->count();
                foreach ($developers as $user) {
                    $this->insertCommission($user->id, $developerPercentage, 'Developer Commission', $targetStore['id'], $share);
                    $totalDistributed += $share;
                }
            }

            if ($managers->count() > 0) {
                $share = $managementPool / $managers->count();
                foreach ($managers as $user) {
                    $this->insertCommission($user->id, $managementPercentage, 'Management Commission', $targetStore['id'], $share);
                    $totalDistributed += $share;
                }
            }

            if ($managementTeams->count() > 0) {
                $share = $managementTeamPool / $managementTeams->count();
                foreach ($managementTeams as $user) {
                    $this->insertCommission($user->id, $managementTeamPercentage, 'Management TM Commission', $targetStore['id'], $share);
                    $totalDistributed += $share;
                }
            }

            $starShares = $this->buildStarSystemShares($levelUsers, $balance, $levelCap);

            foreach ($starShares as $shareData) {
                $share = round($shareData['amount'], 8);
                if ($share > 0) {
                    $this->insertCommission(
                        $shareData['user']->id,
                        $shareData['percent'],
                        'Store Commission',
                        $targetStore['id'],
                        $share
                    );
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

        $error = DB::transaction(function () use ($payload) {
            $balance = $this->lockBalance();

            if (!$balance) {
                return 'System balance not found';
            }

            if ($balance['current'] < $payload['amount']) {
                return 'Insufficient system balance';
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

            return null;
        });

        if ($error) {
            return back()->with('error', $error);
        }

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
            'amount' => ['required', 'numeric', 'min:0.01'],
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

    private function applyWithdrawSearch($query, string $search): void
    {
        $searchableColumns = collect([
            'pay_by',
            'phone',
            'account_number',
            'store_req',
            'maintenance_fee',
            'server_fee',
            'remarks',
        ])->filter(fn ($column) => Schema::hasColumn('withdraws', $column))->values();

        $query->where(function ($builder) use ($search, $searchableColumns) {
            foreach ($searchableColumns as $index => $column) {
                if ($index === 0) {
                    $builder->where($column, 'like', '%' . $search . '%');
                } else {
                    $builder->orWhere($column, 'like', '%' . $search . '%');
                }
            }

            $builder->orWhereHas('user', function ($userQuery) use ($search) {
                $userQuery
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        });
    }

    private function buildWithdrawPayload(array $payload, array $overrides = []): array
    {
        $payTo = ($payload['accountNumber'] ?? null) ?: (($payload['phone'] ?? null) ?: null);

        $data = [
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

        return $this->onlyExistingWithdrawColumns($data);
    }

    private function onlyExistingWithdrawColumns(array $data): array
    {
        if (!Schema::hasTable('withdraws')) {
            return $data;
        }

        return collect($data)
            ->only(Schema::getColumnListing('withdraws'))
            ->all();
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

    private function buildStoreMetrics(): array
    {
        $now = now();
        $developerPercentage = (float) SystemSettings::get('DEVELOPER_PERCENTAGE', '0');
        $managementPercentage = (float) SystemSettings::get('MANAGEMENT_PERCENTAGE', '0');
        $managementTeamPercentage = (float) SystemSettings::get('MANAGEMENT_TEAM_PERCENTAGE', '0');
        $currentStart = $this->settlementStart($now);
        $previousStart = $currentStart->copy()->subMonthNoOverflow();

        $current = $this->buildPeriodStore(
            $currentStart,
            $developerPercentage,
            $managementPercentage,
            $managementTeamPercentage,
            true
        );
        $previous = $this->buildPeriodStore(
            $previousStart,
            $developerPercentage,
            $managementPercentage,
            $managementTeamPercentage,
            false
        );

        $earnings = $this->sumConfirmedStore();
        $distributed = $this->sumDistributedCommissions();

        return [
            'current' => $current,
            'previous' => $previous,
            'totals' => [
                'earnings' => $earnings,
                'distributed' => $distributed,
                'remaining' => max(0, round($earnings - $distributed, 2)),
            ],
            'percentages' => [
                'developer' => $developerPercentage,
                'management' => $managementPercentage,
                'management_team' => $managementTeamPercentage,
                'star_system' => (float) ($current['star_system_percentage'] ?? 0),
            ],
        ];
    }

    private function settlementStart(Carbon $date): Carbon
    {
        return $date->copy()->startOfMonth()->addDays(5)->startOfDay();
    }

    private function settlementEnd(Carbon $start): Carbon
    {
        return $start->copy()->addMonthNoOverflow()->day(5)->endOfDay();
    }

    private function buildPeriodStore(
        Carbon $start,
        float $developerPercentage,
        float $managementPercentage,
        float $managementTeamPercentage,
        bool $allowFutureWindow
    ): array {
        $end = $this->settlementEnd($start);
        $now = now();
        $effectiveEnd = $allowFutureWindow && $now->lt($start)
            ? null
            : ($now->lt($end) ? $now->copy() : $end->copy());

        $totalBalance = $effectiveEnd
            ? $this->sumConfirmedStoreBetween($start, $effectiveEnd)
            : 0.0;

        $store = $this->syncStoreSnapshot($start, $totalBalance);
        $distributedBalance = $this->sumDistributedForStore($store, $start, $end);
        $currentBalance = max(0, round($totalBalance - $distributedBalance, 2));
        $developerBalance = round(($totalBalance * $developerPercentage) / 100, 2);
        $managementBalance = round(($totalBalance * $managementPercentage) / 100, 2);
        $managementTeamBalance = round(($totalBalance * $managementTeamPercentage) / 100, 2);
        $starSystemCap = max(0, round($totalBalance - $developerBalance - $managementBalance - $managementTeamBalance, 2));
        $starSystemBalance = round(
            $this->buildStarSystemShares(
                $this->qualifiedLevelUsersFromHistory(),
                $totalBalance,
                $starSystemCap
            )->sum('amount'),
            2
        );
        $starSystemPercentage = $totalBalance > 0
            ? round(($starSystemBalance / $totalBalance) * 100, 2)
            : 0;
        $totalShare = round($developerBalance + $managementBalance + $managementTeamBalance + $starSystemBalance, 2);

        if (!empty($store)) {
            $store['total_balance'] = $totalBalance;
            $store['current_balance'] = $currentBalance;
            $store['distribute_balance'] = $distributedBalance;
            $store['developer_balance'] = $developerBalance;
            $store['management_balance'] = $managementBalance;
            $store['management_team_balance'] = $managementTeamBalance;
            $store['star_system_balance'] = $starSystemBalance;
            $store['developer_percentage'] = $developerPercentage;
            $store['management_percentage'] = $managementPercentage;
            $store['management_team_percentage'] = $managementTeamPercentage;
            $store['star_system_percentage'] = $starSystemPercentage;
            $store['total_share'] = $totalShare;
            $store['label'] = $start->format('F Y');
            $store['range_label'] = $start->format('d M Y') . ' - ' . $end->format('d M Y');
        }

        if (!empty($store['id']) && $this->storeHasColumns(['total_balance', 'current_balance', 'distribute_balance'])) {
            $updates = [
                'total_balance' => $totalBalance,
                'current_balance' => $currentBalance,
                'distribute_balance' => $distributedBalance,
                'updated_at' => now(),
            ];

            foreach ([
                'developer_percentage' => $developerPercentage,
                'management_percentage' => $managementPercentage,
                'management_team_percentage' => $managementTeamPercentage,
                'star_system_percentage' => $starSystemPercentage,
                'total_share' => $totalShare,
            ] as $column => $value) {
                if (Schema::hasColumn('stores', $column)) {
                    $updates[$column] = $value;
                }
            }

            DB::table('stores')
                ->where('id', $store['id'])
                ->update($updates);
        }

        return $store;
    }

    private function syncStoreSnapshot(Carbon $start, float $totalBalance): array
    {
        if (!$this->storeHasColumns(['year', 'month', 'total_balance', 'current_balance', 'distribute_balance', 'generate'])) {
            return $this->resolveStore($start);
        }

        $store = DB::table('stores')
            ->where('year', $start->year)
            ->where('month', $start->month)
            ->first();

        if (!$store) {
            $insert = [
                'year' => $start->year,
                'month' => $start->month,
                'total_balance' => $totalBalance,
                'current_balance' => $totalBalance,
                'distribute_balance' => 0,
                'generate' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table('stores')->insert($insert);

            $store = DB::table('stores')
                ->where('year', $start->year)
                ->where('month', $start->month)
                ->first();
        }

        return $store ? (array) $store : [];
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
        if (!Schema::hasTable('withdraws') || !Schema::hasColumn('withdraws', $column)) {
            return 0;
        }

        $credit = Withdraw::where(['type' => 'credit', 'status' => true])->sum($column);
        $debit = Withdraw::where(['type' => 'debit', 'status' => true])->sum($column);
        return (float) $credit - (float) $debit;
    }

    private function sumConfirmedStore(): float
    {
        if (!Schema::hasTable('take_comissions') || !Schema::hasColumn('take_comissions', 'store')) {
            return 0;
        }

        return (float) TakeComissions::query()
            ->where('confirmed', true)
            ->sum('store');
    }

    private function sumConfirmedStoreBetween(Carbon $start, Carbon $end): float
    {
        if (!Schema::hasTable('take_comissions') || !Schema::hasColumn('take_comissions', 'store')) {
            return 0;
        }

        return (float) TakeComissions::query()
            ->where('confirmed', true)
            ->whereBetween('created_at', [$start, $end])
            ->sum('store');
    }

    private function sumDistributedCommissions(): float
    {
        if (!Schema::hasTable('distribute_comissions') || !Schema::hasColumn('distribute_comissions', 'amount')) {
            return 0;
        }

        return (float) DistributeComissions::query()
            ->whereIn('info', $this->storeDistributionInfoTypes())
            ->where('confirmed', true)
            ->sum('amount');
    }

    private function sumDistributedForStore(array $store, Carbon $start, Carbon $end): float
    {
        if (!Schema::hasTable('distribute_comissions') || !Schema::hasColumn('distribute_comissions', 'amount')) {
            return 0;
        }

        $query = DistributeComissions::query()
            ->whereIn('info', $this->storeDistributionInfoTypes())
            ->where('confirmed', true);

        if (Schema::hasColumn('distribute_comissions', 'store_id') && !empty($store['id'])) {
            $query->where('store_id', $store['id']);
        } else {
            $query->whereBetween('created_at', [$start, $end]);
        }

        return (float) $query->sum('amount');
    }

    private function storeDistributionInfoTypes(): array
    {
        return ['Store Commission', 'Developer Commission', 'Management Commission', 'Management TM Commission'];
    }

    private function canDistributeStore(array $store): bool
    {
        return !empty($store['id'])
            && empty($store['generate'])
            && now()->day === 5
            && (float) ($store['total_balance'] ?? 0) > 0
            && (float) ($store['current_balance'] ?? 0) > 0;
    }

    private function lockBalance(): ?array
    {
        if (!$this->balanceHasColumn('current')) {
            return null;
        }

        $balance = DB::table('balances')->lockForUpdate()->first();
        if (!$balance) {
            return null;
        }

        return (array) $balance;
    }

    private function insertCommission(int $userId, ?float $percentage, string $info, int $storeId, float $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        $data = [
            'user_id' => $userId,
            'confirmed' => 1,
            'amount' => $amount,
            'range' => $percentage ?? 0,
            'info' => $info,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('distribute_comissions', 'store_id')) {
            $data['store_id'] = $storeId;
        }

        DB::table('distribute_comissions')->insert($data);
        UserWalletController::add($userId, $amount);
    }

    private function buildStarSystemShares($levelUsers, float $totalBalance, float $cap)
    {
        $shares = collect($levelUsers)
            ->map(function (User $user) use ($totalBalance) {
                $percent = (float) ($user->currentLevel->bonus ?? 0);

                return [
                    'user' => $user,
                    'percent' => $percent,
                    'amount' => round(($totalBalance * $percent) / 100, 8),
                ];
            })
            ->filter(fn ($share) => $share['amount'] > 0)
            ->values();

        $rawTotal = (float) $shares->sum('amount');

        if ($rawTotal <= 0 || $cap <= 0) {
            return collect();
        }

        $scale = $rawTotal > $cap ? $cap / $rawTotal : 1;

        return $shares
            ->map(function (array $share) use ($scale) {
                $share['amount'] = round($share['amount'] * $scale, 8);
                return $share;
            })
            ->filter(fn ($share) => $share['amount'] > 0)
            ->values();
    }

    private function qualifiedLevelUsersFromHistory()
    {
        if (!Schema::hasTable('level_histories')) {
            return collect();
        }

        return LevelHistory::query()
            ->with([
                'user',
                'toLevel',
            ])
            ->whereNotNull('user_id')
            ->whereNotNull('to_level_id')
            ->whereHas('user')
            ->whereHas('toLevel', fn ($query) => $query->where('status', true)->where('bonus', '>', 0))
            ->latest('id')
            ->get()
            ->unique('user_id')
            ->map(function (LevelHistory $history) {
                $user = $history->user;

                if (!$user || !$history->toLevel) {
                    return null;
                }

                $user->setRelation('currentLevel', $history->toLevel);
                return $user;
            })
            ->filter()
            ->values();
    }

    private function refreshQualifiedLevelUsers()
    {
        $levels = Level::query()
            ->where('status', true)
            ->orderBy('req_users')
            ->orderBy('vip_users')
            ->orderBy('id')
            ->get();

        if ($levels->isEmpty()) {
            return collect();
        }

        $userRefs = DB::table('user_has_refs')
            ->select('user_id', 'ref')
            ->whereNotNull('ref')
            ->get()
            ->keyBy('user_id');

        if ($userRefs->isEmpty()) {
            return collect();
        }

        $referenceCodes = $userRefs->pluck('ref')->filter()->values();
        $referralCounts = User::query()
            ->select('reference', DB::raw('COUNT(*) as total_refs'))
            ->whereIn('reference', $referenceCodes)
            ->groupBy('reference')
            ->pluck('total_refs', 'reference');

        $vipReferralCounts = $this->vipReferralCounts($referenceCodes);
        $qualified = collect();

        User::query()
            ->with('currentLevel')
            ->whereIn('id', $userRefs->keys())
            ->chunkById(200, function ($users) use ($levels, $userRefs, $referralCounts, $vipReferralCounts, $qualified) {
                foreach ($users as $user) {
                    $ref = $userRefs[$user->id]->ref ?? null;
                    if (!$ref) {
                        continue;
                    }

                    $reqUsers = (int) ($referralCounts[$ref] ?? 0);
                    $vipUsers = (int) ($vipReferralCounts[$ref] ?? 0);
                    $matchedLevel = $levels
                        ->filter(fn (Level $level) => $reqUsers >= (int) $level->req_users && $vipUsers >= (int) $level->vip_users)
                        ->sortByDesc('id')
                        ->first();

                    if (!$matchedLevel) {
                        continue;
                    }

                    if ((int) $user->current_level_id !== (int) $matchedLevel->id) {
                        LevelHistory::create([
                            'user_id' => $user->id,
                            'from_level_id' => $user->current_level_id,
                            'to_level_id' => $matchedLevel->id,
                        ]);

                        $user->forceFill(['current_level_id' => $matchedLevel->id])->save();
                        $user->setRelation('currentLevel', $matchedLevel);
                    }

                    if ((float) $matchedLevel->bonus > 0) {
                        $qualified->push($user);
                    }
                }
            });

        return $qualified->values();
    }

    private function vipReferralCounts($referenceCodes)
    {
        if (Schema::hasColumn('users', 'vip')) {
            return User::query()
                ->select('reference', DB::raw('COUNT(*) as total_vips'))
                ->whereIn('reference', $referenceCodes)
                ->where('vip', true)
                ->groupBy('reference')
                ->pluck('total_vips', 'reference');
        }

        if (!Schema::hasTable('vips') || !Schema::hasColumn('vips', 'refer')) {
            return collect();
        }

        return DB::table('vips')
            ->join('user_has_refs', 'vips.refer', '=', 'user_has_refs.user_id')
            ->whereIn('user_has_refs.ref', $referenceCodes)
            ->where('vips.status', 1)
            ->select('user_has_refs.ref', DB::raw('COUNT(*) as total_vips'))
            ->groupBy('user_has_refs.ref')
            ->pluck('total_vips', 'user_has_refs.ref');
    }

    private function buildStoreMap($commissions): array
    {
        if (!Schema::hasColumn('distribute_comissions', 'store_id')) {
            return [];
        }

        $items = $this->extractCollection($commissions);
        $storeIds = $items->pluck('store_id')->filter()->unique()->values();
        if ($storeIds->isEmpty()) {
            return [];
        }

        $stores = DB::table('stores')->whereIn('id', $storeIds)->get()->keyBy('id');
        $map = [];

        foreach ($items as $commission) {
            $map[$commission->id] = $stores[$commission->store_id] ?? null;
        }

        return $map;
    }

    private function extractCollection($items)
    {
        return method_exists($items, 'getCollection')
            ? $items->getCollection()
            : collect($items);
    }

    private function dateQueryValue(Request $request, string $key): string
    {
        $value = trim((string) $request->input($key, ''));

        if ($value === '') {
            return '';
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value, $matches) !== 1) {
            return '';
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $matches[0])->format('Y-m-d');
        } catch (\Throwable) {
            return '';
        }
    }

    private function approvedPartnershipUsers(string $modelClass)
    {
        $userIds = $modelClass::query()
            ->where('status', 1)
            ->pluck('applied_id')
            ->filter()
            ->unique()
            ->values();

        if ($userIds->isEmpty()) {
            return collect();
        }

        return User::query()
            ->whereIn('id', $userIds)
            ->get();
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
