<div x-data="{showSlides : true}">

    <x-dashboard.page-header>
        Static Slider-Slides #{{$id}}
    </x-dashboard.page-header>
    {{-- Do your work, then step back. --}}

    <x-dashboard.container>
        <x-dashboard.section>

            <div class="py-2 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <p>Slides</p>
                    <x-primary-button @click="$dispatch('open-modal', 'slides-create-modal')">
                        <i class="fas fa-plus mr-2"></i> Image
                    </x-primary-button>
                </div>
                <div @click="showSlides = !showSlides">
                    <i class="fas fa-angle-down"></i>
                </div>
            </div>
            <hr>

            <div class="py-3">

                <div x-show="showSlides" x-transition class="p-3">
                    <hr>
                    @foreach ($slides as $item)

                    <div class="p-1 rounded-md shadow mb-2 relative">
                        <img src=" {{asset('storage/' . $item->image)}} " class="w-full rounded-md" alt="">

                        <div class="absolute bottom-0 left-0 w-full bg-gray-100/50 p-3">
                            <div class=" p-3">
                                <div class="mb-1">
                                    <p class="text-xs font-bold"> Action URL</p>
                                    <p class="text-sm text-gray-600"> {{$item->action_url ?? "N\A" }} </p>
                                </div>

                                <div class="inline-block px-3 py-1 text-xs rounded-md shadow-md bg-red-400 hover:bg-red-700 text-white"
                                    wire:click='deleteImage({{$item->id}})'>
                                    <i class="fas fa-trash mr-2"></i> Erase
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </x-dashboard.section>
    </x-dashboard.container>



    <x-modal name="slides-create-modal">
        <div class="p-3">
            <div class="flex items-center justify-between">
                <div>
                    Add Slides Image
                </div>

                <div @click="$dispatch('close-modal', 'slides-create-modal')">
                    <i class="fas fa-times"></i>
                </div>
            </div>
        </div>
        <hr>
        <div class="p-3">
            @if ($image)
            <img src=" {{$image->temporaryURL()}} " class="rounded shadow w-full" alt="">
            @endif
            <br>
            <x-input-label value="Slide Image" />
            <div class="relative">
                <label for="slides" class="fas fa-upload"></label>
                <x-text-input type="file" wire:model.live='image' class="absolute hidden" id="slides" />
                @error('image')
                <p class="text-red-400"> {{$message}} </p>
                @enderror
            </div>
            <br>
            <div>
                <x-input-label value="Action URL" />
                <x-text-input type="text" placeholder="action url" wire:model.live='url' class="w-full" />
            </div>
        </div>
        <hr>
        <div class="p-3 text-end flex justify-end items-center space-x-2">
            <x-secondary-button @click="$dispatch('close-modal', 'slides-create-modal')"> <i
                    class="fas fa-times mr-2"></i>
                close </x-secondary-button>
            <x-primary-button wire:click='createSlides'> <i class="fas fa-file mr-2"></i> Save </x-primary-button>
        </div>
    </x-modal>




</div>