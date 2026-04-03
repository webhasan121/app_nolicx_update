<div>
    <x-dashboard.section>
        <x-dashboard.section.header>
            <x-slot name="title">
                Rider Request Form
            </x-slot>

            <x-slot name="content">
                <x-nav-link-btn wire:navigate href="{{route('upgrade.rider.index')}}" class="">
                    previous request
                </x-nav-link-btn>

            </x-slot>
        </x-dashboard.section.header>
    </x-dashboard.section>

    <form wire:submit.prevent="store" content-typ="multipart/form-data" class="w-full">
        <div class="md:flex gap-2">
            <x-dashboard.section>
                <x-dashboard.section.inner>

                    <div class="p-2 flex-1">
                        <x-input-field label="Your Phone No" name="phone" error="phone" wire:model.live="phone"
                            error="phone" placeholder="Your phone No" class="w-full" />
                        <x-input-field label="Your Email" name="email" error="email" wire:model.live="email"
                            error="email" placeholder="Your email" class="w-full" />


                        <x-hr />
                        <x-input-field label="Your Family Phone No" name="otherPhone" error="otherPhone"
                            wire:model.live="otherPhone" error="otherPhone" placeholder="Your Family phone No"
                            class="w-full" />

                        <x-input-field label="Your NID No" name="nid" error="nid" wire:model.live="nid" error="nid"
                            placeholder="Your NID No" class="w-full" />

                        <x-input-file label="You NID Front Image (max 1Mb)" name="nid_photo_front"
                            error="nid_photo_front">
                            @if ($nid_photo_front)
                            <img src="{{$nid_photo_front->temporaryUrl()}}" alt="NID Front"
                                style="width: 200px; height:100px" srcset="">
                            @endif
                            <x-text-input type="file" wire:model="nid_photo_front" id="nid_front" max="1024" />
                        </x-input-file>
                        <x-input-file label="You NID Back Image (max 1Mb)" name="nid_photo_back" error="nid_photo_back">
                            @if ($nid_photo_back)
                            <img src="{{$nid_photo_back->temporaryUrl()}}" alt="NID Back"
                                style="width: 200px; height:100px" srcset="">
                            @endif
                            <x-text-input type="file" wire:model="nid_photo_back" id="nid_back" max="1024" />
                        </x-input-file>
                    </div>

                </x-dashboard.section.inner>
            </x-dashboard.section>
            <x-dashboard.section>
                <x-dashboard.section.inner>
                    <div class="p-2 flex-1">
                        <div class="p-2 rounded bg-gray-50">

                            <div>
                                <x-input-file label="Country" name="country" error="country">
                                    <select wire:model.live="country" id="country" class="w-full rounded-md ">
                                        <option value="Bangladesh">Bangladesh</option>
                                    </select>
                                </x-input-file>
                                <x-hr />
                                <x-input-file label="State" name="state" error="state_name">
                                    <select wire:model.live="state_name" id="states" class="w-full rounded-md ">
                                        <option value=""> -- Select State --</option>
                                        @foreach ($states as $state)
                                        <option value="{{$state->name}}">{{$state->name}}</option>
                                        @endforeach
                                    </select>
                                </x-input-file>
                                <x-hr />
                                <x-input-file label="City" name="city" error="city_name">
                                    <select wire:model.live="city_name" id="city" class="w-full rounded-md ">
                                        <option value=""> -- Select City --</option>
                                        @foreach ($cities as $item)
                                        <option value="{{$item->name}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </x-input-file>
                                <x-hr />
                                <x-input-file label="Area" name="targeted_area" error="area_name">

                                    <select wire:model.live="area_name" id="area" class="w-full rounded-md ">
                                        <option value=""> -- Select Area --</option>
                                        @foreach ($area as $item)
                                        <option value="{{$item->name}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </x-input-file>
                                <x-hr />
                            </div>
                            <x-input-file label="Chose Your Area" name="area_condition" error="area_condition">
                                <div class="w-48 space-y-2">

                                    <div class="flex items-center justify-start border rounded-lg shadow-sm px-3 py-2">
                                        <x-text-input style="width:20px; height:20px" type="radio" name="area_condition"
                                            :checked='true' class="mr-3 m-0" value="dhaka"
                                            wire:model.live="area_condition" id="area_condition_1" />
                                        <x-input-label for="area_condition_1" class="m-0">Inside of Dhaka
                                        </x-input-label>
                                    </div>
                                    <div class="flex items-center justify-start border rounded-lg shadow-sm px-3 py-2">
                                        <x-text-input style="width:20px; height:20px" type="radio" name="area_condition"
                                            class="mr-3 m-0" value="other" wire:model.live="area_condition"
                                            id="area_condition_2" />
                                        <x-input-label for="area_condition_2" class="m-0"> Outside Of Dhaka
                                        </x-input-label>
                                    </div>
                                </div>

                            </x-input-file>
                        </div>

                        {{-- rider vehicle info --}}
                        <x-input-file label="Vehicle Type" name="vehicle_type" error="vehicle_type">
                            <x-text-input wire:model.live="vehicle_type" placeholder="e.g. Bike, Car" />
                        </x-input-file>
                        <x-input-file label="Vehicle Number" name="vehicle_number" error="vehicle_number">
                            <x-text-input wire:model.live="vehicle_number" placeholder="e.g. Dhaka Metro 1234" />
                        </x-input-file>
                        <x-input-file label="Vehicle Model" name="vehicle_model" error="vehicle_model">
                            <x-text-input wire:model.live="vehicle_model" placeholder="e.g. Yamaha YZF-R3" />
                        </x-input-file>
                        <x-input-file label="Vehicle Color" name="vehicle_color" error="vehicle_color">
                            <x-text-input wire:model.live="vehicle_color" placeholder="e.g. Red" />
                        </x-input-file>
                    </div>
                </x-dashboard.section.inner>
            </x-dashboard.section>
        </div>

        <x-dashboard.section>
            <x-dashboard.section.inner>

                <div>
                    <x-input-file label="You Fixed Address" name="fixed_address" error="fixed_address" class="block">
                        <p class="text-xs">
                            Your permanent address based on NID. This address will be used for verification purposes. We
                            use
                            this address to verify your location and provide better service.
                        </p>
                        <textarea wire:model.live="fixed_address" class="w-full rounded-md" id=""
                            placeholder="Your Permanent Address based on NID "></textarea>
                    </x-input-file>
                    <x-input-file label="You Current Address" name="current_address" error="current_address">
                        <p class="text-xs">
                            Your current address where you are living now. You will receive the parcel from this
                            address.
                        </p>
                        <p class="text-xs">
                            Please provide any additional information about your current address that may help us verify
                            your location.
                        </p>
                        <textarea wire:model.live="current_address" class="w-full rounded-md" id=""
                            placeholder="Your Current Address"></textarea>
                    </x-input-file>
                </div>
                <x-hr />
                <x-primary-button> <i class="fas fa-file-alt pr-2"></i> Confirm</x-primary-button>

            </x-dashboard.section.inner>
        </x-dashboard.section>

    </form>



</div>