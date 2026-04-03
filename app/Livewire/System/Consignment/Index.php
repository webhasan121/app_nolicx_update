<?php

namespace App\Livewire\System\Consignment;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\cod;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;

#[layout('layouts.app')]
class Index extends Component
{
    #[URL]
    public $type = 'Pending', $sdate, $edate;

    use WithPagination;

    public function mount()
    {
        $this->sdate = now()->format('Y-m-d');
        $this->edate = now()->format('Y-m-d');
    }
    public function render()
    {
        $query = cod::query();

        if ($this->type != 'All') {
            # code...
            $query->where(['status' => $this->type]);
        }
        $query->whereBetween('created_at', [$this->sdate, carbon::parse($this->edate)->endOfDay()]);

        $cod = $query->orderBy('id', 'desc')->paginate(30);
        $widgets = $this->widgets();
        return view('livewire.system.consignment.index', compact('cod', 'widgets'));
    }

    private function widgets() {
        return [
            [ 'title' => 'Completed', 'value' => $this->status('Completed') ],
            [ 'title' => 'Pending', 'value' => $this->status('Pending') ],
            [ 'title' => 'Received', 'value' => $this->status('Received') ],
            [ 'title' => 'Returned', 'value' => $this->status('Returned') ],
        ];
    }

    private function status($status = 'Pending') {
        $query = cod::query();
        $status = $query->where('status', $status)->count();
        return $status;
    }
}
