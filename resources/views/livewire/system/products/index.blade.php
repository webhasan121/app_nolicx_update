<div>

    <div>
        {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
        <x-dashboard.page-header>
            <div class="flex justify-between items-center">
                <div>
                    Products
                    <br>
                    <x-nav-link href="{{route('reseller.resel-product.index')}}">Browse</x-nav-link>
                </div>

                <x-nav-link-btn>
                    <i class="fas fa-filter pr-2"></i> Filter
                </x-nav-link-btn>
            </div>

        </x-dashboard.page-header>

        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">

                    <div class="flex justify-between items-center">

                        <div class="flex items-center">
                            <select wire:model.live="from" id="">
                                <option value="">All</option>
                                {{-- <option value="id">Find</option> --}}
                                <option value="vendor">Vendor</option>
                                <option value="reseller">Reseller</option>
                            </select>
                            <x-text-input wire:model.live="find" type="search" placeholder="ID" />
                        </div>


                        <div>

                        </div>
                    </div>

                </x-slot>
                <x-slot name="content">
                    {{-- <x-nav-link :active="$filter == 'Active'" wire:click="$filter = 'Active'">Active</x-nav-link>
                    <x-nav-link :active="$filter == 'Disable'" wire:click="$filter = 'Disable'">Disable</x-nav-link>
                    --}}
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <div class="md:flex ">

                    <div class=" block md:hidden">
                        <div class="flex">
                            <input type="radio" wire:model.live="filter" value="Active" id="active"
                                style="width:20px; height:20px" class="mr-3">
                            <div> Active </div>
                        </div>
                        <div class="flex mt-2">
                            <input type="radio" wire:model.live="filter" value="Disable" id="disable"
                                style="width:20px; height:20px" class="mr-3">
                            <div> Disable </div>
                        </div>
                    </div>
                    <div class="hidden md:block text-start" style="width:160px; text-aling:left">
                        <div
                            style="display: grid; justify-content:center; grid-template-columns: repeat(auto-fill, 150px)); grid-gap:10px">
                            <div>
                                <div class="text-xs">status</div>
                                <div class="flex">
                                    <input type="radio" wire:model.live="filter" value="Active" id="lactive"
                                        style="width:20px; height:20px" class="mr-3">
                                    <div> Active </div>
                                </div>
                                <div class="flex mt-2">
                                    <input type="radio" wire:model.live="filter" value="Disable" id="ldisable"
                                        style="width:20px; height:20px" class="mr-3">
                                    <div> Disable </div>
                                </div>
                                <div class="flex mt-2">
                                    <input type="radio" wire:model.live="filter" value="both" id="ldisable"
                                        style="width:20px; height:20px" class="mr-3">
                                    <div> Both </div>
                                </div>
                            </div>
                            <div x-show="$wire.from == 'reseller'">
                                <hr>
                                <div class="text-xs">reseller</div>
                                <div class="flex">
                                    <input type="checkbox" wire:model.live="isIncludeResel" value="true" id="isResel"
                                        style="width:20px; height:20px" class="mr-3">
                                    <div> Include Resel </div>
                                </div>

                                <hr>
                            </div>
                            <div>
                                <div class="text-xs">order status</div>
                                <div class="flex">
                                    <input type="checkbox" name="" value="Active" id="accept"
                                        style="width:20px; height:20px" class="mr-3">
                                    <div> Accept </div>
                                </div>
                                <div class="flex mt-2">
                                    <input type="checkbox" name="" value="Disable" id="pending"
                                        style="width:20px; height:20px" class="mr-3">
                                    <div> Pending </div>
                                </div>
                                <div class="flex mt-2">
                                    <input type="checkbox" name="" value="Disable" id="cancel"
                                        style="width:20px; height:20px" class="mr-3">
                                    <div> Cancel </div>
                                </div>
                                <div class="flex mt-2">
                                    <input type="checkbox" name="" value="Disable" id="reject"
                                        style="width:20px; height:20px" class="mr-3">
                                    <div> Reject </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full">
                        {{-- @if ($products->links())
                        @endif --}}
                        {{$products?->links() ?? ""}}
                        <x-dashboard.table :data="$products">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Product</th>
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
                                            href="{{route('products.details', ['id' => $item->id ?? '',  'slug' => $item->slug ?? ''])}}">
                                            <img width="30px" height="30px"
                                                src="{{asset('storage/'. $item->thumbnail)}}" alt=""
                                                class="mr-2 rounded-full">
                                            {{$item->name ?? "N/A" }}
                                        </x-nav-link>
                                        <br>
                                        <div class="text-xs border rounded inline-block">
                                            {{$item->status ?? 'N/A'}}
                                        </div>
                                    </td>

                                    <td>
                                        <div>
                                            <div class="text-gray-700 ">
                                                @switch($item->belongs_to_type)
                                                @case('reseller')
                                                {{$item->owner?->resellerShop()->shop_name_en ?? $item->owner?->name}}
                                                @break
                                                @case('vendor')
                                                {{$item->owner?->vendorShop()->shop_name_en ?? $item->owner?->name}}
                                                @break
                                                @endswitch
                                                {{-- {{$item->owner?->name}} ({{$item->user_id}}) --}}
                                            </div>
                                            @if ($item->isResel()->count())
                                            <span class="rounded-full p-1 text-xs bg-indigo-900 text-white">
                                                <i class="fas fa-caret-left"></i>R
                                            </span>
                                            @endif
                                            @if ($item->resel()->count())
                                            <span class="rounded-full p-1 text-xs bg-indigo-900 text-white">
                                                {{$item->resel()->count()}}<i class="fas fa-caret-right"></i>
                                            </span>
                                            @endif
                                            <div class="text-xs">
                                                {{$item->belongs_to_type}}
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        {{$item->price ?? 0}} TK
                                        @if ($item->offer_type)

                                        <div class="flex items-center text-center p-1 rounded bg-gray-100 text-xs">
                                            D: {{$item->discount}} |
                                            @php
                                            if ($item->offer_type) {
                                            // $ds = $item->price - $item->discount;
                                            $com = round((100 * ($item->price - $item->discount)) / $item->price, 0);
                                            echo($com . "% off");
                                            };
                                            @endphp
                                        </div>
                                        @endif
                                    </td>
                                    {{-- <td>
                                        {{$item->orders?->count()}}
                                    </td> --}}
                                    {{-- <td>
                                        {{$item->comissionsTake()?->sum('take_comission')}} -
                                        {{$item->comissionsTake()?->sum('distribute_comission')}} =
                                        {{$item->comissionsTake()?->sum('store')}}
                                    </td> --}}
                                    <td>
                                        {{$item->created_at?->toFormattedDateString()}}
                                    </td>
                                    <td>
                                        <div class="flex">
                                            {{-- <x-nav-link href="/">
                                                Disable
                                            </x-nav-link> --}}
                                            <x-nav-link
                                                href="{{route('system.products.edit', ['product' => $item->id])}}">
                                                View
                                            </x-nav-link>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </x-dashboard.table>


                    </div>
                </div>
            </x-dashboard.section.inner>
        </x-dashboard.section>


    </div>
</div>