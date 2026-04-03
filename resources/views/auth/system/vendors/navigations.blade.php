{{-- <x-dashboard.container>
    <x-dashboard.section>
        <x-dashboard.section.inner>
        </x-dashboard.section.inner>
    </x-dahsboard.section>
</x-dashboard.container> --}}
<x-dashboard.section.header>
    <x-slot name="title">
        {{$vendor?->shop_name_en ?? "N/A"}} / {{$vendor?->shop_name_bn ?? "N/a"}}
    </x-slot>
    
    <x-slot name="content">
        <div class="text-sm">
            <x-nav-link href="{{route('system.users.edit', ['id' => $vendor?->user?->id])}}">
                {{$vendor?->user?->name ?? "N/A"}}
            </x-nav-link>
            - {{$vendor?->status ?? "N/A"}}
        </div>
        <span class="text-xs text-gray-400">
            {{$vendor?->created_at?->toFormattedDateString() ?? ""}}
        </span>
    </x-slot>
    {{-- @livewire('component', ['user' => $user], key($user->id)) --}}
</x-dashboard.section.header>
<br>
<x-nav-link :active="request()->routeIs('system.vendor.edit')" href="{{route('system.vendor.edit', ['id' => $vendor?->id])}}">User</x-nav-link>
<x-nav-link :active="request()->routeIs('system.vendor.settings')" href="{{route('system.vendor.settings', ['id' => $vendor?->id])}}">Settings</x-nav-link>
<x-nav-link :active="request()->routeIs('system.vendor.documents')" href="{{route('system.vendor.documents', ['id' => $vendor?->id])}}">Documents</x-nav-link>
<x-nav-link :active="request()->routeIs('system.products.index')" href="{{route('system.products.index', ['find' => $vendor?->id, 'from' => 'vendor'])}}">Products</x-nav-link>
{{-- <x-nav-link :active="request()->routeIs('system.vendor.categories')" href="{{route('system.vendor.categories', ['id' => $vendor?->id])}}">Categories</x-nav-link> --}}
{{-- <x-nav-link class="" :active="request()->routeIs('system.vendor.orders')" href="{{route('system.vendor.orders', ['id' => $vendor?->id])}}">Order</x-nav-link> --}}