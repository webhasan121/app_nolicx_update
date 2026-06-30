@props(['item', 'active' => false, 'cat' => '', 'style' => ""])
{{-- @props(['item', 'active' => false]) --}}

@if ($item->slug != 'default-category')

    <div class="cat-item" x-data="{ open: true }">
        <div class="py-1 text-lg">
            <div class="flex items-center justify-between {{ $active ? 'bg-white' : '' }}">
                <div class="flex-1 text-lg">

                    <x-nav-link class=" {{ $active ? 'bg_secondary text-white' : '' }} font-bold text-bold text-gray-900 text-md w-full" href="{{ route('category.products', ['cat' => $item->slug]) }}">
                        {{-- <i class="fas fa-chevron-right"></i> --}}
                        <div class="{{$style}}"> {{ Str::ucfirst( $item->name) }} </div>
                    </x-nav-link>

                </div>
                <div class="{{ $item->children->count() > 0 ? '' : 'hidden' }} text-sm text-gray-500" x-on:click="open = !open">
                    {{-- <i class="fas fa-chevron-right"></i> --}}
                    {{-- <i class="text-gray-500 fas fa-chevron-right"></i> --}}
                    <i x-show="open" class="text-gray-500 fas fa-chevron-down"></i>
                    <i x-show="!open" class=" {{ $active ? 'text-indigo-900' : '' }} fas fa-chevron-right"></i>
                </div>
                {{-- <i class=" {{ $item->children->count() > 0 ? '' : 'hidden' }} fas fa-chevron-right"></i> --}}

            </div>
        </div>
        @if ($item->children->count() > 0)
            {{-- <i class="text-gray-500 fas fa-chevron-down"></i> --}}
            <div class="pl-2 border-l border-gray-900 ms-2" x-show="open" x-collapse>

                @foreach ($item->children as $child)
                    <x-client.cat-loop :item="$child" :key="$child->id" :active="$cat == $child->slug" :cat="$cat" />
                @endforeach
            </div>
        @endif
    </div>
@endif
