<?php

namespace App\Livewire\System\Withdraw;

use App\Models\Withdraw;
use Carbon\Carbon as CarbonCarbon;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

#[layout('layouts.app')]
class Index extends Component
{
    use WithPagination;
    #[URL]
    public $where, $q, $fst = 'Pending', $sdate, $edate;
    public $total, $pending, $reject, $paid;

    public function getWithdraw()
    {
        $q = Withdraw::query();
        $this->total = $q->count();
        $this->pending = withdraw::pending()->count();
        $this->paid = withdraw::accepted()->count();
        $this->reject = withdraw::rejected()->count();
        $this->sdate = now()->subday(30)->format('Y-m-d');
        $this->edate = now()->format('Y-m-d');
    }

    public function print()
    {
        // Build query string for customization options
        $url = route('system.withdraw.print', ['where' => $this->where, 'fst' => $this->fst, 'sdate' => $this->sdate, 'edate' => $this->edate]);
        $this->dispatch('open-printable', ['url' => $url]);
    }

    public function filter()
    {
        $this->withdraws();
    }
    private function withdraws()
    {
        $qry = Withdraw::query();
        if ($this->fst == 'Reject') {
            $qry->rejected();
        };

        if ($this->fst != 'Reject') {
            $sts = $this->fst == 'Accept' ? 1 : 0;
            $qry->where(['status' => $sts]);
        }

        if ($this->where == 'query') {
            $qry->whereHas('user', function ($query) {
                $query->where('name', 'like', '%' . $this->q . '%');
            });
        } elseif ($this->where == 'find') {
            $qry->where('id', $this->q);
        }

        return $qry->whereBetween('created_at', [$this->sdate, Carbon::parse($this->edate)->endOfDay()]);
    }

    public function render()
    {


        $withdraw = $this->withdraws()->orderBy('id', 'desc')->paginate(config('app.pagination'));
        // dd($withdraw);
        return view('livewire.system.withdraw.index', compact('withdraw'));
    }
}
