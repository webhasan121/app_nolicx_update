<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <x-dashboard.page-header>
        Consignment
    </x-dashboard.page-header>

    <x-dashboard.container>
        <x-dashboard.section>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4" >
                @foreach ($widgets as $widget)
                    <x-dashboard.overview.div>
                        <x-slot name="title">
                            {{ __($widget['title']) }}
                        </x-slot>
                        <x-slot name="content">
                            {{-- <div>
                                {{$rs}} / {{$ars}}
                            </div> --}}
                            {{ $widget['value'] }}
                        </x-slot>
                    </x-dashboard.overview.div>
                @endforeach
            </div>
        </x-dashboard.section>
    </x-dashboard.container>

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <select wire:model.live="type" class="py-1 rounded-md " id="">
                                <option value="All">All</option>
                                <option value="Pending">Pending</option>
                                <option value="Received">Received</option>
                                <option value="Completed">Complete</option>
                                <option value="Returned">Returned</option>
                            </select>
                            {{--
                            <div class="flex items-center relative w-48">
                                <input type="search" wire:model.live="query" placeholder="Type ID" id="search_cod"
                                    class="w-12 py-1 rounded-md w-full">
                                <select wire:model.live="query_for" id="select_query"
                                    class="absolute top-0 right-0 py-1 rounded-md ">
                                    <option value="order_id">Order</option>
                                    <option value="rider_id">Rider</option>
                                    <option value="seller_id">Seller</option>
                                    <option value="buyer_id">Buyer</option>
                                </select>
                            </div> --}}
                        </div>

                        <div class="flex gap-2 items-center">
                            <x-text-input type="date" wire:model.live='sdate' />
                            <x-text-input type="date" wire:model.live='edate' />
                        </div>
                    </div>
                </x-slot>

                <x-slot name="content"></x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                {{$cod->links()}}

                <x-dashboard.table table-border="1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Order ID</th>
                            <th>Rider</th>
                            <th>Amount</th>
                            <th>Rider Amount</th>
                            <th>Total</th>
                            <th>Comission</th>
                            <th>C Rate</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>A/C</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cod as $item)
                        <tr>
                            <td> {{$loop->iteration}} </td>
                            <td> {{$item->id }} </td>
                            <td> {{$item->order_id }} </td>
                            <td> {{$item->rider?->name }} </td>
                            <td> {{$item->amount}} </td>
                            <td> {{$item->rider_amount}} </td>
                            <td> {{$item->total_amount}} </td>
                            <td> {{$item->system_comission}} </td>
                            <td> {{$item->comission}} </td>
                            <td> {{$item->status}} </td>
                            <td> {{Carbon\Carbon::parse($item->created_at)->format('Y-M-d')}} </td>
                            <td>
                                <div class="flex gap-2 items-center">
                                    <x-danger-button>
                                        <i class="fas fa-trash"></i>
                                    </x-danger-button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-cyan-300" >
                        <tr>
                            <td>
                                {{count($cod)}}
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td> {{$cod->sum('amount')}} </td>
                            <td> {{$cod->sum('rider_amount')}} </td>
                            <td> {{$cod->sum('total_amount')}} </td>
                            <td> {{$cod->sum('system_comission')}} </td>
                            <td> {{$cod->sum('comission')}} </td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>

</div>