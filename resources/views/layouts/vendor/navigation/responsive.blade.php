<x-responsive-nav-link href="{{route('my-shop', ['user' => Str::slug(auth()->user()->name)])}}"
    :active="request()->routeIs('my-shop')">
    <i class="fas fa-shop pr-2 w-6"></i> My Shop
</x-responsive-nav-link>
<x-hr />
<x-responsive-nav-link href="{{route('vendor.products.view')}}" :active="request()->routeIs('vendor.products.*')">
    <i class="fas fa-layer-group pr-2"></i>Products
</x-responsive-nav-link>

<x-responsive-nav-link href="{{route('vendor.products.create')}}"
    :active="request()->routeIs('vendor.products.create')">
    <i class="fas fa-plus pr-2"></i>Products
</x-responsive-nav-link>

{{-- <x-responsive-nav-link href="{{route('vendor.category.view')}}" :active="request()->routeIs('vendor.category.*')">
    Categories
</x-responsive-nav-link> --}}

<x-responsive-nav-link href="{{route('vendor.orders.index')}}" :active="request()->routeIs('vendor.orders.*')">
    <i class="fas fa-sort pr-2 w-6"></i> Orders
</x-responsive-nav-link>
<x-responsive-nav-link href="{{route('reseller.sel.index')}}" :active="request()->routeIs('reseller.sel.*')">
    <i class="fas fa-shopping-cart pr-2 w-6"></i> Sel & Earn
</x-responsive-nav-link>
{{-- <x-responsive-nav-link href="">
    <i class="fas fa-money-bill-transfer pr-2 w-6"></i> Comissions
</x-responsive-nav-link> --}}