<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Order Details
                    <br>
                    <div class="text-xs">
                        {{$orders->created_at->toFormattedDateString()}} at {{$orders->created_at?->format('H:i a')}}
                    </div>
                </x-slot>

                <x-slot name="content">
                    <div>
                        Order Id : {{$orders->id}}
                    </div>
                    <div
                        class="md:flex justify-between items-center space-y-2 w-full overflow-hidden overflow-x-scroll">
                        <div>
                            <div class="mb-2 flex gap-2">
                                <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                                    text-center", 'bg-indigo-900 text-white'=> in_array($orders->status, ['Pending',
                                    'Accept', 'Picked', 'Delivery', 'Delivered', 'Confirm']) , 'bg-gray-100' =>
                                    $orders->status == 'Pending']) title="Buyer placed the order. Order in
                                    Pending">Placed
                                    <br>
                                    <div @class([in_array($orders->status, ['Pending','Accept', 'Picked', 'Delivery',
                                        'Delivered', 'Confirm']) ? 'block' : 'hidden'])>
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                                    text-center", 'bg-indigo-900 text-white'=> in_array($orders->status, ['Accept',
                                    'Picked', 'Delivery', 'Delivered', 'Confirm']) , 'bg-gray-100' => $orders->status ==
                                    'Pending']) title="Accept the order for process">Accept
                                    <br>
                                    <div @class([in_array($orders->status, ['Accept', 'Picked', 'Delivery', 'Delivered',
                                        'Confirm']) ? 'block' : 'hidden'])>
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                                    text-center", 'bg-indigo-900 text-white'=> in_array($orders->status, [ 'Picked',
                                    'Delivery', 'Delivered', 'Confirm']) , 'bg-gray-100' => $orders->status ==
                                    'Accept']) title="Find and collect the product"> {{$orders->status == 'Picked' ?
                                    'Collected' : 'Collecting'}}
                                    <br>
                                    <div @class([in_array($orders->status, ['Picked', 'Delivery', 'Delivered',
                                        'Confirm']) ? 'block' : 'hidden'])>
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                                    text-center", 'bg-indigo-900 text-white'=> in_array($orders->status, ['Delivery',
                                    'Delivered', 'Confirm']) , 'bg-gray-100' => $orders->status == 'Picked'])
                                    title="product shipped to rider or courier.">Delivery
                                    <br>
                                    <div @class([in_array($orders->status, ['Delivery', 'Delivered', 'Confirm']) ?
                                        'block' : 'hidden'])>
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                                    text-center", 'bg-indigo-900 text-white'=> in_array($orders->status, ['Delivered',
                                    'Confirm']) , 'bg-gray-100' => $orders->status == 'Delivery']) title="product
                                    delivered to the buyer.and buyer successfully received the order">Delivered
                                    <br>
                                    <div @class([in_array($orders->status, ['Delivered', 'Confirm']) ? 'block' :
                                        'hidden'])>
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                                    text-center", 'bg-indigo-900 text-white'=> $orders->status == 'Confirm' ,
                                    'bg-gray-100' => $orders->status == 'Delivered'])>Confirm
                                    <br>
                                    <div @class([$orders->status == 'Confirm' ? 'block' : 'hidden'])>
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div>

                            <div class="mb-2 flex gap-2" >
                                @if($orders->status === 'Delivered')
                                  @if($orders->received_at !== null)
                                    <div class="flex items-center gap-2 p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600 text-center bg-indigo-900 text-white" >
                                      <i class="fas fa-check-circle" ></i>
                                      <span>{{ __('Already Received') }}</span>
                                    </div>
                                  @else
                                    <x-primary-button wire:click="markAsReceived" >{{ __('Mark as Received') }}</x-primary-button>
                                  @endif
                                @endif
                                <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                                    text-center", 'bg-indigo-900 text-white'=> $orders->status == 'Hold' , 'bg-gray-100'
                                    => $orders->status == 'Delivered'])>Hold
                                    <br>
                                    <div @class([$orders->status == 'Hold' ? 'block' : 'hidden'])>
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                {{-- <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                                    text-center", 'bg-indigo-900 text-white'=> $orders->status == 'Cancel' ,
                                    'bg-gray-100' => $orders->status == 'Delivered'])>Cancel
                                    <br>
                                    <div @class([$orders->status == 'Cancel' ? 'block' : 'hidden'])>
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div> --}}
                                <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                                    text-center", 'bg-indigo-900 text-white'=> $orders->status == 'Reject' ,
                                    'bg-gray-100' => $orders->status == 'Delivered'])>Reject
                                    <br>
                                    <div @class([$orders->status == 'Reject' ? 'block' : 'hidden'])>
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                            @if ($orders->status == 'Rejecte')
                            <x-danger-button> Order Cancelled </x-danger-button>
                            @endif
                        </div>

                    </div>
                </x-slot>
            </x-dashboard.section.header>
        </x-dashboard.section>

        <x-dashboard.section>
            {{-- <x-dashboard.foreach :data="$orders->toArray()"> --}}
                <x-dashboard.table>

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Attr</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @if ($orders->cartOrders)
                        @endif --}}
                        @foreach ($orders->cartOrders as $key => $order)
                        <tr>
                            <td>{{ $loop->iteration ++ }}</td>

                            <td>
                                {{-- {{ $order->product?->name ?? "Not Found !" }} --}}
                                <x-nav-link class="text-xs"
                                    href="{{route('products.details', ['id' => $order->product?->id ?? '',  'slug' => $order->product?->slug ?? ''])}}">
                                    <img width="30px" height="30px"
                                        src="{{asset('storage/'. $order->product?->thumbnail)}}" alt="">
                                    {{$order->product?->name ?? "N/A" }}
                                </x-nav-link>
                            </td>
                            <td> {{$order->quantity}} </td>
                            <td> {{$order->size}} </td>
                            <td> {{ $order->price}} </td>
                            <td> {{ $order->total}} </td>
                            {{-- <td> {{$order->product?->buying_price ?? "0" }} </td> --}}
                        </tr>
                        {{-- @php
                        $buyingPrice += $order->product?->buying_price ?? '0';
                        // $comission += $order->product?->comissions->sum('comission');
                        @endphp --}}
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100">
                            <td colspan="5" class="text-right">Total</td>
                            <td colspan="2" class=""> {{$orders->total ?? "0"}} TK </td>
                        </tr>

                        <tr>
                            <td colspan="5" class="text-right">Shipping</td>
                            <td colspan="2" class="">{{$orders->shipping ?? "120"}} Tk </td>
                        </tr>

                        <tr class="bg-gray-200">
                            <td colspan="5" class="text-right">Payable</td>
                            <td colspan="2" class="">{{$orders->shipping + $orders->total}} TK </td>
                        </tr>

                    </tfoot>
                </x-dashboard.table>
        </x-dashboard.section>

        <div x-data="{'shipping' : false}" class="max-w-md">
            <x-dashboard.section>

                <div class="flex justify-between items-center" @click="shipping = !shipping">
                    <div>
                        Shipping
                    </div>
                    <div>
                        <div class="px-2 py-1 bg-indigo-900 text-white rounded-lg">
                            {{$orders->shipping ?? "0"}} TK
                        </div>
                    </div>
                </div>

                <div class="pt-2">

                    <div class="text-xs flex items-center sapce-x-2">
                        {{$orders->delevery }} Delevery {{$orders->area_condition == 'Dhaka' ? 'in Dhaka' : 'Outside of
                        Dhaka'}}
                    </div>
                </div>

                <div class=" text-sm mb-10">
                    <b>
                        {{$orders->location ?? "N/A"}}
                    </b>
                    <br>
                    Phone : {{$orders->number ?? "N/A"}}
                </div>
                {{-- assign rider --}}
                <div class="mb-6">
                    <h3 class="mb-2">Assign To</h3>
                    <x-hr />
                    <div>
                        @if ($orders->hasRider())
                        @php
                        $rider = $orders->hasRider()?->latest()->first();
                        @endphp
                        <div class="text-lg text-bold font-bold">
                            {{$rider?->rider?->name}}
                        </div>
                        <p>
                            {{$rider?->phone ?? $rider?->rider?->phone}}
                        </p>
                        @else
                        <div>
                            N/A
                        </div>
                        @endif
                    </div>
                </div>

                {{-- order status --}}
                <div>

                    @if ($orders->status == 'Pending')
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Placed the order
                            </p>
                            <p class="text-xs">
                                {{$orders->created_at->toFormattedDateString()}}
                            </p>
                        </div>
                    </div>
                    @endif

                    @if ($orders->status == 'Accept')
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order has been accepted by seller.
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Placed the order
                            </p>
                            <p class="text-xs">
                                {{$orders->created_at->toFormattedDateString()}}
                            </p>
                        </div>
                    </div>
                    @endif

                    @if ($orders->status == 'Picked')
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order Packed.
                            </p>
                            <p class="text-xs">
                                Order product has been packed and ready for shipment.
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order has been accepted by seller.
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Placed the order
                            </p>
                            <p class="text-xs">
                                {{$orders->created_at->toFormattedDateString()}}
                            </p>
                        </div>
                    </div>
                    @endif

                    @if ($orders->status == 'Delivery')
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order Send
                            </p>
                            <p class="text-xs">
                                @if ($orders->delevery == 'cash')
                                Order has been send to {{$orders->location}}
                                @else
                                Order has beed send to {{$orders->location}}.
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order Packed.
                            </p>
                            <p class="text-xs">
                                Order product has been packed and ready for shipment.
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order has been accepted by seller.
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Placed the order
                            </p>
                            <p class="text-xs">
                                {{$orders->created_at->toFormattedDateString()}}
                            </p>
                        </div>
                    </div>
                    @endif

                    @if ($orders->status == 'Delivery' && $orders->hasRider())
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order Assignedd to Rider
                            </p>
                            <p class="text-xs">
                                @if ($orders->delevery == 'cash' && $orders->hasRider())
                                Assigned to rider <b> {{$rider?->rider?->name ?? "N/A"}} </b>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order Send
                            </p>
                            <p class="text-xs">
                                @if ($orders->delevery == 'cash')
                                Order has been send to {{$orders->location}}
                                @else
                                Order has beed send to {{$orders->location}}.
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order Packed.
                            </p>
                            <p class="text-xs">
                                Order product has been packed and ready for shipment.
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order has been accepted by seller.
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Placed the order
                            </p>
                            <p class="text-xs">
                                {{$orders->created_at->toFormattedDateString()}}
                            </p>
                        </div>
                    </div>
                    @endif

                    @if ($orders->status == 'Delivered' && $orders->hasRider())
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Delivered
                            </p>
                            <p class="text-xs">
                                Order has been marked as delivered to you by rider at.
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order Assigned
                            </p>
                            <p class="text-xs">
                                @if ($orders->delevery == 'cash' && $orders->hasRider())
                                Assigned to rider <b> {{$rider?->rider?->name ?? "N/A"}} </b>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order Send
                            </p>
                            <p class="text-xs">
                                @if ($orders->delevery == 'cash')
                                Order has been send to {{$orders->location}}
                                @else
                                Order has beed send to {{$orders->location}}.
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order Packed.
                            </p>
                            <p class="text-xs">
                                Order product has been packed and ready for shipment.
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order has been accepted by seller.
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Placed the order
                            </p>
                            <p class="text-xs">
                                {{$orders->created_at->toFormattedDateString()}}
                            </p>
                        </div>
                    </div>
                    @endif

                    @if ($orders->status == 'Confirm')
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Success and Finished
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order Assigned
                            </p>
                            <p class="text-xs">
                                @if ($orders->delevery == 'cash' && $orders->hasRider())
                                Assigned to rider <b> {{$rider?->rider?->name ?? "N/A"}} </b>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order Send
                            </p>
                            <p class="text-xs">
                                @if ($orders->delevery == 'cash')
                                Order has been send to {{$orders->location}}
                                @else
                                Order has beed send to {{$orders->location}}.
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order Packed.
                            </p>
                            <p class="text-xs">
                                Order product has been packed and ready for shipment.
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Order has been accepted by seller.
                            </p>
                        </div>
                    </div>
                    <div class="relative px-2 py-2 flex items-center border-l">
                        <i class="fas absolute fa-check-circle w-12 h-12" style="left:-8px; top:12px;"></i>
                        <div class="px-4">
                            <p>
                                Placed the order
                            </p>
                            <p class="text-xs">
                                {{$orders->created_at->toFormattedDateString()}}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </x-dashboard.section>
        </div>

    </x-dashboard.container>



</div>