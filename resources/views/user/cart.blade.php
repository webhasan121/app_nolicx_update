@extends("layouts.user.dash.userDash")
@section('content')

<div style="display:flex;align-items:center;justify-content:start;">
    <h4>Cart</h4>
</div>

<div class="" style=" overflow:hidden; overflow-x:scroll">
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Product Image</th>
                <th scope="col">Product Name</th>
                <th scope="col">Price</th>
                <th scope="col">Remove</th>
            </tr>
        </thead>
        <tbody>
            @foreach($carts as $product)
                <tr>
                    <td><img style="width:50px;height:50px;" src="{{ asset('product-images/' . $product->product?->image) }}"></td>
                    <td>{{$product->product?->name ?? "Not Available"}}</td>
                    @if($product->product?->offer_type == 'yes')
                        <td>@if($product->product?->price_in_usd != null) ${{$product->product?->discount}} @else {{$product->product?->discount}} tk @endif</td>
                    @else  
                        @if($product->product?->price_in_usd != null)
                            <td>${{$product->product?->price_in_usd}}</td>  
                        @else 
                            <td>{{$product->product?->price_in_bdt}}tk</td>
                        @endif    
                    @endif   
                    <td>
                        <form action="{{route('cart.remove' , $product->id)}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if($carts->count() > 0)
    <div style="display:flex;align-items:center;justify-content:center;margin: 3em 7em;">
        <button onclick="window.location.href='{{ route('order.index') }}'" class="btn btn-success">Checkout</button>
    </div>
@endif

@endsection