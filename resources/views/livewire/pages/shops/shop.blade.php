<div x-init="$wire.getDeta()">
    {{-- The Master doesn't talk, he acts. --}}
    {{-- @livewire('pages.slider') --}}
    <div>
        <div class="bg-white overflow-hidden">
            <div class="relative">
                <img class="w-full bg-indigo-900" style="aspect-ration:16/9" src="{{asset('storage/'. $shops->banner)}}"
                    alt="">
                <img class="rounded-full  absolute left-0 top-0 bg-white m-2" style="height: 80px; width:80px"
                    src="{{asset('storage/'.$shops->logo)}}" alt="">
            </div>
            <x-dashboard.container>

                <div class="">

                    <div class="flex flex-wrap gaps-10 ">
                        <div class="w-48 m-1 border p-2 rounded-lg ">
                            <p>Shop</p>
                            <div class="">
                                {{$shops->shop_name_en}}
                                {{-- Lorem ipsum dolor sit amet. --}}
                            </div>
                            <p class="text-xs">
                                {{$shops->village}}, {{$shops->upozila}}, {{$shops->district}}
                            </p>
                            <div class="py-3">
                                <div class="flex items-center">
                                    <i class="fas fa-star text-indigo-900"></i>
                                    <i class="fas fa-star text-indigo-900"></i>
                                    <i class="fas fa-star text-indigo-900"></i>
                                    <i class="fas fa-star text-indigo-900"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <div class="mt-2 flex justify-between items-center space-x-2 space-y-2">

                                <div class="px-2 bg-sky-900 text-white rounded-lg inline-block text-xs">
                                    reseller
                                </div>

                                {{-- <div class="px-2 bg-gray-900 text-white rounded-lg inline-block text-xs">
                                    Eruhi Choise
                                </div> --}}
                            </div>

                        </div>

                        <div class="w-48 m-1 border p-2 rounded-lg">
                            <p>Owner</p>
                            <div class="text-md">
                                {{$shops->user?->name}}
                                {{-- Lorem ipsum dolor sit amet. --}}
                            </div>
                            <p class="text-xs">
                                <i class="fas fa-caret-right pr-3"></i> {{$shops->email }}
                            </p>
                            <p class="text-xs">
                                <i class="fas fa-caret-right pr-3"></i> {{$shops->phone }}
                            </p>
                            <p class="text-xs">
                                {{$shops->user?->village}}, {{$shops->user?->upozila}}, {{$shops->user?->district}}
                            </p>


                        </div>
                    </div>

                    <x-hr />
                    <div class="flex justify-center space-x-3">
                        <div>
                            <i class="fas fa-heart"></i>
                        </div>
                        <x-nav-link
                            href="{{route('shops.visit', ['id' => $shops->id, 'name'=>$shops->shop_name_en ?? 'not_found'])}}">
                            Visit Shops <i class="fas fa-caret-right px-2"></i>
                        </x-nav-link>
                    </div>
                </div>

            </x-dashboard.container>
        </div>
    </div>
    <x-dashboard.container>

        <div class="flex justify-start items-center py-3 mb-3">
            <x-nav-link href="/">
                <i class="fas fa-home pe-2"></i>
            </x-nav-link>
            {{-- <i class="fas fa-slash-back px-2 py-0 m-0"></i> --}}
            <x-nav-link href="{{route('shops.reseller')}}">
                Shops
            </x-nav-link>
            <div class="text-gray-600 ">
                {{$shops?->shop_name_en}}
            </div>
        </div>

    </x-dashboard.container>


    <x-dashboard.container class="my-[100]">

        <div x-loading.disabled x-transition>

            <div class="product_section w-full md:w-3/4">

                <div class="text-sm py-2">Products</div>
                @if ($products)
                <div class=""
                    style="display: grid; justify-content:start; grid-template-columns: repeat(auto-fill, 160px); grid-gap:10px">

                    @foreach ($products as $product)
                    <x-client.product-cart :$product :key="$product->id" />
                    @endforeach

                </div>
                @endif
            </div>
        </div>
    </x-dashboard.container>

</div>