<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    <x-dashboard.page-header>
        Geolocation - Cities
    </x-dashboard.page-header>

    <x-dashboard.container>
        <div class="flex items-center gap-2">
            <x-nav-link-btn href="{{route('system.geolocations.countries')}}">Countries</x-nav-link-btn>
            <x-nav-link-btn href="{{route('system.geolocations.states')}}">States</x-nav-link-btn>
            <x-nav-link-btn href="{{route('system.geolocations.cities')}}">Cities</x-nav-link-btn>
            <x-nav-link-btn href="{{route('system.geolocations.area')}}">Areas</x-nav-link-btn>
        </div>

    </x-dashboard.container>
    
    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex justify-between items-center">

                        <div class="flex gap-2">
                            <div>
                                <x-input-label value="Country" />
                                <select wire:model.live="country" class="py-1 rounded-md">
                                    <option value=""> -- Country -- </option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" >{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label value="State" />
                                <select wire:model.live="state_id" class="py-1 rounded-md" id="selectState">
                                    <option value=""> -- State -- </option>
                                    @foreach ($states as $item)
                                    <option value="{{$item->id}}"> {{$item->name}} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label value="City" />
                                <select wire:model.live="city_id" class="py-1 rounded-md" id="selectState">
                                    <option value=""> -- City -- </option>
                                    @foreach ($cities as $item)
                                    <option value="{{$item->id}}"> {{$item->name}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <x-primary-button @click="$dispatch('open-modal', 'newAreaModal')">
                            <i class="fas fa-plus mr-2"></i> Area
                        </x-primary-button>
                    </div>
                </x-slot>
                <x-slot name="content">

                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                @foreach ($areas as $item)
                <div class="flex justify-between items-center mb-2 p-2 shadow">
                    <div class="flex items-center">
                        <div class="mr-2">
                            {{$loop->iteration}}
                        </div>
                        <div>
                            {{$item->name}}
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <x-danger-button wire:click='deleteCity({{$item->id}})'>
                            <i class="fas fa-trash"></i>
                        </x-danger-button>
                        <x-nav-link-btn href="">
                            <i class="fas fa-angle-right"></i>
                        </x-nav-link-btn>
                    </div>
                </div>
                @endforeach

                @if ($state_id && $city_id)

                <div class="p-2 rounded-md border ">
                    <p>Area Name</p>
                    <x-text-input wire:model.live='area_name' placeholder="Area Name" class="w-full"></x-text-input>
                    @error('area_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <div class="flex items-center justify-end my-2">
                        <x-primary-button wire:click='newArea'>Add</x-primary-button>
                    </div>
                </div>
                @endif
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>

    <x-modal name="newAreaModal">
        <div class="p-3">
            Add New City
        </div>
        <hr class="my-2">

        <div class="p-3">
            <form wire:submit.prevent="newArea">

                <div class="mb-2 flex items-center gap-2">
                    <div class="mb-3">
                        <x-input-label value="Country" />
                        <select wire:model.live="country" class="py-1 rounded-md">
                            <option value=""> -- Country -- </option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" >{{ $country->name }}</option>
                            @endforeach
                        </select>
                        @error('country_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror

                    </div>
                    <div class="mb-3">
                        <x-input-label value="State" />
                        <select wire:model.live="state_id" class="py-1 rounded-md w-full">
                            <option value=""> -- Select State -- </option>
                            @foreach ($states as $item)
                            <option value="{{$item->id}}"> {{$item->name}} </option>
                            @endforeach
                        </select>
                        @error('state_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <x-input-label value="City" />
                        <select wire:model.live="city_id" class="py-1 rounded-md" id="selectState">
                            <option value=""> -- City -- </option>
                            @foreach ($cities as $item)
                                <option value="{{$item->id}}"> {{$item->name}} </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <x-input-label value="Area Name" />
                    <x-text-input type="text" wire:model.live="area_name" class="w-full"
                        placeholder="Enter area Name" />
                    @error('area_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>


                <div class="flex justify-end">
                    <x-primary-button type="submit">
                        Save Area
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>