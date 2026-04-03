<?php

namespace App\Livewire\User\Wallet;

use App\Models\user_task;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[layout('layouts.user.dash.userDash')]
class Task extends Component
{
    public function render()
    {
        $tasks = user_task::where(['user_id' => Auth::id()])->orderBy('id', 'desc')->get();
        // dd($tasks);
        return view('livewire.user.wallet.task', compact('tasks'));
    }
}
