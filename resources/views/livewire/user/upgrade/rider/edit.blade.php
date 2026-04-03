<div>
    <x-dashboard.section>
        <x-dashboard.section.header>
            <x-slot name="title">
                Rider request
            </x-slot>
            <x-slot name="content">
                Edit and Upgrade Your Vendor Request Form <a class="border-b font-bold" wire:navigate
                    href="{{route('upgrade.rider.index')}}">Previous Request</a>
                <br>
                {{--
                <x-client.upgrade-status :upgrade="$upgrade" :$id /> --}}
                {{-- @includeIf('components.client.upgrade-status') --}}
            </x-slot>
        </x-dashboard.section.header>
    </x-dashboard.section>

    <form wire:submit.prevent="store">
        <x-dashboard.section>
            <x-dashboard.section.inner>
                <x-input-file label="You phone No" name="phone" error="phone">
                    <x-text-input class="w-full" wire:model="rider.phone" id="" placeholder="Your phone No " />
                </x-input-file>
                <x-input-file label="You email No" name="email" error="email">
                    <x-text-input type="email" class="w-full" wire:model="rider.email" id=""
                        placeholder="Your email No " />
                </x-input-file>
                <x-hr />

                <x-input-file label="You NID No" name="nid" error="nid">
                    <x-text-input class="w-full" wire:model="rider.nid" id="" placeholder="Your NID No " />
                </x-input-file>

                <x-input-file label="You NID Front Image" name="nid_photo_front" error="nid_photo_front">
                    <div>
                        <div class="flex">
                            @if($nid_photo_front)
                            <img class="mb-2" style="width:150px; height:100px"
                                src="{{$nid_photo_front->temporaryUrl()}}" alt="">
                            @endif
                            @if ($rider['nid_photo_front'])
                            <x-image class="mb-2" style="width:150px; height:100px"
                                src="{{asset('storage/' . $rider['nid_photo_front'])}}" alt="" />
                            @endif
                        </div>
                        {{-- <x-image-temp :model="$rider['nid_photo_front']" :temp="$nid_photo_front"
                            :src="asset('storage/' . $rider['nid_photo_front'])"></x-image-temp> --}}
                        <input type="file" class="w-full" wire:model="nid_photo_front" id="">
                    </div>
                </x-input-file>
                <x-input-file label="You NID Back Image" name="nid_photo_back" error="nid_photo_back">
                    <div>

                        <div class="flex">
                            @if($nid_photo_back)
                            <img class="mb-2" style="width:150px; height:100px"
                                src="{{$nid_photo_back->temporaryUrl()}}" alt="">
                            @endif
                            @if ($rider['nid_photo_back'])
                            <x-image class="mb-2" style="width:150px; height:100px"
                                src="{{asset('storage/' . $rider['nid_photo_back'])}}" alt="" />
                            @endif
                        </div>
                        {{-- <img class="mb-2" style="width:150px; height:100px"
                            src="{{asset('storage/' . $rider['nid_photo_back'])}}" alt=""> --}}
                        <input type="file" class="w-full" wire:model="nid_photo_back" id="">
                    </div>
                </x-input-file>

                <x-hr />
                <x-input-file label="You Fixed Address" name="fixed_address" error="fixed_address">
                    <textarea class="rounded-md w-full" wire:model="rider.fixed_address" id=""
                        placeholder="Your Permanent Address based on NID "></textarea>
                </x-input-file>
                <x-input-file label="You Current Address" name="current_address" error="current_address">
                    <textarea class="rounded-md w-full" wire:model="rider.current_address" id=""
                        placeholder="Your Current Address based on NID "></textarea>
                </x-input-file>
            </x-dashboard.section.inner>
        </x-dashboard.section>

        <x-dashboard.section>
            <x-dashboard.section.inner>
                <x-input-file label="Chose Your Area" name="area_condition" error="area_condition">
                    <div class="w-48 space-y-2">

                        <div class="flex items-center justify-start border rounded-lg shadow-sm px-3 py-2">
                            <x-text-input style="width:20px; height:20px" type="radio" name="area_condition"
                                :checked='true' class="mr-3 m-0" value="dhaka" wire:model.live="rider.area_condition"
                                id="area_condition_1" />
                            <x-input-label for="area_condition_1" class="m-0">Inside of Dhaka</x-input-label>
                        </div>
                        <div class="flex items-center justify-start border rounded-lg shadow-sm px-3 py-2">
                            <x-text-input style="width:20px; height:20px" type="radio" name="area_condition"
                                class="mr-3 m-0" value="other" wire:model.live="rider.area_condition"
                                id="area_condition_2" />
                            <x-input-label for="area_condition_2" class="m-0"> Outside Of Dhaka </x-input-label>
                        </div>
                    </div>

                </x-input-file>
                <x-hr />
                <div wire:transition>
                    <x-input-file label="Targetted Area" name="targeted_area" error="targeted_area">
                        {{$rider['targeted_area']}}
                    </x-input-file>
                </div>
                <x-hr />
                <x-primary-button>
                    <i class="fas fa-sync pr-2"></i> Update & Save
                </x-primary-button>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </form>
</div>