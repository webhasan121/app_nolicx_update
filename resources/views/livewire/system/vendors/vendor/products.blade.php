<div>
    {{-- <x-dashboard.container>
    </x-dashboard.container> --}}
    
    <x-dashboard.page-header>
     
        @include('auth.system.vendors.navigations')
    </x-dashboard.page-header>
    
    <x-dashboard.container>
        <x-dashboard.overview.section>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Total Product
                </x-slot>
    
                <x-slot name='content'>
                    2090
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Total Product
                </x-slot>
    
                <x-slot name='content'>
                    2090
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Total Product
                </x-slot>
    
                <x-slot name='content'>
                    2090
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Total Product
                </x-slot>
    
                <x-slot name='content'>
                    2090
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Total Product
                </x-slot>
    
                <x-slot name='content'>
                    2090
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Total Product
                </x-slot>
    
                <x-slot name='content'>
                    2090
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Total Product
                </x-slot>
    
                <x-slot name='content'>
                    2090
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Total Product
                </x-slot>
    
                <x-slot name='content'>
                    2090
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Total Product
                </x-slot>
    
                <x-slot name='content'>
                    2090
                </x-slot>
            </x-dashboard.overview.div>
        </x-dashboard.overview.section>


        @livewire('system.products.index', ['find' => $vendor->id, 'from' => 'vendor'], key($vendor->id))
    </x-dashboard.container>

</div>
