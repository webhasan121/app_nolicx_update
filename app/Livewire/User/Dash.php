<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\user_has_refs;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;

#[layout('layouts.user.dash.userDash')]
class Dash extends Component
{

    public $newRef;
    public function checkRef()
    {
        if (empty($this->newRef)) {
            $this->dispatch('info', 'Give a valid ref code');
        }

        if (config('app.comission')) {
            $reffArray = user_has_refs::all('ref', 'user_id');
            $reference = $this->newRef;
            $reff = $reffArray->where('ref', $reference)->first();
            if (auth()->user()->created_at->diffInHours(Carbon::now()) > 72 || auth()->user()->reference_accepted_at) {
                // return redirect()->back()->with('warning', 'Time up.')->withInput();
                $this->dispatch('info', 'Time Up. You can not update your ref');
            } else {

                if (auth()->user()->created_at->diffInHours(Carbon::now()) < 72 && $reff && $reff->owner->id != auth()->user()->id && $reff->owner->id != request()->user()->id) {
                    auth()->user()->reference = $reference;
                    auth()->user()->reference_accepted_at = today();
                    auth()->user()->save();

                    // return redirect()->back()->with('success', 'Referred Accepted !')->withInput();
                    $this->dispatch('refresh');
                    $this->dispatch('success', 'Ref Accepted');
                } else {
                    // return redirect()->back()->with('warning', 'Error, Try again')->withInput();
                    $this->dispatch('warning', 'Try Again');
                }
            }
        }

        // if (auth()->user()->reference_accepted_at || !auth()->user()->created_at->diffInHours(\Carbon\Carbon::now()) > 72) {
        // }
    }

    public function mount()
    {
        if (auth()->user()->reference_accepted_at || auth()->user()->created_at->diffInHours(\Carbon\Carbon::now()) > 72) {
            $this->newRef = auth()->user()->reference;
        }
    }


    public function render()
    {
        return view('livewire.user.dash');
    }
}
