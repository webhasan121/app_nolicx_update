<div>

    <x-dashboard.page-header>
        Products
        <br>
        <div>
            <x-nav-link href="{{route('vendor.products.view')}}" :active="request()->routeIs('vendor.products.*')">Your
                Product</x-nav-link>
            {{-- if ther user is reseller then show this link --}}
            @if (auth()->user()->hasRole('reseller'))
            <x-nav-link href="{{route('reseller.resel-products.index')}}"
                :active="request()->routeIs('reseller.resel-products.*')">Reseller Product</x-nav-link>
            @endif
            {{-- <x-nav-link href="{{route('reseller.resel-products.catgory')}}"
                :active="request()->routeIs('reseller.resel-product.*')">Vendor Product</x-nav-link> --}}
        </div>
    </x-dashboard.page-header>

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Your Products
                </x-slot>
                <x-slot name="content">
                    Your have product to resel
                </x-slot>
            </x-dashboard.section.header>


            <x-dashboard.section.inner>
                <x-nav-link-btn href="{{route('vendor.products.create')}}">
                    Add Product
                </x-nav-link-btn>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex justify-between items-center">
                        <div>

                            <div x-show="!$wire.selectedModel.length > 0">
                                <x-nav-link href="?nav=Active" :active="$nav && !$take">
                                    Active
                                </x-nav-link>

                                <x-nav-link href="?take=trash" :active="$take == 'trash'">
                                    Trash
                                </x-nav-link>
                            </div>
                            <div x-show="$wire.selectedModel.length && !$wire.take" wire-transition>

                                <x-primary-button wire:click="moveToTrash">
                                    Move to Trash
                                </x-primary-button>
                            </div>
                            <div x-show="$wire.selectedModel.length && $wire.take" wire-transition>

                                <x-primary-button wire:click="restoreFromTrash">
                                    Restore
                                </x-primary-button>
                            </div>
                        </div>


                        <div class="flex items-center">
                            <x-text-input type="search" wire:model.live="search" placeholder="Search by name"
                                class="mx-2 hidden lg:block py-1"></x-text-input>
                            <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'filter-modal')">
                                Filter</x-primary-button>
                        </div>
                    </div>
                </x-slot>
                <x-slot name='content'></x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>

                <x-dashboard.foreach :data="$products">

                    <x-dashboard.table>
                        <thead>
                            <tr>
                                <th></th>
                                <th>#</th>
                                <th>Product</th>
                                <th>Stock</th>
                                <th>Build Cost</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Status</th>
                                <th>Insert At</th>
                                <th>A/C</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                            <tr>
                                <td>
                                    <input type="checkbox" wire:model.live="selectedModel" value="{{$product->id}}"
                                        style="width:20px; height:20px" />
                                </td>
                                <td> {{$loop->iteration}} </td>
                                <td>
                                    <div class="flex items-center">
                                        <img class="w-8 h-8 mr-2 rounded-md"
                                            src="{{asset('/storage/'. $product->thumbnail)}}" />
                                        {{$product->name ?? "N/A"}}
                                    </div>
                                </td>
                                <td>
                                    {{$product->unit}}
                                </td>
                                <td>
                                    {{$product->buying_price}}
                                </td>
                                <td>
                                    {{$product->price}}
                                </td>
                                <td>
                                    {{$product->discount ?? "0"}}
                                </td>
                                {{-- <td>
                                    {{$product->orders()->count()}}
                                </td> --}}
                                <td>
                                    {{$product->status ? 'Active' : "In Active"}}
                                </td>

                                <td>
                                    {{$product->created_at?->diffForHumans() ?? "N/A"}}
                                </td>
                                <td>
                                    <x-nav-link-btn
                                        href="{{route('vendor.products.edit', ['product' => encrypt($product->id) ])}}">
                                        view</x-nav-link-btn>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </x-dashboard.table>

                </x-dashboard.foreach>

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