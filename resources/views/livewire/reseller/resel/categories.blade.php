<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
    <x-dashboard.page-header>
        Categories to resel
        <br>
        <div>
            <x-nav-link href="{{route('reseller.resel-product.index')}}" > View All Products </x-nav-link>
        </div>
    </x-dashboard.page-header>

    @foreach ($categories as $item)
        @if ($item->slug != 'default-category')
            <div class="p-2 border-b border-gray-200 hover:bg-gray-50 cursor-pointer ">
            
                <div>
                    <x-nav-link :active="$cat == $item->id" href="{{route('reseller.resel-product.index', ['cat' => $item->id])}}">
                        {{ Str::ucfirst($item->name)}}
                
                    </x-nav-link>
            
                    <div>
                        {{-- subcategories: --}}
                        @if ($item->children()->count() > 0)
                            <div class="px-2 py-1 border-l ">
                                @foreach ($item->children as $child)
                                <div class="">
                                        <x-nav-link :active="$cat == $child->id" href="{{route('reseller.resel-product.index', ['cat' => $child->id])}}">
                                            {{ Str::ucfirst($child->name)}}
                                        
                                        </x-nav-link>
                                        
                                        <div class="ps-2">
                                            @if ($child->products->count() > 0)
                                            @endif
                                            @foreach ($child->children as $sc)
                                                <x-nav-link :active="$cat == $sc->id" href="{{route('reseller.resel-product.index', ['cat' => $sc->id])}}">
                                                    {{ Str::ucfirst($sc->name)}}
                                                
                                                </x-nav-link>
                                                @endforeach
                                        </div>
                                    </div>
                                    
                                    {{-- {{ !$loop->last ? ', ' : '' }} --}}
                                @endforeach
                            </div>
                        @else
                            <span class="text-sm text-gray-500">
                                {{ __('No subcategories') }}
                            </span>
                        @endif
                    </div>
                </div>
            
            </div>
        @endif     
    @endforeach


    {{-- <div style="display: grid; justify-content:start; grid-template-columns: repeat(auto-fill,100px); grid-gap:10px">
        @foreach ($categories as $item)
            <a href="{{route('reseller.resel-product.index', ['cat' => $item->id])}}" style="height: 100px" @class(['relative bg-white rounded'])>
                
                <img style="height:100px; width:100px" class="rounded" src="{{asset('storage/'. $item->image)}}" alt="">
                <div @class(['absolute bottom-0 text-center w-full px-1 bg-gray-200', 'bg-indigo-900 text-white' => $cat && $cat == $item->id])>
                    {{$item->name}}
                </div>

            </a>
        @endforeach
    </div> --}}
</div>
