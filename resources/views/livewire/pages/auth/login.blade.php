<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;


new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;
    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        if (Auth::user()->hasRole('system')) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        }else{
            $this->redirectIntended(default: route('user.dash', absolute: false), navigate: true);
        }
    }
};

?>

<div class="w-full p-3 bg-white" style="max-width: 400px">
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        {{-- {{$title}} --}}
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="form.email" id="email" class="block w-full mt-1" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        {{-- add hide show password toggle --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <style>
                .pasDiv{
                    position: relative;
                    width: 100%!important;
                }
                .showOrHide{

                    position: absolute;
                    top: 13px;
                    right: 10px;
                    /* transform: translateY(-50%); */
                    /* background-color: #3498db; */
                    border-radius: 50%;
                    cursor: pointer;
                }
            </style>
            <div class="pasDiv" >
                <x-text-input wire:model="form.password" id="password" class="block w-full mt-1"
                    type="password"
                    name="password"
                    required autocomplete="current-password" />

                <div onclick="showOrHide(this, '#password')" class="showOrHide">show</div>
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>



        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500" name="remember">
                <span class="text-sm text-gray-600 ms-2">{{ __('Remember me') }}</span>
            </label>
        </div>

        @if (Route::has('register'))
            <hr>
            @if (Route::has('password.request'))
            <div class="text-center">
                <a class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            </div>
            @endif
        <hr>
        @endif
        <div class="flex items-center justify-between mt-4">
            <a class="text-center" href="/register" >Register</a>

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
    <script>
        function showOrHide(div, input) {
            if (div.textContent === 'show') {
                div.textContent = 'hide';
                document.querySelector(input).type = 'text';
            } else {
                div.textContent = 'show';
                document.querySelector(input).type = 'password';
            }
        }
    </script>

</div>
