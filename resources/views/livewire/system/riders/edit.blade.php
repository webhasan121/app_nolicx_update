<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    <x-dashboard.page-header>
        Riders -
        {{$rider->user?->name ?? "N/A" }}
        <br>
        <div class="text-sm my-2">
            Area - {{$rider->area_condition}}, {{$rider->targeted_area ?? '' }}
        </div>
        <div class="text-xs">
            {{$rider->status}}
        </div>

        <div class="text-red">
            {{$rider->rejected_for}}
        </div>

        <x-nav-link :active="$nav =='user'" href="?nav=user">User</x-nav-link>
        <x-nav-link :active="$nav =='document'" href="?nav=document">Documents</x-nav-link>
        <x-nav-link :active="$nav =='delevary'" href="?nav=delevary">Delevary</x-nav-link>
    </x-dashboard.page-header>

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Rider Upate - Delevary Man
                </x-slot>
                <x-slot name='content'>
                    <x-hr />
                    <div>
                        <form wire:submit.prevent='updateStatus'>

                            <div class="flex items-center justify-between">

                                <div>
                                    <div class="">
                                        <p class="text-sm">
                                            update : {{$rider->updated_at->diffForHumans()}}
                                        </p>
                                    </div>
                                    <p class="text-sm">Current Status is : <strong> {{$rider->status}} </strong>. Change
                                        status to - </p>
                                    <select id="" wire:model="requestStatus" class="rounded-lg py-1">
                                        <option value="Select Status">-- Select -- </option>
                                        <option @if($rider->status == 'Active') selected @endif value="Active">Active
                                        </option>
                                        <option @if($rider->status == 'Pending') selected @endif value="Pending">Pending
                                        </option>
                                        <option @if($rider->status == 'Disabled') selected @endif
                                            value="Disabled">Disabled</option>
                                        <option @if($rider->status == 'Suspended') selected @endif
                                            value="Suspended">Suspended</option>
                                    </select>

                                    <div class="mt-1" x-show="sd != 'Active'">
                                        <textarea class="rounded-lg" name="" id="" rows="2"></textarea>
                                    </div>

                                </div>
                            </div>
                            <x-hr />
                            <div class="flex justify-between items-center">
                                <div class="text-md">
                                    Comission (%)
                                </div>
                                <x-text-input wire:model.lazy="comission" value="10" placeholder='' />
                            </div>
                            <x-hr />
                            <x-primary-button class="ml-2"> <i class="fas fa-sync pr-2"></i> Update </x-primary-button>
                        </form>
                    </div>
                </x-slot>
            </x-dashboard.section.header>
        </x-dashboard.section>

        @if ($nav == 'document')
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Submitted Documents
                </x-slot>
                <x-slot name="content"></x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <x-hr />
                <x-input-file label="Rider Phone" name="phone" error="phone">
                    <x-text-input type="text" name="phone" value="{{$rider->phone ?? '' }}" />
                </x-input-file>
                <x-hr />
                <x-input-file label="Rider Email" name="email" error="email">
                    <x-text-input type="text" name="email" value="{{$rider->email ?? '' }}" />
                </x-input-file>
                <x-hr />
                <x-input-file label="Rider NID" name="nid" error="nid">
                    <x-text-input type="text" name="nid" value="{{$rider->nid ?? '' }}" />
                </x-input-file>
                <x-hr />
                <x-input-file label="Rider Photo Front" name="nid_photo_front" error="nid_photo_front">

                    <div class="flex">
                        <x-image src="{{asset('storage/'. $rider->nid_photo_front ?? '')}}" alt="nid_photo_front" />
                        <x-image src="{{asset('storage/'. $rider->nid_photo_back ?? '')}}" alt="nid_photo_back" />
                    </div>
                </x-input-file>
            </x-dashboard.section.inner>
        </x-dashboard.section>


        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Rider Address and Area
                </x-slot>
                <x-slot name='content'>
                    See the rider areas about the rider address
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <x-input-file label="Rider Present Address" name="nid" error="nid">
                    <div>
                        {{$rider->current_address ?? '' }}
                    </div>
                </x-input-file>
                <x-hr />
                <x-input-file label="Rider Permanent Address" name="nid" error="nid">
                    <div>
                        {{$rider->fixed_address ?? '' }}
                    </div>
                </x-input-file>
                <x-hr />
                <x-input-file label="Rider Targetted Area" name="nid" error="nid">
                    <div>
                        {{$rider->area_condition}}, {{$rider->targeted_area ?? '' }}
                    </div>

                </x-input-file>
                <x-hr />
            </x-dashboard.section.inner>
        </x-dashboard.section>
        @endif

    </x-dashboard.container>
    @if ($nav == 'user')
    @livewire('system.users.edit', ['id' => $rider->user?->id], key($rider->user?->id))
    @endif

</div>