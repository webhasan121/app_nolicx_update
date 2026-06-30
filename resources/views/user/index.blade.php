@extends("layouts.user.app")
@section('title')
    Home | Gorom Bazar
@endsection
@section('content')
<style>
    /* @media only screen and (max-width: 600px) { */
    @media (min-width: 767px) {
        .detail-box h1 {
            font-size: 3rem!important;
            margin-bottom: 0px;
        }
    }
    @media (min-width: 1199px) {
        .detail-box h1 {
            font-size: 4rem!important;
            margin-bottom: 0px;
        }
    }
    @media (max-width: 570px){

        .slider_bg_box img {
            width: 100%;
            height: auto;
            /* -o-object-fit: cover; */
            /* object-fit: cover; */
            /* -o-object-position: top right;
            object-position: top right; */
            aspect-ratio: 16 / 9;
        }
        .detail-box h1 {
            font-size: 4rem!important;
            margin-bottom: 0px;
        }
    }
    @media (max-width: 767px) {

        .slider_bg_box img {
            width: 100%;
            height: 100%;
            /* -o-object-fit: cover; */
            object-fit: cover;
            -o-object-position: top right;
            object-position: top right;
            aspect-ratio: 16 / 9;
        }
        .slider_section {
            padding: 20px 10px;
        }
        .detail-box h1 {
            font-size: 1.5rem!important;
            margin-bottom: 0px;
        }
        .detail-box a {
            margin-top: 0px!important;
            padding: 10px!important;
            font-weight: 500!important;
        }
        .slider_section .detail-box,
        .about_section .detail-box {
            margin-bottom: 0px;
        }
        .slider_section .carousel-indicators li {
            background-color: #ffffff;
            width: 12px!important;
            height: 12px!important;
            border-radius: 100%;
            opacity: 1;
        }

    }
</style>

<!-- slider section -->
@if ($slider)
<section class="slider_section ">
    <div class="slider_bg_box">
        @php
            $img = $slider?->image ? "slider/".$slider->image : "assets/user/images/slider-bg.jpg";
        @endphp
        <img src="{{asset($img)}}" alt="">
        {{-- <img src="{{asset($img)}}" alt=""> --}}

    </div>
    <div id="customCarousel1" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            @foreach ($slider->slides as $key => $slides)
                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                    <div class="container ">
                        <div class="row">
                            <div class="col-md-7 ">
                                <div class="detail-box text-left">
                                    <h1 class="display-lg-4 bold" style="color:{{$slides->status}}!important">
                                        <span style="color:{{$slides->status}}!important">
                                            {{$slides?->main_title ?? ""}}
                                        </span>
                                        <br>
                                        {{$slides?->subtitle ?? ""}}
                                    </h1>
                                    <p class="d-none d-lg-block" style="color:{{$slides->status}}!important">
                                        {{$slides?->description ?? ""}}
                                    </p>
                                    <div class="">
                                        <a href="{{route('uproducts.index')}}" class="btn btn_secondary">
                                                Shop Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            {{-- <div class="carousel-item ">
                <div class="container ">
                    <div class="row">
                        <div class="col-md-7 col-lg-6 ">
                            <div class="detail-box text-left">
                                <h1 class="display-lg-4 bold">
                                    <span>
                                        Sale 20% Off
                                    </span>
                                    <br>
                                    On Everything
                                </h1>
                                <p class="d-none d-lg-block">
                                    Explicabo esse amet tempora quibusdam laudantium, laborum eaque magnam
                                    fugiat hic? Esse dicta aliquid error repudiandae earum suscipit fugiat
                                    molestias, veniam, vel architecto veritatis delectus repellat modi impedit
                                    sequi.
                                </p>
                                <div class="">
                                    <a href="{{route('uproducts.index')}}" class="btn btn_secondary">
                                            Shop Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="container ">
                    <div class="row">
                        <div class="col-md-7 col-lg-6 ">
                            <div class="detail-box text-left">
                                <h1 class="display-lg-4 bold">
                                    <span>
                                        Sale 20% Off
                                    </span>
                                    <br>
                                    On Everything
                                </h1>
                                <p class="d-none d-lg-block">
                                    Explicabo esse amet tempora quibusdam laudantium, laborum eaque magnam
                                    fugiat hic? Esse dicta aliquid error repudiandae earum suscipit fugiat
                                    molestias, veniam, vel architecto veritatis delectus repellat modi impedit
                                    sequi.
                                </p>
                                <div class="">
                                    <a href="{{route('uproducts.index')}}" class=" btn btn_secondary">
                                            Shop Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
        <div class="container d-flex justify-content-center justify-content-lg-start">
            <ol class="carousel-indicators px-1 m-0 p-0 rounded mt-lg-5" style="background-color: rgb(234, 234, 234)">
                @if (count($slider->slides) > 1)      
                    @foreach ($slider->slides as $key => $slides)
                    <li data-target="#customCarousel1" data-slide-to="{{$key}}" class=" {{ $key == 0 ? 'active' : ""}} "></li>
                    @endforeach
                @endif
                {{-- <li data-target="#customCarousel1" data-slide-to="1"></li>
                <li data-target="#customCarousel1" data-slide-to="2"></li> --}}
            </ol>
        </div>
    </div>
    
</section>
@endif
<!-- end slider section -->


<section class="product_section layout_padding">
    <div class="container">
        <div class="heading_container heading_center">
            <h2 class="text_secondary bold">
                Gorom <span>Bazar</span>
            </h2>
        </div>
        {{--         
        <div class="" style="display: grid; justify-content:center; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); grid-gap:10px">
            @foreach($products as $product)
                <div class="">
                    <x-product-card :$product :key="$product->id" />
                </div>
            @endforeach    
        </div> --}}
        {{-- <div class="row m-0">
            @foreach($products as $product)
                <div class="col-6 col-md-4 col-lg-3 p-1 px-2 mb-3">
                    <x-product-card :$product :key="$product->id" />
                </div>
            @endforeach    
        </div> --}}

        <x-product_loop :$products />

        <div class="text-center">
            <a href="{{route('uproducts.index')}}" class="btn btn_outline_secondary">
                View All products
            </a>
        </div>
    </div>
</section>
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