<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\User;
use App\Models\Level;
use App\Models\user_has_refs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;

#[layout('layouts.user.dash.userDash')]
class Dash extends Component {
    public $current, $upcoming, $widgets;
    public $newRef;
    public function checkRef() {
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

    public function mount() {
        $user = Auth::user();

        if (auth()->user()->reference_accepted_at || auth()->user()->created_at->diffInHours(\Carbon\Carbon::now()) > 72) {
            $this->newRef = auth()->user()->reference;
        }



        $this->current = [
            'name' => $user->currentLevel->name,
            // 'req_users' => $user->myRef->count() ?? 0,
            'req_users' => User::where('reference', $user->myRef->ref)->count() ?? 0,
            'vip_users' => $user->getMyvipRef->count() ?? 0,
            'rewards' => null
        ];

        $level = Level::where('id', ($user->current_level_id + 1))->first();

        if ($level) {
            $this->upcoming = [
                'name'  => $level->name,
                'req_users' => $level->req_users,
                'vip_users' => $level->vip_users,
                'rewards' => $level->rewards
            ];
        } else {
            $this->upcoming = [
                'name' => 'Max',
                'req_users' => null,
                'vip_users' => null,
                'rewards' => null
            ];
        }


        $this->widgets = [
            [
                'name' => $this->current['name'], 'data' => [
                    'req_users' => $this->current['req_users'],
                    'vip_users' => $this->current['vip_users'],
                ], 'rewards' => $this->current['rewards'],
            ],
            [
                'name' => $this->upcoming['name'], 'data' => [
                    'req_users' => $this->upcoming['req_users'],
                    'vip_users' => $this->upcoming['vip_users'],
                ], 'rewards' => $this->upcoming['rewards'],
            ],
        ];
    }


    public function render()
    {
        return view('livewire.user.dash');
    }
}
