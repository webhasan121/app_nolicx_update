<div>
    <x-dashboard.page-header>
        <div class="flex justify-between items-start">
            Products

            <div class="flex space-x-1">
                <x-nav-link-btn href="{{route('vendor.products.create')}}">
                    <i class="fas fa-plus pr-2"></i> New
                </x-nav-link-btn>
                <x-nav-link-btn href="{{route('reseller.resel-product.index')}}">Recel from vendor</x-nav-link-btn>
            </div>
        </div>
        <br>

        @php
        $nav = request('nav') ?? 'own';
        @endphp
        <x-nav-link href="{{url()->current()}}/?nav=own" :active="$nav == 'own'">
            Your Product
        </x-nav-link>
        <x-nav-link href="{{url()->current()}}/?nav=resel" :active="$nav == 'resel'">
            Resel Product
        </x-nav-link>
    </x-dashboard.page-header>

    <x-dashboard.container>

        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title" class="float-right clearfix">
                    <div class="flex justify-between items-center">
                        {{-- <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'filter-modal')">
                            <i class="fas fa-filter"></i>
                        </x-primary-button> --}}
                        <x-text-input type="search" wire:model.live='search' placeholder="Search by name"
                            class="mx-2 hidden lg:block py-1">
                        </x-text-input>
                    </div>

                </x-slot>
                <x-slot name="content">
                    <div class="flex justify-between items-center">
                        <div>
                            <x-nav-link href="?pd=Active" :active="$pd == 'Active'">
                                Active
                            </x-nav-link>
                            {{-- <x-nav-link href="?nav=Draft">
                                In Active
                            </x-nav-link> --}}
                            <x-nav-link href="?pd=Trash" :active="$pd== 'Trash'">
                                Trash
                            </x-nav-link>
                        </div>

                    </div>
                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>
                <x-dashboard.table>
                    <thead>
                        <tr>
                            <th></th>
                            <th>#</th>
                            <th>Product</th>
                            <th>In Stock</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Order</th>
                            <th>Cost</th>
                            <th>Price</th>
                            <th>Sel Price</th>
                            <th>Insert At</th>
                            <th>A/C</th>
                        </tr>
                    </thead>

                    <tbody>
                    <tbody>
                        @foreach ($data as $product)
                        <tr>
                            <td>
                                <input type="checkbox" wire:model.live="selectedModel" class="rounded"
                                    value="{{$product->id}}" style="width:20px; height:20px" />
                            </td>
                            <td> {{$loop->iteration}} </td>
                            <td>
                                <div class="flex items-start">
                                    <img class="w-8 h-8 rounded-md shadow"
                                        src="{{asset('/storage/'. $product->thumbnail)}}" />
                                </div>
                            </td>
                            <td>
                                {{$product->unit}}
                            </td>
                            <td>
                                <p>
                                    {{$product->name ?? "N/A"}}
                                </p>
                                <a title="Pending Order #{{$product->orders()?->first()->id ?? ""}}" @class(['rounded
                                    text-white px-1 bg-red-900 mr-1 inline-flex text-xs hidden' , ' block'=>
                                    $product->orders()?->pending()->exists()])>
                                    {{$product->orders()?->first()->id ?? 'N\A'}}
                                </a>
                                <a title="Accept Order #{{$product->orders()?->first()->id ?? ""}}" @class(['rounded
                                    text-white px-1 bg-green-900 mr-1 inline-flex text-xs hidden', ' block'=>
                                    $product->orders()?->accept()->exists()])>
                                    {{$product->orders()?->first()->id ?? 'N\A'}}
                                </a>
                            </td>
                            <td>
                                {{$product->status ? 'Active' : "In Active"}}
                            </td>
                            <td>
                                {{$product->orders()?->count()}}
                            </td>
                            <td>
                                {{$product->buying_price }}
                            </td>
                            <td>
                                {{-- {{
                                $product->orders()->confirm()->sum('total') . " / " .
                                $product->orders()->sum('total')
                                }} --}}
                                {{$product->price}}
                                {{-- @if ($product->offer_type)
                                <div class="px-2 bg-gray-100"> {{$product->discount}} </div>
                                @endif --}}
                            </td>
                            <td>
                                {{$product->offer_type ? $product->discount : $product->price}}
                            </td>
                            <td>
                                {{$product->created_at?->diffForHumans() ?? "N/A"}}
                            </td>
                            <td>
                                <x-nav-link
                                    href="{{route('reseller.products.edit', ['id' => encrypt($product->id) ])}}">edit
                                </x-nav-link>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    </tbody>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>

    {{-- filter model --}}
    <x-modal name="filter-modal" maxWidth="xl" focusable class="h-screen overflow-y-scroll">
        <div class="p-3">
            <x-dashboard.section.header>
                <x-slot name="title">
                    Filter Your Own
                </x-slot>
                <x-slot name="content">

                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>
                <form action="" method="get">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3>Filter by Create date</h3>
                            <ul class="ms-4 mt-2">
                                <li>
                                    <div class="flex items-center mb-2">
                                        <x-text-input class="p-0 m-0 mr-3" type="radio" name="" value="today" />
                                        <x-input-label class="p-0 m-0">Today</x-input-label>
                                    </div>
                                    <div class="flex items-center mb-2">
                                        <x-text-input class="p-0 m-0 mr-3" type="radio" name="" value="today" />
                                        <x-input-label class="p-0 m-0">Today</x-input-label>
                                    </div>
                                    <div class="flex items-center mb-2">
                                        <x-text-input class="p-0 m-0 mr-3" type="radio" name="" value="today" />
                                        <x-input-label class="p-0 m-0">Today</x-input-label>
                                    </div>
                                    <div class="flex items-center mb-2">
                                        <x-text-input class="p-0 m-0 mr-3" type="radio" name="" value="today" />
                                        <x-input-label class="p-0 m-0">Today</x-input-label>
                                    </div>
                                </li>
                            </ul>
                        </div>


                        <div>
                            <h3>Filter by Status</h3>
                            <ul class="ms-4 mt-2">
                                <li>
                                    <div class="flex items-center mb-2">
                                        <x-text-input class="p-0 m-0 mr-3" type="radio" name="" value="today" />
                                        <x-input-label class="p-0 m-0">Active</x-input-label>
                                    </div>
                                    <div class="flex items-center mb-2">
                                        <x-text-input class="p-0 m-0 mr-3" type="radio" name="" value="today" />
                                        <x-input-label class="p-0 m-0">Disable</x-input-label>
                                    </div>
                                    <div class="flex items-center mb-2">
                                        <x-text-input class="p-0 m-0 mr-3" type="radio" name="" value="today" />
                                        <x-input-label class="p-0 m-0">Trash</x-input-label>
                                    </div>

                                </li>
                            </ul>
                        </div>
                        <div></div>

                    </div>
                </form>
            </x-dashboard.section.inner>
        </div>
    </x-modal>

</div>