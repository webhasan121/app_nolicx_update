<?php

namespace App\Livewire\System\Deposit;

use App\Models\userDeposit;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Carbon;


#[layout('layouts.app')]
class Index extends Component
{
    use WithPagination;
    #[URL]
    public $status = false, $sdate = '', $edate = '', $search;

    public function mount()
    {
        $this->sdate = now()->subday(30)->format('Y-m-d');
        $this->edate = today()->format('Y-m-d');
    }

    public function confirmDeposit($id)
    {
        $dp = userDeposit::findOrFail($id);
        $dp->user?->increment('coin', $dp->amount);
        $dp->confirmed = true;
        $dp->save();
        $this->dispatch('success', "User recharged successfully!");
        // dd($dp);
        $this->dispatch('refresh');
    }
    public function denayDeposit($id)
    {
        $dp = userDeposit::destroy($id);
        $this->dispatch('refresh');
        // $dp = userDeposit::findOrFail($id);
    }

    public function print()
    {
        $url = route('system.deposit.print-summery', [
            'status' => $this->status,
            'sdate' => $this->sdate,
            'edate' => $this->edate,
            'search' => $this->search,
        ]);
        $this->dispatch('open-printable', [
            'url' => $url,
        ]);
    }

    public function render()
    {
        $qry = userDeposit::query();
        $qry->where(['confirmed' => $this->status]);

        $qry->whereBetween('created_at', [$this->sdate, Carbon::parse($this->edate)->endOfDay()]);

        $history = $qry->orderBy('id', 'desc')->paginate(config('app.config'));
        return view('livewire.system.deposit.index', compact('history'));
    }
}
