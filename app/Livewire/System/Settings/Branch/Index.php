<?php

namespace App\Livewire\System\Settings\Branch;

use App\Models\Branch;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[layout('layouts.app')]
class Index extends Component {
    public function delete(int $id) {
        $branch = Branch::findOrFail($id);
        $branch->delete();

        $this->dispatch('success', message: 'Branch deleted successfully!');
    }

    public function render() {
        $branches = Branch::latest('id')->get();
        return view('livewire.system.settings.branch.index', get_defined_vars());
    }
}
