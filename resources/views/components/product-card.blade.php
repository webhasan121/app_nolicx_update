@props(['product'])

<div>{{ $product->name ?? $product->title ?? '' }}</div>
