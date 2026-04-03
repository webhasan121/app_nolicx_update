<div>

    <div class="border rounded-md mb-2 shadow " x-data="{showSlider : true}">
        <div class="px-3 py-2 flex justify-between items-center" @click="showSlider = !showSlider">

            <div class="flex items-center">
                <div class=" px-2 bg-gray-200 rounded mr-3">
                    {{$index ?? ''}}
                </div>
                <strong class="text-lg">
                    {{$slider['name']}}
                </strong>
            </div>

            <div>
                <i class="fas fa-angle-down"></i>
            </div>

        </div>

        <div x-show="showSlider" x-transition>
            <hr class="">

            <div class="px-3">
                <div class="lg:flex items-start justify-between p-2">

                    <div class="p-3">
                        <div class="flex justify-start items-start my-2 border-b py-2">
                            <input type="checkbox" id="home_page" wire:model.live='slider.home'
                                @checked($slider['home']) style="width:20px; height:20px" class="me-3" />
                            <div>
                                <x-input-label class="py-0 my-0" for="home_page" value="Home Page" />
                                <p class="text-xs">
                                    If checked, Banner will display on <strong>Home Page</strong>.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-start items-start my-2 border-b py-2">
                            <input type="checkbox" id="about_page" wire:model.live='slider.about'
                                @checked($slider['about']) style="width:20px; height:20px" class="me-3" />
                            <div>
                                <x-input-label class="py-0 my-0" for="about_page" value="About Page" />
                                <p class="text-xs">
                                    If checked, Banner will display on <strong>About-Us Page</strong>.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-start items-start my-2 border-b py-2">
                            <input type="checkbox" id="order_page" @checked($slider['order'])
                                wire:model.live="slider.order" style="width:20px; height:20px" class="me-3" />
                            <div>
                                <x-input-label class="py-0 my-0" for="order_page" value="Order Page" />
                                <p class="text-xs">
                                    If checked, Banner will display on <strong>Order Page</strong>.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-start items-start my-2 border-b py-2">
                            <input type="checkbox" id="product_details_page" style="width:20px; height:20px"
                                @checked($slider['product_details']) wire:model.live='slider.product_details'
                                class="me-3" />
                            <div>
                                <x-input-label class="py-0 my-0" for="product_details_page"
                                    value="Product Details Page" />
                                <p class="text-xs">
                                    If checked, Banner will display on <strong>Product Details Page</strong>.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-start items-start my-2 border-b py-2">
                            <input type="checkbox" id="categories_product_page" @checked($slider['categories_product'])
                                wire:model.live='slider.categories_product' style="width:20px; height:20px"
                                class="me-3" />
                            <div>
                                <x-input-label class="py-0 my-0" for="categories_product_page"
                                    value="Categories Product Page" />
                                <p class="text-xs">
                                    If checked, Banner will display on <strong>Categories Product Page</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="p-3 bg-gray-100">
                        <div class="flex justify-start items-start my-2 border-b py-2">
                            <input type="checkbox" id="page_top" @checked($slider['placement_top'])
                                wire:model.live='slider.placement_top' style="width:20px; height:20px" class="me-3" />
                            <div>
                                <x-input-label class="py-0 my-0" for="page_top" value="Top" />
                                <p class="text-xs">
                                    If checked, Banner will display on <strong>Top Of The Page</strong>.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-start items-start my-2 border-b py-2">
                            <input type="checkbox" id="page_middle" @checked($slider['placement_middle'])
                                wire:model.live='slider.placement_middle' style="width:20px; height:20px"
                                class="me-3" />
                            <div>
                                <x-input-label class="py-0 my-0" for="page_middle" value="Middle" />
                                <p class="text-xs">
                                    If checked, Banner will display on <strong>Middle Of The Page</strong>.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-start items-center my-2 border- py-2">
                            <input type="checkbox" id="page_bottom" @checked($slider['placement_bottom'])
                                wire:model.live='slider.placement_bottom' style="width:20px; height:20px"
                                class="me-3" />
                            <div>
                                <x-input-label class="py-0 my-0" for="page_bottom" value="Bottom" />
                                <p class="text-xs">
                                    If checked, Banner will display on <strong>Bottom Of The Page</strong>.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="p-2 bg-gray-100">
                <div class="flex justify-between items-center px-4">
                    <div>
                        @if ($slider['status'])
                        <input type="checkbox" checked wire:change="updateStatusFalse({{$id}})" name=""
                            style="width:20px; height:20px" id="">
                        @else
                        <input type="checkbox" wire:change="updateStatusTrue({{$id}})" style="width:20px; height:20px"
                            id="">
                        @endif

                        {{$slider['status'] ? "Active" : "Deactive"}}
                    </div>
                    <x-nav-link-btn href="{{route('system.static-slider.slides', ['id' => $slider['id']])}}">
                        <i class="fas fa-angle-right mr-2"></i> slides
                    </x-nav-link-btn>
                </div>
            </div>

            <hr class="" />

            {{-- slides --}}
            {{-- <div>
                @livewire('system.static-slider.sliders', ['id' => $id], key($id))
            </div> --}}
            {{-- slides --}}
        </div>


        <div class="px-3 flex justify-between items-center py-2 flex space-x-2">

            <x-primary-button wire:click='updateSlider'>
                <i class="fas fa-sync mr-2"></i> Update & Save
            </x-primary-button>

            <x-danger-button wire:confirm='Are your sure want to delete ?' type="button"
                wire:click.prevent="deleteSide">
                <i class="fas fa-trash mr-2"></i> delete
            </x-danger-button>


            {{-- <x-nav-link href="{{route('system.slider.slides', ['id' => $item['id']])}}">slides
            </x-nav-link> --}}
        </div>
    </div>


    <x-modal name="open-slides-modal">
        <div class="px-3 py-2">Edit Slider</div>
        <div class="p-3">
            <form wire:submit.prevent="updateSlider">
                <div class="">
                    <x-input-label value="Name" />
                    <x-text-input wire:model="slider.name" class="rounded-0 py-1 w-full"
                        placeholder="Give Slider Name" />
                </div>
                @error('sliderName')
                <span class="text-xs text-red-900">{{$message }}</span>
                @enderror

                <div class="lg:flex items-start justify-between p-2">

                    <div class="p-3">
                        <div class="flex justify-start items-start my-2 border-b py-2">
                            <input type="checkbox" id="home_page" wire:model="slider.home"
                                style="width:20px; height:20px" class="me-3" />
                            <div>
                                <x-input-label class="py-0 my-0" for="home_page" value="Home Page" />
                                <p class="text-xs">
                                    If checked, Banner will display on <strong>Home Page</strong>.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-start items-start my-2 border-b py-2">
                            <input type="checkbox" id="about_page" wire:model="slider.about"
                                style="width:20px; height:20px" class="me-3" />
                            <div>
                                <x-input-label class="py-0 my-0" for="about_page" value="About Page" />
                                <p class="text-xs">
                                    If checked, Banner will display on <strong>About-Us Page</strong>.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-start items-start my-2 border-b py-2">
                            <input type="checkbox" id="order_page" wire:model="slider.order"
                                style="width:20px; height:20px" class="me-3" />
                            <div>
                                <x-input-label class="py-0 my-0" for="order_page" value="Order Page" />
                                <p class="text-xs">
                                    If checked, Banner will display on <strong>Order Page</strong>.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-start items-start my-2 border-b py-2">
                            <input type="checkbox" id="product_details_page" wire:model="slider.product_details"
                                width="25px" height="25px" class="me-3" />
                            <div>
                                <x-input-label class="py-0 my-0" for="product_details_page"
                                    value="Product Details Page" />
                                <p class="text-xs">
                                    If checked, Banner will display on <strong>Product Details Page</strong>.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-start items-start my-2 border-b py-2">
                            <input type="checkbox" id="categories_product_page" wire:model="slider.categories_product"
                                style="width:20px; height:20px" class="me-3" />
                            <div>
                                <x-input-label class="py-0 my-0" for="categories_product_page"
                                    value="Categories Product Page" />
                                <p class="text-xs">
                                    If checked, Banner will display on <strong>Categories Product Page</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="p-3 bg-gray-100">
                        <div class="flex justify-start items-start my-2 border-b py-2">
                            <input type="checkbox" id="page_top" wire:model="slider.top" style="width:20px; height:20px"
                                class="me-3" />
                            <div>
                                <x-input-label class="py-0 my-0" for="page_top" value="Top" />
                                <p class="text-xs">
                                    If checked, Banner will display on <strong>Top Of The Page</strong>.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-start items-start my-2 border-b py-2">
                            <input type="checkbox" id="page_middle" wire:model="slider.middle"
                                style="width:20px; height:20px" class="me-3" />
                            <div>
                                <x-input-label class="py-0 my-0" for="page_middle" value="Middle" />
                                <p class="text-xs">
                                    If checked, Banner will display on <strong>Middle Of The Page</strong>.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-start items-center my-2 border- py-2">
                            <input type="checkbox" id="page_bottom" wire:model="slider.bottom"
                                style="width:20px; height:20px" class="me-3" />
                            <div>
                                <x-input-label class="py-0 my-0" for="page_bottom" value="Bottom" />
                                <p class="text-xs">
                                    If checked, Banner will display on <strong>Bottom Of The Page</strong>.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- <div class="py-2">
                    <div class="flex py-1 border rounded px-2 mb-1">
                        <input type="radio" wire:model="slider.placement" value="web" class="h-5 w-5 me-3" id="web">
                        <label for="Web">For Web</label>
                    </div>
                    <div class="flex py-1 border rounded px-2 mb-1">
                        <input type="radio" wire:model="slider.placement" value="apps" class="h-5 w-5 me-3" id="apps">
                        <label for="Web">For Apps</label>
                    </div>
                    <div class="flex py-1 border rounded px-2 mb-1">
                        <input type="radio" wire:model="slider.placement" value="both" class="h-5 w-5 me-3" id="both">
                        <label for="Web">Both (Web & Apps) </label>
                    </div>
                </div> --}}

                {{-- <div class="flex justify-start items-start my-2 border-b py-2">
                    <input type="checkbox" id="active" wire:model="status" style="width:20px; height:20px"
                        class="me-3" />
                    <x-input-label class="py-0 my-0" for="active" value="Active Now " />
                </div>
                @error('status')
                <span class="text-xs text-red-900">{{$message }}</span>
                @enderror --}}
                <div class="flex justify-between">

                    <x-secondary-button x-on:click="$dispatch('close-modal', 'open-slides-modal')" type="button"
                        class="mt-2">Cancel</x-secondary-button>
                    <x-primary-button class="mt-2"> <i class="fas fa-sync pr-2"></i> Save & Update</x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>