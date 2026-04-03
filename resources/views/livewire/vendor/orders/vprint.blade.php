<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    <x-dashboard.page-header>
        View Orders
    </x-dashboard.page-header>


    <x-dashboard.container>
       
        <x-dashboard.section>
            <div class="flex justify-between items-start px-5">
                <div class="order-info">
                    <div class="flex items-center">
                        {{-- <img width='30px' height="30px" src="{{asset('logo.png')}}" alt="">
                        {{config('app.name')}} --}}
                    </div>
                    {{-- <div>Order ID   : {{ $orders->id }}</div> --}}
                    <div>Order Date : <span class="text-xs"> {{ $orders->created_at->toDayDateTimeString() }}</span> </div>

                </div>
                <div class="order-total text-end">
                    <table class="table">
                        <tr>
    
                            <strong>{{ $orders->user?->name ?? "Not Found !" }} </strong> 
                            <p class="text-xs">
                                {{$orders->location}}, 
                                {{ $orders->house_no ?? 'Not Defined !' }},
                            </p> 
                            <p class="text-xs">
                                Road - {{$orders->road_no ?? "N/A !"}},
                                House - {{$orders->house_no ?? "N/A"}}
                            </p>
                            <div class="text-xs">
                                {{now()->toDayDateTimeString()}}
                            </div>
                        </tr>
                       
                    </table>
                </div>
            </div>
        </x-dashboard.section>


        <x-dashboard.section>
          
            <x-dashboard.table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Attr</th>
                        <th>Price</th>
                    </tr>
                </thead>
                    @foreach ($orders->cartOrders as $item)
                        <tr>
                            <td> {{$loop->iteration}} </td>
                            <td> {{$item->id ?? "N/A"}} </td>
                            <td> 
                                <div class="flex items-start">
                                    <img width="30px" height="30px" src="{{asset('storage/' . $item->product?->thumbnail)}}" alt="">
                                    <div>
                                        {{$item->product?->title ?? "N/A"}} 
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{$item->quantity}}
                            </td>
                            <td>
                                {{$item->size ?? "N/A"}}
                            </td>
                            <td>
                                {{$item->total}}
                            </td>
                        </tr>
                    @endforeach
                    <tr class="text-md bg-gray-200">
                        <td class="text-end" colspan="4">Sub Total</td>
                        <td></td>
                        <td>{{$orders->total}}</td>
                    </tr>
                    <tr class="text-md">
                        <td class="text-end" colspan="4">Shipping</td>
                        <td></td>
                        <td>{{$orders->shipping}}</td>
                    </tr>
                    <tr class="text-md">
                        <td class="text-end" colspan="4">Total Payable</td>
                        <td></td>
                        <td>{{$orders->shipping + $orders->total}}</td>
                    </tr>
                <tbody>
                </x-dashboard.table>
               

        </x-dashboard.section>

    </x-dashboard.container>
    <script>
        setTimeout(() => {
            window.print();
        }, 2000);
    </script>
</div>
