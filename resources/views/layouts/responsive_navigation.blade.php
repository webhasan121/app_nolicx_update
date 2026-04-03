@if (auth()->user()?->hasRole('admin') || auth()->user()?->hasRole('system'))
@can('users_view')
<x-responsive-nav-link :href="route('system.users.view')" :active="request()->routeIs('system.users.*')">
    <i class="fas fa-users pr-2 w-6"></i> {{ __('Users') }}
</x-responsive-nav-link>
@endcan

@can('admin_view')
<x-responsive-nav-link :href="route('system.admin')" :active="request()->routeIs('system.admin')">
    <i class="fas fa-user-lock pr-2 w-6"></i> {{ __('Admin') }}
</x-responsive-nav-link>
@endcan
@can('vendors_view')
<x-responsive-nav-link :href="route('system.vendor.index')" :active="request()->routeIs('system.vendor.*')">
    <i class="fas fa-shop pr-2 w-6"></i> {{ __('Vendor') }}
</x-responsive-nav-link>
@endcan

@can('resellers_view')
<x-responsive-nav-link :href="route('system.reseller.index')" :active="request()->routeIs('system.reseller.*')">
    <i class="fas fa-shop pr-2 w-6"></i> {{ __('Reseller') }}
</x-responsive-nav-link>
@endcan

@can('riders_view')
<x-responsive-nav-link :href="route('system.rider.index')" :active="request()->routeIs('system.rider.*')">
    <i class="fas fa-truck-fast pr-2 w-6"></i> {{ __('Rider') }}
</x-responsive-nav-link>
@endcan
@can('role_list')
<x-responsive-nav-link :href="route('system.role.list')" :active="request()->routeIs('system.role.*')">
    <i class="fas fa-user-shield pr-2 w-6"></i> {{ __('Role') }}
</x-responsive-nav-link>
@endcan

{{-- @if(Route::has('system.report.index'))
<x-responsive-nav-link :href="route('system.report.index')" :active="request()->routeIs('system.report.*')">
    <i class="fas fa-chart-bar pr-2 w-6"></i> Report
</x-responsive-nav-link>
@endif --}}

<x-responsive-nav-link :href="route('system.consignment.index')" :active="request()->routeIs('system.consignment.*')">
    <i class="fas fa-truck-fast pr-2 w-6"></i> Consignment
</x-responsive-nav-link>


<x-hr />
@can('product_view')
<x-responsive-nav-link :href="route('system.products.index')" :active="request()->routeIs('system.products.*')">
    <i class="fas fa-layer-group pr-2 w-6"></i> {{ __('Products') }}
</x-responsive-nav-link>
@endcan
@can('category_view')
<x-responsive-nav-link :href="route('system.categories.index')" :active="request()->routeIs('system.categories.*')">
    <i class="fas fa-table pr-2 w-6"></i> {{ __('Categories') }}
</x-responsive-nav-link>
@endcan
@can('order_view')

<x-responsive-nav-link :href="route('system.orders.index')" :active="request()->routeIs('system.orders.*')">
    <i class="fas fa-cart-plus pr-2 w-6"></i> {{ __('Orders') }}
</x-responsive-nav-link>
@endcan


{{-- @if (auth()->user()->hasRole('system'))


@endif --}}

{{-- @can('role_list')
@endcan --}}
@can('vip_view')

