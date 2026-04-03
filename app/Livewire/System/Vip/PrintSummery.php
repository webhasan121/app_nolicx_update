<?php

namespace App\Livewire\System\Vip;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\vip;
use Illuminate\Support\Carbon;


class PrintSummery extends Component
{
    #[URL]
    public $nav = 'Pending', $search = '', $sdate, $edate, $type, $validity;
    public function render()
    {
        $st = false;
        $vip = '';
        $query = vip::query()->whereBetween('created_at', [$this->sdate, Carbon::parse($this->edate)->endOfDay()]);
        if ($this->nav !== 'Pending') {
            $st = true;
        } else {
            $st = false;
        }

        if ($this->nav == 'Trash') {
            $query->onlyTrashed();
        } elseif ($this->nav != 'Trash') {
            $query->where(function ($q) use ($st) {
                $q->where(['status' => $st]);
            });
        }

        /**
         * type monthly or daily
         */
        if ($this->type != 'All') {
            $query->where(['task_type' => $this->type]);
        }


        /**
         * validity
         */
        if ($this->validity != 'All') {
            $query->whereDate('valid_till', $this->validity == 'valid' ? ">" : "<", now()->format('Y-m-d'));
        }


        if (isset($this->search) && !empty($this->search)) {
            $vip = vip::where('name', 'like', '%' . $this->search . '%')->orWhere('phone', 'like', '%' . $this->search . '%')->paginate(config('app.paginate'));
        }

        $vip = $query->paginate(config('app.paginate'));
        return view('livewire.system.vip.print-summery', compact('vip'));
    }
}
