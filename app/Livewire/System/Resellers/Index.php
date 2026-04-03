<?php

namespace App\Livewire\System\Resellers;

use App\Models\reseller;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Reactive;
use App\Models\vendor;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    #[URL]
    public $filter = 'Active', $find;


    public $trs,$ars, $prs, $srs, $drs;

    public function mount()
    {
            
    }
    

    public function render()
    {


        if (!empty($this->search) && $this->filter == "*") {
            $resellers = reseller::where('shop_name_en', 'like', '%' . $this->find . '%')->latest('id')->paginate(200);
        } else {
            $resellers = reseller::where('shop_name_en', 'like', '%' . $this->find . '%')->where(['status' => $this->filter])->latest('id')->paginate(200);
        }

        // $selellers = $data->where(['status' => $this->filter])->pginate(200);
        return view('livewire.system.resellers.index', compact('resellers'))->layout('layouts.app');
    }
}
