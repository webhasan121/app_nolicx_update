@extends('layouts.user.app')
@section('title')
    Search Product | Gorom Bazar
@endsection

@section('content')

    <div class="container product_section pb-5">
        @if ($data->count())
            <div class="">
                <p> {{ $data->total() }} products found. Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} results.</p>
            </div>
            <div class="row m-0 p-0">
                @foreach ($data as $product)
                    <div class="col-6 col-md-4 col-lg-2 p-2 mb-2">
                        <x-product-card :$product :key="$product->id" />
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $data->links() }}
            </div>
        @else
            <div class="p-3 text-center alert alert-warning">
                No Product Found! against your search <b>{{$_GET['search']}}</b>
            </div>
            <h5>
                Trending Products 
            </h5>
            <hr>
            <div class="row m-0 p-0">
                @foreach ($Products as $product)
                    <div class="col-6 col-md-4 col-lg-2 p-2 mb-2">
                        <x-product-card :$product :key="$product->id" />
                    </div>
                @endforeach
            </div>
        @endif
    </div>

@endsection
