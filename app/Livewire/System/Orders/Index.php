<?php

namespace App\Livewire\System\Orders;

use App\Models\Order;
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
    public $search = '', $date, $sd, $ed, $qf = 'id', $type, $status;
    public $pagn = 20, $pendingCount, $acceptCount, $totalAmount;

    public function mount()
    {
        // $this->date = 'today';
        $this->date = 'today';
        $this->pagn = config('app.paginate');
        $this->sd = now()->format('Y-m-d');
        $this->ed = now()->format('Y-m-d');
    }

    public function updated($prop)
    {
        // $this->dispatch('refresh');
        if ( $prop == 'date' &&  $this->date == 'between') {
            $this->sd = now()->format('Y-m-d');
            $this->ed = now()->format('Y-m-d');
        }
    }

    public function print()
    {
        $url = route(
            'system.orders.sprint',
            [
                'date' => $this->date,
                'sd' => $this->sd,
                'ed' => $this->ed,
                'search' => $this->search,
                'type' => $this->type,
                'qf' => $this->qf,
                'status' => $this->status
            ]
        );
        $this->dispatch('open-printable', ['url' => $url]);
    }


    public function render()
    {
        $orders = [];

        $query = Order::query();
        if ($this->search) {
            $query->where([$this->qf => $this->search]);
        }

        if ($this->date) {
            switch ($this->date) {
                case 'today':
                    $this->sd = '';
                    $this->ed = '';;
                    $query->whereDate('created_at', today());
                    break;

                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;


                case 'between':
                    
                    if (!empty($this->sd) && !empty($this->ed)) {
                        $query->whereBetween('created_at', [$this->sd, carbon::parse($this->ed)->endofDay()]);
                    }
                    break;
            }

            // $query->whereDate('created_at', $this->date);
        }

        if ($this->type) {
            $query->where(['user_type' => $this->type]);
        }

        if ($this->status) {
            $query->where(['status' => $this->status]);
        }
        $orders = $query->orderBy('id', 'desc')->paginate($this->pagn);
        $or = Order::all();
        return view('livewire.system.orders.index', compact('orders', 'or'));
    }

    public function delete($id)
    {
        // dd($id);
        Order::destroy($id);
        $this->dispatch('refresh');
    }
}
