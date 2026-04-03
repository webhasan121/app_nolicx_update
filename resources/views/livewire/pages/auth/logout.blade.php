<?php

use App\Livewire\Actions\Logout;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.user.dash.userDash')] class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <x-dashboard.container>
        <x-dashboard.section>
            <div class="alert alert-danger">
                <div class="mb-3 text-md">
                    Are you sure to logout from your current session.
                </div>

                <x-danger-button wire:click="logout">
                    {{ __('Log Out') }}
                </x-danger-button>
            </div>
        </x-dashboard.section>
    </x-dashboard.container>
</div>
