<?php

namespace App\Livewire\System\Deposit;

use Livewire\Component;
use Illuminate\Support\Carbon;
use App\Models\userDeposit;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

#[Layout('layouts.print')]
class PrintSummery extends Component
{
    use WithPagination;
    #[URL]
    public $status = false, $sdate = '', $edate = '', $search;
    
    public function render()
    {
        $qry = userDeposit::query();
        $qry->where(['confirmed' => $this->status]);

        $qry->whereBetween('created_at', [$this->sdate, Carbon::parse($this->edate)->endOfDay()]);

        $history = $qry->orderBy('id', 'desc')->paginate(config('app.config'));
        return view('livewire.system.deposit.print-summery', compact('history'));
    }
}
