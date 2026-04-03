<div>
    {{-- The whole world belongs to you. --}}

    <x-dashboard.page-header>
        <div class="flex justify-between">
            <div>

                Resel Products
                <br>
                <div>
                    {{-- <x-nav-link href="{{route('vendor.products.view')}}"
                        :active="request()->routeIs('vendor.products.*')">Your Product</x-nav-link> --}}
                    <x-nav-link href="{{route('reseller.resel-product.index')}}"
                        :active="request()->routeIs('reseller.resel-product.*')"> Product</x-nav-link>
                    {{-- <x-nav-link href="{{route('reseller.resel-products.catgory')}}"
                        :active="request()->routeIs('reseller.resel-products.*')"> Category</x-nav-link> --}}
                </div>
            </div>

            <div>
                <div class="flex bg-indigo-900 border border-indigo-900 rounded-xl">
                    <div class="px-2 bg-white" title="Total Resell Products">
                        {{$totalReselProducts}}
                    </div>
                    <div class="px-2 text-white" title="Max Resell Products">
                        {{$shop->max_resell_product}}
                    </div>
                </div>
            </div>

        </div>
    </x-dashboard.page-header>

    <x-dashboard.container>
       
        @if (!$ableToAdd)
        <div class="p-2 bg-red-200 text-red-800">
            You have reached the maximum number of products you can upload {{$shop->max_resell_product}}. Please delete
            some products to add new ones or upgrade your plan.
        </div>
        @endif

        <div>
            <div class="block">
                <div @click="$dispatch('open-modal', 'category-view-modal')"
                    class="flex justify-between items-center px-3 py-1 mb-2 border rounded-md hover:bg-white" transition
                    duration-150>
                    <div>
                        Categories
                    </div>
                    <div>
                        <i class="fas fa-angle-right"></i>
                    </div>
                </div>
                {{-- <div x-show="open" x-collapse>
                    @livewire('reseller.resel.categories', ['cat' => $cat])
                </div> --}}
            </div>
            {{-- <div class="hidden md:block text-start" style="width:250px; text-aling:left">
                <div>
                    @livewire('reseller.resel.categories', ['cat' => $cat])
                </div>
            </div> --}}

            <div>

                @if ($products->links())
                {{$products->links()}}
                @endif
                <div
                    style="display: grid; justify-content:start; grid-template-columns: repeat(auto-fill, 160px); grid-gap:10px">
                    @foreach ($products as $pd)
                    @includeIf('components.dashboard.reseller.resel-product-cart')
                    @endforeach
                </div>
            </div>
            @if (count($products) < 1) <div class="p-2 bg-gray-200 h-auto">
                No Products Found !
        </div>
        @endif
    </x-dashboard.container>

    <x-modal name="category-view-modal" class="h-screen">
        <div class="p-3 flex items-center justify-between">
            <div>
                Explore Categories
            </div>
            <div @click="$dispatch('close-modal', 'category-view-modal')">
                <i class="fas fa-times"></i>
            </div>
        </div>
        <hr />
        <div class="p-3 flex-1 overflow-y-scroll">
            @livewire('reseller.resel.categories', ['cat' => $cat])
        </div>
        <hr />
        <div class="w-full text-end p-3">
            <x-danger-button @click="$dispatch('close-modal', 'category-view-modal')">
                <i class="fas fa-times mr-2"></i> Close
            </x-danger-button>
        </div>
    </x-modal>


</div>