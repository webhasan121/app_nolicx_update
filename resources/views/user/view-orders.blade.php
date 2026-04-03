@extends("layouts.user.dash.userDash")
@section('site_title')
    Order | {{config('app.name')}}
@endsection
@section('content')
<div style="display:flex;align-items:center;justify-content:start;;">
    <h4>Your Orders - {{count($orders)}} </h4>
</div>



<div class="container" style=" overflow:hidden; overflow-x:scroll" >
    <table class="table table-striped">
        <thead>
            <tr>
                <th></th>
                <th scope="col">Product Images</th>
                <th>Quentaty</th>
                <th scope="col">Total Price</th>
                <th scope="col">Order Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                @if ($order->product_id == null && !$order->cartOrders->count())
                    <tr>
                        <td>
                            <a href="{{route('user.order.details', ['id' => $order->id])}}" class="btn btn-info btn-sm" href="">View</a>
                        </td>
                        <td>
                            <img src="{{asset('storage/'.$order->product?->image)}}" alt="" width="50 " height="50">
                            Product Not Found !
                        </td>
                        <td>
                            1
                        </td>
                        <td>{{$order->total }}</td>
                        <td style="margin-top:10px;" class="btn btn-danger">{{$order->status}}</td>
                    </tr>

                    {{-- <tr>
                        <td colspan="5">Order is empty</td>
                    </tr> --}}
                @else
                    <tr>
                        <td>
                            <a href="{{route('user.order.details', ['id' => $order->id])}}" class="btn btn-info btn-sm" href="">View</a>
                        </td>
                        <td>
                            @if($order->cartOrders->count() > 0)
                                <div style="display: flex; gap: 10px;">
                                    @foreach($order->cartOrders as $item)
                                        <div style="margin-left:7px" class="d-flex align-items-center">
                                            <img style="width:30px; height:30px;" src="{{ asset('product-images/' . $item->product?->image) }}"> 
                                        </div>
                                    @endforeach
                                        <div class="px-3"> Multiple Product </div>
                                </div>
                            @else
                                <div class="d-flex align-items-center">

                                    <img style="width:30px; height:30px;" src="{{ asset('product-images/' . $order->product?->image) }}">
                                    <div class="px-3">{{$order->product?->title ?? "Not Found !" }} </div>
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($order->cartOrders->count() > 0)
                                {{$order->cartOrders->count()}}
                            @else
                                1
                            @endif
                        </td>
                        @if($order->cartOrders->count() > 0)
                            <td>
                            @if($order->total_in_usd > 0)
                                ${{$order->total_in_usd}}
                                @if($order->total_in_bdt > 0)
                                    {{$order->total_in_bdt}}tk
                                @endif
                            @elseif($order->total_in_bdt > 0)
                                {{$order->total_in_bdt}}tk
                            @endif
                            </td>
                        @else
                            <td>@if($order->product?->price_in_usd != null) ${{$order->total}} @else {{$order->total}} tk @endif</td>
                        @endif    
                        <td>
                            <button class="btn border rounded shadow btn-sm">
                                {{$order->status}}
                            </button>    
                        </td>
                    </tr>
                        
                @endif
            @endforeach
        </tbody>
    </table>
</div>


@endsection