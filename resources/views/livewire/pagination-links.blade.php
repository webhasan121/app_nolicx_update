<div class="flex items-center justify-between mt-4 text-sm text-gray-600">
    {{-- Showing text --}}
    <div>
        Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }}
        of {{ $paginator->total() }} results
    </div>

    {{-- Pagination buttons --}}
    <div class="inline-flex items-center space-x-1">
        {{-- Previous --}}
        <button
            wire:click="previousPage"
            @if($paginator->onFirstPage()) disabled @endif
            class="px-3 py-1 border rounded {{ $paginator->onFirstPage() ? 'bg-gray-100 text-gray-400' : 'hover:bg-gray-100' }}">
            Prev
        </button>

        {{-- Pages --}}
        @php
            $total = $paginator->lastPage();
            $current = $paginator->currentPage();
            $range = 1; // pages to show on left/right
        @endphp

        {{-- First page --}}
        @if($current > 2)
            <button wire:click="gotoPage(1)" class="px-3 py-1 border rounded hover:bg-gray-100">1</button>
            @if($current > 3)
                <span class="px-2">…</span>
            @endif
        @endif

        {{-- Left page --}}
        @if($current - $range > 1)
            <button wire:click="gotoPage({{ $current - 1 }})"
                class="px-3 py-1 border rounded hover:bg-gray-100">{{ $current - 1 }}</button>
        @endif

        {{-- Current page --}}
        <button class="px-3 py-1 border rounded bg-indigo-600 text-white">{{ $current }}</button>

        {{-- Right page --}}
        @if($current + $range < $total)
            <button wire:click="gotoPage({{ $current + 1 }})"
                class="px-3 py-1 border rounded hover:bg-gray-100">{{ $current + 1 }}</button>
        @endif

        {{-- Last page --}}
        @if($current < $total - 1)
            @if($current < $total - 2)
                <span class="px-2">…</span>
            @endif
            <button wire:click="gotoPage({{ $total }})"
                class="px-3 py-1 border rounded hover:bg-gray-100">{{ $total }}</button>
        @endif

        {{-- Next --}}
        <button
            wire:click="nextPage"
            @if(!$paginator->hasMorePages()) disabled @endif
            class="px-3 py-1 border rounded {{ !$paginator->hasMorePages() ? 'bg-gray-100 text-gray-400' : 'hover:bg-gray-100' }}">
            Next
        </button>
    </div>
</div>
