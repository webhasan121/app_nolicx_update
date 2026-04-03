<div>
    <style>
        @page {
            size: A4;
        }
    </style>
    <x-dashboard.container>
        <div class="w-ful text-center">

            <div class="tex-xl">
                <x-application-name />
            </div>
            <div>
                <p class=""> Comisstion Summery form {{carbon\carbon::parse($from)->format('d/m/Y')}}
                    to {{carbon\carbon::parse($to)->format('d/m/Y') }} </p>
            </div>
        </div>
        <x-dashboard.section>
            <x-dashboard.table :data="$comissions">

                <thead>
                    <th>ID</th>
                    @if ($where == 'user_id')
                    <th>Seller</th>
                    @endif
                    @if ($where == 'order_id')

                    <th>Order</th>
                    @endif
                    @if ($where == 'product_id')

                    <th>Product</th>
                    @endif
                    <th>Buy</th>
                    <th>Sell</th>
                    <th>Profit</th>
                    <th>Rate</th>
                    <th>Take</th>
                    <th>Give</th>
                    <th>Store</th>
                    <th>Date</th>
                    <th>Confirmed</th>
                </thead>

                <tbody>

                    @foreach ($comissions as $item)
                    <tr>
                        <td> {{$item->id ?? "N/A"}} </td>
                        @if ($where == 'user_id')

                        <th>
                            {{$item->user_id}}
                        </th>
                        @endif
                        @if ($where == 'order_id')

                        <td> {{$item->order_id ?? 0}} </td>
                        @endif
                        @if ($where == 'product_id')

                        <td>
                            {{$item->product_id ?? 0}}
                        </td>
                        @endif
                        <td> {{$item->buying_price ?? 0}} </td>
                        <td> {{$item->selling_price ?? 0}} </td>
                        <td> {{$item->profit ?? "0"}} </td>
                        <td> {{$item->comission_range ?? "0"}} % </td>
                        <td> {{$item->take_comission ?? "0"}}</td>
                        <td> {{$item->distribute_comission ?? "0"}}</td>
                        <td> {{$item->store ?? "0"}}</td>
                        <td>
                            {{ $item->created_at?->toFormattedDateString() }}
                        </td>
                        <td>
                            @if ($item->confirmed == true)
                            <span class="p-1 px-2 rounded-xl bg-green-900 text-white">Confirmed</span>

                            @else
                            <span class="p-1 px-2 rounded-xl bg-gray-900 text-white">Pending</span>

                            @endif
                        </td>
                    </tr>
                    @endforeach

                </tbody>

                <tfoot>
                    <tr class="py-2 bg-gray-200">
                        <td>
                            {{count($comissions)}}
                        </td>
                        <td class="font-bold"> {{$comissions->sum('buying_price')}} </td>
                        <td class="font-bold"> {{$comissions->sum('selling_price')}} </td>
                        <td class="font-bold"> {{$comissions->sum('profit')}} </td>
                        <td></td>
                        <td class="font-bold"> {{$comissions->sum('take_comission')}} </td>
                        <td class="font-bold"> {{$comissions->sum('distribute_comission')}} </td>
                        <td class="font-bold"> {{$comissions->sum('store')}} </td>
                        <td class="font-bold"></td>
                        <td class="font-bold"></td>


                    </tr>
                </tfoot>
            </x-dashboard.table>
        </x-dashboard.section>
    </x-dashboard.container>


    {{-- <x-modal name="filter-modal" maxWidth="lg">
        <div class="p-2">
            <div>
                Filter
            </div>
            <x-hr />
            <div>
                <div class="md:flex space-y-2 justify-between items-center">
                    <div>
                        <div class="">
                            <x-input-label class=" capitalize pb-2" value="Date From" />
                            <x-text-input type="date" wire:model="start_date" />
                        </div>
                    </div>
                    <div>
                        <div class="">
                            <x-input-label class=" capitalize pb-2" value="Date To" />
                            <x-text-input type="date" wire:model="end_date" vale="{{today()}}" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="" wire:show="ord">
                <x-hr />
                <x-input-label class=" capitalize pb-2" value='Shop ID' for="shop_id" />
                <x-text-input type="text" wire:model="qry" />
                <x-hr />
            </div>
        </div>
    </x-modal> --}}

    <script>
        // If opened via browser printable flow, auto open print dialog
        setTimeout(() => window.print(), 500);
        // if (window.location.search.includes('autoPrint=1')) {
        // }
        
        // close the window, while close window.print()
        
        
    </script>
</div>