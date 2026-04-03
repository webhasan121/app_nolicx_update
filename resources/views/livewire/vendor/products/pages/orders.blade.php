<div>
    {{-- In work, do what you enjoy. --}}
    <x-dashboard.page-header>
        Product Orders
        @includeIf('components.dashboard.vendor.products.navigations')
    </x-dashboard.page-header>


    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                   
                    <div>
                        <x-image src="{{asset('storage/'.$products->thumbnail ?? '')}}" />
                    </div>
                </x-slot>
                
                <x-slot name="content">
                    <div>
                        {{$products->title ?? ""}}
                    </div>

                    <div class="text-sm">
                        category : <strong> {{$products->category?->name ?? "N/A"}} </strong>
                    </div>
                </x-slot>
            </x-dashboard.section.header>
        </x-dashboard.section>
    </x-dashboard.container>
</div>
