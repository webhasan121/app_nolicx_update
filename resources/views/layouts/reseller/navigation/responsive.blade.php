<x-responsive-nav-link href="{{route('my-shop', ['user' => auth()->user()->name])}}"
    :active="request()->routeIs('my-shop')">
    <i class="fas fa-shop pr-2 w-6"></i> My Shop
</x-responsive-nav-link>
<hr />

<x-responsive-nav-link href="{{route('reseller.products.list')}}" :active="request()->routeIs('reseller.products.*')">
    <i class="fas fa-layer-group pr-2 w-6"></i> Your Products
</x-responsive-nav-link>

<x-responsive-nav-link href="{{route('vendor.products.create')}}" :active="request()->routeIs('vendor.products.*')">
    <i class="fas fa-plus pr-2 w-6"></i> Add Products
</x-responsive-nav-link>

<x-responsive-nav-link href="{{route('reseller.resel-product.index')}}"
    :active="request()->routeIs('reseller.resel-product.*')">
    <i class="fas fa-sync pr-2 w-6"></i> Resel Product
</x-responsive-nav-link>
<x-responsive-nav-link href="{{route('shops')}}" :active="request()->routeIs('shops')">
    <i class="fas fa-shop pr-2 w-6"></i> Vendor Shop
</x-responsive-nav-link>
<hr />

<x-responsive-nav-link href="{{route('vendor.orders.index')}}" :active="request()->routeIs('vendor.orders.*')">
    <i class="fas fa-sort pr-2 w-6"></i> Orders
</x-responsive-nav-link>

<x-responsive-nav-link href="{{route('reseller.sel.index')}}" :active="request()->routeIs('reseller.sel.*')">
    <i class="fas fa-shopping-cart pr-2 w-6"></i> Sel & Earn
</x-responsive-nav-link>

{{-- <x-responsive-nav-link href="{{route('reseller.comissions.index')}}"
    :active="request()->routeIs('reseller.comissions.*')">
    <i class="fas fa-money-bill-transfer pr-2 w-6"></i>Comissions
</x-responsive-nav-link> --}}