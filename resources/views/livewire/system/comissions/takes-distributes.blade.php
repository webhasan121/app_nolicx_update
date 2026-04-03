<div x-init="$wire.getData()">
    <x-dashboard.page-header>
        Products Comissions
    </x-dashboard.page-header>

    <x-dashboard.container>
        <div class="flex space-x-2">
            <x-primary-button>Seller</x-primary-button>
            <x-primary-button>Buyer</x-primary-button>
            <x-primary-button>Product</x-primary-button>
            {{-- <x-dashboard.section>
                <x-dashboard.section.inner>
                    <x-dashboard.table>
                        <thead>
                            <tr>
                                <th> Seller </th>
                                <th> Shop </th>
                                <th> Wallet </th>
                                <th> Comission Rate </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td> 
                                    {{$takes?->user?->name ?? "N/A"}} ref by <strong>{{ $takes?->user?->getReffOwner?->owner?->name ?? $takes?->user?->reference }}</strong>
                                </td>
                                <td>
                                    <span class="px-1 rounded-lg text-white bg-indigo-900"> {{$takes?->user?->account_type()}} </span>
    
                                    @php
                                        $shop;
                                    @endphp
                                    @if ($takes?->user?->account_type() == 'vendor')
                                        @php
                                            $shop = $takes?->user?->vendorShop();
                                        @endphp
                                        <x-nav-link href="{{route('system.vendor.edit', ['id' => $shop->id ?? 0, 'filter' => 'Active'])}}">
                                            {{$shop->shop_name_en ?? "N/A"}} 
                                            - 
                                            {{$shop->shop_name_bn ?? "N/A"}} <span class="px-1 text-indigo-900 text-xs mx-1 rounded-xl border "> {{$shop->status}} </span>
                                            
                                        </x-nav-link>
                                    @endif 
                                    @if ($takes?->user?->account_type() == 'reseller')
                                        @php
                                            $shop = $takes?->user?->resellerShop();
                                        @endphp
                                        <x-nav-link href="{{route('system.reseller.edit', ['id' => $shop->id ?? 0, 'filter' => 'Active'])}}">
                                            {{$shop->shop_name_en ?? "N/A"}} 
                                            -
                                            {{$shop->shop_name_bn ?? "N/A"}} <span class="px-1 text-indigo-900 text-xs mx-1 rounded-xl border "> {{$shop->status}} </span>
                                        </x-nav-link>
                                    
                                    @endif
                                    
                                </td>
                                <td>
                                    {{$takes?->user?->coin ?? 0}}
                                </td>
                                <td> {{ $shop->system_get_comission ?? 0}} % </td>
                            </tr>
                        </tbody>
                    </x-dashboard.table>
                </x-dashboard.section.inner>
            </x-dashboard.section>
            
            <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        Seller Info
                    </x-slot>
                    <x-slot name="content">
    
                    </x-slot>
                </x-dashboard.section.header>
                <x-dashboard.section.inner>
                    <x-dashboard.table>
                        <thead>
                            <tr>
                                <th> Seller </th>
                                <th> Shop </th>
                                <th> Wallet </th>
                                <th> Comission Rate </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td> 
                                    {{$takes?->user?->name ?? "N/A"}} ref by <strong>{{ $takes?->user?->getReffOwner?->owner?->name ?? $takes?->user?->reference }}</strong>
                                </td>
                                <td>
                                    <span class="px-1 rounded-lg text-white bg-indigo-900"> {{$takes?->user?->account_type()}} </span>
    
                                    @php
                                        $shop;
                                    @endphp
                                    @if ($takes?->user?->account_type() == 'vendor')
                                        @php
                                            $shop = $takes?->user?->vendorShop();
                                        @endphp
                                        <x-nav-link href="{{route('system.vendor.edit', ['id' => $shop->id ?? 0, 'filter' => 'Active'])}}">
                                            {{$shop->shop_name_en ?? "N/A"}} 
                                            - 
                                            {{$shop->shop_name_bn ?? "N/A"}} <span class="px-1 text-indigo-900 text-xs mx-1 rounded-xl border "> {{$shop->status}} </span>
                                            
                                        </x-nav-link>
                                    @endif 
                                    @if ($takes?->user?->account_type() == 'reseller')
                                        @php
                                            $shop = $takes?->user?->resellerShop();
                                        @endphp
                                        <x-nav-link href="{{route('system.reseller.edit', ['id' => $shop->id ?? 0, 'filter' => 'Active'])}}">
                                            {{$shop->shop_name_en ?? "N/A"}} 
                                            -
                                            {{$shop->shop_name_bn ?? "N/A"}} <span class="px-1 text-indigo-900 text-xs mx-1 rounded-xl border "> {{$shop->status}} </span>
                                        </x-nav-link>
                                    
                                    @endif
                                    
                                </td>
                                <td>
                                    {{$takes?->user?->coin ?? 0}}
                                </td>
                                <td> {{ $shop->system_get_comission ?? 0}} % </td>
                            </tr>
                        </tbody>
                    </x-dashboard.table>
                </x-dashboard.section.inner>
            </x-dashboard.section> --}}


        </div>

        <x-dashboard.section>
             <x-dashboard.section.header>
                <x-slot name="title">
                    Comissions of Products In Order
                </x-slot>
                <x-slot name="content">
                    {{-- <div class="flex items-center">
                        <label for="shop">Shop</label>
                        <select name="" id="shop" class="border-0">
                            <option value="1">1</option>
                        </select>
                      
                        <label for="product">Product</label>
                        <select name="" id="product" class="border-0">
                            <option value="1">1</option>
                        </select>
                        
                    </div> --}}
                </x-slot>
            </x-dashboard.section.header>

             <x-dashboard.section.inner>

                <x-dashboard.table>
                    
                    <thead>
                        <th>ID</th>
                        <th>Order</th>
                        <th>Product</th>
                        <th>Buy</th>
                        <th>Sell</th>
                        <th>Profit</th>
                        <th>Rate</th>
                        <th>Take</th>
                        <th>Give</th>
                        <th>Store</th>
                        <th>Return</th>
                        <th>Confirmed</th>
                      
                    </thead>
    
                    <tbody>
    
                            <tr >
                                <td> {{$takes->id ?? "N/A"}} </td>
                                <td> {{$takes->order_id ?? 0}} </td>
                                <td>
                                    <img src="{{asset('storage/') . $takes?->product?->thumbnail}}" alt="">
                                    {{$takes->product?->name ?? 0}}
                                </td>
                                <td> {{$takes->buying_price ?? 0}} </td>
                                <td> {{$takes->selling_price ?? 0}} </td>
                                <td> {{$takes->profit ?? "0"}} </td>
                                <td> {{$takes->comission_range ?? "0"}} % </td>
                                <td> {{$takes->take_comission ?? "0"}}</td>
                                <td> {{$takes->distribute_comission ?? "0"}}</td>
                                <td> {{$takes->store ?? "0"}}</td>
                                <td> {{$takes->return ?? "0"}}</td>
                                <td>
                                    {{-- @if ($takes->Confirmed)
                                        <span class="p-1 px-2 rounded-xl bg-green-900 text-white">Confirmed</span>
                                    @else 
                                        <span class="p-1 px-2 rounded-xl bg-gray-900 text-white">Pending</span>
                                    @endif --}}
                                    {{-- {{$tk->confirmed}} --}}
                                </td>
                                {{-- <td>
                                    <div class="flex space-x-2">
                                        <x-nav-link x-show="$wire.item.confirm" > Refund </x-nav-link>
                                        <x-nav-link x-show="!$wire.item.confirm" > Confirm </x-nav-link>
                                        <x-nav-link href="{{route('system.comissions.distributes', ['id' => $item->id])}}">Details</x-nav-link>
                                    </div>
                                </td> --}}
                            </tr>    
                    
                    </tbody>
                    
                </x-dashboard.table>

            </x-dashboard.section.inner>
        </x-dashboard.section>

        <x-dashboard.section>
           
            <x-dashboard.section.inner>
                <x-dashboard.table :data="$distributes">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Product</th>
                            <th>Amount</th>
                            <th>Range</th>
                            <th>Confirmed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($distributes as $item)
                            <tr>
                                <td> {{$item->id}} </td>
                                <td> 
                                    
                                    {{$item->user?->name ?? 0}}
                                    @if ($item->user_id == $takes->user_id)
                                        <i class="fas fa-check-circle px-1"></i>
                                    @endif
                                </td>
                                <td> {{$item->product?->name ?? 0}} </td>
                                <td> {{$item->amount ?? 0}} </td>
                                <td> {{$item->range ?? 0}} % </td>
                                <td>
                                    @if ($item->confirmed == true)
                                        <span class="p-1 px-2 rounded-xl bg-green-900 text-white">Confirmed</span>
                                        <x-nav-link href="{{route('system.comissions.distribute.refund', ['id' => $item->id])}}"> Refund </x-nav-link>
                                    @else 
                                        <span class="p-1 px-2 rounded-xl bg-gray-900 text-white">Pending</span>
                                        <x-nav-link href="{{route('system.comissions.distribute.confirm', ['id' => $item->id])}}"> Confirm </x-nav-link>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>
</div>
