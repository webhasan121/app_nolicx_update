<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <x-dashboard.page-header>
        Orders
        <br>

        @if (auth()->user()->active_nav == 'reseller')
        <div>
            <x-nav-link href="{{route('vendor.orders.index')}}" :active="request()->routeIs('vendor.orders.*')"> User
                Orders </x-nav-link>
            <x-nav-link href="{{route('reseller.resel-order.index')}}"
                :active="request()->routeIs('reseller.resel-order.*')"> My Resel Order </x-nav-link>
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
            <x-dashboard.overview.div>

            </x-dashboard.overview.div>
        </x-dashboard.overview.section>


        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex justify-between items-center">
                        <x-secondary-button x-on:click.prevent="$dispatch('open-modal', 'filter-order')">
                            <i class="fas fa-filter"></i>
                        </x-secondary-button>
                    </div>
                </x-slot>
                <x-slot name="content">
                    <div class="flex justify-between">
                        <div>
                            <x-nav-link href="?nav=Pending" :active="$nav == 'Pending'">Pending</x-nav-link>
                            <x-nav-link href="?nav=Accept" :active="$nav == 'Accept'">Accept</x-nav-link>
                            <x-nav-link href="?nav=Picked" :active="$nav == 'Picked'">Picked</x-nav-link>
                            <x-nav-link href="?nav=Delivery" :active="$nav == 'Delivery'">Delivery</x-nav-link>
                            <x-nav-link href="?nav=Delivered" :active="$nav == 'Delivered'">Delivered</x-nav-link>
                            <x-nav-link href="?nav=Confirm" :active="$nav == 'Confirm'">Confirm</x-nav-link>
                            <x-nav-link href="?nav=Hold" :active="$nav == 'Hold'">Confirm</x-nav-link>
                            <x-nav-link href="?nav=Cancel" :active="$nav == 'Cancel'">Cancel</x-nav-link>
                            <x-nav-link href="?nav=Cancelled" :active="$nav == 'Cancelled'">Cancel by Buyer</x-nav-link>
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
                                    <x-nav-link-btn href="{{route('reseller.order.view', ['order' => $item->id])}}">
                                        view </x-nav-link-btn>
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
                                    {{$item->status ?? "Pending"}}
                                </td>
                                <td>
                                    <div class="text-nowarp">
                                        <div>
                                            {{$item->created_at->diffForHumans()}}
                                        </div>
                                        <div class="text-xs">
                                            {{$item->created_at->toFormattedDateString()}}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p> {{$item->delevery}} </p>
                                    <p class="border px-2 rounded bg-gray-900 text-white inline-block bold">{{
                                        $item->area_condition }}</p>
                                </td>
                                <td>
                                    <span class="text-xs">
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
            <div class="md:flex">
                <div>
                    <div>

                        <div>
                            Delevery Type
                        </div>
                        <div class="px-2">
                            <div class="flex items-center mb-2 rounded-md border p-2">
                                <input id="home_del" value="Home" type="radio" name="" class="w-5 h-5 p-0 m-0 mr-3"
                                    id="">
                                <label for="home_del" class="p-0 m-0"> Home Delebery </label>
                            </div>
                            <div class="flex items-center mb-2 rounded-md border p-2">
                                <input id="home_del" value="Courier" type="radio" name="" class="w-5 h-5 p-0 m-0 mr-3"
                                    id="">
                                <label for="home_del" class="p-0 m-0"> Courier Delebery </label>
                            </div>
                            <div class="flex items-center mb-2 rounded-md border p-2">
                                <input id="home_del" value="Shop" type="radio" name="" class="w-5 h-5 p-0 m-0 mr-3"
                                    id="">
                                <label for="home_del" class="p-0 m-0"> Hand To Hand from shop </label>
                            </div>
                        </div>

                    </div>

                    <div class="mt-2">
                        <div>
                            Delevery Area
                        </div>
                        <div class="px-2">
                            <div class="flex items-center mb-2 rounded-md border p-2">
                                <input id="home_del" value="Dhaka" type="radio" name="" class="w-5 h-5 p-0 m-0 mr-3"
                                    id="">
                                <label for="home_del" class="p-0 m-0"> Inside Dhaka </label>
                            </div>
                            <div class="flex items-center mb-2 rounded-md border p-2">
                                <input id="home_del" value="Other" type="radio" name="" class="w-5 h-5 p-0 m-0 mr-3"
                                    id="">
                                <label for="home_del" class="p-0 m-0"> Outside of Dhaka </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-2">

                    <div class=" border rounded-md">
                        <div class=" p-2 ">

                            <div class="flex items-center p-2 ">
                                <input id="home_del" value="date" type="radio" name="" class="w-5 h-5 p-0 m-0 mr-3"
                                    id="">
                                <label for="home_del" class="p-0 m-0"> Date </label>
                            </div>
                            <div class="flex items-center p-2 ">
                                <input id="home_del" value="between" type="radio" name="" class="w-5 h-5 p-0 m-0 mr-3"
                                    id="">
                                <label for="home_del" class="p-0 m-0"> Date Between </label>
                            </div>


                        </div>

                        <div class="flex justify-between items-center p-2 ">
                            <div>
                                Start
                                <input class="rounded-md" type="date" name="start_date" id="">
                            </div>
                            <div>
                                End
                                <input class="rounded-md" type="date" name="end_date" id="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </x-modal>

</div>