<?php

namespace App\Livewire\System\Vip\Package;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Packages;
use Livewire\Attributes\Url;
use Spatie\LaravelPackageTools\Package;

#[layout('layouts.app')]
class Index extends Component
{
    #[URL]
    public $nav = 'Active';
    public $packages;

    public function mount()
    {
        // get all packages
        $this->packages = Packages::all();

        if ($this->nav == 'Trash') {
            $this->packages = Packages::onlyTrashed()->get();
        }
    }

    public function trash($id)
    {
        Packages::destroy($id);
        $this->dispatch('success', 'Packages now in Trash');
    }
    public function restore($id)
    {
        Packages::find($id)->restore();
        $this->dispatch('success', 'Packages restored');
    }


    public function render()
    {
        return view('livewire.system.vip.package.index');
    }
}
