<div class="py-4" x-init="$wire.getData()">
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}


    <div class="">
        @includeIf('components.client.common-heading')
    </div>
    <x-dashboard.container>

        <div class="lg:flex justify-start items-start">
            <div style="width:300px" class="bg-white hidden md:block ">
                {{-- @livewire('reseller.resel.categories') --}}
                @livewire('pages.categories')
                {{-- @foreach ($categories as $item)
                <x-client.cat :cat="$cat" :active="($cat->name == $this->cat)" />
                <x-client.cat-loop :item="$item" :key="$item->id" :active="$cat == $item->slug" :cat="$cat" />
                @endforeach --}}
            </div>

            <div class="block md:hidden p-2 border rounded-md mb-2 bg-white" x-data="{ open: false }">
                <div x-on:click="open = !open" class="flex justify-between items-center">
                    <div>Categories</div>
                    <div>
                        <i x-show="open" class="fas fa-chevron-down"></i>
                        <i x-show="!open" class="fas fa-chevron-up"></i>
                    </div>
                </div>
                <div x-show="open" x-collapse class="overflow-x-scroll border-t mt-2">
                    {{-- @foreach ($categories as $item)
                    <x-client.cat-loop :item="$item" :key="$item->id" :active="$cat == $item->slug" :cat="$cat" />
                    @endforeach --}}
                    @livewire('pages.categories')

                </div>
            </div>

            <div class="px-2 w-full">

                <div class="flex flex-wrap justify-between items-center mb-3">

                    <div>
                        <x-text-input type="search" placeholder="Search ...." class="py-1" />
                    </div>
                    <div class="flex items-center justify-between space-x-2">
                        <x-secondary-button>
                            <i class="fas fa-filter"></i>
                        </x-secondary-button>
                        <select wire:model.live="sort" id="sort_by" class="w-24 rounded py-1">
                            <option value="desc">Newest</option>
                            <option value="asc">Oldest</option>
                        </select>
                    </div>
                </div>

                <div class="product_section" x-loading.disabled x-transition>
                    {{--
                    <x-client.products-loop :$products /> --}}
                    @if (count($products))
                    <div class=""
                        style="display: grid; justify-content:start; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); grid-gap:10px">

                        @foreach ($products as $product)
                        @livewire('pages.product-cart', ['product' => $product], key($product->id))
                        @endforeach

                    </div>
                    @endif

                    <div class="text-center" wire:show="load">
                        <button wire:click.prevent="loadMore" class="px-3 py-1 rounded border mt-3">Load More</button>
                    </div>
                </div>

            </div>
        </div>



    </x-dashboard.container>

    {{-- <div class="text-center">
        <a href="{{route('uproducts.index')}}" class="px-3 py-2 rounded btn_outline_secondary">
            View All products
        </a>
    </div> --}}


    <script>
        let ps = document.getElementsByClassName('product_section')[0];
        let html = 
        `
        <div class="p-3 rounded shadow m-1">
            new item
        </div>
        `;
        document.addEventListener('scroll', (e)=>{
            let documentHeight = document.body.clientHeight;
            let scrollToTop = document.documentElement.scrollTop;
            let windowHeight = window.innerHeight;
            
            console.log(documentHeight, scrollToTop, documentHeight - scrollToTop);
            
            // ps.insertAdjacentHTML('beforeend', html)

            // if(documentHeight, scrollToTop){

            // }
            
        });
    </script>
</div>