<?php

namespace App\Livewire\System\Comissions;

use App\Models\TakeComissions;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Illuminate\Support\Carbon;

#[layout('layouts.print')]
class Takes extends Component
{
    #[URL]
    public $where, $to, $from, $wid, $confirm;

    // public function check() {}
    private function queryResult()
    {
        $q = TakeComissions::query();


        return $q->when($this->where == 'user_id', function ($query) {
            return $query->where('user_id', $this->wid);
        })
            ->when($this->where == 'product_id', function ($query) {
                return $query->where('product_id', $this->wid);
            })
            ->when($this->where == 'order_id', function ($query) {
                return $query->where('order_id', $this->wid);
            })
            ->when($this->from, function ($query) {
                return $query->whereDate('created_at', '>=', $this->from);
            })
            ->when($this->to, function ($query) {
                return $query->whereDate('created_at', '<=', $this->to);
            })
            ->when($this->wid && $this->where == '', function ($query) {
                return $query->where('id', $this->wid);
            });

        if ($this->confirm != 'All') {
            $q->where(['confirmed' => $this->confirm == 'true' ? 1 : 0]);
        }
    }

    public function render()
    {
        return view('livewire.system.comissions.takes', ['comissions' => $this->queryResult()->get()]);
    }
}
