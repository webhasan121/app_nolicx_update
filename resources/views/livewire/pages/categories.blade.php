<div>
    {{-- Success is as dangerous as failure. --}}
    <x-dashboard.container>
        <div class="">
                    
            <div>
                <x-nav-link-btn href="{{route('products.index')}}">All Product</x-nav-link-btn>
                <br>
            </div>
            {{-- <div class="row"> --}}
            @foreach ($categories as $item)
                <x-client.cat-loop :item="$item" :key="$item->ids" style="font-bold" />
            @endforeach  
            {{-- </div> --}}
        </div>
    </x-dashboard.container>
</div>
