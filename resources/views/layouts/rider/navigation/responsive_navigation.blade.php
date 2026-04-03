<x-responsive-nav-link href="{{route('rider.me')}}" :active="request()->routeIs('rider.me')">
    <i class="fas fa-person-biking pr-2 w-6"></i> My Rider
</x-responsive-nav-link>
<hr />

<x-responsive-nav-link href="{{route('rider.consignment')}}" :active="request()->routeIs('rider.consignment')">
    <i class="fas fa-truck-fast pr-2 w-6"></i> Consignments
</x-responsive-nav-link>

{{-- <x-responsive-nav-link href="{{route('my-shop', ['user' => auth()->user()->name])}}"
    :active="request()->routeIs('my-shop')">
    <i class="fas fa-shop pr-2 w-6"></i> Pending Order
</x-responsive-nav-link> --}}