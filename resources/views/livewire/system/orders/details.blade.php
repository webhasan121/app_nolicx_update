<div>
    <x-dashboard.page-header>
        Order Details
        <br>
        <div class="flex items-center text-xs">
            {{ $order->user_type }} <i class="fas fa-arrow-right px-2"></i> {{ $order->belongs_to_type }}
        </div>
        <x-hr />
        <div>
            <x-nav-link href="?nav=tab" :active="$nav == 'tab'">Details</x-nav-link>
            <x-nav-link href="?nav=earn" :active="$nav == 'earn'">comissions</x-nav-link>

            @if ($order->user_type == 'reseller')
            <x-nav-link href="?nav=profit" :active="$nav=='profit'">Reseller Profit</x-nav-link>
            @endif
        </div>
    </x-dashboard.page-header>


    <x-dashboard.container>

        @if ($nav == 'tab')

        <x-dashboard.section.header>
            <x-slot name="title">
                <div class="flex justify-between items-start px-5">
                    <div class="order-info">

                        <div>Order ID: {{ $order->id }}</div>
                        <div> <span class="text-xs"> {{ $order->created_at->toDayDateTimeString() }}</span> </div>

                        <x-nav-link-btn href="{{route('vendor.orders.cprint', ['order' => $order->id])}}">Print
                        </x-nav-link-btn>
                        {{-- <a target="_blank" href="{{route('admin.order.print')}}"
                            class="btn btn-sm btn-outline-primary"> <i class="fas fa-print pe-2"></i> Print</a>
                        <a target="_blank"
                            href="{{route('admin.order.print', ['id' => $order->id, 'target' => 'excel'])}}"
                            class="btn btn-sm btn-outline-primary"> <i class="fab fa-excel pe-2"></i> Excel</a> --}}
                        {{-- <table class="table"></table> --}}
                    </div>
                    <div class="order-total text-end">
                        <table class="table">
                            <tr>

                                <p>
                                    <strong>{{ $order->user?->name ?? "Not Found !" }} <br> </strong>
                                    {{$order->location}}
                                    <br>
                                    {{ $order->house_no ?? 'Not Defined !' }},</> {{$order->road_no ?? "Not Defined !"}}
                                    <br>
                                    {{$order->number}}


                                </p>
                            </tr>
                            <tr>
                                {{-- <th>House</th>
                                <td>
                                </td>
                            </tr>
                            <tr>
                                <th>Road</th>
                                <td>
                                </td>
                            </tr> --}}
                        </table>
                    </div>
                </div>
            </x-slot>
            <x-slot name="content">
                <div class="flex">



                    <form action=""></form>

                </div>
            </x-slot>
        </x-dashboard.section.header>

        {{-- <x-dashboard.section>
            <x-dashboard.section.inner>
                <x-dashboard.table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th></th>
                        </tr>
                    </thead>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section> --}}
        <x-dashboard.section>
            <x-dashboard.table>

                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Owner</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Attr</th>
                        <th>Buy</th>
                        <th>Profit</th>
                        <th>Comissions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($order->cartOrders as $key => $item)
                    <tr>
                        <td> {{$loop->iteration}} </td>
                        <td> {{$item->id ?? "N/A"}} </td>
                        <td>
                            <div class=" ">
                                <img width="30px" height="30px" src="{{asset('storage/' . $item->product?->thumbnail)}}"
                                    alt="">
                                <div>
                                    {{$item->product?->title ?? "N/A"}}
                                </div>
                            </div>
                        </td>
                        <td>
                            @if ($item->product?->isResel)
                            <span class="bg-indigo-900 text-md text-white rounded-lg px-2"> Vendor </span>
                            @else
                            <span class="bg-indigo-900 text-md text-white rounded-lg px-2"> Reseller </span>
                            @endif
                        </td>
                        <td>
                            {{$item->price}} TK
                        </td>
                        <td>
                            {{$item->quantity}}
                        </td>
                        <td>
                            {{$item->total}} TK
                        </td>
                        <td>
                            {{$item->size ?? "N/A"}}
                        </td>
                        <td>
                            {{$item->product?->buying_price ?? "N/A"}} TK
                        </td>
                        <td>
                            {{ ($item->price - $item->buying_price) * $item->quantity }}
                        </td>
                        <th>

                            <div class="flex rounded border justify-between bg-gray-200">

                                {{-- <div class="bg-white px-1 rounded">
                                    {{$item->order?->comissionsInfo ?
                                    $item->order?->comissionsInfo[$key]?->take_comission : 0}}
                                </div> --}}

                                <div class="flex space-x-1 px-1">

                                    <form action="{{route('system.comissions.destroy')}}" method="post" class="px-2">
                                        @csrf
                                        @method('POST')
                                        <input type="hidden" name="id" value="{{$order?->id}}">
                                        <button>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                    <form method="post"
                                        action="{{route('system.comissions.confirm', ['id' => $order?->id])}}">
                                        @method('post')
                                        @csrf
                                        <button>
                                            <i class="fas fa-sync"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </th>
                    </tr>
                    @endforeach
                <tbody>

                <tfoot>
                    <tr class="border-t">
                        <td colspan="6" class="text-right">Sub Total</td>
                        <td>
                            {{ $order->cartOrders->sum('total')}} Tk
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-right">Shipping</td>
                        <td>
                            {{ $order->shipping ?? 0}} Tk
                        </td>
                    </tr>
                    <tr class="border-t font-bold text-lg bg-gray-100">
                        <td colspan="6" class="text-right">Total</td>
                        <td>
                            {{ $order->shipping + $order->cartOrders->sum('total')}} Tk
                        </td>
                        <td colspan="5"></td>
                    </tr>
                </tfoot>

            </x-dashboard.table>

        </x-dashboard.section>

        @endif



        {{-- comissions --}}
        @if ($nav == 'earn')
        @livewire('system.comissions.takes', ['qry' => $order->id, 'query_type' => 'order_id', 'ord' => true])
        @endif

        @if ($nav == 'profit')
        <div class=""> Total Reseller Profit : {{$resellerProfit->sum('profit')}} </div>

        <x-hr />
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex">
                        <x-primary-button wire:click="confirmResellerProfit">Confirm</x-primary-button>
                        <x-danger-button wire:click="retundResellerProfit">Refund</x-danger-button>
                    </div>
                </x-slot>
                <x-slot name="content"></x-slot>
            </x-dashboard.section.header>
            <x-dashboard.table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Buy</th>
                        <th>Sell</th>
                        <th>Profit</th>
                        <th>Confirmed</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($resellerProfit as $item)
                    <tr>
                        <td> {{$loop->iteration}} </td>
                        <td> {{$item->id}} </td>
                        <td> {{$item->buy}} </td>
                        <td> {{$item->sel}} </td>
                        <td> {{$item->profit}} </td>
                        <td>
                            @if ($item->confirmed == true)
                            <span class="p-1 px-2 rounded-xl bg-green-900 text-white">Confirmed</span>
                            {{-- <x-nav-link wire:show="ord"
                                href="{{route('system.comissions.take.refund', ['id' => $item->id])}}"> Refund
                            </x-nav-link> --}}
                            @else
                            <span class="p-1 px-2 rounded-xl bg-gray-900 text-white">Pending</span>
                            {{-- <x-nav-link wire:show="ord"
                                href="{{route('system.comissions.take.confirm', ['id' => $item->id])}}"> Confirm
                            </x-nav-link> --}}
                            @endif
                        </td>
                        <td>
                            {{$item->created_at?->toFormattedDateString()}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>

                {{-- <tfoot>
                    <tr>
                        <th colspan="5"> </th>
                    </tr>
                </tfoot> --}}

            </x-dashboard.table>
        </x-dashboard.section>
        @endif
    </x-dashboard.container>
</div>