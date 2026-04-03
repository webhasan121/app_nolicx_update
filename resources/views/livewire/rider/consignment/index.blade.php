<div>
    {{-- The best athlete wants his opponent at his best. --}}


    <x-dashboard.container>

        @php
        $lt = 0;
        $earn = 0;
        @endphp

        <div class="flex justify-between items-center">
            <div class="flex gap-2 items-center">
                <select wire:model.live="status" class="py-1 mt-1 rounded" id="select_status">
                    <option value="All"> -- All -- </option>
                    <option value="Pending">Pending</option>
                    <option value="Received">Received</option>
                    <option value="Completed">Delivered</option>
                    <option value="Returned">Returned</option>
                </select>

            </div>

            {{-- <div>
                <input type="date" name="datetime" id="datetime" class="py-1" />
            </div> --}}
            <div>
                <x-primary-button @click="$dispatch('open-modal', 'filter-consignment')">
                    <i class="fas fa-filter"></i>
                </x-primary-button>
            </div>
        </div>
        {{-- The best athlete wants his opponent at his best. --}}
        @if (count($consignments) > 0)

        <div style="display:grid; grid-template-columns:repeat(auto-fit, 160px); gap:1rem;">
            @foreach ($consignments as $cod)

            <div class="relative bg-white rounded shadow text-center flex flex-col justify-between">



                <div class="py-2 bg-gray-200">
                    <h3 class="text-xs text-gray-500"> Order ID
                        <div wire:click='viewConsignment({{$cod->id}})'
                            class="cursor-pointer text-xs px-2 inline-block rounded-xl bg-indigo-900 text-white shadow">
                            View </div>
                    </h3>
                    <div class="font-bold">
                        {{$cod->order?->id}}

                    </div>
                </div>

                <div class="p-2">
                    @php
                    $totalFroNotResel = 0;
                    @endphp
                    <div class="flex justify-center items-center -space-x-2 overflow-hidden">
                        @foreach ($cod->order?->cartOrders as $item)
                        @if (!$item->product?->isResel)
                        @php
                        $totalFroNotResel += $item->total;
                        @endphp
                        <img src="{{asset('storage/' . $item->product?->thumbnail)}}"
                            class="inline-block size-10 rounded-full ring-2 ring-white outline -outline-offset-1 outline-black/5"
                            alt="" srcset="">
                        @endif
                        @endforeach
                    </div>
                </div>

                <div class="px-3 py-2">

                    <div class="text-2xl font-bold flex justify-center ">
                        {{$totalFroNotResel + $cod->system_comission }} Tk
                    </div>
                    <div class="text-sm text-gray-500 flex justify-center items-center text-center">
                        {{-- <div>
                            <sup>
                                ৳
                            </sup>
                        </div> --}}
                        <div class=" pl-1 font-bold">
                            {{$totalFroNotResel ?? "N/A"}}
                        </div>
                        <div class="px-1" style="line-height:8px">
                            +
                        </div>
                        <div class="flex justify-center items-cenrer">
                            <div class="">
                                {{$cod->system_comission ?? "N/A"}}
                            </div>
                        </div>
                    </div>

                </div>

                <div class="px-3 py-2">
                    <p class="text-xswwww">
                        {{$cod->created_at->toFormattedDateString()}}
                    </p>
                    <div class="text-xs text-gray-500">
                        <i class="fas fa-map-marker-alt pr-1"></i>
                        {{-- {{$cod->upozila}}, {{$cod->district}} --}}
                        {{$cod->order?->location ?? "N/A"}}
                    </div>
                    {{-- <div class="text-gray-500 text-sm">
                        {{$cod->number}}
                    </div> --}}
                </div>
                {{-- @if ($cod->order?->status == 'Delivery') --}}
                @if ($cod->status == 'Pending')
                <div class="pb-2">
                    <button class="rounded border px-2 py-1 bg-indigo-900 text-white shadow text-sm"
                        wire:click.prevent="confirmOrder({{$cod->id}}, 'Received')"
                        wire:confirm='Are you received the purcel from sender?'>
                        Mark as Received
                    </button>
                </div>

                <div class="absolute p-1" style="top:43px; left:50%; transform:translatex(-50%)">
                    <div class="text-xs px-2 rounded-xl bg-white shadow"> Pending </div>
                </div>
                @endif

                @if ($cod->status == 'Received')
                <div class="pb-2">
                    <button class=" rounded border px-2 py-1 bg-indigo-900 text-white shadow text-sm"
                        wire:click="confirmOrder({{$cod->id}}, 'Completed')">
                        Mark as Delivered
                    </button>
                </div>

                <div class="absolute p-1" style="top:43px; left:50%; transform:translatex(-50%)">
                    <div class="text-xs px-2 rounded-xl bg-indigo-200 shadow"> Received </div>
                </div>
                @endif
                @if ($cod->status == 'Completed')
                <p class="p-2 bg-green-200 text-green-900 font-bold">
                    <i class="fas fa-check-circle ps-2"></i> Earn ({{$cod->order->shipping}}TK)
                </p>
                <div class="absolute p-1" style="top:43px; left:50%; transform:translatex(-50%)">
                    <div class="text-xs px-2 rounded-xl bg-green-900 text-white shadow"> Done </div>
                </div>
                @endif

            </div>
            @php
            $lt += $totalFroNotResel;
            $earn += $cod->order->shipping;
            @endphp
            @endforeach

        </div>
        <table class="w-full border p-2">
            <tr class="p-2">
                <td>
                    Delivery
                </td>
                <td>
                    {{$lt}} TK
                </td>
            </tr>
            <tr class="p-2">
                <td>
                    Earn
                </td>
                <td>
                    {{$earn}}
                </td>
            </tr>
        </table>

        @else
        <p class="bg-gray-50 p-1">No Consignment Found !</p>
        @endif

        <x-modal name="filter-consignment" maxWidth="md">
            <div class="p-4 border-b flex justify-between items-center">
                Filter
                <div @click="$dispatch('close-modal', 'filter-consignment')">
                    <i class="fas fa-close"></i>
                </div>
            </div>
            <div class="p-4">
                <div class="md:flex justify-between items-start">
                    <div class="p-2">
                        <div class="flex p-2 border-b mb-1">
                            <input type="radio" wire:model.live='status' name="status" id="All" value="All"
                                style="width:20px; height:20px" class="mr-3"> All
                        </div>
                        <div class="flex p-2 border-b mb-1">
                            <input type="radio" wire:model.live='status' id="pending" value="Pending"
                                style="width:20px; height:20px" class="mr-3"> Pending
                        </div>
                        <div class="flex p-2 border-b mb-1">
                            <input type="radio" wire:model.live='status' id="received" value="Received"
                                style="width:20px; height:20px" class="mr-3"> Rececived
                        </div>
                        <div class="flex p-2 border-b mb-1">
                            <input type="radio" wire:model.live='status' id="Completed" value="Completed"
                                style="width:20px; height:20px" class="mr-3"> Delivered
                        </div>
                        <div class="flex p-2 border-b mb-1">
                            <input type="radio" wire:model.live='status' id="returned" value="Returned"
                                style="width:20px; height:20px" class="mr-3"> Returned
                        </div>
                    </div>
                    <div class="p-2">
                        <div class="flex p-2 border-b mb-1">
                            <input type="radio" wire:model.live='created_at' name="date" id="today" value="Today"
                                style="width:20px; height:20px" class="mr-3" id=""> Today
                        </div>
                        <div class="flex p-2 border-b mb-1">
                            <input type="radio" wire:model.live='created_at' name="date" id="yestarday"
                                value="Yesterday" style="width:20px; height:20px" class="mr-3" id=""> Yestarday
                        </div>
                        <div class="flex p-2 border-b mb-1">
                            <input type="radio" wire:model.live='created_at' name="date" id="weak" value="Weak"
                                style="width:20px; height:20px" class="mr-3" id=""> This Weak
                        </div>
                        <div class="flex p-2 border-b mb-1">
                            <input type="radio" wire:model.live='created_at' name="date" id="month" value="Month"
                                style="width:20px; height:20px" class="mr-3" id=""> This Monty
                        </div>
                        <div class="flex p-2 border-b mb-1">
                            <input type="radio" wire:model.live='created_at' name="date" id="between" value="between"
                                style="width:20px; height:20px" class="mr-3" id=""> Date Between
                        </div>
                        <div class="flex p-2 bg-gray-100 border-b mb-1">
                            <input type="radio" wire:model.live='created_at' name="date" id="any" value="any"
                                style="width:20px; height:20px" class="mr-3" id=""> Any Time
                        </div>
                        <div wire:show="created_at == 'between'">
                            <hr />
                            <div class="mb-1">
                                <p>From </p>
                                <input type="date" name="start_time" wire:model.live='start_time' id="start_time">
                            </div>
                            <div class="mb-1">
                                <p>to </p>
                                <input type="date" name="end_time" wire:model.live='end_time' id="end_time">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-modal>
    </x-dashboard.container>
</div>