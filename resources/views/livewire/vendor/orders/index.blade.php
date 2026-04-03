<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <x-dashboard.page-header>
        Orders
        <br>

        @if (auth()->user()->active_nav == 'reseller')
        <div>
            <x-nav-link href="{{route('vendor.orders.index')}}" :active="request()->routeIs('vendor.orders.*')"> User
                Orders
            </x-nav-link>
            <x-nav-link href="{{route('reseller.resel-order.index')}}"
                :active="request()->routeIs('reseller.resel-order.*')"> My Resel Order</x-nav-link>
        </div>

        @endif

    </x-dashboard.page-header>


    <x-dashboard.container>
        <x-dashboard.overview.section>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Orders
                </x-slot>
                <x-slot name="content">
                    {{auth()->user()->orderToMe()->where(['belongs_to_type' => $account])->count() ?? "0"}}
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Pending
                </x-slot>
                <x-slot name="content">
                    {{auth()->user()->orderToMe()->where(['belongs_to_type' => $account, 'status' =>
                    'Pending'])->count() ?? "0"}}
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Cancel
                </x-slot>
                <x-slot name="content">
                    {{auth()->user()->orderToMe()->where(['belongs_to_type' => $account, 'status' => 'Cancel'])->count()
                    ?? "0"}}
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Cancel by User
                </x-slot>
                <x-slot name="content">
                    {{auth()->user()->orderToMe()->where(['belongs_to_type' => $account, 'status' =>
                    'Cancelled'])->count() ?? "0"}}
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Accepted
                </x-slot>
                <x-slot name="content">
                    {{auth()->user()->orderToMe()->where(['belongs_to_type' => $account, 'status' => 'Accept'])->count()
                    ?? "0"}}
                </x-slot>
            </x-dashboard.overview.div>

        </x-dashboard.overview.section>


        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex justify-start items-center space-x-2">
                        <x-secondary-button x-on:click.prevent="$dispatch('open-modal', 'filter-order')">
                            <i class="fas fa-filter pr-2"></i> Filter
                        </x-secondary-button>
                        <x-dropdown>
                            <x-slot name="trigger">
                                <x-secondary-button class="inline-flex items-center ">
                                    Delivery <i class="fas fa-angle-down ps-2"></i>
                                </x-secondary-button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="flex items-center w-full p-2 text-sm">
                                    <input type="radio" style="width:20px; height:20px" class="mr-2"
                                        wire:model.live="delivery" value="all"> Not Defined
                                </div>
                                <hr />
                                <div class="flex items-center w-full p-2 text-sm">
                                    <input type="radio" style="width:20px; height:20px" class="mr-2"
                                        wire:model.live="delivery" value="cash"> Home Delivery
                                </div>
                                <hr />
                                <div class="flex items-center w-full p-2 text-sm">
                                    <input type="radio" style="width:20px; height:20px" class="mr-2"
                                        wire:model.live="delivery" value="courier"> Courier Delivery
                                </div>
                                <hr />
                                <div class="flex items-center w-full p-2 text-sm">
                                    <input type="radio" style="width:20px; height:20px" class="mr-2"
                                        wire:model.live="delivery" value="hand"> Hand-to-Hand
                                </div>
                            </x-slot>
                        </x-dropdown>

                        <x-dropdown>
                            <x-slot name="trigger">
                                <x-secondary-button>
                                    Area <i class="fas fa-angle-down ps-2"></i>
                                </x-secondary-button>
                            </x-slot>
                            <x-slot name="content">
                                <div class="flex items-center mb-2 rounded-md border p-2 text-sm">
                                    <input id="home_del" wire:model.live='area' value="all" type="radio" name=""
                                        class="w-5 h-5 p-0 m-0 mr-3" id="">
                                    <label for="home_del" class="p-0 m-0"> Both </label>
                                </div>
                                <div class="flex items-center mb-2 rounded-md border p-2 text-sm">
                                    <input id="home_del" wire:model.live='area' value="Dhaka" type="radio" name=""
                                        class="w-5 h-5 p-0 m-0 mr-3" id="">
                                    <label for="home_del" class="p-0 m-0"> Inside Dhaka </label>
                                </div>
                                <div class="flex items-center mb-2 rounded-md border p-2 text-sm">
                                    <input id="home_del" wire:model.live='area' value="Other" type="radio" name=""
                                        class="w-5 h-5 p-0 m-0 mr-3" id="">
                                    <label for="home_del" class="p-0 m-0"> Outside of Dhaka </label>
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </x-slot>
                <x-slot name="content">
                    <div class="flex justify-between">
                        <div>
                            <x-nav-link href="?nav=All" :active="$nav == 'All'">All </x-nav-link>
                            <x-nav-link href="?nav=Pending" :active="$nav == 'Pending'">Pending </x-nav-link>
                            <x-nav-link href="?nav=Accept" :active="$nav == 'Accept'">Accept</x-nav-link>
                            <x-nav-link href="?nav=Picked" :active="$nav == 'Picked'">Picked</x-nav-link>
                            <x-nav-link href="?nav=Delivery" :active="$nav == 'Delivery'">Delivery</x-nav-link>
                            <x-nav-link href="?nav=Delivered" :active="$nav == 'Delivered'">Delivered</x-nav-link>
                            <x-nav-link href="?nav=Confirm" :active="$nav == 'Confirm'">Confirm</x-nav-link>
                            <x-nav-link href="?nav=Hold" :active="$nav == 'Hold'">Hold</x-nav-link>
                            <x-nav-link href="?nav=Reject" :active="$nav == 'Reject'">Reject</x-nav-link>
                            <x-nav-link href="?nav=Cancelled" :active="$nav == 'Cancelled'">Cancel by User</x-nav-link>
                        </div>

                        <x-nav-link href="?nav=Trash" :active="$nav == 'Trash'">Trash</x-nav-link>

                    </div>
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>

                <x-dashboard.foreach :data="$data">

                    {{$data->links()}}

                    <x-dashboard.table>
                        <thead>
                            <tr class="">
                                <th colspan="3"> {{count($data)}} Products </th>
                                <th>
                                    {{$data->sum('total')}} TK
                                </th>
                            </tr>
                        </thead>
                    </x-dashboard.table>

                    <x-dashboard.table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th></th>
                                <th>ID</th>
                                <th>Pd</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Shipping</th>
                                <th>Contact</th>
                                <th>Com</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($data as $item)
                            <tr>
                                <td> {{$loop->iteration}} </td>
                                <td>
                                    <x-nav-link href="{{route('vendor.orders.view', ['order' => $item->id])}}"> view
                                    </x-nav-link>
                                    <x-nav-link href="{{route('vendor.orders.cprint', ['order' => $item->id])}}"> Pint
                                    </x-nav-link>
                                </td>
                                <td> {{$item->id ?? "N/A"}} </td>

                                <td>
                                    {{$item->cartOrders()->count() ?? "N/A"}} / {{$item->quantity ?? "N/A"}}
                                </td>

                                <td>
                                    {{$item->total ?? "N/A"}} <br> <span class="text-xs">+ {{$item->shipping}}</span>
                                </td>
                                <td>
                                    {{-- {{$item->status ?? "Pending"}} --}}
                                    {{-- badge --}}
                                    @if ($item->status == 'Pending')
                                    <span
                                        class="text-xs p-1 border rounded-md bg-yellow-200 text-yellow-900">Pending</span>
                                    @elseif ($item->status == 'Accept')
                                    <span
                                        class="text-xs p-1 border rounded-md bg-green-200 text-green-900">Accept</span>
                                    @elseif ($item->status == 'Picked')
                                    <span class="text-xs p-1 border rounded-md bg-lime-200 text-lime-900">Picked</span>
                                    @elseif ($item->status == 'Delivery')
                                    <span class="text-xs p-1 border rounded-md bg-sky-200 text-sky-900">Delivery</span>
                                    @elseif ($item->status == 'Delivered')
                                    <span
                                        class="text-xs p-1 border rounded-md bg-blue-200 text-blue-900">Delivered</span>
                                    @elseif ($item->status == 'Confirm')
                                    <span
                                        class="text-xs p-1 border rounded-md bg-indigo-200 text-indigo-900">Confirm</span>
                                    @elseif ($item->status == 'Hold')
                                    <span class="text-xs p-1 border rounded-md bg-gray-200 text-gray-900">Hold</span>
                                    @elseif ($item->status == 'Reject')
                                    <span class="text-xs p-1 border rounded-md bg-red-200 text-red-900">Reject</span>
                                    @elseif ($item->status == 'Cancelled')
                                    <span class="text-xs p-1 border rounded-md bg-red-200 text-red-900">Cancelled</span>
                                    @else
                                    <span class="text-xs p-1 border rounded-md bg-gray-200 text-gray-900">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-nowarp text-xs">
                                        <div>
                                            {{$item->created_at->diffForHumans()}}
                                        </div>
                                        <div class="text-xs">
                                            {{$item->created_at->toFormattedDateString()}}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex space-x-1">
                                        <p
                                            class="text-xs px-1 rounded {{ $item->delevery == 'cash' ? 'bg-green-200' : 'bg-blue-200' }} ">
                                            {{$item->delevery}}
                                        </p>
                                        {{-- <p class="border px-2 rounded bg-gray-900 text-white inline-block bold">{{
                                            $item->area_condition }}
                                        </p> --}}
                                    </div>
                                    <p class="text-xs">
                                        {{$item->location}}
                                    </p>
                                </td>
                                <td>
                                    <span class="text-xs">
                                        <div class="text-xs"> {{$item->user?->name}} </div>
                                        {{$item->number ?? "N/A"}}

                                    </span>
                                </td>
                                <th>
                                    {{ $item->comissionsInfo?->sum('take_comission') }}
                                </th>
                            </tr>
                            @endforeach
                        </tbody>
                    </x-dashboard.table>
                </x-dashboard.foreach>
            </x-dashboard.section.inner>

        </x-dashboard.section>
    </x-dashboard.container>

    <x-modal name="filter-order" maxWidth="xl">
        <div class="p-2">
            <div>
                Filter
            </div>
            <x-hr />
            <div class="md:flex justify-between">
                <div>
                    <div>

                        <div>
                            Delevery Type
                        </div>
                        <div class="px-2">
                            <div class="flex items-center w-full p-2 text-sm">
                                <input type="radio" style="width:20px; height:20px" class="mr-2"
                                    wire:model.live="delivery" value="all"> Not Defined
                            </div>
                            <hr />
                            <div class="flex items-center w-full p-2 text-sm">
                                <input type="radio" style="width:20px; height:20px" class="mr-2"
                                    wire:model.live="delivery" value="cash"> Home
                                Delivery
                            </div>
                            <hr />
                            <div class="flex items-center w-full p-2 text-sm">
                                <input type="radio" style="width:20px; height:20px" class="mr-2"
                                    wire:model.live="delivery" value="courier"> Courier
                                Delivery
                            </div>
                            <hr />
                            <div class="flex items-center w-full p-2 text-sm">
                                <input type="radio" style="width:20px; height:20px" class="mr-2"
                                    wire:model.live="delivery" value="hand">
                                Hand-to-Hand
                            </div>
                        </div>

                    </div>
                </div>

                <div class="mt-2 w-1/2">

                    <div class=" border rounded-md">
                        <div class=" p-2 ">

                            <div class="flex items-center w-full p-2 text-sm">
                                <input type="radio" style="width:20px; height:20px" class="mr-2"
                                    wire:model.live="create" value="all">All Time
                            </div>
                            <hr />
                            <div class="flex items-center w-full p-2 text-sm">
                                <input type="radio" style="width:20px; height:20px" class="mr-2"
                                    wire:model.live="create" value="day">From First Date
                            </div>
                            <hr />
                            <div class="flex items-center w-full p-2 text-sm">
                                <input type="radio" style="width:20px; height:20px" class="mr-2"
                                    wire:model.live="create" value="between">Between in Range
                            </div>


                        </div>

                        <div class="space-y-2 p-2 ">
                            <div>
                                First Date
                                <input wire:model.live='start_date' class="rounded-md" type="date" name="start_date"
                                    id="">
                            </div>
                            <div>
                                Last Date
                                <input wire:model.live='end_date' class="rounded-md" type="date" name="end_date" id="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </x-modal>
</div>