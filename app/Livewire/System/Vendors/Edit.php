<?php

namespace App\Livewire\System\Vendors;

use App\Models\vendor;
use Livewire\Component;
use Livewire\Attributes\Url;

class Edit extends Component
{

    /**
     * URL paramiter
     */
    #[URL]
    public $id;

    /**
     * components data
     */
    private $vendor;


    /**
     * mount method
     */
    public function mount()
    {
        $this->getData();
    }


    /**
     * get all related data from database
     */
    public function getData()
    {
        $this->vendor = vendor::find($this->id);
    }


    public function render()
    {
        return view('livewire.system.vendors.edit', ['vendor' => $this->vendor])->layout('layouts.app');
    }
}
