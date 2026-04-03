<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <x-dashboard.page-header>
        Edit VIP Users
        <br>
        <x-nav-link href="{{route('system.vip.users')}}"> Index <i class="fa-solid fa-arrow-right ms-2"></i>
        </x-nav-link>
    </x-dashboard.page-header>


    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex justify-between items-center text-wrap">
                        <div class="flex items-center">
                            <x-nav-link href="{{route('system.users.edit', ['id' => $vipData->user_id])}}">
                                {{$vipData->name ?? "N/A"}}</x-nav-link>
                            <div class="px-2"></div>
                            <div class="text-xs">{{$vipData->created_at?->toFormattedDateString() }}</div>
                        </div>
                        <div class="text-sm px-2 py-1 border rounded shadow bg-slate-900 text-white">
                            {{$vipData->package?->name ?? "N/A"}}
                        </div>
                    </div>
                </x-slot>
                <x-slot name="content">
                    <div class="flex text-sm ">
                        <div wire:click="updateStatusToActive()" @class(["px-2 rounded
                            cursor-pointer", "bg-indigo-800 text-white text-bold"=> $vipData->status &&
                            !$vipData->deleted_at])>Active</div>
                        <div wire:click="updateStatusToPending()" @class(["px-2 rounded cursor-pointer
                            space-x-2", "bg-indigo-800 text-white text-bold"=> !$vipData->status &&
                            !$vipData->deleted_at])>Pending</div>
                        <div wire:click="updateStatusToReject()" @class(["px-2 rounded
                            cursor-pointer", "bg-indigo-800 text-white text-bold"=> $vipData->deleted_at])>Trash</div>
                    </div>
                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>

                @if($vipData->valid_till < now() && $vipData->status)

                    <div class="px-1 rounded inline-flex bg-yellow-200 text-xs">
                        Expired
                    </div>

                    @endif
            </x-dashboard.section.inner>
            <hr />
            <x-dashboard.section.inner>
                <div class="flex justify-end items-center space-x-2">
                    {{-- button for re-calculate ref comission --}}
                    <x-secondary-button wire:click="reCalculateRefComission">
                        Re-Calculate Comission
                    </x-secondary-button>

                    {{-- button for push back ref comission --}}
                    <x-secondary-button wire:click="pushBackRefComission">
                        Push Back Comission
                    </x-secondary-button>
                </div>
            </x-dashboard.section.inner>
        </x-dashboard.section>

        {{-- user payment details --}}
        <x-dashboard.section x-data={up : false}>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Users Payment and Package
                </x-slot>
                <x-slot name="content">
                    view here vip users payment and packages informations.
                </x-slot>
            </x-dashboard.section.header>

            <div x:show="up">
                <x-dashboard.section.inner>

                    <div class="flex flex-wrap">
                        <div class="w-md py-2 border-b">
                            <div class="text-sm">
                                Payment Method
                            </div>
                            <div class="text-md">
                                {{$vipData->payment_by ?? "N/A"}}
                            </div>
                        </div>
                        <div class="w-md py-2 border-b">
                            <div class="text-sm">
                                TRX ID
                            </div>
                            <div class="text-md">
                                {{$vipData->trx ?? "N/A"}}
                            </div>
                        </div>
                        <div class="w-md py-2 border-b">
                            <div class="text-sm">
                                NID
                            </div>
                            <div class="text-md">
                                {{$vipData->nid ?? "N/A"}}
                            </div>
                        </div>
                        <div class="w-md py-2 border-b">
                            <div class="text-sm">
                                Phone
                            </div>
                            <div class="text-md">
                                {{$vipData->phone ?? "N/A"}}
                            </div>
                        </div>
                        <div class="w-md py-2 border-b">
                            <div class="text-sm">
                                Date
                            </div>
                            <div class="text-md">
                                {{$vipData->created_at?->toFormattedDateString()}}
                            </div>
                        </div>
                        <div class="w-md py-2 border-b">
                            <div class="text-sm">
                                Comission
                            </div>
                            <div class="text-md">
                                {{$vipData->comission ?? "N/A"}} TK
                            </div>
                        </div>
                        <div class="w-md py-2 border-b">

                            <div class="text-sm">
                                Reffer By
                            </div>
                            <div class="text-md">
                                {{$vipData->referBy->name ?? "N/A"}} -
                                {{$vipData->referBy->email ?? "N/A"}}
                            </div>
                        </div>
                        <div class="w-md py-2 border-b">

                            <div class="text-sm">
                                Ref Code
                            </div>
                            <div class="text-md">
                                {{$vipData->reference ?? "N/A"}}
                            </div>
                        </div>

                        {{-- <table class="w-full">
                            <thead class="text-left">
                                <th class="">Payment Method</th>
                                <th class="">TRX ID</th>
                                <th class="">NID</th>
                                <th class="">Phone</th>
                                <th class="">Time</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        {{$vipData->payment_by ?? "N/A "}}
                                    </td>
                                    <td>
                                        {{$vipData->trx ?? "N/A"}}
                                    </td>
                                    <td>
                                        {{$vipData->nid ?? "N/A"}}
                                    </td>
                                    <td>
                                        {{$vipData->phone ?? "N/A"}}
                                    </td>
                                    <td>
                                        {{$vipData->created_at?->toFormattedDateString()}}
                                    </td>
                                </tr>
                            </tbody>
                        </table> --}}
                    </div>
                </x-dashboard.section.inner>

                <div class="flex space-x-3 mt-2">
                    <img class="border rounded" src="{{{asset('storage/'. $vipData->nid_front ?? "")}}}" width="300px"
                        height="80px" alt="">
                    <img class="border rounded" src="{{{asset('storage/'. $vipData->nid_back ?? "")}}}" width="300px"
                        height="80px" alt="">
                </div>
            </div>
        </x-dashboard.section>

        {{-- package udpate --}}
        <div class=" md:flex justify-start items-start">
            <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        User VIP Package Update
                    </x-slot>
                    <x-slot name="content">
                        currently user belongs to <strong>{{$vipData->package?->name ?? "N/A" }}</strong> package.
                        Migrate to other package.
                    </x-slot>
                </x-dashboard.section.header>
                <x-dashboard.section.inner>
                    @foreach ($vips as $item)
                    <div class="flex items-center mb-3 p-2 border rounded">
                        <input type="radio" wire:model="package" value="{{$item->id}}" style="width:20px; height:20px"
                            class="rounded mr-3" id="package_{{$item->id}}">
                        <div class="flex items-center">
                            <x-input-label :value="$item->name" />
                            <i class="fa-solid fa-arrow-right px-2"></i>
                            <div>
                                {{$item->price}} TK
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <br>
                    <div class="text-end">
                        <x-secondary-button>Procced to Migrate</x-secondary-button>
                    </div>
                </x-dashboard.section.inner>
            </x-dashboard.section>

            <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        Update Task Type
                    </x-slot>
                    <x-slot name="content">
                        user currentry use <strong>{{$vipData->task_type }}.</strong>
                    </x-slot>
                </x-dashboard.section.header>
                <x-dashboard.section.inner>
                    <div class="flex flex-wrap">
                        <div class="flex items-center m-1 border rounded p-2">
                            <input type="radio" style="width:20px; height:20px" wire:model="task" value="daily"
                                class="rounded mr-3" id="valid_1">
                            <x-input-label value="Daily" />
                        </div>
                        <div class="flex items-center m-1 border rounded p-2">
                            <input type="radio" style="width:20px; height:20px" wire:model="task" value="monthly"
                                class="rounded mr-3" id="valid_2">
                            <x-input-label value="Monthly" />
                        </div>
                        <div class="flex items-center m-1 border rounded p-2">
                            <input type="radio" style="width:20px; height:20px" wire:model="task" value="disabled"
                                class="rounded mr-3" id="valid_3">
                            <x-input-label value="Disabled Task" />
                        </div>
                    </div>
                    <br>
                    <div class="text-end">
                        <x-primary-button type="button" wire:click.prevent="updateTask">
                            Update
                        </x-primary-button>
                    </div>
                </x-dashboard.section.inner>
            </x-dashboard.section>
        </div>

        {{-- validation update --}}
        <div>
            <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        Update Validation
                    </x-slot>
                    <x-slot name="content">
                        Update validation time for next 360 days, or your custom days. Give the valid day in input.
                    </x-slot>
                </x-dashboard.section.header>

                <x-dashboard.section.inner>

                    <div class="p-3 my-2 w-full bg-red-100 text-red-900 rounded">
                        <div class="p-2 rounded">
                            Package will expire on <strong> {{
                                \Carbon\Carbon::parse($vipData->valid_till)->toFormattedDateString()}} </strong>
                            ({{\Carbon\Carbon::parse($vipData->valid_till)->diffForHumans()}})
                        </div>

                        <div class="" x-show="new">
                            <x-input-label value="New Valid Days" />
                            <x-text-input wire:model.live="vlid_days" />
                        </div>
                    </div>
                    <div class="text-end">

                        <x-primary-button type="button" wire:click.prevent="updateValidity">
                            Update Validation
                        </x-primary-button>
                    </div>
                </x-dashboard.section.inner>
            </x-dashboard.section>

            @if ($vipData->deleted_at)
            <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        <div class="text-red-900">VIP in Trash</div>
                    </x-slot>
                    <x-slot name="content">
                        trashed may be restored or deleted permanently.
                    </x-slot>
                </x-dashboard.section.header>

                <x-dashboard.section.inner>
                    <x-danger-button wire:click="restore">Restore</x-danger-button>
                    <x-danger-button wire:click="delete">Permanently Delete</x-danger-button>
                </x-dashboard.section.inner>
            </x-dashboard.section>
            @endif
        </div>

        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex justify-between items-center">
                        <div>
                            User Tasks
                        </div>
                    </div>
                </x-slot>
                <x-slot name="content">
                    user tasks and earning against this packages.
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <x-nav-link-btn href="">
                    View All
                </x-nav-link-btn>
            </x-dashboard.section.inner>
        </x-dashboard.section>


    </x-dashboard.container>
</div>