<x-responsive-nav-link :href="route('system.vip.index')" :active="request()->routeIs('system.vip.*')">
    <i class="fas fa-box-open pr-2 w-6"></i> {{ __('ViP Package') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('system.vip.users')" :active="request()->routeIs('system.vip.users')">
    <i class="fas fa-user-tie pr-2 w-6"></i> {{ __('ViP Users') }}
</x-responsive-nav-link>
@endcan

@can('slider_view')
<x-responsive-nav-link :href="route('system.slider.index')" :active="request()->routeIs('system.slider.*')">
    <i class="fas fa-photo-film pr-2 w-6"></i>{{ __('Carousel') }}
</x-responsive-nav-link>
@endcan

@can('slider_view')
<x-responsive-nav-link :href="route('system.static-slider.index')"
    :active="request()->routeIs('system.static-slider.*')">
    <i class="fas fa-photo-film pr-2 w-6"></i>{{ __('Static Slider') }}
</x-responsive-nav-link>
@endcan
{{-- @can('', $post)

@endcan
<x-responsive-nav-link :href="route('system.navigations.index')" :active="request()->routeIs('system.navigations.*')">
    {{ __('Navigations') }}
</x-responsive-nav-link> --}}

<x-hr />
<x-responsive-nav-link href="{{route('system.settings.index')}}" :active="request()->routeIs('system.settings.*')">
    <i class="fas fa-gear pr-2 w-6"></i> Settings
</x-responsive-nav-link>

<x-hr />
{{-- @can('partnership_index')
<x-responsive-nav-link :href="route('system.partnership.index')" :active="request()->routeIs('system.partnership.index*')">
    <i class="fas fa-handshake pr-2 w-6"></i> {{ __('Partnership') }}
</x-responsive-nav-link>
@endcan --}}

{{-- @can('partnership_developer') --}}
<x-responsive-nav-link :href="route('system.partnership.developer')" :active="request()->routeIs('system.partnership.developer*')">
    <i class="fas fa-handshake pr-2 w-6"></i> {{ __('Developer') }}
</x-responsive-nav-link>
{{-- @endcan --}}

{{-- @can('partnership_management') --}}
<x-responsive-nav-link :href="route('system.partnership.management')" :active="request()->routeIs('system.partnership.management*')">
    <i class="fas fa-handshake pr-2 w-6"></i> {{ __('Management') }}
</x-responsive-nav-link>
{{-- @endcan --}}
<x-hr />

@can('store_view')
<x-responsive-nav-link :href="route('system.store.index')" :active="request()->routeIs('system.store.*')">
    <i class="fas fa-store pr-2 w-6"></i> {{ __('Store') }}
</x-responsive-nav-link>
@endcan

<x-hr />
@can('deposit_view')

<x-responsive-nav-link :href="route('system.deposit.index')" :active="request()->routeIs('system.deposit.*')">
    <i class="fas fa-sign-in pr-2 w-6"></i> {{ __('Deposit') }}
</x-responsive-nav-link>
@endcan

@can('comission_view')

<x-responsive-nav-link :href="route('system.earn.index')" :active="request()->routeIs('system.earn.*')">
    <i class="fas fa-money-bill pr-2 w-6"></i> {{ __('Sell') }}
</x-responsive-nav-link>

<x-responsive-nav-link :href="route('system.comissions.index')" :active="request()->routeIs('system.comissions.*')">
    <i class="fas fa-money-bill-transfer pr-2 w-6"></i> {{ __('Comission') }}
</x-responsive-nav-link>
{{-- <x-responsive-nav-link :href="route('system.comissions.index')"
    :active="request()->routeIs('system.comissions.*')">
    <i class="fas fa-money-bill-transfer pr-2 w-6"></i> {{ __('Com Distribute') }}
</x-responsive-nav-link> --}}
@endcan

@can('withdraw_view')

<x-responsive-nav-link :href="route('system.withdraw.index')" :active="request()->routeIs('*.withdraw.*')">
    <i class="fas fa-arrow-up-from-bracket pr-2 w-6"></i> {{ __('Withdraw') }}
</x-responsive-nav-link>
@endcan

{{-- <x-responsive-nav-link :href="route('system.logs')" :active="request()->routeIs('system.logs')">
    <i class="fas fa-file-lines pr-2 w-6"></i> {{ __('Logs') }}
</x-responsive-nav-link> --}}
{{-- <x-responsive-nav-link :href="route('system.footer.builder')" :active="request()->routeIs('system.footer.*')">
    <i class="fas fa-pen pr-2 w-6"></i> Builder
</x-responsive-nav-link> --}}
{{-- @if (auth()->user()->hasRole('system'))
@endif --}}
@endif