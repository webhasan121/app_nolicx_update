<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    @livewire('pages.slider')
    <x-dashboard.container>
        <div class="container">
            @includeIf('components.client.common-heading')

            <div class="flex justify-start items-center py-3 border-y mb-3">
                <i class="fas fa-home pe-2"></i>
                {{-- <i class="fas fa-slash-back px-2 py-0 m-0"></i> --}}
                <div>
                    Category
                </div>
                {{-- <i class="fas fa-caret-right px-2 py-0 m-0"></i> --}}
                <x-nav-link href="{{ route('category.products', ['cat' => $cat]) }}" class="">
                    <span class="text-primary px-2 text-gray-600">{{ $cat }}</span>
                </x-nav-link>
            </div>

            <div class="product_section ">

                {{-- <div class=""
                    style="display: grid; justify-content:center; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); grid-gap:10px">
                    @foreach($products as $product)
                    <x-client.product-cart :$product :key="$product->id" />
                    @endforeach
                </div> --}}

                <div class="md:flex w-full">
                    <div style="width: 300px" class=" bg-white p-3 hidden md:block">
                        {{-- @livewire('reseller.resel.categories') --}}
                        <div>
                            <x-nav-link-btn href="{{route('products.index')}}">All Product</x-nav-link-btn>
                            <br>
                        </div>
                        @foreach ($categories as $item)
                        {{--
                        <x-client.cat :cat="$cat" :active="($cat->name == $this->cat)" /> --}}
                        <x-client.cat-loop :item="$item" :key="$item->id" :active="$cat == $item->slug" :cat="$cat"
                            style="font-bold" />
                        @endforeach
                    </div>

                    <div class="block md:hidden px-3 bg-white mb-2 " x-data="{ open: false }">
                        <div x-on:click="open = !open" class="flex justify-between items-center">
                            <div>Categories</div>
                            <div>
                                <i x-show="open" class="fas fa-chevron-down"></i>
                                <i x-show="!open" class="fas fa-chevron-up"></i>
                            </div>
                        </div>
                        <div x-show="open" x-collapse class=" border-t mt-2">
                            <div>
                                <x-nav-link-btn href="{{route('products.index')}}">All Product</x-nav-link-btn>
                                <br>
                            </div>
                            <div>
                                @foreach ($categories as $product)
                                <x-client.cat-loop :item="$product" :key="$product->id" :active="$cat == $product->slug"
                                    :cat="$cat" />
                                @endforeach
                            </div>
                        </div>
                    </div>



                    <div class="px-2 w-full">
                        {{--
                        <x-client.products-loop :$products /> --}}
                        <div class="w-full"
                            style="display: grid; justify-content:start; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); grid-gap:10px">
                            @foreach ($products as $product)
                            @livewire('pages.product-cart', ['product' => $product], key($product->id))
                            @endforeach
                        </div>
                        @if (!$products || count($products) == 0)
                        <div class="alert alert-info">No Product Found !</div>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </x-dashboard.container>
</div>