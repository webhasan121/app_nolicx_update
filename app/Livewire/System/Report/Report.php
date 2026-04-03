<?php

namespace App\Livewire\System\Report;

use App\Models\Order;
use App\Models\Product;
use App\Models\userDeposit;
use App\Models\vip;
use App\Models\Withdraw;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Illuminate\Support\Carbon;

#[Layout('layouts.print')]
class Report extends Component
{
    #[URL]
    public $nav, $sdate, $edate, $id;
    // private $model;

    // public function mount()
    // {
    //     switch ($this->nav) {
    //         case 'Deposit':
    //             $this->model = userDeposit::class;
    //             break;

    //         case 'Withdraw':
    //             $this->model = Withdraw::class;
    //             break;

    //         case "Sells":
    //             $this->model = Order::class;
    //             break;

    //         case "Vip":
    //             $this->model = vip::class;
    //             break;

    //         case 'Product':
    //             $this->model = Product::class;
    //             break;

    //         default:
    //             $this->model = userDeposit::class;
    //             break;
    //     }
    // }
    public function render()
    {
        // $qry = $this->model::query();
        // $qry->whereBetween('created_at', [$this->sdate, Carbon::parse($this->edate)->endOfDay()]);

        return view('livewire.system.report.report');
    }
}
