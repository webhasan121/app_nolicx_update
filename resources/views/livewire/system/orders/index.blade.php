<div>
    <x-dashboard.page-header>
        <div class="flex items-center justify-between">
            Orders

            {{-- <x-secondary-button type="button" x-on:click="$dispatch('open-modal', 'filter-modal')">
                <i class="fas fa-filter"></i>
            </x-secondary-button> --}}
            {{-- <x-dropdown>
                <x-slot name="trigger">
                    <div class="p-2 px-3 rounded-md w-36">
                        Filter <i class="fas fa-caret-down ps-3"></i>
                    </div>
                </x-slot>
                <x-slot name="content">

                    <div class="px-2">

                        <div class="flex items-center mb-1">
                            <input type="radio" id="Accept" value="Accept" wire:model.live="status"
                                style="width:20px; height:20px" class="mr-3">
                            <x-input-label value="Accept" />
                        </div>
                        <div class="flex items-center mb-1">
                            <input type="radio" id="pending" value="Pending" wire:model.live="status"
                                style="width:20px; height:20px" class="mr-3">
                            <x-input-label value="Pending" />
                        </div>
                        <div class="flex items-center mb-1">
                            <input type="radio" id="cancel" value="Cancel" wire:model.live="status"
                                style="width:20px; height:20px" class="mr-3">
                            <x-input-label value="Cancel" />
                        </div>
                        <div class="flex items-center mb-1">
                            <input type="radio" id="cancelled" value="Cancelled" wire:model.live="status"
                                style="width:20px; height:20px" class="mr-3">
                            <x-input-label value="Cancelled" />
                        </div>

                    </div>
                    <x-hr />
                </x-slot>
            </x-dropdown> --}}

        </div>
    </x-dashboard.page-header>

    <x-dashboard.container>
        <x-dashboard.overview.section>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Orders
                </x-slot>
                <x-slot name="content">
                    {{ $or->count() }}

                    {{-- {{$orders->count()}} --}}
                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Amount
                </x-slot>
                <x-slot name="content">
                    {{$or->sum('total')}} TK
                    {{-- {{$orders->sum('total')}} TK --}}
                </x-slot>
            </x-dashboard.overview.div>

        </x-dashboard.overview.section>


        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <select class="border-0 rounded" wire:model.live="qf">
                                <option value="id">Order</option>
                                <option value="user_id">Buyer</option>
                                <option value="belongs_to">Seller</option>
                                {{-- <option value="belongs_to">Date</option> --}}
                            </select>
                            <x-text-input type="search" wire:model.live="search" placeholder="Search" />
                        </div>

                        <select class="border-0 rounded " wire:model.live="type" id="">
                            <option value="">Both ({{$or->count() ?? 0}})</option>
                            <option value="user">U > R ({{$or->where('belongs_to_type', 'reseller')->count() ?? 0}} )
                            </option>
                            <option value="reseller">R > V ({{$or->where('belongs_to_type', 'vendor')->count() ?? 0}} )
                            </option>
                        </select>
                        <select class="border-0 rounded " wire:model.live="status" id="">
                            <option value="">Any</option>
                            <option value="Pending">Pending</option>
                            <option value="Accept">Accept</option>
                            <option value="Picked">Picked</option>
                            <option value="Delivery">Delivery</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Confirm">Finished</option>
                            <option value="Cancel">Cancel</option>
                            <option value="Hold">Hold</option>
                            <option value="Cancelled">Buyer Cancel</option>
                            <option value="None">None</option>
                        </select>

                        {{--
                        <x-text-input type="date" wire:model.live="date" id="datePic" /> --}}
                        <select wire:model.live="date" id="" class="bg-transparent border-0">
                            <option value="">Null</option>
                            <option selected value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="between">Custom</option>
                        </select>
                        <x-primary-button wire:click='print'>
                            <i class="fas fa-print"></i>
                        </x-primary-button>
                    </div>

                    {{-- <x-primary-button>Filter</x-primary-button> --}}
                </x-slot>
                <x-slot name="content">
                    @if ($date == 'between')
                    <div class="flex">
                        <x-text-input type="date" wire:model.live="sd" id="sd" />
                        <x-text-input type="date" wire:model.live="ed" id="ed" />
                    </div>
                    @endif
                    {{$orders->links()}}
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                {{$orders->links()}}

                <x-dashboard.table :data="$orders">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Buyer</th>
                            <th>Flow</th>
                            <th>Seller</th>
                            <th>
                                Status
                            </th>
                            <th>
                                Amount
                            </th>
                            <th>
                                Comission
                            </th>
                            <th>Date</th>
                            <th>A/C</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                        $totalCom = 0;
                        @endphp
                        @foreach ($orders as $item)
                        <tr>
                            <td> {{$loop->iteration }} </td>
                            <td> {{$item->id ?? "N/A"}} </td>
                            <td>
                                @if ($item->user)

                                <x-nav-link-btn href="{{route('system.users.edit', ['id' => $item->user?->id ?? ''])}}">
                                    {{$item->user?->name ?? 'N/A'}}
                                </x-nav-link-btn>
                                @endif

                                {{$item->user?->phone ?? "N/A"}}| {{$item->user?->email ?? "N/A"}}
                            </td>
                            <td>
                                <div class="flex items-center">
                                    <div>

                                        <span class="text-xs"></span>{{ $item->user_type }}
                                    </div>
                                    <i class="px-2 fas fa-caret-right"></i>
                                    {{ $item->belongs_to_type }}
                                </div>
                            </td>
                            <td>
                                @if ($item->seller)

                                <x-nav-link-btn
                                    href="{{route('system.users.edit', ['id' => $item->seller?->id ?? ''])}}">
                                    {{$item->seller?->name ?? 'N/A'}}
                                </x-nav-link-btn>

                                @endif
                                {{$item->seller?->phone ?? "N/A"}} | {{$item->seller?->email ?? "N/A"}}
                            </td>
                            <td>
                                {{-- {{$item->status ?? "N/A"}} --}}
                                <x-dashboard.order-status :status="$item->status" />
                            </td>
                            <td>
                                {{$item->total ?? 0}} TK
                            </td>
                            <td>

                                {{$item->comissionsInfo()->sum('take_comission') ?? 0}} TK
                                @php
                                $totalCom += $item->comissionsInfo()->sum('take_comission');
                                @endphp
                            </td>
                            <td>
                                {{$item->created_at?->toFormattedDateString()}}
                            </td>
                            <td>
                                <div class="flex">

                                    <x-nav-link href="{{route('system.orders.details', ['id' => $item->id])}}">Details
                                    </x-nav-link>
                                    <x-danger-button wire:click="delete({{$item->id}})">
                                        <i class="fas fa-trash"></i>
                                    </x-danger-button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="6">
                                {{count($orders)}} Item
                            </td>
                            <td>
                                {{$orders->sum('total')}}
                            </td>
                            <td>
                                {{$totalCom}}
                            </td>
                            <td></td>

                        </tr>
                    </tfoot>
                </x-dashboard.table>

            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>

    <x-modal name="filter-modal" maxWidth="lg">
        <div class="p-3">

            <h2>Filter</h2>
            <form>

                <x-hr />



                <x-hr />
                <div class="flex justify-between space-x-2">
                    <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'filter-modal')">Close
                    </x-secondary-button>
                    <x-primary-button type="submit">Filter</x-primary-button>
                </div>
            </form>

        </div>
    </x-modal>

    <script>
        window.addEventListener('open-printable', (e) => {
                // console.log(e.detail[0].url);
                window.open(e.detail[0].url, '_blank');
            });

    </script>
</div>
