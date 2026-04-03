<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    <x-dashboard.container>


        <x-dashboard.section>

            <x-dashboard.section.header>
                <x-slot name="title">
                    Your Orders
                </x-slot>

                <x-slot name="content">
                    {{-- <div class="flex justify-between">
                        <div>
                            <x-nav-link href="?nav=Accept" :active="$nav == 'Accept'">Accepted</x-nav-link>
                            <x-nav-link href="?nav=Pending" :active="$nav == 'Pending'">Pending</x-nav-link>
                            <x-nav-link href="?nav=Rejected" :active="$nav == 'Rejected'">Reject</x-nav-link>
                        </div>
                        <x-nav-link href="?nav=Cancelled" :active="$nav == 'Cancelled'">Cancel</x-nav-link>
                    </div> --}}
                </x-slot>
            </x-dashboard.section.header>

        </x-dashboard.section>

        <x-dashboard.section>

            <x-dashboard.table :data="$orders">
                <thead>
                    <tr>
                        <th></td>
                        <th>ID</th>
                        <th>Status</th>
                        <th>Product</th>
                        <th>Total</th>
                        <th>Shop</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $item)
                        <tr>
                            <td>
                                <x-nav-link href="{{route('user.orders.details', ['id' => $item->id])}}">View</x-nav-link>
                            </td>
                            <td>{{$item->id}}</td>
                            <td>
                                <x-dashboard.order-status :status="$item->status" />
                            </td>
                            <td>
                                {{-- <div class="flex">

                                    <img width="30px" height="30px" src="{{asset('storage/'. $item->product?->thumbnail)}}" alt="" srcset="">
                                    {{$item->product?->name ?? "N/A"}}
                                </div> --}}
                                {{$item->cartOrders?->count() ?? "N/A"}} | {{$item->quantity ?? "N/A"}}
                                {{-- @if($item->cartOrders->count() > 0)
                                    <div style="display: flex; gap: 10px;">
                                        @foreach($item->cartOrders as $item)
                                            <div style="margin-left:7px">
                                                <img style="width:25px; height:25px;" src="{{ asset('storage/' . $item->product?->thumbnail) }}">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif --}}
                            </td>

                            <td>
                                {{$item->total ?? "N/A"}} TK
                            </td>
                            <td>
                                {{$item->Shop?->shop_name_en}} <i class="px-1 fas fa-caret-right"></i> {{$item->shop?->shop_name_bn}}
                                <br>
                                <div class="text-xs">
                                    {{$item->shop?->village ?? 'n/a'}}, {{$item->shop?->upozila ?? 'n/a'}}, {{$item->shop?->district ?? 'n/a'}}
                                </div>
                            </td>
                            <td>
                                <x-secondary-button wire:click="cancelOrder({{$item->id}})">cancel</x-secondary-button>
                                {{-- <x-danger-button wire:click="remove({{$item->id}})">Del</x-danger-button> --}}
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </x-dashboard.table>

        </x-dashboard.section>
    </x-dashboard.container>
</div>
