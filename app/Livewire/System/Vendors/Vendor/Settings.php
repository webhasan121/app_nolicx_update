<?php

namespace App\Livewire\System\Vendors\Vendor;

use App\Models\vendor;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Attributes\Url;
use Spatie\Permission\Models\Permission;

class Settings extends Component
{
    #[URL]
    public $id;

    protected $data;

    /**
     * component data
     */
    public $vendor, $permissions, $varray;

    // listeners for refresh event 
    protected $listener = ['$refresh'];

    /**
     * mount 
     */
    public function mount()
    {
        $this->getDate();
    }

    /**get data */
    public function getDate()
    {
        $this->data = vendor::find($this->id);
        $this->vendor = $this->data;
        $this->varray = $this->data->toArray();
        $this->permissions = Permission::all();
        // dd($this->varray);
    }

    /**
     * update vendor
     */
    public function update()
    {
        vendor::find($this->id)->update($this->varray);
        // Session()->flash('success', 'Vendor Updated!');
        $this->dispatch('success', 'Updated');
    }


    public function render()
    {
        return view('livewire.system.vendors.vendor.settings')->layout('layouts.app');
    }
}
