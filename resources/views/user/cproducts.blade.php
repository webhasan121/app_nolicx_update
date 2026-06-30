@extends("layouts.user.app")
@section('title')
    Products | Gorom Bazar
@endsection
@section('content')
<style>
    .discount-badge {
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
    }
</style>
<section class="product_section layout_padding">
    <div class="container">
        <div class="heading_container heading_center">
            <h2>
                Our <span>products</span>
            </h2>
        </div>
        {{-- <div class="" style="display: grid; justify-content:center; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); grid-gap:10px">
            @foreach($products as $product)
               <x-product-card :$product :key="$product->id"/>
            @endforeach    
        </div> --}}
        
        <x-product_loop :$products />

        @if (!$products || count($products) == 0)
            <div class="alert alert-info">No Product Found !</div>
        @endif
    </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    // @if (session('success'))
    //     toastr.success("{{ session('success') }}", 'Success', {
    //         positionClass: 'toast-top-right',
    //         timeOut: 3000
    //     });
    // @endif

    // @if (session('warning'))
    //         toastr.warning("{{ session('warning') }}", 'Warning', {
    //             positionClass: 'toast-top-right',
    //             timeOut: 3000
    //         });
    //     @endif
</script>
@endsection