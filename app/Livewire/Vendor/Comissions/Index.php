<?php

namespace App\Livewire\Vendor\Comissions;

use App\Models\ResellerResellProfits;
use App\Models\TakeComissions;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;


#[layout('layouts.app')]
class Index extends Component
{
    #[URL]
    public $query_for, $qry, $date;

    use WithPagination;


    public function mount()
    {
        $this->query_for = 'user_id';
        $this->qry = Auth::id();
        $this->date = today();
    }


    public function render()
    {
        $takes = TakeComissions::where([$this->query_for => $this->qry, 'confirmed' => true])->whereDate('created_at', $this->date)->latest('id')->paginate(100);
        $gives = ResellerResellProfits::where('to', Auth::id())->latest('id')->paginate(100);

        return view('livewire.vendor.comissions.index');
    }
}
