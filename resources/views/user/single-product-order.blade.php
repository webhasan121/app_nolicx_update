@extends("layouts.user.app")
@section('title')
    Product | Gorom Bazar
@endsection
@section('content')

<div style="align-content:center; max-width: 1100px; margin:0 auto">
   <x-product-single :$product />
</div>
<hr class="my-1">
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
                    <span class="text-muted">1</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total</span>
                    @if($product->offer_type == 'yes')
                        <strong>@if($product->price_in_usd != null) ${{$product->discount}} @else {{$product->discount}} tk @endif</strong>
                    @else 
                        @if($product->price_in_usd != null)
                            <strong>${{$product->price_in_usd}}</strong>
                        @else
                            <strong>{{$product->price_in_bdt}}TK</strong>
                        @endif
                    @endif    
                </li>
            </ul>
        </div>
        <div class="col-md-8 order-md-1">
            <h4 class="mb-3">Billing</h4>
            <form class="needs-validation" action="{{route('order.single.store' , $product->id)}}" method="post">
                @csrf
                <div class="row">
                    @if (!empty($product->attr->value))
                        <div class="col-md-6 mb-3">
                            @php
                                $arrayOfAttr = explode(',', $product->attr?->value);
                            @endphp
                            <label for="size">{{ $product->attr?->name }}</label>
                            <select name="size" class="form-control @error('size') is-error @enderror">
                                
                                        <option value="Size Less" selected disable>select size</option>
                                @if (count($arrayOfAttr) > 0)     
                                    @foreach ($arrayOfAttr as $attr)
                                        <option value="{{$attr ?? "Size Less"}}"  disable>{{ $attr ?? "Size Less" }}</option>
                                    @endforeach
                                @endif
                                
                            </select>
                        </div>
                    @endif
                    <div class="col-md-6 mb-3">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="location">Location</label>
                        <input type="text" value="{{old('location')}}" class="form-control" name="location" id="location" placeholder="Location" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="road_no">Road No</label>
                        <input type="text" value="{{old('road_no')}}" class="form-control" name="road_no" id="road_no" placeholder="Road No" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="house_no">House No</label>
                        <input type="text" value="{{old('house_no')}}" class="form-control" name="house_no" id="house_no" placeholder="House No" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="1st Phone Number">1st Phone Number</label>
                    <div class="input-group">
                        <input type="text" value="{{old('number_1')}}" class="form-control" name="number_1" id="1st Phone Number" placeholder="1st Phone Number" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="2nd Phone Number">2nd Phone Number</label>
                    <div class="input-group">
                        <input type="text" value="{{old('number_2')}}" class="form-control" name="number_2" id="2nd Phone Number" placeholder="2nd Phone Number">
                    </div>
                </div>

                <hr class="mb-4">
                <button style="margin: 1.5em 0em;" class="btn btn-danger btn-lg btn-block" type="submit">Confirm Order</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    @if (session('success'))
        toastr.success("{{ session('success') }}", 'Success', {
            positionClass: 'toast-top-right',
            timeOut: 3000
        });
    @endif

    @if (session('warning'))
            toastr.warning("{{ session('warning') }}", 'Warning', {
                positionClass: 'toast-top-right',
                timeOut: 3000
            });
        @endif
</script>
@endsection