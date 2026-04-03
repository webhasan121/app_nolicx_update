<div>
    <x-dashboard.page-header>
        Vendors
    </x-dashboard.page-header>

    <x-dashboard.container>
        <x-dashboard.section>
            {{-- <x-dashboard.section.header>
                <x-slot name="title">
                    <x-nav-link class="rounded-lg border px-3 py-2 shadow mb-2"> 
                        Make a vendor
                    </x-nav-link>
                </x-slot>
                <x-slot name="content">
                    
                </x-slot>
            </x-dashboard.section.header> --}}


            <x-dashboard.overview.section>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Total Vendor
                    </x-slot>
                    <x-slot name="content">
                        {{$tvd}}
                    </x-slot>
                </x-dashboard.overview.div>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Pending
                    </x-slot>
                    <x-slot name="content">
                        {{$pvd}}
                    </x-slot>
                </x-dashboard.overview.div>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Active
                    </x-slot>
                    <x-slot name="content">
                       {{$avd}}
                    </x-slot>
                </x-dashboard.overview.div>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Disabled
                    </x-slot>
                    <x-slot name="content">
                        {{$dvd}}
                    </x-slot>
                </x-dashboard.overview.div>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Suspended
                    </x-slot>
                    <x-slot name="content">
                        {{$svd}}
                    </x-slot>
                </x-dashboard.overview.div>
                {{-- <x-dashboard.overview.div>
                    <x-slot name="title">
                        Active Product
                    </x-slot>
                    <x-slot name="content">
                        0
                    </x-slot>
                </x-dashboard.overview.div> --}}
            </x-dashboard.overview.section>
        </x-dashboard.section>
        
        <x-dashboard.section>
            {{-- filter  --}}

            {{-- <div class="row justify-content-between m-0 mb-3 p-0">
                <div class="col-lg-6">
                    <x-text-input type='search' class="w-100" placeholder="Search Vendors" />
                </div>

                <div class="col-lg-3"></div>
                <div class="col-lg-3 mt-3">
                    <x-primary-button x-on:click.prevent="$dispatch('open-modal', 'vendor-filter-modal')">
                        Filter
                    </x-primary-button>
                </div>
                
            </div> --}}
            {{-- @php
                $filter = request('filter') ?? "Active";
            @endphp --}}

        
            <div class="flex justify-between items-start">
                <div>
                    <x-nav-link href="?filter=*" class="px-2 mb-2" :active="$filter == '*' " >All</x-nav-link>
                    <x-nav-link href="?filter=Active" class="px-2 mb-2" :active="$filter == 'Active' " >Active</x-nav-link>
                    <x-nav-link href="?filter=Pending" class="px-2 mb-2" :active="$filter == 'Pending' " >Pending</x-nav-link>
                    <x-nav-link href="?filter=Disabled" class="px-2 mb-2" :active="$filter == 'Disabled' " >Disabled</x-nav-link>
                    <x-nav-link href="?filter=Suspended" class="px-2 mb-2" :active="$filter == 'Suspended' " >Suspended</x-nav-link>
                </div>

                <div>
                    <x-text-input wire:model.live="find" wire:keydown.enter="search" type="search" placeholder="Search Vendor" class="my-1 py-1" />
                    <x-primary-button type="button" x-on:click.prevent="$dispatch('open-modal', 'vendor-filter-modal')">Filter</x-primary-button>
                </div>
                            
            </div>

            

            {{-- section inner  --}}
            <x-dashboard.section.inner>
                <x-dashboard.table>
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Commission</th> 
                            <th>Product</th>
                            <th>Join</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <x-dashboard.foreach :data="$vendors">
                        
                            @foreach ($vendors as $key => $vendor)    
                                <tr>
                                    <td> {{$loop->iteration}} </td>
                                    <td>{{$vendor->id }}</td>
                                    <td>
                                        <div class="text-nowrap">
                                            {{$vendor->user?->name ?? "N/A"}}
                                        </div>
                                        <div class="badge badge-info text-nowrap"> {{$vendor->shop_name_en ?? "N/A"}} </div>
                                    </td>
                                    <td>
                                        <div class="text-nowrap">
                                            {{$vendor->user?->email ?? "N/A"}}
                                        </div>
                                        <div class="text-nowrap">
                                            {{$vendor->user?->phone ?? "N/A"}}
                                        </div>
                                        <div class="text-nowrap">
                                             {{$vendor->user?->upazila ?? "N/A"}}, {{$vendor->user?->district ?? "N/A"}}, {{$vendor->user?->country ?? "N/A"}}
                                        </div>
                                    </td>
                                    <td>
                                        {{$vendor->status ?? "N/A"}}
                                    </td>
                                    <td>
                                        <span class="badge badge-success"> {{$vendor->system_get_comission ?? "N/A"}} </span> %
                                        {{-- <span>  </span> --}}
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{$vendor->products_count ?? 0}}
                                        </span>
                                        <x-nav-link href="{{route('system.products.index', ['find' => $vendor->id, 'from' => 'vendor'])}}">
                                            View
                                        </x-nav-link>
                                    </td>
                                    <td> {{$vendor->created_at?->toFormattedDateString()}} </td>
                                    <td>
                                        {{-- <form action="{{route('system.vendor.edit', ['id' => $vendor->id])}}" method="get">
                                            <x-primary-button>Edit</x-primary-button>
                                        </form> --}}
                                        <x-nav-link href="{{route('system.vendor.settings', ['id' => $vendor->id])}}">
                                            Edit
                                        </x-nav-link>
                                    </td>
                                </tr>
                            @endforeach
                            
                        </x-dashboard.foreach>
                    </tbody>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>

    <x-modal name="vendor-filter-modal" id="vendor-filter-modal" focusable class="h-screen overflow-y-scroll">
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Filter Vendor
                </x-slot>
                <x-slot name='content'>
                    {{-- <x-danger-button type="button" @click="close()"> --}}
                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner class="overflow-y-scroll">
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-modal>
</div>