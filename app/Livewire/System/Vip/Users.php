<?php

namespace App\Livewire\System\Vip;

use App\Models\vip;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

#[layout('layouts.app')]
class Users extends Component
{
    use WithPagination;
    #[URL]
    public $nav = 'Pending', $search = '', $sdate, $edate, $type, $validity;

    public function mount()
    {
        $this->validity = 'All';
        $this->type = 'All';
        $this->sdate = now()->subDay(30)->format('Y-m-d');
        $this->edate = now()->format('Y-m-d');
    }

    public function print()
    {
        $url = route('system.vip.print-summery', [
            'nav' => $this->nav,
            'search' => $this->search,
            'sdate' => $this->sdate,
            'edate' => $this->edate,
            'type' => $this->type,
            'validity' => $this->validity,
        ]);
        $this->dispatch('open-printable', ['url' => $url]);
    }

    public function render()
    {
        $st = false;
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

        $vip = $query->paginate(config('app.paginate'));

        if (isset($this->search) && !empty($this->search)) {
            $vip = vip::where('name', 'like', '%' . $this->search . '%')->orWhere('phone', 'like', '%' . $this->search . '%')->latest('id')->paginate(config('app.paginate'));
        }

        return view('livewire.system.vip.users', compact('vip'));
    }
}
