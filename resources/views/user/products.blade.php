@extends("layouts.user.app")
@section('title')
    Products | Gorom Bazar
@endsection
@section('content')
<style>
    /* .discount-badge {
        position: absolute;
        top: 0;
        left: 0;
        background-color: gray;
        color: white;
        font-weight: bold;
        padding: 5px 10px;
        clip-path: polygon(0 0, 100% 0, 0 100%);
        width: 80px;
        height: 65px;
        text-align: center;
        display: flex;
        font-size: 15px;
    } */
</style>
<section class="product_section layout_padding">
    <div class="container">
        <div class="heading_container heading_center">
            <h2>
                Our <span>products</span>
            </h2>
        </div>
        {{-- <div class="" style="display: grid; justify-content:center; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); grid-gap:10px">
            @foreach ($products as $product)
                <x-product-card :$product :key="$product->id" />
                
            @endforeach

        </div> --}}
        {{-- <div class="col-sm-6 col-md-4 col-lg-4">
            <div class="box" style="padding: 35px 5px;">
                @if($product->offer_type == 'yes' && $product->discount)
                    @php
                        $originalPrice = $product->price_in_usd ?? $product->price_in_bdt;
                        $discountedPrice = $product->discount;
                        $discountPercentage = (($originalPrice - $discountedPrice) / $originalPrice) * 100;
                    @endphp
                    <div class="discount-badge">{{ round($discountPercentage, 2) }}%</div>
                @endif  
                @auth
                    <div class="option_container" style="background-color:transparent;">
                        <div class="options" style="gap:10px">
                            <form action="{{route('cart.add' , $product->id)}}" method="post">
                                @csrf
                                <button style="border-radius: 20px;" type="submit" class="option1">
                                    Add To Cart
                                </button>
                            </form>
                            <button type="button" onclick="window.location.href='{{ route('order.single', ['id' => $product->id]) }}'"
                                class="option2" style="border-radius: 20px;" class="option2">
                                    Order Now
                            </button>
                            <button onclick="window.location.href='{{ route('product.details', ['id' => $product->id]) }}'"
                                style="border-radius: 20px;" class="option1">
                                View Details
                            </button>
                        </div>
                    </div>
                @endauth
                <div class="img-box">
                    <img src="{{ asset('product-images/' . $product->image) }}">
                </div>
                <div style="display:flex;align-items: center;@if($product->offer_type == 'yes')justify-content: space-between;@else justify-content: center; @endif font-weight: bold;">
                    <div>
                        MRP @if($product->price_in_usd != null) ${{$product->price_in_usd}} @else {{$product->price_in_bdt}} tk @endif 
                    </div>
                    @if($product->offer_type == 'yes')
                        <div>
                            Selling Price  
                                @if($product->price_in_usd != null)                                
                                    ${{$product->discount}}
                                @else 
                                    {{$product->discount}} tk 
                                @endif    
                        </div>
                    @endif 
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin: 10px 0px;">
                    <div style="background-color:gray;width:50%;display:flex;align-items: center;justify-content: center;padding: 0.7em;border-top-right-radius: 100px; border-bottom-right-radius: 100px; font-weight:bold;flex-wrap:nowrap;">
                        {{ $product->name }}
                    </div>
                    <div style="background-color:gray;width:35%;display:flex;align-items: center;justify-content: center;padding: 0.7em;border-top-right-radius: 35px; border-top-left-radius: 35px; font-weight:bold; flex-wrap:nowrap;">
                        {{ $product->unit }}
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="row m-0">
            {{-- @foreach($products as $product)
            
                <div class="col-6 col-md-4 col-lg-3 px-2 mb-3 ">
                    <x-product-card :$product />
                    
                </div>
                
                @endforeach     --}}
        </div>
        <x-product_loop :$products />
    </div>
</section>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> --}}
<script>
    // @if (session('success'))
    //     toastr.success("{{ session('success') }}", 'Success', {
    //         positionClass: 'toast-top-right',
    //         timeOut: 3000
    //     });
    // @endif

    // @if (session('warning'))
    //     toastr.warning("{{ session('warning') }}", 'Warning', {
    //         positionClass: 'toast-top-right',
    //         timeOut: 3000
    //     });
    // @endif
</script>
@endsection