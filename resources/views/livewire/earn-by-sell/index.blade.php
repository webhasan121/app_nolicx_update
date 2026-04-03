<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}

    <x-dashboard.container>

        <p class="text-xl"> Sell and Profit </p>
        <x-dashboard.overview.section>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Total Sell
                </x-slot>
                <x-slot name="content">
                    <p class="">
                        {{$totalSell}} TK
                    </p>
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Profit
                </x-slot>
                <x-slot name="content">
                    {{$tp}} TK
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Neet
                </x-slot>
                <x-slot name="content">
                    {{$tn}} TK
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Shop
                </x-slot>
                <x-slot name="content">
                    {{$shop}}
                </x-slot>
            </x-dashboard.overview.div>

            {{-- <x-dashboard.overview.div>
                <x-slot name="title">
                    Vendor Shop
                </x-slot>
                <x-slot name="content">
                    {{$tpr}}
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Reseller Shop
                </x-slot>
                <x-slot name="content">
                    {{$tprr}}
                </x-slot>
            </x-dashboard.overview.div> --}}
        </x-dashboard.overview.section>


        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    {{-- <p class="text-xl">Product</p> --}}
                    <div class="flex items-center justify-between">

                        <div class="flex space-x-2">

                            <select wire:model.live="nav" id="" class="rounded py-1">
                                <option value="all">Both</option>
                                <option value="sold">Sold</option>
                                <option value="selling">On-Selling</option>
                            </select>
                        </div>
                        {{-- <div>
                            <select wire:model.live="user_type" id="" class="rounded py-1">
                                <option value="all">Both</option>
                                <option value="user">Reseller Shop</option>
                                <option value="reseller">Vendor Shop</option>
                            </select>
                        </div> --}}
                        <x-primary-button @click="$dispatch('open-modal', 'filter-modal')">Filter <i
                                class="fas fa-sort ms-2"></i> </x-primary-button>
                    </div>

                </x-slot>
                <x-slot name="content">
                    <p class="text-sm">
                        {{$products ? count($products) . " items found / Unique : " .
                        count($products->groupBy('product_id')) : "No
                        Data
                        Found "}}
                    </p>

                </x-slot>
            </x-dashboard.section.header>
            <hr>
            <x-dashboard.section.inner>
                {{$products?->links() ?? ""}}

                <x-dashboard.table :data="$products" class="p-2">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Flow</th>
                            <th>Owner</th>
                            <th>Price</th>
                            {{-- <th>Sell</th> --}}
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($products as $item)

                        <tr>
                            <td> {{$loop->iteration}} </td>
                            <td> {{$item->id}} </td>

                            <td>
                                <x-nav-link class="text-xs"
                                    href="{{route('products.details', ['id' => $item->product?->id ?? '',  'slug' => $item->product?->slug ?? ''])}}">
                                    <img width="30px" height="30px"
                                        src="{{asset('storage/'. $item->product?->thumbnail)}}" alt=""
                                        class="mr-2 rounded-full">
                                    {{$item->product?->name ?? "N/A" }}
                                </x-nav-link>
                                <br>
                                <div class="text-xs border rounded inline-block">
                                    {{$item->product?->status ?? 'N/A'}}
                                </div>
                            </td>

                            <td>
                                <div class="flex items-center">
                                    {{$item->user_type}} <i class="fas fa-angle-right mx-2"></i>
                                    {{$item->belongs_to_type}}
                                </div>
                            </td>

                            <td>
                                <div>
                                    <div class="text-gray-700 ">
                                        @switch($item->product?->belongs_to_type)
                                        @case('reseller')
                                        {{$item->product?->owner?->resellerShop()->shop_name_en ??
                                        $item->product?->owner?->name}}
                                        @break
                                        @case('vendor')
                                        {{$item->product?->owner?->vendorShop()->shop_name_en ??
                                        $item->product?->owner?->name}}
                                        @break
                                        @endswitch
                                        {{-- {{$item->product?->owner?->name}} ({{$item->product?->user_id}}) --}}
                                    </div>
                                    @if ($item->product?->isResel()->count())
                                    <span class="rounded-full p-1 text-xs bg-indigo-900 text-white">
                                        <i class="fas fa-caret-left"></i>R
                                    </span>
                                    @endif
                                    @if ($item->product?->resel()->count())
                                    <span class="rounded-full p-1 text-xs bg-indigo-900 text-white">
                                        {{$item->product?->resel()->count()}}<i class="fas fa-caret-right"></i>
                                    </span>
                                    @endif

                                </div>
                            </td>

                            <td>
                                {{$item->product?->price ?? 0}} TK
                                @if ($item->product?->offer_type)

                                <div class="flex items-center text-center p-1 rounded bg-gray-100 text-xs">
                                    D: {{$item->product?->discount}} |
                                    @php
                                    if ($item->product?->offer_type) {
                                    // $ds = $item->product?->price - $item->product?->discount;
                                    $com = round((100 * ($item->product?->price - $item->product?->discount)) /
                                    $item->product?->price, 0);
                                    echo($com . "% off");
                                    };
                                    @endphp
                                </div>
                                @endif
                            </td>
                            {{-- <td>
                                {{$item->product?->orders?->count()}}
                            </td> --}}
                            {{-- <td>
                                {{$item->product?->comissionsTake()?->sum('take_comission')}} -
                                {{$item->product?->comissionsTake()?->sum('distribute_comission')}} =
                                {{$item->product?->comissionsTake()?->sum('store')}}
                            </td> --}}
                            <td>
                                {{$item->product?->created_at?->toFormattedDateString()}}
                            </td>
                            <td>
                                {{-- <div class="flex">
                                    <x-nav-link href="/">
                                        Disable
                                    </x-nav-link>
                                    <x-nav-link href="/">
                                        View
                                    </x-nav-link>
                                </div> --}}
                                @if ($item->status == 'Pending')
                                <span class="text-xs p-1 border rounded-md bg-yellow-200 text-yellow-900">Pending</span>
                                @elseif ($item->status == 'Accept')
                                <span class="text-xs p-1 border rounded-md bg-green-200 text-green-900">Accept</span>
                                @elseif ($item->status == 'Picked')
                                <span class="text-xs p-1 border rounded-md bg-lime-200 text-lime-900">Picked</span>
                                @elseif ($item->status == 'Delivery')
                                <span class="text-xs p-1 border rounded-md bg-sky-200 text-sky-900">Delivery</span>
                                @elseif ($item->status == 'Delivered')
                                <span class="text-xs p-1 border rounded-md bg-blue-200 text-blue-900">Delivered</span>
                                @elseif ($item->status == 'Confirm')
                                <span class="text-xs p-1 border rounded-md bg-indigo-200 text-indigo-900">Confirm</span>
                                @elseif ($item->status == 'Hold')
                                <span class="text-xs p-1 border rounded-md bg-gray-200 text-gray-900">Hold</span>
                                @elseif ($item->status == 'Cancel')
                                <span class="text-xs p-1 border rounded-md bg-red-200 text-red-900">Cancel</span>
                                @elseif ($item->status == 'Cancelled')
                                <span class="text-xs p-1 border rounded-md bg-red-200 text-red-900">Cancelled</span>
                                @else
                                <span class="text-xs p-1 border rounded-md bg-gray-200 text-gray-900">Unknown</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>


        <x-modal name="filter-modal">
            <div class="p-2 flex justify-between items-center">
                <div>
                    Filter
                </div>
                <div @click="$dispatch('close-modal', 'filter-modal')">
                    <i class="fas fa-times"></i>
                </div>
            </div>
            <hr>
            <div class="p-3">
                <form action="{{route('reseller.sel.index')}}" method="get">

                    <div class="w-full flex items-bottom justify-betweeen space-x-2">
                        <div>
                            <p class="text-xs">First Date</p>
                            <input type="date" id="firstDate" name="fd" class="py-1 rounded font-normal text-sm" />
                            <div class="text-xs">
                                {{\Carbon\Carbon::parse($fd)->format('d, M Y')}}
                            </div>
                        </div>

                        <div>
                            <p class="text-xs">Last Date</p>
                            <x-text-input type="date" name='lastDate' class="py-1 rounded font-normal text-sm" />
                            <div class="text-xs">
                                {{\Carbon\Carbon::parse($lastDate)->format('d, M Y')}}
                            </div>
                        </div>

                    </div>
                    <button class="rounded bg-lime-400 px-4 mt-1 py-1 text-sm border" type="submit">Check</button>
                </form>
            </div>
        </x-modal>
    </x-dashboard.container>
</div>