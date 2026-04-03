<?php

namespace App\Livewire\System\Settings\Branch;

use App\Models\Branch;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;

#[layout('layouts.app')]
class Create extends Component {
    public $name;
    public $email;
    public $phone;
    public $slug;
    public $address;
    public $type;

    public function rules() {
        return [
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'phone'   => ['required', 'string', 'max:20'],
            'slug'    => ['required', 'string', 'unique:branches,slug'],
            'address' => ['nullable', 'string'],
        ];
    }

    public function store() {
        $this->validate();

        Branch::create([
            'name'    => $this->name,
            'email'   => $this->email,
            'phone'   => $this->phone,
            'slug'    => $this->slug ?? Str::slug($this->name),
            'address' => $this->address,
            'type'    => 'Other',
        ]);

        $this->reset();

        $this->dispatch('success', message: 'Branch created successfully!');
    }

    public function render() {
        return view('livewire.system.settings.branch.create');
    }
}
