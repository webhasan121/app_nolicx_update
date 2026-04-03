<?php

namespace App\Livewire\System\Comissions;

use App\Models\DistributeComissions;
use App\Models\TakeComissions;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;

#[layout('layouts.app')]
class Index extends Component
{
    use WithPagination;
    #[URL]
    public $seller = 0, $product = 0, $order = 0, $from, $to, $where, $wid, $confirm;


    // PDF customization options
    public $paper = 'A4';
    public $orientation = 'portrait'; // portrait or landscape
    public $margins = '10mm';
    public $fullWidth = true;


    public function openPrintable()
    {
        $url =  route('system.comissions.takes', ['confirm' => $this->confirm, 'where' => $this->where, 'from' => $this->from, 'to' => $this->to, 'wid' => $this->wid]);

        $this->dispatch('open-printable', ['url' => $url]);
    }

    public function mount()
    {
        $this->from = now()->format('Y-m-d');
        $this->to = now()->format('Y-m-d');
    }

    // public function print()
    // {
    //     $url =  route('system.comissions.takes', ['confirm' => $this->confirm, 'where' => $this->where, 'from' => $this->from, 'to' => $this->to, 'wid' => $this->wid], true);
    //     return redirect()->to($url);
    // }

    private function queryResult()
    {
        $q = TakeComissions::query();

        if ($this->confirm != 'All') {
            $q->where(['confirmed' => $this->confirm == 'true' ? 1 : 0]);
        }
        
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
    }

    public function render()
    {

        return view(
            'livewire.system.comissions.index',
            [
                'comissions' => $this->queryResult()->latest('id')->paginate(20)
            ]
        );
    }
}
