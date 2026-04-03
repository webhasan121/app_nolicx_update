<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;
use function Livewire\Volt\{computed};

new class extends component
{

    public $get;


    private $roles, $hasMultipleRole = false;

    public function mount()
    {
        $this->roles = auth()->user()->getRoleNames();
        // dd(auth()->user()->active_nav);
        if (!$this->roles->contains(auth()->user()->active_nav)) {
            auth()->user()->active_nav = $this->roles[0];
            auth()->user()->save();
        }
        // set default nav
        $this->get = auth()->user()->active_nav;


        if (empty($this->get)) {
            // dd($this->roles);
            if (count($this->roles) > 2) {
                $this->hasMultipleRole = true;

                auth()->user()->active_nav = $this->roles[0];
                auth()->user()->save();
            }else{

                $this->hasMultipleRole = false;
                if ($this->roles->contains('venodor')) {
                    auth()->user()->active_nav = 'vendor';
                    auth()->user()->save();
                }

                if ($this->roles->contains('reseller')) {

                    auth()->user()->active_nav = 'reseller';
                    auth()->user()->save();
                }

                if ($this->roles->contains('rider')) {
                    $this->$get = 'rider';
                }
            }
        }
    }

    public function getNavigation($name) {
        // dd($name);
        auth()->user()->active_nav = $name;
        auth()->user()->save();
        return redirect()->route('dashboard');
    }



    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
};

?>


