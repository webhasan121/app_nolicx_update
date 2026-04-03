<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}

    <div class="flex justify-between items-center p-2">
        <div>

            @if (count($orders))
            {{count($orders)}} consignment are available.
            @else
            No consignment found !
            @endif
        </div>
        <div>
            <div class="inline px-2 py-1 rounded-xl bg-indigo-900 text-white shadow text-sm">
                <i class="fas fa-location pr-2"></i> {{$riderInfo?->targetedArea->name ?? "N/A"}}
            </div>
        </div>
    </div>
    <x-hr />
    <div>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, 160px); gap:1rem;">
            @foreach ($orders as $order)
            @if (count($order->cartOrders) == 1 && !$order->cartOrders[0]->product?->isResel)

            <div class="bg-white rounded shadow text-center flex flex-col justify-between">
                <div class="py-2 bg-gray-200">
                    <h3 class="text-xs text-gray-500"> Order ID
                    </h3>
                    <div class="font-bold">
                        {{$order->id}}
                    </div>
                </div>

                <div class="p-2">
                    @php
                    $totalFroNotResel = 0;
                    @endphp
                    <div class="flex justify-center items-center -space-x-2 overflow-hidden">
                        @foreach ($order->cartOrders as $item)
                        @if (!$item->product?->isResel)
                        @php
                        $totalFroNotResel += $item->total;
                        @endphp
                        <img src="{{asset('storage/' . $item->product?->thumbnail)}}"
                            class="inline-block size-10 rounded-full ring-2 ring-white outline -outline-offset-1 outline-black/5"
                            alt="" srcset="">
                        @endif
                        @endforeach
                    </div>
                </div>

                <div class="px-3 py-2">
                    @php
                    $rider_cm_range = auth()->user()?->isRider()?->comission;
                    $system_cm = ($order->shipping * $rider_cm_range) / 100
                    @endphp
                    <div class="text-4xl font-bold ">
                        <sup>
                            ৳
                        </sup>
                        {{$totalFroNotResel + $system_cm}}
                    </div>
                    <div class="text-sm text-gray-500 flex justify-center items-center text-center">
                        {{-- <div>
                            <sup>
                                ৳
                            </sup>
                        </div> --}}
                        <div class=" pl-1 font-bold">
                            {{$totalFroNotResel ?? "N/A"}}
                        </div>
                        <div class="px-1" style="line-height:8px">
                            +
                        </div>
                        <div class="flex justify-center items-cenrer">

                            {{-- <div>
                                <sup>
                                    ৳
                                </sup>
                            </div> --}}
                            <div class="">
                                {{$system_cm ?? "N/A"}}
                            </div>
                        </div>
                    </div>

                </div>

                <div class="px-3 py-2">
                    <div class="text-xs text-gray-500">
                        <i class="fas fa-map-marker-alt pr-1"></i>
                        {{-- {{$order->upozila}}, {{$order->district}} --}}
                        {{$order->location}}
                    </div>
                    {{-- <div class="text-gray-500 text-sm">
                        {{$order->number}}
                    </div> --}}
                </div>
                <div class="">
                    @if ($order->hasRider()?->accept()->exists() || $order->hasRider()?->pending()->exists() ||
                    $order->hasRider()?->complete()->exists())

                    <div class="bg-red-300 p-1 flex justify-center items-center">
                        PICKED <div class="px-2 text-xs"> ({{$order->shipping}}TK) </div>
                    </div>
                    @else
                    <div class="p-1">
                        <x-primary-button wire:click="confirmOrder({{$order->id}})">
                            pick <div class="px-2 text-xs"> ({{$order->shipping}}TK) </div>
                        </x-primary-button>
                    </div>
                    @endif

                </div>
            </div>
            @endif

            @endforeach
        </div>
    </div>

</div>