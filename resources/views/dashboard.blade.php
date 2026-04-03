<?php

use Livewire\Volt\Component;

// use function Livewire\Volt\{placeholder, computed};

// new class extends Component {
//     public $usercount = 0, $vd, $avd, $ri, $ari, $rs, $ars, $adm = 0, $aadm, $vp, $avp, $cat;

//     public function mount()
//     {

//     }
// }


?>

<x-app-layout>
    <x-dashboard.page-header>
        @if (auth()->user()->hasRole('vendor') && auth()->user()->active_nav == 'vendor')
        Vendor
        @endif
        @if (auth()->user()->hasRole('rider') && auth()->user()->active_nav == 'rider')
        Rider
        @endif
        @if (auth()->user()->hasRole('admin'))
        Admin
        @endif
        @if (auth()->user()->hasRole('reseller') && auth()->user()->active_nav == 'reseller')
        Reseller
        @endif
        Dashboard
    </x-dashboard.page-header>

    {{-- system dashboard over view --}}
    @if (auth()->user()->hasAnyRole('admin','system'))
    @livewire('system.dashboard.index')
    @endif


    {{-- vendor dashboard overview --}}
    @if (auth()->user()->active_nav == 'vendor')
    <x-has-role name="vendor">
        @includeIf('layouts.vendor.vendor')
    </x-has-role>
    @endif

    @if (auth()->user()->active_nav == 'reseller')

    {{-- reseller dashboard overview --}}
    <x-has-role name="reseller">
        @livewire('reseller.dashboard', key('reseller_dash'))
    </x-has-role>
    @endif

    @if (auth()->user()?->active_nav == 'rider')
    {{-- rider dashboard overview --}}
    <x-has-role name="rider">
        @livewire('rider.consignment.index')
    </x-has-role>
    @endif

</x-app-layout>
