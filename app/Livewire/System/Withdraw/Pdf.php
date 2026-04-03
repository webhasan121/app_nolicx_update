<?php

namespace App\Livewire\System\Withdraw;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
// use Spatie\LaravelPdf\Facades\Pdf as laravelPdf;
// use Barryvdh\DomPDF\PDF as domPdf;
use App\Models\Withdraw;
use Illuminate\Support\Carbon;

#[layout('layouts.print')]
class Pdf extends Component
{
    #[URL]
    public $sdate, $edate, $fst = 'Accept', $q, $where;

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
        return view('livewire.system.withdraw.pdf', ['withdraws' => $withdraw]);
    }
}
