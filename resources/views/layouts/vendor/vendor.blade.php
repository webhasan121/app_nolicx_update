<x-dashboard.container>
    @include('layouts.vendor.overview.overview')
</x-dashboard.container>

<x-dashboard.container>
    <p class="text-xs mb-2">
        Recent Orders
    </p>
</x-dashboard.container>
@livewire('vendor.orders.index', ['nav' => 'All', 'create' => 'day', 'start_date' => now()->format('Y-m-d')])