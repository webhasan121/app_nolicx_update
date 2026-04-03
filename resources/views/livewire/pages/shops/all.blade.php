<div>
    @livewire('pages.slider')

    <div class="py-4">
        <div >
            <div class="w-auto w-full mb-3 text-3xl text-center  heading_center">
                <h2 class="flex justify-center gap-3">
                    <x-application-name  /> <span class="font-bold text-green-900" >Shops</span>
                </h2>
            </div>
        </div>
    </div>
    <x-dashboard.container>

        <div class="items-center justify-between space-y-2 md:flex">

            <div class="flex items-center justify-start py-3">
                <x-nav-link href="/">
                    <i class="fas fa-home pe-2"></i>
                </x-nav-link>
                {{-- <i class="px-2 py-0 m-0 fas fa-slash-back"></i> --}}
                <x-nav-link href="{{route('shops.reseller')}}">
                    <x-application-name /> <div class="px-2">Shops</div>
                </x-nav-link>
            </div>

            <div class="flex items-center">
                <input type="search" wire:model.live="q" class="py-1 rounded-md" placeholder="search shops by name" id="">
                <div>
                    @auth
                        <div @click="$dispatch('open-modal', 'shop-location-modal')" class="px-3 py-2 text-xs bg-white border rounded ms-1">
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

        {{-- <div style="display: grid; grid-template-columns:repeat(auto-fit, 300px); justify-content:start; align-items:start; gird-gap:10px">

            <x-client.shops-cart/>

        </div> --}}
        @guest
            <div class="w-full p-1 text-center bg-gray-200 ">
                Login to get access the shops based on your location.
            </div>
        @endguest
        @if ($q || $location)
            {{$shops->links()}}
           <div style="display: grid; grid-template-columns:repeat(auto-fit, 300px); justify-content:start; align-items:start; grid-gap:10px">

                @if (count($shops) > 0)
                    @foreach ($shops as $shop)
                        <x-client.shops-cart :$shop :key="$shop->id"/>
                    @endforeach
                @else
                    <p>
                        No Shops Found !
                    </p>
                @endif
            </div>
        @else
            @livewire('pages.shops.shop-list')
        @endif

        <x-modal name="shop-location-modal" maxWidth="sm">
            <div class="p-3" x-data="{tab : me}">
                <p class="text-xs ">
                    Shop will be displayed based on you expectation. From where you want to get the shop.
                </p>
                <br>
                <div class="space-y-3 text-center">
                    @auth
                        <x-primary-button wire:click="getShopByMyLocation" class="flex items-center justify-center w-full p-3 text-white bg-indigo-300 rounded">
                            My Location ({{auth()->user()->city}}) <i class="px-2 fas fa-location"></i>
                        </x-primary-button>
                    @endauth

                    <x-secondary-button wire:click="getAllShops" class="flex justify-center w-full p-3 items-centere">
                        All Shops
                    </x-secondary-button>

                    <div class="p-2 bg-gray-200 rounded">
                        <input type="search" wire:model.live="location" id="find_shop" class="w-full py-1 mb-1 rounded" placeholder="search shop by state, city or town">
                    </div>

                </div>
            </div>
        </x-modal>
    </x-dashboard.container>

</div>
