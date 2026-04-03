@extends("layouts.user.app")
@section('title')
    Categories | Gorom Bazar
@endsection
@section('content')

<section class=" product_section layout_padding">
    <div class="container">
        <div class="heading_container heading_center">
            <h2>
                Our <span>Categories</span>
            </h2>
        </div>
        
        {{-- <div class="row"> --}}
        <div style="display: grid; grid-template-columns:repeat(auto-fill, minmax(149px, 1fr));grid-gap: 10px;">
            @foreach($categories as $product)
                <x-cat :cat="$product" :key="$product->id" />
            @endforeach    
        </div>
    </div>
</section>
@endsection