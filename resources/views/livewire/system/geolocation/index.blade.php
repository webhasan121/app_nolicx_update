<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <x-dashboard.page-header>
        Grolocations
    </x-dashboard.page-header>

    <x-dashboard.container>
        <div class="flex items-center gap-2">
            <x-nav-link-btn href="{{route('system.geolocations.countries')}}">Countries</x-nav-link-btn>
            <x-nav-link-btn href="{{route('system.geolocations.states')}}">States</x-nav-link-btn>
            <x-nav-link-btn href="{{route('system.geolocations.cities')}}">Cities</x-nav-link-btn>
            <x-nav-link-btn href="{{route('system.geolocations.area')}}">Areas</x-nav-link-btn>
        </div>

    </x-dashboard.container>


    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Targeted Area
                </x-slot>
                <x-slot name="content">
                    Manage your targeted area from here. You can add, edit and delete countries, states and cities.
                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>

            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>
</div>