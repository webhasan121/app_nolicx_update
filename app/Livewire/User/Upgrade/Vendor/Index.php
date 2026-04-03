<?php

namespace App\Livewire\User\Upgrade\Vendor;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Reactive;


class Index extends Component
{
    #[URL] 
    public $upgrade = 'vendor';

    /**
     * modal 
     */
    public $isShowCreateModal;

    public function render()
    {
        return view('livewire.user.upgrade.vendor.index')->layout('layouts.user.dash.userDash');
    }
}