<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a wire:navigate href="/" class="flex items-center">
                        <img height="50px" width="60px" src="{{asset('icon.png')}}" alt="">
                        <div class="ps-2 text-lg font-bold">
                            {{-- app name --}}
                            <x-application-name />
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 md:-my-px md:ms-10 md:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- @includeif('layouts.primary_navigation') --}}

                    @if (auth()->user()->hasRole('vendor') && $this->get == 'vendor')
                    {{-- vendor primary nav --}}
                    {{-- @includeif('layouts.vendor.navigation.primary') --}}
                    @endif

                    @if (auth()->user()->hasRole('reseller') && $this->get == 'reseller')
                    {{-- reseller primary nav --}}
                    {{-- @includeif('layouts.reseller.navigation.primary') --}}
                    @endif

                    @if (auth()->user()->hasRole('rider') && $this->get == 'rider')
                    {{-- rider primary nav --}}
                    @endif

                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden md:flex md:items-center md:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="px-2 py-1 border bg-orange-500 border-transparent text-white rounded-md mx-1">
                                {{$this->get}}</div>
                            <div x-data="{{ json_encode(['name' => Str::limit(auth()->user()->name, 8)]) }}"
                                x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">

                        {{-- @if ($this->hasMultipleRole)


                        @endif --}}
                        <div class="border-b border-gray-200 px-4 py-2">

                            <div class="flex justify-between px-2 ">
                                <div>Wallet</div>
                                <div> {{ auth()->user()->abailCoin()}} </div>
                            </div>

                            <div class="text-end w-full pt-1 uppercase font-bold">
                                <x-nav-link class="text-center text-orange-900 uppercase font-bold"
                                    href="{{route('user.wallet.diposit')}}"> <i class="fas fa-plus pr-2"></i> Add
                                    Balance</x-nav-link>
                            </div>

                        </div>
                        @if (auth()->user()->hasRole('vendor') )
                        <x-dropdown-link wire:click="getNavigation('vendor')">
                            @if ($this->get == 'vendor')
                            <i class="fas fa-check mr-3"></i>
                            @endif

                            <i class="fas fa-shop pr-2"></i> Vendor Dashboard
                        </x-dropdown-link>
                        @endif
                        @if (auth()->user()->hasRole('reseller'))
                        <x-dropdown-link wire:click="getNavigation('reseller')">
                            @if ($this->get == 'reseller')
                            <i class="fas fa-check mr-3"></i>
                            @endif
                            <i class="fas fa-shop pr-2"></i> Reseller Dashboard
                        </x-dropdown-link>
                        @endif
                        @if (auth()->user()->hasRole('rider'))
                        <x-dropdown-link wire:click="getNavigation('rider')">
                            @if ($this->get == 'rider')
                            <i class="fas fa-check mr-3"></i>
                            @endif
                            <i class="fas fa-truck-fast pr-2"></i> Rider Dashboard
                        </x-dropdown-link>
                        @endif

                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            <i class="fas fa-user pr-2"></i> {{ __('Profile') }}
                        </x-dropdown-link>

                        {{-- other link --}}
                        <x-dropdown-link wire:navigate>
                            <i class="fas fa-gear pr-2"></i> {{ __('Settings') }}
                        </x-dropdown-link>

                        <x-dropdown-link wire:navigate>
                            <i class="fas fa-bell pr-2"></i> {{ __('Notice') }}
                        </x-dropdown-link>
                        <x-hr />
                        <x-dropdown-link wire:navigate target="_blank" :href="route('user.dash')">
                            <i class="fas fa-gauge pr-2"> </i> {{ __('Back to User Panel') }}
                        </x-dropdown-link>
                        <x-dropdown-link wire:navigate target="_blank" :href="route('home')">
                            <i class="fas fa-globe pr-2"></i> {{ __('Visit Website') }}
                        </x-dropdown-link>

                        <x-hr />
                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                <i class="fas fa-sign-out pr-2"></i> {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center md:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden">
        <div class="pt-2 pb-3 space-y-1">

            <div class="flex justify-between px-2 ">
                <div>Wallet</div>
                <div> {{ auth()->user()->abailCoin()}} </div>
            </div>
            <div class="text-end w-full pt-1 uppercase font-bold">
                <x-nav-link class="text-center text-orange-900 uppercase font-bold"
                    href="{{route('user.wallet.diposit')}}"> <i class="fas fa-plus pr-2"></i> Add Balance</x-nav-link>
            </div>
            <x-hr />

            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                <i class="fas fa-home pr-2"></i>{{ __('Dashboard') }}
            </x-responsive-nav-link>

            @includeif('layouts.responsive_navigation')

            @if (auth()->user()->hasRole('vendor') && $this->get == 'vendor')
            {{-- vendor primary nav --}}
            @includeif('layouts.vendor.navigation.responsive')
            @endif

            @if (auth()->user()->hasRole('reseller') && $this->get == 'reseller')
            {{-- reseller primary nav --}}
            @includeif('layouts.reseller.navigation.responsive')
            @endif

            @if (auth()->user()->hasRole('rider') && $this->get == 'rider')
            {{-- rider primary nav --}}
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800"
                    x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                    x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            @if (auth()->user()->hasRole('vendor') )
            <x-dropdown-link wire:click="getNavigation('vendor')">
                @if ($this->get == 'vendor')
                <i class="fas fa-check mr-3"></i>
                @endif

                <i class="fas fa-shop pr-2"></i> Vendor Dashboard
            </x-dropdown-link>
            @endif
            @if (auth()->user()->hasRole('reseller'))
            <x-dropdown-link wire:click="getNavigation('reseller')">
                @if ($this->get == 'reseller')
                <i class="fas fa-check mr-3"></i>
                @endif
                <i class="fas fa-shop pr-2"></i> Reseller Dashboard
            </x-dropdown-link>
            @endif
            @if (auth()->user()->hasRole('rider'))
            <x-dropdown-link wire:click="getNavigation('rider')">
                @if ($this->get == 'rider')
                <i class="fas fa-check mr-3"></i>
                @endif
                <i class="fas fa-truck-fast pr-2"></i> Rider Dashboard
            </x-dropdown-link>
            @endif

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link wire:navigate target="_blank" :href="route('user.dash')">
                    <i class="fas fa-gauge pr-2"></i> Back to User Dash
                </x-responsive-nav-link>

                <x-responsive-nav-link wire:navigate target="_blank" :href="route('home')">
                    <i class="fas fa-globe pr-2"></i> Visit Website
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    <i class="fas fa-user pr-2"> </i> {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        <i class="fas fa-sign-out pr-2"></i> {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
