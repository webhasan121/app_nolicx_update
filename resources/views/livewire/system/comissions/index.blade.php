<div x-data="{}">

    <x-dashboard.page-header>
        <div class="flex justify-between">
            <div>
                Comissions
            </div>
        </div>
    </x-dashboard.page-header>

    <x-dashboard.container>
        <div class="flex justify-between items-end mb-4">
            <div>
                <x-primary-button @click="$dispatch('open-modal', 'comission-filter-modal')">
                    <i class="fas fa-filter"></i>
                </x-primary-button>
            </div>
            <div class="flex justify-start items-end mb-2 space-x-1">
                <x-primary-button wire:click="openPrintable" class="btn">
                    <i class="fas fa-print"></i>
                </x-primary-button>
                {{-- <x-primary-button wire:click="print" class="btn">
                    <i class="fas fa-print"></i>
                </x-primary-button> --}}

                <div>
                    <x-text-input class=" py-1 w-full " type="date" wire:model.live="from" />
                    <x-input-error :messages="$errors->get('from')" class="mt-2" />
                </div>

                <div>
                    <x-text-input class=" py-1 w-full " type="date" wire:model.live="to" />
                    <x-input-error :messages="$errors->get('to')" class="mt-2" />
                </div>

            </div>
        </div>

        <x-hr class="my-2" />


        <x-dashboard.section.inner>
            <div>

                <x-dashboard.table :data="$comissions">
                    <thead>
                        <tr>
                            <th>Seller Total Profit</th>
                            <th>Cut comission</th>
                            <th>Distribute</th>
                            <th>Store</th>
                            <th>Return</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td> {{$comissions->sum('profit') ?? 0}} </td>
                            <td> {{$comissions->sum('take_comission') ?? 0}} </td>
                            <td> {{$comissions->sum('distribute_comission') ?? 0}} </td>
                            <td> {{$comissions->sum('store') ?? 0}} </td>
                            <td> {{$comissions->sum('return') ?? 0}} </td>
                        </tr>
                    </tbody>
                </x-dashboard.table>
            </div>
        </x-dashboard.section.inner>

        <x-dashboard.section id="pdf-content">
            <hr>
            {{$comissions->links()}}
            <x-dashboard.table>
                <thead>
                    <th>#</th>
                    <th>DT</th>
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
                    <th>
                        A/C
                    </th>
                </thead>

                <tbody>

                    @foreach ($comissions as $item)
                    <tr>
                        <td>{{$loop->iteration }}</td>
                        <td>
                            {{ Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                        </td>
                        <td> {{$item->id ?? "N/A"}} </td>
                        <td> {{$item->order_id ?? 0}} </td>
                        <td> {{$item->product_id ?? 0}} </td>
                        <td> {{$item->buying_price ?? 0}} </td>
                        <td> {{$item->selling_price ?? 0}} </td>
                        <td> {{$item->profit ?? "0"}} </td>
                        <td> {{$item->comission_range ?? "0"}} % </td>
                        <td> {{$item->take_comission ?? "0"}}</td>
                        <td> {{$item->distribute_comission ?? "0"}}</td>
                        <td> {{$item->store ?? "0"}}</td>
                        <td> {{$item->return ?? "0"}}</td>
                        <td>
                            @if ($item->confirmed == true)
                            <span class="p-1 px-2 rounded-xl bg-green-900 text-white">Confirmed</span>
                            <x-nav-link href="{{route('system.comissions.take.refund', ['id' => $item->id])}}"> Refund
                            </x-nav-link>
                            @else
                            <span class="p-1 px-2 rounded-xl bg-gray-900 text-white">Pending</span>
                            <form action="{{route('system.comissions.take.confirm', ['id' => $item->id])}}"
                                method="post">
                                @csrf
                                <button type="submit">Confirm</button>
                            </form>
                            {{-- <x-nav-link href="{{route('system.comissions.take.confirm', ['id' => $item->id])}}">
                                Confirm
                            </x-nav-link> --}}
                            @endif
                        </td>
                        <td>
                            <div class="flex space-x-2">
                                <x-nav-link href="{{route('system.comissions.distributes', ['id' => $item->id])}}">
                                    Details</x-nav-link>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                </tbody>

                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th> {{$comissions->sum('buying_price')}} </th>
                        <th> {{$comissions->sum('selling_price')}} </th>
                        <th> {{$comissions->sum('profit')}} </th>
                        <td></td>
                        <th> {{$comissions->sum('take_comission')}} </th>
                        <th> {{$comissions->sum('distribute_comission')}} </th>
                        <th> {{$comissions->sum('store')}} </th>
                        <th> {{$comissions->sum('return')}} </th>
                        <th></th>
                        <th></th>


                    </tr>
                </tfoot>

            </x-dashboard.table>
        </x-dashboard.section>

    </x-dashboard.container>

    <x-modal name="comission-filter-modal">
        <div class="p-3">
            Filter Comissions
        </div>
        <hr class='my-1' />

        <div class="p-3">
            <div class="my-2 flex justify-between items-start space-x-1">
                <div>
                    <select wire:model.live='where' class="rounded-md w-full py-1">
                        <option value="">-- Select -- </option>
                        <option value="user_id">User</option>
                        <option value="product_id">Product</option>
                        <option value="order_id">Order</option>
                    </select>
                </div>
                <div>
                    <select wire:model.live="confirm" class="py-1 rounded-md" id="">
                        <option value="All">Both</option>
                        <option value="true">Confirmed</option>
                        <option value="false">Pending</option>
                    </select>
                </div>
            </div>
            <div>
                <x-text-input class="w-full" placeholder="Search By ID" wire:model.live="wid" />
            </div>
        </div>
        <hr class="my-1" />
        <div class="p-3">
            <div class="flex w-full justify-end items-center space-x-1">
                <x-secondary-button class="" wire:click="$dispatch('close-modal', 'comission-filter-modal')">
                    Cancel
                </x-secondary-button>

            </div>
        </div>
    </x-modal>

    <script>
        window.addEventListener('open-printable', (e) => {
            // console.log(e.detail[0].url);
            window.open(e.detail[0].url, '_blank');
            
        });
        
        
        // window.addEventListener('notify', e => {
        //     console.log(e);
        //     alert(e.detail.message);
        // });
    </script>

</div>