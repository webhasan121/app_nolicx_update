<div>
    <x-dashboard.container>

        <div class="flex justify-start items-center py-3 mb-3">
            <i class="fas fa-home pe-2"></i>
            {{-- <i class="fas fa-slash-back px-2 py-0 m-0"></i> --}}
            <div>
                search
            </div>
            {{-- <i class="fas fa-caret-right px-2 py-0 m-0"></i> --}}
            <div class="px-2">
                {{$q}}
            </div>
        </div>
        @if ($product->count() > 0)
        <div class="product_section">
            <div class=""
                style="display: grid; justify-content:start; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); grid-gap:10px">
                @foreach ($product as $prod)
                <x-client.product-cart :product="$prod" :key="$prod->id" />
                @endforeach
            </div>
        </div>
        {{ $product->links() }}
        @else
        <p>No products found.</p>
        @endif
    </x-dashboard.container>
    <x-hr />
    <x-dashboard.container>
        @if ($shop->count() > 0)
        <div
            style="display: grid; grid-template-columns:repeat(auto-fit, 300px); justify-content:start; align-items:start; grid-gap:10px">

            @foreach ($shop as $sh)
            <x-client.shops-cart :shop="$sh" :key="$sh->id" />
            @endforeach

        </div>
        <x-hr />
        @endif
    </x-dashboard.container>
    {{--
    <x-hr /> --}}
    <x-dashboard.container>

        @if (count($category) > 0)
        <div>
            Categories
        </div>
        @includeIf('components.client.display-category', ['categories' => $category])
        @endif

    </x-dashboard.container>
</div>