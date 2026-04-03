<?php

namespace App\Livewire\System\Partnership;

use App\Models\DeveloperAccess;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[layout('layouts.app')]
class Developer extends Component {

    public function accept($id) {
        DeveloperAccess::where('id', $id)->update([
            'status'       => 1,
            'response_by' => Auth::id(),
        ]);

        $this->dispatch('refresh');
        $this->dispatch('success', 'Application approved');
    }

    public function reject($id) {
        DeveloperAccess::where('id', $id)->update([
            'status'       => 0,
            'response_by' => Auth::id(),
        ]);

        $this->dispatch('refresh');
        $this->dispatch('success', 'Application rejected');
    }


    public function delete($id) {
        DeveloperAccess::where('id', $id)->delete();

        $this->dispatch('refresh');
        $this->dispatch('success', 'Application deleted');
    }

    public function render() {
        $applications = DeveloperAccess::latest('id')->paginate(20);
        return view('livewire.system.partnership.developer', get_defined_vars());
    }
}
