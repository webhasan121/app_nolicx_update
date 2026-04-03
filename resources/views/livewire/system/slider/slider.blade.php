<div>
    <x-dashboard.page-header>
        Slider
    </x-dashboard.page-header>

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex justify-between items-center">

                        <div>

                            <x-nav-link href="?nav=web" :active="$nav=='web'">Web</x-nav-link>
                            <x-nav-link href="?nav=apps" :active="$nav=='apps'">App</x-nav-link>
                            <x-nav-link href="?nav=both" :active="$nav=='both'">Both</x-nav-link>

                        </div>

                        <x-secondary-button x-on:click="$dispatch('open-modal', 'open-slider-modal')"> <i
                                class="fas fa-plus pr-2"></i> Add</x-secondary-button>
                    </div>

                </x-slot>
                <x-slot name="content">

                </x-slot>
            </x-dashboard.section.header>


            <x-dashboard.section.inner>
                <x-dashboard.table :data="$slider">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Placement</th>
                            <th>Slides</th>
                            <th></th>
                            <th>A/C</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($slider as $item)

                        <tr>
                            <td> {{$loop->iteration}} </td>
                            <td> {{$item->name}} </td>
                            <td> {{$item->placement}} </td>
                            <td>
                                {{$item->slides->count() ?? "0"}} Slides
                            </td>
                            <td>
                                @if ($item->status)
                                <input type="checkbox" checked wire:change="updateStatusFalse({{$item->id}})" name=""
                                    style="width:20px; height:20px" id="">
                                @else
                                <input type="checkbox" wire:change="updateStatusTrue({{$item->id}})" name=""
                                    style="width:20px; height:20px" id="">

                                @endif
                                {{$item->status ? "Active" : "Deactive"}}
                            </td>
                            <td>
                                <div class="flex space-x-2">
                                    <x-danger-button wire:click="deleteSide({{$item->id}})">
                                        <i class="fas fa-trash"></i>
                                    </x-danger-button>

                                    <x-primary-button wire:click="openUpdateModal({{$item->id}})">
                                        <i class="fas fa-edit"></i>
                                    </x-primary-button>
                                    <x-nav-link href="{{route('system.slider.slides', ['id' => $item->id])}}">slides
                                    </x-nav-link>
                                </div>
                            </td>
                        </tr>

                        @endforeach
                    </tbody>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>


    <x-modal name="open-slider-modal" maxWidth="sm">
        <div class="px-2 py-2">Slider Modal</div>
        <div class="p-3">
            <strong>{{ $sler }}</strong>
            <form wire:submit.prevent="createNewSlider">
                <div class="flex">
                    <x-text-input wire:model="sliderName" class="rounded-0 py-1 w-full"
                        placeholder="Give Slider Name" />
                    <select class="py-1 rounded shadow" wire:model="sliderPlacement">
                        <option selected value="web">Web</option>
                        <option value="apps">Apps</option>
                        <option value="both">Both</option>
                    </select>
                </div>
                @error('sliderName')
                <span class="text-xs text-red-900">{{$message }}</span>
                @enderror

                {{-- <br>
                <div class="flex items-center justify-between">
                    Background Color
                    <input type="color" class="w-8 h-8 rounded shadow" wire:model.live='background_color'>
                </div> --}}
                {{-- <div>
                    @if ($sliderImage)
                    <img src="{{$sliderImage->temporaryUrl()}}" height="20" alt="" srcset="">
                    <br>
                    @endif
                    <x-text-input type="file" wire:model="sliderImage" class="w-full border" />
                </div>
                @error('sliderImage')
                <span class="text-xs text-red-900">{{$message }}</span>
                @enderror --}}
                <div class="flex justify-start items-center my-2 border-t border-b py-2">
                    <input type="checkbox" id="active" wire:model="status" width="25px" height="25px" class="me-3" />
                    <x-input-label class="py-0 my-0" for="active" value="Active Now " />
                </div>
                @error('status')
                <span class="text-xs text-red-900">{{$message }}</span>
                @enderror
                <div class="flex justify-between">

                    <x-secondary-button x-on:click="$dispatch('close-modal', 'open-slider-modal')" type="button"
                        class="mt-2">Cancel</x-secondary-button>
                    <x-primary-button class="mt-2">Add</x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="open-slides-modal" maxWidth="sm">
        <div class="px-3 py-2">Edit Slider</div>
        <div class="p-3">
            <form wire:submit.prevent="updateSlider">
                <div class="">
                    <x-input-label value="Name" />
                    <x-text-input wire:model="updateable.name" class="rounded-0 py-1 w-full"
                        placeholder="Give Slider Name" />
                </div>
                @error('sliderName')
                <span class="text-xs text-red-900">{{$message }}</span>
                @enderror


                <div class="py-2">
                    <div class="flex py-1 border rounded px-2 mb-1">
                        <input type="radio" wire:model="updateable.placement" value="web" class="h-5 w-5 me-3" id="web">
                        <label for="Web">For Web</label>
                    </div>
                    <div class="flex py-1 border rounded px-2 mb-1">
                        <input type="radio" wire:model="updateable.placement" value="apps" class="h-5 w-5 me-3"
                            id="apps">
                        <label for="Web">For Apps</label>
                    </div>
                    <div class="flex py-1 border rounded px-2 mb-1">
                        <input type="radio" wire:model="updateable.placement" value="both" class="h-5 w-5 me-3"
                            id="both">
                        <label for="Web">Both (Web & Apps) </label>
                    </div>
                </div>

                {{-- <div class="flex justify-start items-center my-2 border-t border-b py-2">
                    <input type="checkbox" id="active" wire:model="status" width="25px" height="25px" class="me-3" />
                    <x-input-label class="py-0 my-0" for="active" value="Active Now " />
                </div>
                @error('status')
                <span class="text-xs text-red-900">{{$message }}</span>
                @enderror --}}
                <div class="flex justify-between">

                    <x-secondary-button x-on:click="$dispatch('close-modal', 'open-slides-modal')" type="button"
                        class="mt-2">Cancel</x-secondary-button>
                    <x-primary-button class="mt-2">Update</x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>