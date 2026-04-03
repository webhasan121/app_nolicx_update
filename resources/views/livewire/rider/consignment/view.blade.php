<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <x-dashboard.page-header>
        <div class="md:flex justify-between items-center">
            <div>
                Consignment #{{$id}} <i class="fas fa-angle-right px-2"></i> assign at {{$order->id}}
            </div>

            <div class="flex gap-2">
                {{-- @if ($cod->status == 'Pending')

                <x-danger-button class="cancelShipment" wire:click='cancelShipment'>
                    Cancel Shipment
                </x-danger-button>
                @endif --}}
                <x-nav-link-btn href="{{route('rider.consignment')}}">
                    <i class="fas fa-angle-left pr-2"></i> Back
                </x-nav-link-btn>
            </div>
        </div>
    </x-dashboard.page-header>

    <x-dashboard.container>

        @php
        $shop = $seller->account_type() == 'reseller' ? $seller->resellerShop() : $seller->vendorShop();
        @endphp
        <div class="flex flex-wrap">
            <div class="m-1 rounded w-72 bg-white">
                <div class="p-2 px-4">
                    Sender ({{$shop->shop_name_en}})
                </div>
                <hr />
                <div class="p-2 px-4">
                    <p class="text-gray-800 mb-2">
                        {{$seller->name}}
                    </p>

                    <p>

                    </p>
                    <p class="text-gray-600 text-sm">
                        @if ($shop->address)
                        {{$shop->address}}
                        @else
                        {{$shop->district}}, {{$shop->upozila}}, {{$shop->village}}
                        @endif
                    </p>
                    <h6>
                        {{$shop->phone}}
                    </h6>
                </div>
            </div>
            <div class="m-1 rounded w-72 bg-white">
                <div class="p-2 px-4">
                    Destination (Buyer)
                </div>
                <hr />
                <div class="p-2 px-4">
                    <div class="mb-2">
                        {{$user->name}}
                    </div>
                    <p class="text-gray-600">
                        {{$order->location}}
                    </p>
                    <p class="">
                        {{$order->number}}
                    </p>
                </div>
            </div>
        </div>
        <x-hr />

        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Products ({{count($co)}})
                </x-slot>
                <x-slot name="content">
                    View products of the order.
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <x-dashboard.table :data="$co">
                    <thead>
                        <tr>
                            <th>Product</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($co as $item)
                        <tr>
                            <td>
                                <div class="flex">
                                    <img src="{{asset('storage/' . $item->product?->thumbnail ?? '')}}"
                                        class=" w-12 h-12 mr-2" alt="">
                                    {{$item->product?->title ?? "N/A"}}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>
        <x-hr />

        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Consignment Amount
                </x-slot>
                <x-slot name="content">

                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>
                <x-dashboard.table>
                    <tbody>
                        <tr class="border-b text-end">
                            <td>
                                Product Price
                            </td>
                            <th>
                                {{$cod->amount}} TK
                            </th>
                        </tr>
                        <tr class="border-b text-end">
                            <td>
                                Paid
                            </td>
                            <th>
                                {{$cod->paid_amount}} TK
                            </th>
                        </tr>
                        <tr class="border-b text-end">
                            <td>
                                Due
                            </td>
                            <th>
                                {{$cod->due_amount}} TK
                            </th>
                        </tr>
                        <tr class="bg-gray-200 text-end">
                            <td>
                                Sub-Total
                            </td>
                            <th>
                                {{$cod->due_amount}} TK
                            </th>
                        </tr>
                        <tr class=" text-end">
                            <td>
                                Comission
                            </td>
                            <th>
                                {{$cod->system_comission}} TK
                            </th>
                        </tr>
                        <tr class="bg-gray-300 text-end">
                            <td>
                                Total
                            </td>
                            <th>
                                {{$cod->total_amount}} TK
                            </th>
                        </tr>
                    </tbody>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>
</div>