<div>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">

    {{-- Care about people's approval and you will be their prisoner. --}}
    {{-- <x-dashboard.page-header>
        <div class="text-xs">

        </div>
        <br>
        {{$shopArray['shop_name_en']}}
        <br>
        <div class="px-2 py-1 rounded-xl bg-white text-xs inline-block">
            {{$shopArray['status']}}
        </div>
    </x-dashboard.page-header> --}}

    <x-dashboard.container>
        <div class="relative">
            <div class="rounded-full  absolute left-0 top-0 bg-white m-2">
                @if ($newLogo)
                <img class="rounded-full" style="height: 80px; width:80px" src="{{$newLogo->temporaryUrl()}}" alt="">
                @else
                <img class="rounded-full" style="height: 80px; width:80px"
                    src="{{asset('storage/'.$shopArray['logo'])}}" alt="">
                @endif
                <input type="file" wire:model.live="newLogo" id="logo" class="absolute hidden">
                <label for="logo"
                    class="absolute bottom-0 right-0 rounded-full bg-white border w-6 h-6 p-1 flex items-center justify-center">
                    <i class="fas fa-upload"></i>
                </label>
            </div>
            @if ($newBanner)
            <img class="w-full bg-indigo-900 h-48 rounded" src="{{ $newBanner->temporaryUrl()}}" alt="">
            @else
            <img class="w-full bg-indigo-900 h-48 rounded" src="{{asset('storage/'. $shopArray['banner'])}}" alt="">
            @endif
            <input type="file" wire:model.live="newBanner" id="banner" class="absolute hidden">
            <label for="banner"
                class="absolute bottom-0 right-0 rounded-full bg-white border w-6 h-6 p-1 flex items-center justify-center">
                <i class="fas fa-upload"></i>
            </label>
        </div>
        <x-hr />
        <main wire:ignore>
            <trix-toolbar id="my_toolbar"></trix-toolbar>
            <div class="more-stuff-inbetween"></div>
            <input type="hidden" name="content" id="my_input" wire:model.live="shopArray.description"
                value="{{$shopArray['description']}}">
            <trix-editor toolbar="my_toolbar" input="my_input"></trix-editor>
        </main>

        <div>
            <div class="md:flex w-full flex-1 gap-10">
                <div class="bg-white rounded-md shadow-sm w-full">
                    <hr>
                    <div class="text-md border-b w-full p-3">
                        <div class="font-bold">Shop ID: </div>
                        <div> {{$shopArray['id'] ?? "N/A"}} </div>
                        {{-- <input type="text" class="w-full rounded-md ring-0" wire:model.live="shopArray.id">
                        --}}
                    </div>
                    <div class="text-md border-b w-full p-3">
                        <div class="font-bold">Shop Owner Name: </div>
                        <div> {{auth()->user()->name ?? "N/A"}} </div>
                    </div>
                    <div class="text-md border-b w-full p-3">
                        <div class="font-bold">Shop Owner Email: </div>
                        <div> {{ auth()->user()->email ?? "N/A"}} </div>
                    </div>
                    <div class="text-md border-b w-full p-3">
                        <div class="font-bold">Shop Owner Phone: </div>
                        <div> {{auth()->user()?->phone ?? "N/A"}} </div>
                    </div>
                    <div class="text-md border-b w-full p-3">
                        <div class="font-bold">Shop Comission (%) : </div>
                        <div> {{$shopArray["system_get_comission"] ?? "N/A"}} </div>
                    </div>
                    <div class="text-md border-b w-full p-3">
                        <div class="font-bold">Product Upload Capability : </div>
                        <div> {{$shopArray["max_product_upload"] ?? "N/A"}} </div>
                    </div>
                    <div class="text-md border-b w-full p-3">
                        <div class="font-bold">Product Resel Capability : </div>
                        <div> {{$shopArray["max_resell_product"] ?? "N/A"}} </div>
                    </div>
                </div>

                <div class="p-3 bg-white rounded-md shadow-sm w-full">
                    <hr>

                    <div class="text-md border-b w-full p-3">
                        <div class="font-bold">Shop: </div>
                        {{-- <div> {{$shopArray['email'] ?? "N/A"}} </div> --}}
                        <input type="text" class="w-full rounded-md ring-0" wire:model="shopArray.shop_name_en">
                    </div>

                    <div class="text-md border-b w-full p-3">
                        <div class="font-bold">Shop Email: </div>
                        {{-- <div> {{$shopArray['email'] ?? "N/A"}} </div> --}}
                        <input type="text" class="w-full rounded-md ring-0" wire:model="shopArray.email">
                    </div>
                    <div class="text-md border-b w-full p-3">
                        <div class="font-bold">Shop Phone: </div>
                        {{-- <div> {{$shopArray['phone'] ?? "N/A"}} </div> --}}
                        <input type="text" class="w-full rounded-md ring-0" wire:model="shopArray.phone">
                    </div>
                    <div class="text-md border-b w-full p-3">
                        <div class="font-bold">Shop Address: </div>
                        {{-- <div> {{$shopArray['address'] ?? "N/A"}} </div> --}}
                        <input type="text" class="w-full rounded-md ring-0" wire:model="shopArray.address">
                    </div>
                    <div class="text-md border-b w-full p-3 space-y-2">
                        <div class="font-bold">Shop Location: </div>
                        <div class="my-1">

                            <label class="my-1" for="dis">District</label>
                            <input type="text" id="dis" class="w-full rounded-md ring-0" wire:model="shopArray.district"
                                placeholder="district">
                        </div>

                        <div class="my-1">
                            <label class="my-1" for="up">Upozila</label>
                            <input type="text" id="up" class="w-full rounded-md ring-0" wire:model="shopArray.upozila"
                                placeholder="upozila">
                        </div>


                        <div class="my-1">

                            <label class="my-1" for="vil">Village</label>
                            <input type="text" id="vil" class="w-full rounded-md ring-0" wire:model="shopArray.village"
                                placeholder="village">
                        </div>

                        <div class="my-1">

                            <label class="my-1" for="zip">Zip</label>
                            <input type="text" id="zip" class="w-full rounded-md ring-0" wire:model="shopArray.zip"
                                placeholder="zip">
                        </div>

                        <div class="my-1">

                            <input type="text" class="w-full rounded-md ring-0" wire:model="shopArray.road_no"
                                placeholder="road no">
                        </div>
                        {{-- <div> {{$shopArray['upozila'] ?? "N/A"}}, {{$shopArray['district'] ?? "N/A"}},
                            {{$shopArray['country'] ?? "N/A"}} </div> --}}

                    </div>


                </div>
            </div>
        </div>
        <br>

        <x-primary-button class="w-full text-center flex justify-center" wire:click="updateInfo">
            update
        </x-primary-button>
    </x-dashboard.container>

    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    @script
    <script>
        document.querySelector("trix-editor").addEventListener('trix-change', ()=> {
            @this.set('shopArray.description', document.querySelector("#my_input").value);            
        })
    </script>
    @endscript
</div>