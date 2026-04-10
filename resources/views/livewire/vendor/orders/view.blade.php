<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    <x-dashboard.page-header>
        View Orders
        <br>
        <div class="text-sm font-normal">
            {{$orders->user_type}} <i class="fas fa-arrow-right mx-2"></i> {{ $orders->belongs_to_type}}
        </div>

        <div class="text-xs flex items-center sapce-x-2">
            {{$orders->delevery }} Delvevery <i class="fas fa-caret-right px-2"></i> {{$orders->area_condition ==
            'Dhaka' ? 'Inside Dhaka' : 'Outside of Dhaka'}}
        </div>
    </x-dashboard.page-header>


    <x-dashboard.container>
        <x-dashboard.section>

            <div class="rounded-md border">
                <div class="p-3">
                    <div @class(['mb-2 flex gap-2'])>
                        <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                            text-center", 'bg-indigo-900 text-white'=>
                            in_array($orders->status, ['Pending', 'Accept', 'Picked', 'Delivery', 'Delivered',
                            'Confirm']) , 'bg-gray-100' => $orders->status == 'Pending']) title="Buyer placed the order.
                            Order in Pending">Placed
                            <br>
                            <div @class([in_array($orders->status, ['Pending','Accept', 'Picked', 'Delivery',
                                'Delivered', 'Confirm']) ? 'block' : 'hidden'])>
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                            text-center", 'bg-indigo-900 text-white'=>
                            in_array($orders->status, ['Accept', 'Picked', 'Delivery', 'Delivered', 'Confirm']) ,
                            'bg-gray-100' => $orders->status == 'Pending']) title="Accept the order for process">Accept
                            <br>
                            <div @class([in_array($orders->status, ['Accept', 'Picked', 'Delivery', 'Delivered',
                                'Confirm']) ? 'block' : 'hidden'])>
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                            text-center", 'bg-indigo-900 text-white'=>
                            in_array($orders->status, [ 'Picked', 'Delivery', 'Delivered', 'Confirm']) , 'bg-gray-100'
                            => $orders->status == 'Accept']) title="Find and collect the product">Picked
                            <br>
                            <div @class([in_array($orders->status, ['Picked', 'Delivery', 'Delivered', 'Confirm']) ?
                                'block' : 'hidden'])>
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                            text-center", 'bg-indigo-900 text-white'=>
                            in_array($orders->status, ['Delivery', 'Delivered', 'Confirm']) , 'bg-gray-100' =>
                            $orders->status == 'Picked']) title="product shipped to rider or courier.">Delivery
                            <br>
                            <div @class([in_array($orders->status, ['Delivery', 'Delivered', 'Confirm']) ? 'block' :
                                'hidden'])>
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                            text-center", 'bg-indigo-900 text-white'=>
                            in_array($orders->status, ['Delivered', 'Confirm']) , 'bg-gray-100' => $orders->status ==
                            'Delivery']) title="product delivered to the buyer.and buyer successfully received the
                            order">Delivered
                            <br>
                            <div @class([in_array($orders->status, ['Delivered', 'Confirm']) ? 'block' : 'hidden'])>
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                            text-center", 'bg-indigo-900 text-white'=>
                            $orders->status ==
                            'Confirm' , 'bg-gray-100' => $orders->status == 'Delivered'])>Confirm
                            <br>
                            <div @class([$orders->status == 'Confirm' ? 'block' : 'hidden'])>
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                            text-center", 'bg-indigo-900 text-white'=>
                            $orders->status ==
                            'Cancelled' , 'bg-gray-100' => $orders->status == 'Delivered'])>Cancelled
                            <br>
                            <div @class([$orders->status == 'Cancelled' ? 'block' : 'hidden'])>
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                            text-center", 'bg-indigo-900 text-white'=>
                            $orders->status ==
                            'Hold' , 'bg-gray-100' => $orders->status == 'Delivered'])>Hold
                            <br>
                            <div @class([$orders->status == 'Hold' ? 'block' : 'hidden'])>
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div @class(["p-2 px-3 rounded-md cursor-pointer text-gray-600 border-gray-600
                            text-center", 'bg-indigo-900 text-white'=>
                            $orders->status ==
                            'Reject' , 'bg-gray-100' => $orders->status == 'Delivered'])>Reject
                            <br>
                            <div @class([$orders->status == 'Reject' ? 'block' : 'hidden'])>
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="p-3">
                    @switch($orders->status)
                    @case('Pending')
                    <div class="pb-2">
                        Order Now on pending for your confirmation !
                    </div>
                    <x-primary-button @click="$dispatch('open-modal', 'order-accept-modal')">
                        Accept order
                    </x-primary-button>
                    @break
                    @case("Accept")
                    <div class="pb-2">
                        <h3>
                            Order Accepted ! It's time to Assign a Rider.
                        </h3>
                        <p>
                        <ul>
                            <li> Order now visible at rider dashboard for confirmation. </li>
                            <li> Or You can assign a rider from bellow button. If you assign a rider, order might hide
                                from rider public dashboard, And assigned rider can view the order shipment.</li>
                            <li> If you wish to send custom shipping method, skip this section and go next. </li>
                        </ul>
                        </p>
                    </div>
                    <div class="flex gap-2 items-center">
                        @if (!$orders?->hasRider())
                        <x-primary-button wire:click="$dispatch('open-modal', 'rider-assign-modal')">
                            <i class="fas fa-plus pr-2"></i> Rider
                        </x-primary-button>
                        @endif
                        <x-primary-button wire:click="updateOrderStatusTo('Picked')">
                            Next
                        </x-primary-button>
                    </div>

                    @break

                    @case('Picked')
                    <div class="pb-2">
                        <h3>
                            Packed The items.
                        </h3>
                        <p>
                        <ul>
                            <li> Get your order item from your wirehouse. </li>
                            <li> Packed the items for shipment.</li>
                            <li> Send items to rider. Or courier delivery. </li>
                            <li> If there have multiple rider assigned, Remove all of them except your targeted one.
                            </li>
                            <li> If done, click next button. Next steps rider able to <strong>Make as Received</strong>
                                the order. </li>
                        </ul>
                        </p>
                    </div>
                    <div class="flex gap-2 items-center">

                        <x-primary-button wire:click="updateOrderStatusTo('Delivery')">
                            Next
                        </x-primary-button>
                    </div>
                    @break
                    @case('Delivery')
                    <div class="pb-2">
                        <h3>
                            On Shipment.
                        </h3>
                        <p>
                        <ul>
                            <li> Product On the Ride. You Already Send your product to rider or direct delivery to the
                                customer.
                            </li>
                            <li> Now rider able to <strong>Make as Received</strong> the order, if you send it through
                                rider. </li>
                            <li> If order send to rider, Check bottom rider status. Make confirm rider status changed to
                                <strong>Received</strong>.
                            </li>
                            <li> Without going to next steps rider unable to <strong>Mark as Delivered</strong>. </li>
                            <li> <strong>Done !</strong> Go to next steps. </li>
                        </ul>
                        </p>
                    </div>
                    <div class="flex gap-2 items-center">

                        <x-primary-button wire:click="updateOrderStatusTo('Delivered')">
                            Next
                        </x-primary-button>
                    </div>
                    @break
                    @case('Delivered')
                      @if($orders->received_at !== null)
                        <div class="pb-2">
                            <h3>
                                Finally Done !
                            </h3>
                            <p>
                            <ul>
                                <li> Customer successfully received the order. </li>
                                <li> <strong>Finished</strong> the order. </li>
                                <li> This dispatch your comission </li>
                            </ul>
                            </p>
                        </div>
                        <div class="flex gap-2 items-center">

                            <x-primary-button wire:click="updateOrderStatusTo('Confirm')">
                                Finished
                            </x-primary-button>
                        </div>
                      @else
                        <div class="pb-2">
                            <h3>
                                Please wait !
                            </h3>
                            <p>
                            <ul>
                                <li> The customer has not yet confirmed receiving the order. </li>
                                <li> The order can be completed once the customer confirms receipt. </li>
                            </ul>
                            </p>
                        </div>
                      @endif
                    @break
                    @case('Confirm')
                    <div class="pb-2">
                        <h3>
                            Congratulation ! Order has been Confirmed.
                        </h3>
                    </div>
                    @break
                    @case('Hold')
                    <div class="pb-2">
                        <h3>
                            Order On-Hold.
                        </h3>
                    </div>
                    @break
                    @case('Cancelled')
                    <div class="pb-2">
                        <h3>
                            Buyer cancelled the Order.
                        </h3>
                    </div>
                    @break
                    @case('Reject')
                    <div class="pb-2">
                        <h3>
                            Order has beed rejected.
                        </h3>
                    </div>
                    @break
                    @endswitch

                </div>
            </div>

            <div class="flex justify-end items-center space-x-2 mt-2">
                {{-- <x-nav-link
                    href="{{route('system.comissions.takes', ['query_for' => 'order_id', 'qry' => $orders->id])}}">
                    COMISSIONS</x-nav-link> --}}
                @if ($orders->hasRider()->count())
                <x-primary-button>
                    <i class="fas fa-truck-fast pr-2"></i> Rider Assigned
                </x-primary-button>
                @endif

                <div>

                    @if (auth()->user()->active_nav == 'vendor' && $orders->name == 'Resel')
                    <x-secondary-button x-on:click="$dispatch('open-modal', 'profit-modal')"> Resel Profit
                        {{$orders->resellerProfit?->sum('profit') ?? 0}} TK </x-secondary-button>
                    @endif
                    @if (auth()->user()->active_nav == 'vendor' && $orders->name == 'Purchase')
                    <x-primary-button> Purchase </x-primary-button>
                    @endif

                    <x-secondary-button x-show="$wire.$orders->user_type == 'reseller'"
                        x-on:click="$dispatch('open-modal', 'comission-modal')"> {{ auth()->user()->account_type() ==
                        'reseller' ? auth()->user()->resellerShop()->system_get_comission ?? 0 :
                        auth()->user()->vendorShop()->system_get_comission ?? 0 }} % comission
                        {{$orders->comissionsInfo?->sum('take_comission') ?? 0}} TK </x-secondary-button>
                </div>
            </div>
        </x-dashboard.section>


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
        </x-dashboard.overview.section>

        <x-dashboard.section>
            <div class="flex justify-between items-start px-5">
                <div class="order-info">

                    <div>Order ID: {{ $orders->id }}</div>
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
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Attr</th>
                        @if (auth()->user()->account_type() == 'vendor')
                        <th>Sell</th>
                        @endif
                        <th>Cost</th>
                        <th>Profit</th>
                        {{-- <th>Comissions</th> --}}
                    </tr>
                </thead>

                <tbody>
                    @foreach ($orders->cartOrders as $item)
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
                            <div class="flex items-center space-x-1 text-xs">
                                {{-- {{$item->product?->isResel}} --}}
                                @if ($item->product?->isResel && auth()->user()?->account_type() == 'reseller')
                                Resel
                                @else
                                <span class="bg-indigo-900 text-md text-white rounded-lg px-2"> You </span>
                                @endif

                                @if ( $item->product?->isResel && auth()->user()?->account_type() == 'reseller')

                                @php
                                $alreadySynced = App\Models\syncOrder::where(['user_order_id' => $orders->id,
                                'reseller_product_id' => $item->product_id])->first();
                                // echo($alreadySynced)
                                @endphp

                                @if ($alreadySynced && $alreadySynced?->count() > 0)
                                <x-nav-link
                                    href="{{route('reseller.order.view', ['order' => $alreadySynced->reseller_order_id])}}"
                                    class="">
                                    <i @class(['fas', 'fa-link'=> $alreadySynced->status == 'Pending',
                                        'fa-checked-circle'
                                        =>
                                        $alreadySynced->status == 'Confirm'])></i>
                                    {{$alreadySynced->status}}
                                </x-nav-link>
                                @else
                                <button class="text-xs rounded border p-2" type="button"
                                    wire:click.prevent="syncOrder({{$item->id}})">
                                    <i class="fas fa-angle-right pr-2"></i> Link to Vendor
                                </button>
                                @endif
                                @endif
                            </div>
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
                        @if (auth()->user()->account_type() == 'vendor')
                        <td>

                            {{-- {{$item->price ?? "N/A"}} TK --}}
                            {{$item->buying_price ?? "N/A"}} TK
                        </td>
                        @endif
                        <td>

                            {{$item->product?->buying_price ?? "N/A"}} TK

                        </td>
                        <td>
                            {{-- @if (a)

                            @endif --}}
                            @php
                            $profit = 0;
                            if ($item->order?->name == 'Resel') {
                            $profit = intVal($item->buying_price) - intVal($item->product?->buying_price);
                            }else{
                            $profit = intVal($item->price) - intVal($item->buying_price);
                            }
                            @endphp
                            {{$profit}} * {{$item->quantity}} = {{ $profit * $item->quantity }} TK
                        </td>
                        {{-- <th>
                            {{$item->order->comissionsInfo?->sum('take_comission')}}

                        </th> --}}
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
                        <td colspan="6" class="text-right">Delivery</td>
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

        @if ($orders->hasRider())
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex justify-between items-center">
                        RIDER
                        <x-primary-button wire:click="$dispatch('open-modal', 'rider-assign-modal')">
                            <i class="fas fa-plus pr-2"></i> Rider
                        </x-primary-button>
                    </div>
                </x-slot>
                <x-slot name='content'>
                    view the rider belongs to this order.
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>

                <x-dashboard.table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Shipping</th>
                            <th>Area</th>
                            <th>Status</th>
                            <th>A/C </th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($orders->hasRider()->get() as $item)
                        <tr>
                            <td> {{$loop->iteration}} </td>
                            <td> {{$item->rider?->name ?? "N/A"}} </td>
                            <td> {{$item->rider?->phone ?? "N/A"}} </td>
                            <td> {{$item->rider?->isRider()?->current_address ?? "N/A"}} </td>
                            <td> {{$item->rider?->isRider()?->targeted_area ?? "N/A"}} </td>
                            <td> {{$item->status ?? "N/A"}} </td>
                            <td>
                                <div class="flex">
                                    <x-danger-button wire:click.prevent="removeRider({{$item->id}})">
                                        <i class="fas fa-trash"></i>
                                    </x-danger-button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>
        @endif


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

        <x-modal name='order-sync-modal'>
            <div class="p-3 bold border-b">
                Reseller Order Product
            </div>
            <div class="p-5">

                <form wire:submit.prevent="confirmSyncOrder">
                    <x-input-field inputClass="w-full" class="md:flex" label="Customer Name" disabled
                        wire:model.live="name" name="name" error="name" />
                    <x-input-field inputClass="w-full" class="md:flex" label="Customer Phone" disabled
                        wire:model.live="phone" name="phone" error="phone" />
                    <x-input-field inputClass="w-full" class="md:flex" label="Customer District" disabled
                        wire:model.live="district" name="district" error="district" />
                    <x-input-field inputClass="w-full" class="md:flex" label="Customer Upozila" disabled
                        wire:model.live="upozila" name="upozila" error="upozila" />
                    <x-input-field inputClass="w-full" label="Customer Full Address" disabled name="location"
                        wire:model.live="location" error="location" />
                    <x-input-field inputClass="w-full" class="md:flex" label="Customer Road No" disabled name="road_no"
                        wire:model.live="road_no" error="house_no" />
                    <x-input-field inputClass="w-full" class="md:flex" label="Customer House No" disabled
                        name="house_no" wire:model.live="house_no" error="house_no" />
                    <x-hr />

                    <x-input-field inputClass="w-full" class="md:flex" label="Reseller Price" disabled
                        wire:model.live="rprice" name="rprice" error="rprice" />

                    <div class="text-xs" wire:show='rprice'>
                        {{-- Profit: <strong> {{$this->rprice - $this->pd->price}} </strong> --}}
                    </div>

                    <x-hr />
                    <x-input-field inputClass="w-full" class="md:flex" disabled label="Product Quantity"
                        wire:model.live="quantity" name="quantity" error="quantity" />
                    <x-input-field inputClass="w-full" class="md:flex" disabled label="Product Size/Attribute"
                        wire:model.live="attr" name="attr" error="attr" />
                    <x-hr />
                    <x-input-file label="Area Condition" error='area_condition' disabled>
                        <select wire:model.live="area_condition" id="">
                            <option value="">Select Area</option>
                            <option value="Dhaka">Inside Dhaka</option>
                            <option value="Other">Out side of Dhaka</option>
                        </select>
                    </x-input-file>
                    <x-input-file label="Delivery Method" name="delevery" error="delevery">
                        <select wire:model.live="delevery" id="">
                            <option value="">Select Shipping Type</option>
                            <option value="courier">Courier</option>
                            <option value="home">Home Delivery</option>
                        </select>
                    </x-input-file>
                    <hr />
                    <x-primary-button>Order</x-primary-button>
                </form>
            </div>
        </x-modal>

        <x-modal name="order-accept-modal">
            <div class="p-3 bold border-b">
                Accept Order
            </div>
            <div class="p-3">
                <p> Add Shipping Amount </p>
                <div class="py-2">
                    <x-text-input name="shipping" wire:model.live="shipping" class="w-full " />
                </div>
                <br>
                <x-primary-button wire:click="acceptOrder">
                    Confirm
                </x-primary-button>
            </div>
        </x-modal>

        <x-modal name="rider-assign-modal">
            <div class="p-2">
                Assign Rider
            </div>
            <x-hr />
            <div class="p-2">
                <form wire:submit.prevent="assignRiderToOrder">
                    <x-input-file label="Select Rider" error='rider_id'>
                        <select wire:model.live="rider_id" class="w-full rounded-md">
                            <option value="">Select Rider</option>
                            @foreach ($rider as $item)
                            <option value="{{$item->id}}"> {{$item->user?->name}} - {{$item->phone}} </option>
                            @endforeach
                        </select>
                    </x-input-file>
                    <x-primary-button>
                        Assign
                    </x-primary-button>
                </form>
            </div>
        </x-modal>
    </x-dashboard.container>
</div>
