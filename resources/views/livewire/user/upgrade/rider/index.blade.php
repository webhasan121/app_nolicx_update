<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <x-dashboard.container>
        <x-dashboard.section>

            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex justify-between items-center">

                        <h5>
                            Rider
                        </h5>

                        <x-nav-link-btn wire:navigate href="{{route('upgrade.rider.create')}}">
                            <i class="fas fa-plus pr-2"></i> New
                        </x-nav-link-btn>
                    </div>
                </x-slot>

                <x-slot name="content">
                    <div class="flex justify-between">

                    </div>
                </x-slot>
            </x-dashboard.section.header>
            {{-- <div>
                <x-nav-link href="{{route('upgrade.vendor.index', ['upgrade' => 'vendor'])}}"> Vendor</x-nav-link>
                <x-nav-link href="{{route('upgrade.vendor.index', ['upgrade' => 'reseller'])}}"> Reseller</x-nav-link>
                <x-nav-link :active="true" href="{{route('upgrade.rider.index')}}"> Rider</x-nav-link>
            </div> --}}
            <x-dashboard.section.inner>

                <div style="display: grid; grid-template-columns:repeat(auto-fit, 160px); grid-gap:10px">

                    @foreach ($rider as $item)

                    <div class="rounded relative bg-gray-100 shadow">
                        <div class="cart-header flex items-center justify-center py-3">
                            {{-- fack rider image 340 x 200 px --}}
                            {{-- <img width="100" height="100"
                                src="https://img.icons8.com/plumpy/100/motorcycle-delivery-single-box.png"
                                alt="motorcycle-delivery-single-box" /> --}}

                            <i class="fas fa-truck-fast " style="font-size: 60px"></i>
                        </div>
                        <div class="">
                            <div class="p-2">
                                <div class="text-xs">Phone :</div>
                                <div class="text-sm text-md font-bold">
                                    {{ $item->phone }}
                                </div>
                            </div>
                            <hr>
                            <div class="p-2">

                                <div class="text-xs"> Tergeted Area : </div>
                                <div class="text-sm text-md font-bold">
                                    {{ Str::upper($item->targeted_area) }}
                                </div>
                            </div>
                            <hr>
                            <div class="p-2">

                                <div class="text-xs"> Create Date : </div>
                                <div class="text-sm text-md font-bold">
                                    {{ Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                </div>
                            </div>

                            <x-hr />
                            <div class="p-2">

                                @if ($item->status == 'Pending')
                                <div class="text-xs w-full p-1 px-2" style="background-color: #fefcbf; color: #b45309;">
                                    <strong>Pending</strong>
                                </div>
                                @endif
                                @if ($item->status == 'Active')
                                <div class="text-xs w-full p-1 px-2" style="background-color: #bbf7d0; color: #166534;">
                                    <strong>Active</strong>
                                </div>
                                @endif
                                @if ($item->is_rejected)
                                <div class="text-xs w-full p-1 px-2" style="background-color: #fee2e2; color: #b91c1c;">
                                    <strong> Rejected </strong>
                                </div>
                                @endif

                                {{-- <div
                                    class="p-1 px-2 rounded-lg shadow-lg text-xs bg-white text-black border border-indigo-900">
                                    {{ Str::upper($item->status)}}
                                </div> --}}
                            </div>
                            @if ($item->status == "Pending")

                            <hr>
                            <div class="p-2">
                                <x-nav-link href="{{route('upgrade.rider.edit', $item->id)}}"> <i
                                        class="fas fa-edit pr-2"></i> Edit </x-nav-link>
                                {{-- <div class="text-xs"> Create Date: </div>
                                <div class="text-sm text-md font-bold">
                                    {{ Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                </div> --}}
                            </div>
                            @endif
                        </div>

                    </div>

                    @endforeach
                </div>

            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>


</div>