<div>
    {{-- The Master doesn't talk, he acts. --}}
    @if (count($products))
    <div class="py-4 flex px-2 justify-between items-center">
        <div class="text-xl font-bold">
            Top Sales
        </div>

    </div>
    <div class="product_section" x-loading.disabled x-transition>
        <x-client.products-loop :$products />
    </div>
    @endif
</div>