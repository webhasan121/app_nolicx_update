<?php

namespace App\Livewire\System\Users;

use App\Models\User;
use App\Models\user_has_refs;
use App\Models\vendor;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[layout('layouts.app')]
class Edit extends Component
{
    #[URL]
    public $id;

    public $users, $user, $cref, $rechargeAmount;

    public function mount()
    {
        $this->getData();
    }


    public function save()
    {
        $this->user->update([
            'name' => $this->users['name'],
            'email' => $this->users['email'],
        ]);

        if ($this->cref) {
            $reffArray = user_has_refs::all('ref', 'user_id');
            $reference = $this->cref;
            $reff = $reffArray->where('ref', $reference)->first();

            if ($reff) {
                # code...
                $this->user->reference_accepted_at = Carbon::now();
                $this->user->reference = $this->cref;
                $this->user->save();
            }
        }

        $this->reset(['cref']);
        // $this->getData();
        $this->dispatch('success', "Updated!");
    }

    public function rechargeUser()
    {
        if (!empty($this->rechargeAmount)) {
            # code...
            $this->dispatch('open-modal', 'confirmRechargeModal');
        }
    }

    public function confirmRecharge()
    {
        $this->user->increment('coin', $this->rechargeAmount);
        $this->reset(['rechargeAmount']);
        $this->dispatch('close-modal', 'confirmRechargeModal');
        $this->dispatch('success', "User recharged successfully!");
    }

    public function confirmRefund()
    {
        $this->user->decrement('coin', $this->rechargeAmount);
        $this->reset(['rechargeAmount']);
        $this->dispatch('close-modal', 'confirmRechargeModal');
        $this->dispatch('success', "User refunded successfully!");
    }


    public function getData()
    {
        $this->user = User::find($this->id);
        $this->users = $this->user->toArray();
        // dd($this->user);
    }


    public function render()
    {
        return view('livewire.system.users.edit');
    }
}
