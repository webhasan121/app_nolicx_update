<x-app-layout>
    <x-dashboard.page-header>
        Edit Vendor info
        <br>
        @include('auth.system.vendors.navigations')
    </x-dashboard.page-header>
    
    @php
        $user = auth()->user();
    @endphp
    
    @include('auth.system.vendors.vendor.overview')
    <x-dashboard.container>
        <x-dashboard.section>
            @include('profile.partials.update-profile-information-form')
            {{-- @include('auth.system.vendors.settings') --}}
            
            {{-- body  --}}
            {{-- @if ($filter == 'Settings')
                
                <x-dashboard.section.inner>
                </x-dashboard.section.inner>
            @endif

            @if ($filter == 'Documents')
                <x-dashboard.section.inner>
                    @include('auth.system.vendors.documents')
                </x-dashboard.section.inner>
            @endif

            @if ($filter == 'Products')
                <x-dashboard.section.inner>
                    @include('auth.system.vendors.products')
                </x-dashboard.section.inner>
            @endif

            @if ($filter == 'Categories')
                <x-dashboard.section.inner>
                    @include('auth.system.vendors.categories')
                </x-dashboard.section.inner>
            @endif --}}
        </x-dashboard.section>
    </x-dashboard.container>
</x-app-layout>