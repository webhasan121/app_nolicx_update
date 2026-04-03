<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    <x-dashboard.page-header>
        Your Reseller Orders
        <br>
        <div class="text-sm font-normal">
            {{$orders->user_type}} <i class="fas fa-caret-right mx-2"></i> {{ $orders->belongs_to_type}}
        </div>

        <div class="text-xs flex items-center sapce-x-2">
            {{$orders->delevery }} Delvevery <i class="fas fa-caret-right px-2"></i> {{$orders->area_condition ==
            'Dhaka' ? 'Inside Dhaka' : 'Outside of Dhaka'}}
        </div>
    </x-dashboard.page-header>


    <x-dashboard.container>
        <x-dashboard.section>
            <div class="flex justify-between items-center space-y-2">

                {{-- <x-dropdown align="left" maxWidth='sm'>
                    <x-slot name="trigger">
                        <x-primary-button>
                            {{$orders->status}}
                        </x-primary-button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="p-2">
                            <div class="px-2 py-1" wire:click="updateStatus('Pending')">
                                Pending
                            </div>
                            <div class="px-2 py-1" wire:click="updateStatus('Accept')">
                                Accept
                            </div>
                            <div class="px-2 py-1" wire:click="updateStatus('Cancel')">
                                Cancel
                            </div>
                        </div>
                    </x-slot>
                </x-dropdown> --}}
                <div class="md:flex justify-between items-center space-y-2 w-full overflow-hidden overflow-x-scroll">
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

                        <div class="mb-2 flex gap-2">
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


            </div>
            <div class="flex justify-end items-center space-x-2">
                @if ($orders->name == 'Resel')
                {{-- <x-nav-link
                    href="{{route('system.comissions.takes', ['query_for' => 'order_id', 'qry' => $orders->id])}}">
                    COMISSIONS</x-nav-link> --}}
                {{-- <x-secondary-button x-show="$wire.$orders->user_type == 'reseller'"
                    x-on:click="$dispatch('open-modal', 'comission-modal')"> comission
                    {{$orders->comissionsInfo?->sum('take_comission') ?? 0}} TK </x-secondary-button> --}}

                <div class="inline-flex items-center px-2 bg-gray-200 text-xs rounded shadow">
                    @php
                    $orderSynced = App\Models\syncOrder::where(['reseller_order_id' => $orders->id])->first();
                    @endphp
                    <x-nav-link href="{{route('vendor.orders.view', ['order' => $orderSynced->user_order_id])}}">
                        synced
                        {{$orderSynced->user_order_id}}
                        /
                        {{$orderSynced->user_cart_order_id}}
                        view
                    </x-nav-link>
                </div>
                @else
                <div class="px-4 rounded-md shadow py-1 text-indigo-900 font-bold">Purchase</div>
                @endif
            </div>
            {{-- <x-nav-link>Print</x-nav-link> --}}
        </x-dashboard.section>

        {{-- @if (auth()->user()->active_nav == 'vendor')
        <x-dashboard.section>

        </x-dashboard.section>
        @endif
        --}}
        <x-dashboard.overview.section>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Order ID
                </x-slot>
                <x-slot name="content">
                    {{$orders->id}}
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Products
                </x-slot>
                <x-slot name="content">
                    {{$orders->cartOrders->count() ?? "0"}}
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Sub Product
                </x-slot>
                <x-slot name="content">
                    {{$orders->cartOrders->sum('quantity') ?? "0"}}
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Your Profit
                </x-slot>
                <x-slot name="content">
                    {{-- @php
                    $buy = $orders->cartOrders->sum('buying_price');
                    $total = $orders->cartOrders->sum('total');
                    @endphp
                    {{ $total - $buy ?? "0"}} --}}
                    @if ($orders->name == 'Resel')
                    {{$orders->resellerProfit()?->sum('profit') ?? "0"}}
                    @endif

                </x-slot>
            </x-dashboard.overview.div>
            {{-- <x-dashboard.overview.div>
                <x-slot name="title">
                    Comissions
                </x-slot>
                <x-slot name="content">
                    {{ $orders->comissionsInfo->sum('take_comission') ?? "0"}}
                </x-slot>
            </x-dashboard.overview.div> --}}
        </x-dashboard.overview.section>

        <x-dashboard.section>
            <div class="flex justify-between items-start px-5">
                <div class="order-info">


                    <div>
                        Order ID: {{ $orders->id }}
                        {{-- @if ($orderSynced)

                        @else
                        <div class="inline text-xs px-2 rounded bg-indigo-900 text-white">Purchase</div>
                        @endif --}}
                    </div>
                    <div>Date: <span class="text-xs"> {{ $orders->created_at->toDayDateTimeString() }}</span> </div>

                    <x-nav-link-btn href="{{route('vendor.orders.cprint', ['order' => $orders->id])}}">Print
                    </x-nav-link-btn>
                    {{-- <a target="_blank" href="{{route('admin.order.print')}}"
                        class="btn btn-sm btn-outline-primary"> <i class="fas fa-print pe-2"></i> Print</a>
                    <a target="_blank" href="{{route('admin.order.print', ['id' => $orders->id, 'target' => 'excel'])}}"
                        class="btn btn-sm btn-outline-primary"> <i class="fab fa-excel pe-2"></i> Excel</a> --}}
                    {{-- <table class="table"></table> --}}
                </div>
                <div class="order-total text-end">
                    <table class="table">
                        <tr>

                            <p>
                                <strong>{{ $orders->user?->name ?? "Not Found !" }} <br> </strong> {{$orders->location}}
                                <br>
                                {{ $orders->house_no ?? 'Not Defined !' }},</> {{$orders->road_no ?? "Not Defined !"}}
                                <br>
                                {{$orders->number}}


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

            <x-dashboard.table>

                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Owner</th>
                        <th>Resel Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Attr</th>
                        <th>Buying Price</th>
                        <th>Profit</th>
                        <th>Comissions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($orders->cartOrders as $item)
                    <tr>
                        <td> {{$loop->iteration}} </td>
                        <td>
                            {{$item->id ?? "N/A"}}
                        </td>
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
                            <x-nav-link
                                href="{{route('shops', ['get' => $item->order?->seller?->vendorShop()->id, 'slug'=>$item->order?->seller?->vendorShop()->shop_name_en ?? 'not_found'])}}">
                                {{$item->order?->seller?->vendorShop()->shop_name_en ?? ''}}
                            </x-nav-link>
                            {{$item->order?->seller?->phone ?? ''}}
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
                            @if ($item->order?->name == "Resel")
                            {{$item->buying_price ?? "N/A"}} TK
                            @endif
                        </td>

                        <td>

                            @if ($item->order?->name == "Resel")
                            {{-- {{$item->buying_price ?? "N/A"}} TK --}}
                            {{ ($item->price -
                            $item->buying_price) * $item->quantity }}
                            @endif
                        </td>
                        <th>
                            {{-- @if ($item->order?->name == 'Resel')
                            {{$item->order->comissionsInfo?->sum('take_comission') ?? '0'}}
                            @endif --}}
                        </th>
                    </tr>
                    @endforeach
                <tbody>

                <tfoot>
                    <tr class="border-t">
                        <td colspan="6" class="text-right">Sub Total</td>
                        <td>
                            {{ $orders->cartOrders->sum('total')}} Tk
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-right">Shipping</td>
                        <td>
                            {{ $orders->shipping ?? 0}} Tk
                        </td>
                    </tr>
                    <tr class="border-t font-bold text-lg bg-gray-100">
                        <td colspan="6" class="text-right">Total</td>
                        <td>
                            {{ $orders->shipping + $orders->cartOrders->sum('total')}} Tk
                        </td>
                        <td colspan="6"></td>
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
                    @if ($orders->status == 'Confirmed')
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

        <x-modal name="comission-modal">
            <div class="p-2">
                COMISSIONS
                <x-hr />

                <x-dashboard.table :data="$orders->comissionsInfo">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Amount</th>
                            <th>Product</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders->comissionsInfo as $item)
                        <tr>
                            <td> {{$loop->iteration}} </td>
                            <td> {{$item->take_comission ?? 0}} </td>
                            <td> {{$item->product?->name ?? "N/A"}} </td>
                        </tr>
                        @endforeach
                    </tbody>
                </x-dashboard.table>
            </div>
        </x-modal>

    </x-dashboard.container>
</div>