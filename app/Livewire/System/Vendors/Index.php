<?php

namespace App\Livewire\System\Vendors;

use Livewire\Component;
use App\Models\vendor;
use Livewire\Attributes\Url;
use Livewire\Attributes\Reactive;


class Index extends Component
{

    /**
     * URL data
     */
    #[URL]
    public $filter = "Active";

    #[URL]
    public $find, $filterSearch;

    /**
     * component data
     */
    public $vendors,$tvd, $avd, $pvd, $svd, $dvd;

    /**mount */
    public function mount()
    {
        $this->getData();
        $this->tvd = vendor::query()->count();
        $this->avd = vendor::query()->active()->count();
        $this->pvd = vendor::query()->pending()->count();
        $this->dvd = vendor::query()->disabled()->count();
        $this->svd = vendor::query()->suspended()->count();
    }

    public function getData()
    {

        $this->vendors = vendor::where(['status' => $this->filter])->orderBy('id', 'desc')->get();
    }

    /**
     * search vendor 
     */
    public function search()
    {
        if ($this->filter == "*") {
            $this->vendors = vendor::where('shop_name_en', 'like', '%' . $this->find . '%')->get();
        } else {
            $this->vendors = vendor::where('shop_name_en', 'like', '%' . $this->find . '%')->where(['status' => $this->filter])->get();
        }
    }

    public function render()
    {
        $vendors = vendor::where(['status' => 'Pending'])->orderBy('id', 'desc')->get();
        return view('livewire.system.vendors.index', ['vendors' => $vendors])->layout('layouts.app');
    }
}
