<div>

    @if ($todays_products)

    <div class="py-4 flex px-2 justify-between items-center">
        <div class="text-xl font-bold">
            Today's
        </div>

        <div class="text-center">
            <x-nav-link href="{{route('products.index', ['tag' => 'today'])}}" class="px-3 py-2 rounded ">
                View All
            </x-nav-link>
        </div>
    </div>

    <div class="product_section pb-4" x-loading.disabled x-transition>
        <x-client.products-loop :products="$todays_products" />
    </div>
    @endif
</div>