<?php

namespace App\Livewire\System\EarnBySell;

use App\Models\CartOrder;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

#[layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[url]
    public $nav = 'sold', $fd, $lastDate, $user_type = 'user';
    public $totalSell, $tp, $tn, $tpr, $tprr, $shop, $ushop;

    public function mount()
    {
        $this->lastDate = now();
        $pfq = CartOrder::query()->whereBetween('created_at', [$this->fd, Carbon::parse($this->lastDate)->endOfDay()]);

        if ($this->user_type != 'all') {
            $pfq->where('user_type', $this->user_type);
        }

        if ($this->nav == 'sold') {
            $pfq->where('status', 'Confirmed');
        } elseif ($this->nav == 'selling') {
            $pfq->whereIn('status', ['Pending', 'Picked', 'Delivery', 'Delivered']);
        }

        $this->totalSell = $pfq->sum('price');
        $this->tn = $pfq->sum('buying_price');
        $this->tp = $this->totalSell - $this->tn;
        $this->shop = $pfq->count();
        // $this->ushop = $pfq->groupBy('product_id')->select('product_id')->count();
        $this->tpr = CartOrder::where('user_type', 'reseller')->groupBy('product_id')->select('product_id')->get()->each(function ($q) {
            return !$q->product->isResel();
        })->count();
        $this->tprr = CartOrder::where('user_type', 'user')->groupBy('product_id')->select('product_id')->get()->each(function ($q) {
            return $q->product->isResel();
        })->count();
    }

    public function render()
    {
        $q = CartOrder::query();

        $q->where(function ($q) {
            if ($this->user_type != 'all') {
                $q->where('user_type', $this->user_type);
            }
            if ($this->nav == 'sold') {
                $q->where('status', 'Confirmed');
            } elseif ($this->nav == 'selling') {
                $q->whereIn('status', ['Pending', 'Picked', 'Delivery', 'Delivered']);
            }
        })->whereBetween('created_at', [$this->fd, Carbon::parse($this->lastDate)->endOfDay()]);

        $products = $q->orderBy('id', 'desc')->paginate(20);
        return view(
            'livewire.system.earn-by-sell.index',
            [
                'products' => $products
            ]
        );
    }
}
