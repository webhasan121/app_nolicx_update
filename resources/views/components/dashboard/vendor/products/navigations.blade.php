<?php 
use Livewire\Volt\Component;
use App\Models\Product;

new class extends component 
{

    public $pd;

    public function mount() 
    {
        $this->pd = Product::find(decrypt($_GET["product"]));    
    }
    
}

?> 
<div>
    
    {{-- @volt('nav')
    <x-dashboard.section>
        <x-dashboard.section.header>
            <x-slot name="title">
                <img height="30px" width="50px" src="{{asset('storage/'.$this->pd->thumbnail)}}" />
                
            </x-slot>
            
            <x-slot name="content">
               
                <div>
                    {{$this->pd->title ?? "N/A"}}
                </div>

                <div class="text-sm">
                    category : <strong> {{$data['category']?->name ?? "N/A"}} </strong>
                </div>
            </x-slot>
        </x-dashboard.section.header>
    </x-dashboard.section>
    @endvolt --}}

    <div class="flex ">

        <x-nav-link href="{{route('vendor.products.edit', ['product' => $product, 'nav' => 'Product'])}}" :active="request()->routeIs('vendor.products.edit')">Product</x-nav-link>
        
        <div>
            {{-- <x-nav-link href="{{route('vendor.products.orders', ['product' => $product])}}" :active="request()->routeIs('vendor.products.orders')">Orders</x-nav-link> --}}
            <x-nav-link href="{{route('vendor.products.resell', ['product' => $product])}}" :active="request()->routeIs('vendor.products.resell')" >Resell</x-nav-link>
        </div>
    </div>
</div>