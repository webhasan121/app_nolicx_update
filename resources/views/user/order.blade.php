@extends("layouts.user.dash.userDash")
@section('content')
<div style="display:flex;align-items:center;justify-content:center;1em 0em;">
    <h4>Confirm Orders </h4>
</div>
@php 
    $total_in_usd = 0;
    $total_in_bdt = 0;
@endphp

<div class="my-2">
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Product Image</th>
                <th scope="col">Product Name</th>
                <th scope="col">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $product)
            <tr>
                <td><img style="width:50px;height:50px;"
                        src="{{ asset('product-images/' . $product->product?->image) }}"></td>
                <td>{{$product->product?->name}}</td>
                @if($product->product?->offer_type == 'yes')
                    <td>@if($product->product?->price_in_usd != null) ${{$product->product?->discount}} @else {{$product->product?->discount}} tk @endif</td>
                @else  
                    @if($product->product?->price_in_usd != null)
                        <td>${{$product->product?->price_in_usd}}</td>  
                    @else 
                        <td>{{$product->product?->price_in_bdt}}tk</td>
                    @endif    
                @endif  
            </tr>
                @php 
                    if ($product->product?->offer_type == 'yes') {
                        if ($product->product?->price_in_usd !== null) {
                            $total_in_usd += $product->product?->discount;
                        } else {
                            $total_in_bdt += $product->product?->discount;
                        }
                    } else {
                        if ($product->product?->price_in_usd !== null) {
                            $total_in_usd += $product->product?->price_in_usd;
                        } else {
                            $total_in_bdt += $product->product?->price_in_bdt;
                        }
                    }     
                @endphp
            @endforeach
        </tbody>
    </table>
</div>


<div class="container">

    <div class="row">
        <div class="col-md-4 order-md-2 mb-4">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Your cart</span>
            </h4>
            <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div>
                        <h6 class="my-0">Total Products</h6>
                        <small class="text-muted"></small>
                    </div>
                    <span class="text-muted">{{$orders->count()}}</span>
                </li>
                @if($total_in_usd > 0)
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total in Usd</span>
                    <strong>${{$total_in_usd}}</strong>
                </li>
                @endif
                @if($total_in_bdt > 0)
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total in Bdt</span>
                    <strong>{{$total_in_bdt}} tk</strong>
                </li>
                @endif
                {{-- <li class="list-group-item d-flex justify-content-between">
                    <span>VAT</span>
                    <strong>0 tk</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Shipping</span>
                    <strong>100 tk</strong>
                </li> --}}
            </ul>
        </div>
        <div class="col-md-8 order-md-1">
            <h4 class="mb-3">Billing</h4>
            <form class="needs-validation" action="{{route('order.store')}}" method="post">
                @csrf
                <input type="hidden" name="total_in_usd" value="{{$total_in_usd}}">
                <input type="hidden" name="total_in_bdt" value="{{$total_in_bdt}}">
                
                <hr>
                <div class="row">
                    @foreach ($orders as $cart)      
                        @if (!empty($cart->product?->attr?->value))
                            <div class="col-md-6 mb-3">
                                @php
                                    $arrayOfAttr = explode(',', $cart->product?->attr?->value);
                                @endphp
                                <label for="size"> {{  $cart->product?->attr?->name }} for - <span class="bg_primary text-white px-1 rounded"> {{ $cart->product?->name }} </span> </label>
                                <select name="size[]" class="form-control @error('size') is-error @enderror">
                                    
                                        <option value="Size Less" selected disable>select size</option>
                                    @foreach ($arrayOfAttr as $attr)
                                        <option value="{{$attr}}">{{ $attr }}</option>
                                    @endforeach
                                    
                                </select>
                            </div>
                        @endif
                    @endforeach

                </div>
                <hr>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="location">Location</label>
                        <input type="text" class="form-control" name="location" id="location" placeholder="Location" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="road_no">Road No</label>
                        <input type="text" class="form-control" name="road_no" id="road_no" placeholder="Road No" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="house_no">House No</label>
                        <input type="text" class="form-control" name="house_no" id="house_no" placeholder="House No" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="1st Phone Number">1st Phone Number</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="number_1" id="1st Phone Number" placeholder="1st Phone Number" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="2nd Phone Number">2nd Phone Number</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="number_2" id="2nd Phone Number" placeholder="2nd Phone Number" required>
                    </div>
                </div>

                <hr class="mb-4">
                <button style="margin: 1.5em 0em;" class="btn btn-danger btn-lg btn-block" type="submit">Confirm Order</button>
            </form>
        </div>
    </div>
</div>


@endsection