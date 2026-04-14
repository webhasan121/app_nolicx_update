<?php

namespace App\Livewire\System\Store;

use App\Models\User;
use App\Models\Store;
use App\Models\Level;
use App\Models\Balance;
use App\Models\Withdraw;
use App\Models\TakeComissions;
use App\Models\DistributeComissions;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

#[layout('layouts.app')]
class Index extends Component {
    use WithPagination;

    public $widgets;
    public $store, $target, $targetStore;
    public $storeId;
    public $balance;
    public $table;
    public $columns1, $columns2;

    public $activeTab = 'commissions';

    #[URL]
    public $nav = 'donation', $type = 'withdraw';

    protected $listeners = ['refresh' => '$refresh'];

    public function mount() {
        $this->table = 'distribute_comissions';
        $this->columns1 = [ 'SL', 'Name of User', 'Store Info', 'Given', 'Range', 'Purpose', 'A/C' ];
        $this->columns2 = [ 'SL', 'Name of User', 'Store', 'Server Cost', 'Donation', 'Method', 'Status', 'Requested At', 'A/C' ];

        $year = now()->year;
        $month = now()->month;

        // $this->store = Store::where('year', $year)->where('month', $month)->first();
        $this->store = Store::firstOrCreate(
            [
                'year' => $year,
                'month' => $month
            ],
            [
                'total_balance' => 0,
                'current_balance' => 0,
                'distribute_balance' => 0,
                'generate' => false
            ]
        );

        $this->storeId = $this->store->id;
        $this->balance = $this->store->total_balance;

        $this->target = now()->subMonth();
        $this->targetStore = Store::where('year', $this->target->year)->where('month', $this->target->month)->first();

        $this->widgets = [
            [ 'label' => 'Total Earnings', 'value' => Store::sum('total_balance') ?? 0 ],
            [ 'label' => 'Final Remaining', 'value' => Balance::sum('current') ?? 0 ],
            [ 'label' => 'Monthly Total', 'value' => $this->balance ],
            [ 'label' => 'Current Balance', 'value' => $this->store->current_balance ],
            [ 'label' => 'Total Distributed', 'value' => $this->store->distribute_balance ?? 0 ],
            [ 'label' => 'Previous Total', 'value' => $this->targetStore->total_balance ?? 0 ],
            [ 'label' => 'Previous Balance', 'value' => $this->targetStore->current_balance ?? 0 ],
            [ 'label' => 'Last Distributed', 'value' => $this->targetStore->distribute_balance ?? 0 ],
        ];
    }

    // public function distribute() {
    //     $targetStore = $this->targetStore;

    //     if (!$targetStore) {
    //         $this->dispatch('error', 'Previous month store not found');
    //         return;
    //     }

    //     if ($targetStore->generate) {
    //         $this->dispatch('error', 'Distribution already generated');
    //         return;
    //     }

    //     $users = User::with([ 'currentLevel', 'developerAccess', 'managementAccess' ])->where('current_level_id', '!=', 1)->get();
    //     $totalDistributed = 0;
    //     $balance = $this->targetStore->total_balance;

    //     if ($balance <= 0) {
    //         $this->dispatch('error', 'No balance available for distribution');
    //         return;
    //     }

    //     DB::transaction(function () use ($users, &$totalDistributed, $targetStore, $balance) {

    //         foreach ($users as $user) {

    //             if ($user->currentLevel) {
    //                 $percent = $user->currentLevel->bonus;
    //                 $this->insertCommission($user, $percent, 'Store Commission', $targetStore->id, $balance);
    //                 $totalDistributed += ($percent / 100) * $balance;
    //             }

    //             if ($user->developerAccess) {
    //                 $percent = $user->developerAccess->commission;
    //                 $this->insertCommission($user, $percent, 'Developer Commission', $targetStore->id, $balance);
    //                 $totalDistributed += ($percent / 100) * $balance;
    //             }

    //             if ($user->managementAccess) {
    //                 $percent = $user->managementAccess->commission;
    //                 $this->insertCommission($user, $percent, 'Management Commission', $targetStore->id, $balance);
    //                 $totalDistributed += ($percent / 100) * $balance;
    //             }
    //         }

    //         $targetStore->decrement('current_balance', $totalDistributed);
    //         $targetStore->increment('distribute_balance', $totalDistributed);

    //         $targetStore->generate = true;
    //         $targetStore->save();
    //     });

    //     $this->dispatch('refresh');
    //     $this->dispatch('success', 'Previous month balance distributed successfully');
    // }

    public function distribute() {
        $targetStore = $this->targetStore;

        if (!$targetStore) {
            $this->dispatch('error', 'Previous month store not found');
            return;
        }

        if ($targetStore->generate) {
            $this->dispatch('error', 'Distribution already generated');
            return;
        }

        $balance = $targetStore->total_balance;

        if ($balance <= 0) {
            $this->dispatch('error', 'No balance available for distribution');
            return;
        }

        // 🎯 Pools
        $developerPool = $balance * 0.05;
        $managementPool = $balance * 0.10;
        $levelPool = $balance * 0.85;

        // 🔹 Separate queries (important!)
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

            // ✅ Developer Distribution
            if ($developers->count() > 0) {
                $share = $developerPool / $developers->count();

                foreach ($developers as $user) {
                    $this->insertCommission($user, null, 'Developer Commission', $targetStore->id, $share);
                    $totalDistributed += $share;
                }
            }

            // ✅ Management Distribution
            if ($managers->count() > 0) {
                $share = $managementPool / $managers->count();

                foreach ($managers as $user) {
                    $this->insertCommission($user, null, 'Management Commission', $targetStore->id, $share);
                    $totalDistributed += $share;
                }
            }

            // ✅ Level Distribution (ONLY current_level_id != 1)
            $totalBonus = $levelUsers->sum(fn($u) => $u->currentLevel->bonus ?? 0);

            if ($totalBonus > 0) {
                foreach ($levelUsers as $user) {
                    $percent = $user->currentLevel->bonus;
                    $share = ($percent / $totalBonus) * $levelPool;

                    $this->insertCommission($user, $percent, 'Store Commission', $targetStore->id, $share);
                    $totalDistributed += $share;
                }
            }

            // ✅ Update balances
            $targetStore->decrement('current_balance', $totalDistributed);
            $targetStore->increment('distribute_balance', $totalDistributed);

            $targetStore->generate = true;
            $targetStore->save();
        });

        $this->dispatch('refresh');
        $this->dispatch('success', 'Previous month balance distributed successfully');
    }

    protected function insertCommission($user, $percentage, $info, $storeId, $balance) {
        $amount = ($percentage / 100) * $balance;

        DB::table($this->table)->insert([
            'user_id' => $user->id,
            'store_id' => $storeId,
            'confirmed' => 1,
            'amount' => $amount,
            'range' => $percentage,
            'info' => $info,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function setTab($tab) {
        $this->activeTab = $tab;
    }

    public function render() {
        $strack = [];
        // switch ($this->nav) {
        //     case 'donation':
        //         $track = $track->donation();
        //         break;

        //     case 'cost':
        //         $track = $track->cost();
        //         break;
        // }
        // $strack = $track->whereDate('created_at', today())->orderBy('created_at', 'desc')->get();

        $tabs = [ 'commissions', 'withdrawals' ];
        $info = ['Store Commission', 'Developer Commission', 'Management Commission'];

        $commissions = DistributeComissions::whereIn('info', $info)->latest('id')->paginate(20);
        $withdrawals = Withdraw::where('type', 'debit')->latest('id')->paginate(20);

        return view('livewire.system.store.index', get_defined_vars());
    }
}
