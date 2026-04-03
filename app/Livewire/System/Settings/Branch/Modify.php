<?php

namespace App\Livewire\System\Settings\Branch;

use App\Models\Branch;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;

#[layout('layouts.app')]
class Modify extends Component {
    public Branch $branch;

    public $name;
    public $email;
    public $phone;
    public $slug;
    public $address;
    public $type;

    /**
     * Load branch data
     */
    public function mount(Branch $branch) {
        $this->branch  = $branch;
        $this->name    = $branch->name;
        $this->email   = $branch->email;
        $this->phone   = $branch->phone;
        $this->slug    = $branch->slug;
        $this->address = $branch->address;
    }

    public function rules() {
        return [
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'phone'   => ['required', 'string', 'max:20'],
            'slug'    => ['required', 'string', 'unique:branches,slug,' . $this->branch->id],
            'address' => ['nullable', 'string'],
        ];
    }

    public function modify() {
        $this->validate();

        $this->branch->update([
            'name'    => $this->name,
            'email'   => $this->email,
            'phone'   => $this->phone,
            'slug'    => $this->slug ?: Str::slug($this->name),
            'address' => $this->address,
        ]);

        $this->dispatch('success', message: 'Branch updated successfully!');
    }

    public function render()
    {
        return view('livewire.system.settings.branch.modify');
    }
}
