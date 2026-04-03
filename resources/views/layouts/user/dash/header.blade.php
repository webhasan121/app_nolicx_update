<?php
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Component;
use function Livewire\Volt\{computed};

$count = computed(function () {
    return auth()->user() ? auth()->user()->myCarts()->count() : "0";
});

new class extends Component{
    /**
     * Log the current user out of the application.
     */
     public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}


?>

<style>
    @media (max-width:991px) {
        .cart-count {
            position: absolute;
            top: 5px !important;
            right: 0px !important;
            background-color: green;
            font-weight: bold;
            color: white;
            font-size: 9px;
            font-weight: bold;
            border-radius: 50%;
            padding: 0px 4px;
            transform: translate(50%, -50%);
        }
    }

    .cart-count {
        position: absolute;
        top: 5px;
        right: 0px;
        background-color: green;
        font-weight: bold;
        color: white;
        font-size: 9px;
        font-weight: bold;
        border-radius: 50%;
        padding: 0px 4px;
        transform: translate(50%, -50%);
    }

    .navbar-expand-lg .navbar-nav {
        -ms-flex-direction: row;
        flex-direction: row;
    }
</style>

<header class="bg-white">
    <x-dashboard.container>
        <div>
            <nav class="flex items-center justify-between">

                <a wire:navigate href="/" class="flex items-center">
                    <img height="50px" width="60px" src="{{asset('icon.png')}}" alt="">
                    <div class="text-lg font-bold ps-2">
                        {{-- app name --}}
                        <x-application-name />
                    </div>
                </a>
                <div class="" id="navbarSupportedContent">
                    <ul class="flex items-center">

                        <li>
                            <a wire:navigate href="{{route('home')}}">
                                Home
                            </a>
                        </li>
                        <li class="px-2">
                            <div class="relative">
                                <a wire:navigate class="nav-link " href="{{route('carts.view')}}"><i
                                        class="fas fa-shopping-cart"></i> <span
                                        class="cart-count">{{auth()->user()->myCarts()->count() ?? "0"}}</span></a>
                            </div>
                        </li>
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 mx-2 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out border rounded hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300">
                                    {{ Str::limit(auth()->user()->name, 8, '...') }}
                                    <div class="ms-1">
                                        <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                {{-- <x-dropdown-link>
                                    Cart
                                </x-dropdown-link> --}}
                                {{-- <x-dropdown-link href="{{route('carts.view')}}" class="mr-3">
                                    <button type="button" class="relative btn">
                                        <i class="fas fa-cart-plus"></i> Cart
                                        <span id="displayCartItem"
                                            class="absolute top-0 ml-3 rounded-lg start-100 translate-middle badge">
                                            {{auth()->user()->myCarts()->count() ?? "0"}}
                                        </span>
                                    </button>
                                </x-dropdown-link> --}}

                                {{-- role-based architecture --}}
                                @php
                                $roles = auth()->user()->getRoleNames();
                                @endphp
                                @if (count($roles) > 1)
                                <x-dropdown-link class="bold" target="_blank" :href="route('dashboard')">
                                    <i class="pr-2 fas fa-home"></i> Go To Dashboard
                                </x-dropdown-link>
                                @else
                                @if (empty(auth()->user()->active_nav))

                                <x-hr />

                                <x-dropdown-link :href="route('upgrade.vendor.create', ['upgrade' => 'vendor'])">
                                    <i class="pr-2 fas fa-shop"></i> {{ __('Open Vendor Shop') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('upgrade.vendor.create', ['upgrade' => 'reseller'])">
                                    <i class="pr-2 fas fa-shop"></i> {{ __('Open Reseller Shop') }}
                                </x-dropdown-link>
                                <x-dropdown-link wire:navigate href="{{route('upgrade.rider.create')}}">
                                    <i class="pr-2 fas fa-truck-fast"></i> {{ __('Request Rider') }}
                                </x-dropdown-link>


                                {{-- <x-dropdown-link :href="route('user.orders.view')">
                                    <i class="pr-2 fas fa-truck"></i> {{ __('Request Rider') }}
                                </x-dropdown-link> --}}

                                <x-hr />
                                @endif
                                {{-- @if (auth()->user()->hasRole('vendor'))
                                <x-dropdown-link wire:navigate class="bold" target="_blank" :href="route('dashboard')">
                                    Vendor Dashboard
                                </x-dropdown-link>
                                @endif
                                @if (auth()->user()->hasRole('reseller'))
                                <x-dropdown-link wire:navigate class="bold" target="_blank" :href="route('dashboard')">
                                    Reseller Dashboard
                                </x-dropdown-link>
                                @endif
                                @if (auth()->user()->hasRole('rider'))
                                <x-dropdown-link wire:navigate class="bold" target="_blank" :href="route('dashboard')">
                                    Rider Dashboard
                                </x-dropdown-link>
                                @endif --}}
                                @endif
                                {{-- role-based architecture --}}

                                {{-- special permission for amdin and system user --}}
                                @if (auth()->user()?->hasRole('system') || auth()->user()->hasRole('admin'))
                                <hr>
                                <div class="py-2">

                                    @can('users_view')
                                    <x-responsive-nav-link :href="route('system.users.view')"
                                        :active="request()->routeIs('system.users.*')">
                                        {{ __('Users Manage') }}
                                    </x-responsive-nav-link>
                                    @endcan

                                    @can('admin_view')
                                    <x-responsive-nav-link :href="route('system.admin')"
                                        :active="request()->routeIs('system.admin')">
                                        {{ __('Admin Manage') }}
                                    </x-responsive-nav-link>
                                    @endcan
                                    @can('vendors_view')
                                    <x-responsive-nav-link :href="route('system.vendor.index')"
                                        :active="request()->routeIs('system.vendor.*')">
                                        {{ __('Vendor Manage') }}
                                    </x-responsive-nav-link>
                                    @endcan

                                    @can('resellers_view')
                                    <x-responsive-nav-link :href="route('system.reseller.index')"
                                        :active="request()->routeIs('system.reseller.*')">
                                        {{ __('Reseller Manage') }}
                                    </x-responsive-nav-link>
                                    @endcan

                                    @can('riders_view')
                                    <x-responsive-nav-link :href="route('system.rider.index')"
                                        :active="request()->routeIs('system.rider.*')">
                                        {{ __('Rider Manage') }}
                                    </x-responsive-nav-link>
                                    @endcan
                                    @can('role_list')
                                    <x-responsive-nav-link :href="route('system.role.list')"
                                        :active="request()->routeIs('system.role.*')">
                                        {{ __('Role Manage') }}
                                    </x-responsive-nav-link>
                                    @endcan

                                    <x-hr />
                                    @can('product_view')
                                    <x-responsive-nav-link :href="route('system.products.index')"
                                        :active="request()->routeIs('system.products.*')">
                                        {{ __('Products Manage' ) }}
                                    </x-responsive-nav-link>
                                    @endcan
                                    @can('category_view')
                                    <x-responsive-nav-link :href="route('system.categories.index')"
                                        :active="request()->routeIs('system.categories.*')">
                                        {{ __('Categories Manage') }}
                                    </x-responsive-nav-link>
                                    @endcan



                                    {{-- @if (auth()->user()->hasRole('system'))


                                    @endif --}}

                                    {{-- @can('role_list')
                                    @endcan --}}
                                    @can('vip_view')

                                    <x-responsive-nav-link :href="route('system.vip.users')"
                                        :active="request()->routeIs('system.vip.*')">
                                        {{ __('ViP Manage') }}
                                    </x-responsive-nav-link>
                                    @endcan
                                    @can('slider_view')

                                    <x-responsive-nav-link :href="route('system.slider.index')"
                                        :active="request()->routeIs('system.slider.*')">
                                        {{ __('Slider Manage') }}
                                    </x-responsive-nav-link>
                                    @endcan
                                    {{-- @can('', $post)

                                    @endcan
                                    <x-responsive-nav-link :href="route('system.navigations.index')"
                                        :active="request()->routeIs('system.navigations.*')">
                                        {{ __('Navigations') }}
                                    </x-responsive-nav-link> --}}
                                    @can('store_view')
                                    <x-responsive-nav-link :href="route('system.store.index')"
                                        :active="request()->routeIs('system.store.*')">
                                        {{ __('StoreManage') }}
                                    </x-responsive-nav-link>
                                    @endcan

                                    <x-hr />
                                    @can('deposit_view')

                                    <x-responsive-nav-link :href="route('system.deposit.index')"
                                        :active="request()->routeIs('system.deposit.*')">
                                        {{ __('Deposit Manage') }}
                                    </x-responsive-nav-link>
                                    @endcan

                                    @can('comission_view')

                                    <x-responsive-nav-link :href="route('system.comissions.index')"
                                        :active="request()->routeIs('system.comissions.*')">
                                        {{ __('Comission Manage') }}
                                    </x-responsive-nav-link>
                                    @endcan

                                    @can('order_view')

                                    <x-responsive-nav-link :href="route('system.orders.index')"
                                        :active="request()->routeIs('system.orders.*')">
                                        {{ __('Orders Manage') }}
                                    </x-responsive-nav-link>
                                    @endcan

                                    @can('withdraw_view')

                                    <x-responsive-nav-link :href="route('system.withdraw.index')"
                                        :active="request()->routeIs('*.withdraw.*')">
                                        {{ __('Withdraw Manage') }}
                                    </x-responsive-nav-link>
                                    @endcan

                                </div>
                                <hr>
                                @endif
                                {{-- special permission --}}

                                @php
                                $get = auth()->user()->active_nav;
                                @endphp
                                {{-- permission for reseller --}}
                                @if (auth()->user()->hasRole('vendor') && $get == 'vendor')
                                {{-- vendor primary nav --}}
                                <hr>
                                @includeif('layouts.vendor.navigation.responsive')
                                <hr>
                                @endif

                                @if (auth()->user()->hasRole('reseller') && $get == 'reseller')
                                {{-- reseller primary nav --}}
                                <hr>
                                @includeif('layouts.reseller.navigation.responsive')
                                <hr>
                                @endif
                                {{-- permission for reseller --}}

                                @if (auth()->user()->hasRole('rider') && $get == 'rider')
                                {{-- reseller primary nav --}}
                                <hr>
                                @includeif('layouts.rider.navigation.responsive_navigation')
                                <hr>
                                @endif


                                <x-dropdown-link href="{{route('edit.profile')}}">
                                    <i class="pr-2 fas fa-user"></i> Profile
                                </x-dropdown-link>
                                {{-- @if (Route::has('logout'))
                                <form method="get" action="{{ route('logout') }}">
                                    @csrf

                                    <x-dropdown-link wire:navigate :href="route('logout')" onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                                @endif --}}
                                <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    <i class="pr-2 fas fa-sign-out"></i> {{ __('Log Out') }}
                                </x-responsive-nav-link>

                            </x-slot>
                        </x-dropdown>
                    </ul>

                </div>
            </nav>
        </div>
    </x-dashboard.container>
</header>
<!-- end header section -->
