<?php

namespace App\Livewire\System\Partnership;

use Livewire\Component;
use App\Models\DeveloperAccess;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[layout('layouts.app')]
class Index extends Component {
    use WithPagination;

    public function render() {
        $applications = DeveloperAccess::latest('id')->paginate(20);
        return view('livewire.system.partnership.index', get_defined_vars());
    }
}
