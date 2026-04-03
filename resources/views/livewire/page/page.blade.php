<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    @if ($pages)
    <x-dashboard.container>
        <div class="md:flex">

            <div class="p-2 hidden md:block" style="max-width: 200px">
                @foreach ($otherPages as $item)
                    <x-nav-link href="/page/{{$item->slug}}" class="py-2 mb-1 border-b w-full" :active="$slug == $item->slug">
                        <i class="fas fa-angle-right text-indigo-900 pr-2"></i> {{ Str::ucfirst($item->name) }}
                    </x-nav-link>
                @endforeach
            </div>

            <x-dashboard.section>

                <div>
                    <div class="text-lg text-bold font-bold bg-gray-50 p-3">
                        {{ Str::upper($pages->name)}}
                    </div>
                    <p>
                        {!! $pages->content !!}
                    </p>
                </div>

            </x-dashboard.section>
        </div>
    </x-dashboard.container>
    @else
        <p class="w-full py-2 bg-gray-50">
            Not Found !
        </p>
    @endif
</div>
