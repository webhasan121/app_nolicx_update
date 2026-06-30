@props(['products' => collect()])

<div>
    @foreach ($products as $product)
        <x-product-card :product="$product" />
    @endforeach
</div>
