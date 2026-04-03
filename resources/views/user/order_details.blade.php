@extends("layouts.user.dash.userDash")

@section('site_title')
    Order Details | {{config('app.name')}}
@endsection

@section('content')
<div style="display:flex;align-items:center;justify-content:start;;">
    <h4>Your Orders - </h4>
</div>


<div class="container">
    <hr>
    <div class="d-flex justify-content-between align-items-start">
        <div class="order-info">

            <h6>Order ID: {{ $orders->id }}</h6>
            <div>Order Date: {{ $orders->created_at->toDayDateTimeString(); }}
            </div>
                <button class="btn border">
                {{ $orders->status }}
            </button> 
            {{-- <table class="table"></table> --}}
        </div>
        <div class="order-total text-end">
            <table class="">
                <tr>
                    <th>Address</th>
                </tr>
                <tr>
                    <td>
                        <strong> {{$orders->user?->name ?? "Not Found !"}} - </strong>
                        {{ $orders->location }},
                        {{ $orders->house_no }},
                        {{ $orders->road_no }}
                    </td>

                </tr>
                   
            </table>
        </div>
    </div>
    <hr>
    
    <div class="overflow-x-scroll w-100">

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <td>Product</td>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Size</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @if ($orders->cartOrders)
                    @foreach ($orders->cartOrders as $key => $order)
                        <tr>
                            <td>{{ $loop->iteration ++ }}</td>
                            <td>
                                <img src="{{asset('product-images/'.$order->product?->image)}}" style="width:50px; height:50ps" alt="">
                            </td>
                            <td>{{ $order->product?->name ?? "Not Found !" }}</td>
                            <td> 1 </td>
                            <td> {{$order->size}} </td>
                            <td> {{ $order->total }} </td>
                        </tr>
                    @endforeach
                @endif
                @if ($orders->product_id == !null)
                    
                    <tr>
                        <td>1</td>
                        <td>
                            <img src="{{asset('product-images/'.$orders->product?->image)}}" style="width:50px; height:50ps" alt="">
                        </td>
                        <td>{{ $orders->product?->name ?? "Not Found !"}}</td>
                        <td>1</td>
                        <td> {{$orders->size}} </td>
                        <td>{{ $orders->total }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>


    <div class="d-flex justify-content-between align-items-start px-5">
        <div class="order-info">
        </div>
        <div class="order-total text-end">
            <table class="table">
                <tr>
                    <th>Sub Total :</th>
                    <td>  {{ $orders->total }} </td>
                </tr>
                <tr>
                    <th> Shipping : </th>
                    <td>
                        {{ $orders->shipping?? "0" }}
                    </td>
                </tr>
                <tr>
                    <th> Total : </th>
                    <td>
                        {{ $orders->total + $orders->shipping }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

@endsection