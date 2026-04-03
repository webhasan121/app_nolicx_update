<?php

namespace App\Livewire\System\Partnership;

use App\Models\ManagementAccess;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[layout('layouts.app')]
class Management extends Component {

    public function accept($id) {
        ManagementAccess::where('id', $id)->update([
            'status'       => 1,
            'response_by' => Auth::id(),
        ]);

        $this->dispatch('refresh');
        $this->dispatch('success', 'Application approved');
    }

    public function reject($id) {
        ManagementAccess::where('id', $id)->update([
            'status'       => 0,
            'response_by' => Auth::id(),
        ]);

        $this->dispatch('refresh');
        $this->dispatch('success', 'Application rejected');
    }


    public function delete($id) {
        ManagementAccess::where('id', $id)->delete();

        $this->dispatch('refresh');
        $this->dispatch('success', 'Application deleted');
    }

    public function render()
    {
        $applications = ManagementAccess::latest('id')->paginate(20);
        return view('livewire.system.partnership.management', get_defined_vars());
    }
}
