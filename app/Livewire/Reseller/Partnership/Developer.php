<?php

namespace App\Livewire\Reseller\Partnership;

use App\Models\DeveloperAccess;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[layout('layouts.app')]
class Developer extends Component {
    public $name;
    public $email;
    public $phone;
    public $message;

    public $hasApplied = false;
    public $developerRequest = null;

    public function mount(){
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;

        $this->developerRequest = DeveloperAccess::where('applied_id', $user->id)->first();

        if ($this->developerRequest) {
            $this->hasApplied = true;
        }
    }

    protected $rules = [
        'message' => 'nullable|max:500',
    ];

    public function submit() {
        if ($this->hasApplied) {
            $this->dispatch('error', 'You already applied!');
            return;
        }

        $this->validate();

        $applied = DeveloperAccess::create([
            'applied_id' => auth()->id(),
            'message' => $this->message,
            'status' => null,
        ]);

        $this->dispatch('success', 'Successfully applied!!!');
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.reseller.partnership.developer');
    }
}
