<?php

namespace App\Livewire\System\Vendors\Vendor;

use App\Models\vendor;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Reactive;


class Documents extends Component
{

    #[URL]
    public $id;

    /**
     * component data
     */
    public $vendor, $deatline;

    // listeners for refresh event 
    protected $listener = ['$refresh'];


    /**
     * mount
     */
    public function mount()
    {
        $this->getData();
        // dd($this->vendor);
    }


    /** get dat */
    public function getData()
    {
        $this->vendor = vendor::find($this->id);
    }


    /**
     * update the deatline
     */
    public function updateDeatline()
    {
        $this->vendor->documents->update(['deatline' => $this->deatline]);
        $this->dispatch('success', 'Updated');
    }


    public function render()
    {
        return view('livewire.system.vendors.vendor.documents')->layout('layouts.app');
    }
}
