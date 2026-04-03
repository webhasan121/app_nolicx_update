<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <x-dashboard.page-header>
        Vendor Shops
    </x-dashboard.page-header>

     <x-dashboard.container>

        <div class="md:flex justify-between items-center space-y-2">

            <div class="flex justify-start items-center py-3">
                <x-nav-link href="/">
                    <i class="fas fa-home pe-2"></i>
                </x-nav-link>
                {{-- <i class="fas fa-slash-back px-2 py-0 m-0"></i> --}}
                <x-nav-link href="{{route('shops')}}">
                    <x-application-name /> <div class="px-2">Shops</div>
                </x-nav-link>
            </div>
        
            <div class="flex items-center">
                <input type="search" wire:model.live="q" class="py-1 rounded-md" placeholder="search shops by name" id="">
                <div>
                    @auth
                        <div @click="$dispatch('open-modal', 'shop-location-modal')" class="py-2 px-3 text-xs ms-1 border rounded bg-white">
                            {{ !empty($location) ? $location : auth()->user()->city ?? 'ANY'}} <i class="ps-2 fas fa-chevron-down"></i>
                        </div>
                    @else 
                        <div @click="$dispatch('open-modal', 'shop-location-modal')" class="px-2">
                            <i class="fas fa-location"></i>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
        @if ($getShops)
            <div>
                <div class="bg-white overflow-hidden">
                    <div class="relative">
                        <img class="w-full bg-indigo-900 h-48" src="{{asset('storage/'. $getShops->banner)}}" alt="">
                        <img class="rounded-full  absolute left-0 top-0 bg-white m-2" style="height: 80px; width:80px" src="{{asset('storage/'.$getShops->logo)}}" alt="">
                    </div>
                    <x-dashboard.container>

                        <div class="">

                            <div class="flex flex-wrap gaps-10 ">
                                <div class="w-48 m-1 border p-2 rounded-lg ">
                                    <p>Shop</p>
                                    <div class="">
                                        {{$getShops->shop_name_en}}
                                        {{-- Lorem ipsum dolor sit amet. --}}
                                    </div>
                                    <p class="text-xs">
                                        {{$getShops->village}}, {{$getShops->upozila}}, {{$getShops->district}}
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
                                        {{$getShops->user?->name}}
                                        {{-- Lorem ipsum dolor sit amet. --}}
                                    </div>
                                    <p class="text-xs">
                                        <i class="fas fa-caret-right pr-3"></i> {{$getShops->email }}
                                    </p>
                                    <p class="text-xs">
                                        <i class="fas fa-caret-right pr-3"></i> {{$getShops->phone }}
                                    </p>
                                    <p class="text-xs">
                                        {{$getShops->user?->village}}, {{$getShops->user?->upozila}}, {{$getShops->user?->district}}
                                    </p>
                                

                                </div>
                            </div>

                            <x-hr/>
                            <div class="flex justify-center space-x-3">
                                <div>
                                    <i class="fas fa-heart"></i>
                                </div>
                                <x-nav-link href="{{route('shops', ['get' => $getShops->id, 'name'=> Str::slug($getShops->shop_name_en) ?? 'not_found'])}}">
                                    Visit Shops <i class="fas fa-angle px-2"></i>
                                </x-nav-link>
                            </div>
                        </div>

                    </x-dashboard.container>
                </div>
            </div>
                      

            <x-dashboard.section class="my-[100]">
                <div x-loading.disabled x-transition>
                    
                    <div class="product_section w-full md:w-3/4" > 
                        
                        <div class="text-sm py-2">Products</div>
                        @if ($products)     
                            <div class="" style="display: grid; justify-content:start; grid-template-columns: repeat(auto-fill, 160px); grid-gap:10px">
                                    
                                @foreach ($products as $pd)
                                        @includeIf('components.dashboard.reseller.resel-product-cart')
                                @endforeach
                                            
                            </div>
                            {{$products->links()}}
                        @endif
                    </div>
                </div>
            </x-dashboard.section>
        @else 

            @if ($q || $location)
            @endif
            {{$shops->links()}}
            <div style="display: grid; grid-template-columns:repeat(auto-fit, 300px); justify-content:start; align-items:start; grid-gap:10px">

                @if (count($shops) > 0)
                    @foreach ($shops as $shop)
                    <div>
                            <div class="bg-white rounded-lg shadow overflow-hidden">
                                <div class="relative">
                                    <img class="w-full bg-indigo-900" style="height:100px" src="{{asset('storage/'. $shop->banner)}}" alt="">
                                    <img class="rounded-full  absolute left-0 top-0 bg-white m-2" style="height: 50px; width:50px" src="{{asset('storage/'.$shop->logo)}}" alt="">
                                </div>
                                <div class="p-3">
                                    <div class="">
                                        {{$shop->shop_name_en}}
                                        {{-- Lorem ipsum dolor sit amet. --}}
                                    </div>
                                    <p class="text-xs">
                                        {{$shop->village}}, {{$shop->upozila}}, {{$shop->district}}
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
                                            vendor
                                        </div>
                                        
                                        {{-- <div class="px-2 bg-gray-900 text-white rounded-lg inline-block text-xs">
                                            Eruhi Choise
                                        </div> --}}
                                    </div>
                                    <x-hr/>
                                    <div class="flex justify-between">
                                        <div>
                                            <i class="fas fa-heart"></i>
                                        </div>
                                        <x-nav-link href="{{route('shops', ['get' => $shop->id, 'slug'=> Str::slug($shop->shop_name_en) ?? 'not_found'])}}">
                                            Visit Shops <i class="fas fa-angle px-2"></i>
                                        </x-nav-link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p>
                        No Shops Found !
                    </p>
                @endif
            </div>

        @endif
   
        <x-modal name="shop-location-modal" maxWidth="sm">
            <div class="p-3" x-data="{tab : me}">
                <p class="text-xs ">
                    Shop will be displayed based on you expectation. From where you want to get the shop.
                </p>
                <br>
                <div class="text-center space-y-3">
                    @auth    
                        <x-primary-button wire:click="getShopByMyLocation" class="p-3 flex justify-center items-center bg-indigo-300 text-white w-full rounded"> 
                            My Location ({{auth()->user()->city}}) <i class="px-2 fas fa-location"></i>
                        </x-primary-button>
                    @endauth

                    <x-secondary-button wire:click="getAllShops" class="p-3 w-full flex justify-center items-centere">
                        All Shops
                    </x-secondary-button>

                    <div class="p-2 rounded bg-gray-200">
                        <input type="search" wire:model.live="location" id="find_shop" class="py-1 w-full rounded mb-1" placeholder="search shop by state, city or town">
                    </div>

                </div>
            </div>
        </x-modal>
    </x-dashboard.container>
</div>
