<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}

    <x-dashboard.page-header>
        Resel Orders
        <br>

        @if (auth()->user()->active_nav == 'reseller')
        <div>
            <x-nav-link href="{{route('vendor.orders.index')}}" :active="request()->routeIs('vendor.orders.*')"> To Me
            </x-nav-link>
            <x-nav-link href="{{route('reseller.resel-order.index')}}"
                :active="request()->routeIs('reseller.resel-order.*')"> Resel Order </x-nav-link>
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
                    {{auth()->user()->myOrdersAsReseller()->count() ?? "0"}}
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Pending
                </x-slot>
                <x-slot name="content">
                    {{auth()->user()->myOrdersAsReseller()->where(['status' =>
                    'Pending'])->count() ?? "0"}}
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Cancel
                </x-slot>
                <x-slot name="content">
                    {{auth()->user()->myOrdersAsReseller()->where(['status' =>
                    'Cancel'])->count()
                    ?? "0"}}
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Cancel by User
                </x-slot>
                <x-slot name="content">
                    {{auth()->user()->myOrdersAsReseller()->where(['status' =>
                    'Cancelled'])->count() ?? "0"}}
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Accepted
                </x-slot>
                <x-slot name="content">
                    {{auth()->user()->myOrdersAsReseller()->where(['status' =>
                    'Accept'])->count()
                    ?? "0"}}
                </x-slot>
            </x-dashboard.overview.div>

        </x-dashboard.overview.section>
        <x-dashboard.section>

            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex items-center justify-start space-x-2">
                        <x-secondary-button x-on:click.prevent="$dispatch('open-modal', 'filter-order')">
                            <i class="fas fa-filter pr-2"></i> Filter
                        </x-secondary-button>
                        <select id="status" wire:model.live='nav' class="py-1 px-2 rounded-md border">
                            <option value="All">Any</option>
                            <option value="Pending">Pending</option>
                            <option value="Accept">Accept</option>
                            <option value="Picked">Picked</option>
                            <option value="Delivery">Delivery</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Confirm">Confirm</option>
                            <hr class="my-1" />
                            <option value="Reject">Reject</option>
                            <option value="Hold">Hold</option>

                        </select>
                        <select id="type" wire:model.live='type' class="py-1 px-2 rounded-md border">
                            <option value="All">All</option>
                            <option value="Resel">Resel</option>
                            <option value="Purchase">Purchase</option>
                        </select>

                    </div>
                </x-slot>`

                <x-slot name="content">
                    View your resel product, income and comission here. You might find the order that have already
                    passed to the vendor for your resel product.
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <x-dashboard.foreach :$data>

                    <x-dashboard.table>
                        <thead>
                            <tr>
                                <th> </th>
                                <th> ID </th>
                                <th> Shop </th>
                                <th> Sync</th>
                                <th> Total </th>
                                <th> Profit </th>
                                <th> Shipping </th>
                                <th> Date </th>
                                <th> Status </th>
                                <th> A/C </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($data as $item)

                            <tr>
                                <td> {{$loop->iteration}} </td>
                                <td>
                                    {{$item->id}}

                                </td>
                                <td>
                                    <x-nav-link-btn
                                        href="{{route('shops', ['get' => $item->seller->vendorShop()->id, 'slug'=>$item->seller->vendorShop()->shop_name_en ?? 'not_found'])}}">
                                        {{$item->seller->vendorShop()->shop_name_en ?? ''}}
                                    </x-nav-link-btn>
                                    {{$item->seller->phone ?? ''}}
                                </td>
                                <td>
                                    @php
                                    $orderSynced = App\Models\syncOrder::where(['reseller_order_id' =>
                                    $item->id])->first();
                                    @endphp
                                    <div>
                                        @if ($orderSynced)
                                        <div class="px-2 bg-gray-200 rounded shadow flex">
                                            {{$orderSynced->user_order_id}}
                                            /
                                            {{$orderSynced->user_cart_order_id}}
                                        </div>
                                        <x-nav-link
                                            href="{{route('vendor.orders.view', ['order' => $orderSynced->user_order_id])}}">
                                            view
                                        </x-nav-link>
                                        @else
                                        <div class="px-2 inline-flex rounded bg-indigo-900 text-white">Purchase</div>
                                        @endif
                                    </div>
                                </td>
                                {{-- <td>

                                    @foreach ($item->cartOrders as $cart)
                                    <div class="flex items-center">
                                        <img class="w-10 h-10 object-cover"
                                            src="{{asset('storage/'.$cart->product->thumbnail)}}" alt="">
                                        <div class="ml-2">
                                            <p class="text-sm font-semibold">{{$cart->product->name}}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </td> --}}
                                <td> {{$item->total ?? 0}} + {{$item->shipping ?? 0}} </td>
                                <td class="font-bold">
                                    @if ($orderSynced || $item->order?->name == 'Sync')
                                    {{ $item->resellerProfit()->sum('profit') ?? 0 }}
                                    @else
                                    0
                                    @endif
                                </td>
                                <td>
                                    <p class="inline-flex text-xs px-1 rounded {{ $item->delevery == 'cash' ?
                                        'bg-green-200' : 'bg-blue-200' }} ">
                                        {{$item->delevery}}
                                    </p>
                                    <p>
                                        {{$item->location ?? 0}}
                                    </p>
                                </td>
                                <td> {{$item->created_at?->toFormattedDateString() ?? 0}} </td>
                                <td>
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
                                    <span class="text-xs p-1 border rounded-md bg-red-200 text-red-900">Rejected</span>
                                    @elseif ($item->status == 'Cancelled')
                                    <span class="text-xs p-1 border rounded-md bg-red-200 text-red-900">Cancelled</span>
                                    @else
                                    <span class="text-xs p-1 border rounded-md bg-gray-200 text-gray-900">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    <x-nav-link href="{{route('reseller.order.view', ['order' => $item->id])}}">view
                                    </x-nav-link>
                                    <x-nav-link href="{{route('vendor.orders.print', ['order' => $item->id])}}">Print
                                    </x-nav-link>
                                </td>
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