<div>

    @php
    $classes = ($active ?? false)
    ? 'inline-flex items-center px-1 pt-1 border-b-2 border-orange-400 text-sm font-medium leading-5 text-gray-900
    focus:outline-none focus:border-orange-700 transition duration-150 ease-in-out'
    : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500
    hover:text-white hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition
    duration-150 ease-in-out';
    @endphp

    {{-- <a {{$attributes->merge(['class' => "inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs
        uppercase tracking-widest hover:text-white hover:bg-sky-700 focus:bg-sky-700 active:bg-sky-900
        focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 transition ease-in-out duration-150"])}}
        wire:navigate>
        {{ $slot }}
    </a> --}}
    <a {{$attributes->merge(['class' => "inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md
        font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none
        focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out
        duration-150"])}} wire:navigate>
        {{ $slot }}
    </a>

</div>