<?php 

use Livewire\Volt\Component;
use Livewire\Attributes\URL;
use App\Models\cart;

new class extends Component
{
    #[URL]
    public $product;
    public $reseller;
    
    public function addToCart()
    {

        if (Auth::guest()) {
            $this->dispatch('warning', 'Login to add Cart');
        } else {

            $isAlreadyInCart = auth()->user()->myCarts()->where(['product_id' => $this->product->id])->exists();
            if ($isAlreadyInCart) {
                $this->dispatch('info', 'Product already in cart');
            } else {
                cart::create(
                    [
                        'product_id' => $this->product?->id,
                        'name' => $this->product?->title,
                        'image' => $this->product?->thumbnail,
                        'price' => $this->product?->offer_type ? $this->product?->discount : $this->product?->price,
                        'user_id' => auth()->user()->id,
                        'user_type' => 'user',
                        'belongs_to' => $this->product?->user_id,
                        'belongs_to_type' => 'reseller',
                        'qty' => 1,
                    ]
                );

                $count = auth()->user()->myCarts()->count();
                // dd($isAlreadyInCart);
                $this->dispatch('cart', $count);
                $this->dispatch('success', 'Product Added to cart');
            }
        }
    }
}

?>

<style>
/* ===== DARAZ STYLE IMAGE ZOOM ===== */

.product-zoom-wrapper {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}

.image-area {
    position: relative;
    width: 420px;
    border: 1px solid #eee;
    background: #fff;
}

#productImage {
    width: 100%;
    display: block;
    object-fit: contain;
}

/* Lens */
#lens {
    position: absolute;
    width: 120px;
    height: 120px;
    border: 2px solid #ff6a00;
    background: rgba(255, 255, 255, 0.35);
    display: none;
    cursor: crosshair;
    z-index: 10;
    pointer-events: none; /* 🔥 IMPORTANT */
}

/* Zoom preview */
#zoomResult {
    position: absolute;
    top: 20px;                 /* adjust if needed */
    right: 0;               /* image width + gap */
    width: 100%;
    height: 420px;

    border: 1px solid #eee;
    background-repeat: no-repeat;
    background-color: #fff;

    display: none;
    z-index: 9999;             /* 🔥 stays above details */
    box-shadow: 0 8px 24px rgba(0,0,0,.15);
}

/* Mobile: disable overlay zoom */
@media (max-width: 768px) {
    #zoomResult {
        display: none !important;
    }
}
</style>

<div>
    @props(['product'])

    <div class="lg:flex justify-start item-start p-2 relative">
        <!-- card left -->
        <div class=" p-3 " style="width:100%; max-width:600px">
            <div class="img-display sm:flex sm:justify-start items-start lg:block rounded product-zoom-wrapper" wire:ignore >
                {{-- <div class="img-showcase" style="" wire:ignore >
                    <img id="preview" class="p-2 rounded-md border"
                        style="width: 100%; object-fit:contain; max-width:600px;" height="400"
                        src="{{ asset('storage/' . $product?->thumbnail) }}" alt="image" >
                </div> --}}

                <div class="image-area" style="" >
                    <img id="productImage" class="p-2 rounded-md border"
                        style="width: 100%; object-fit:contain; max-width:600px;" height="400"
                        src="{{ asset('storage/' . $product?->thumbnail) }}" alt="image" />
                    <div id="lens"></div>
                </div>

                @if ($product?->showcase)
                <div class="flex items-center md:block lg:flex flex-wrap">
                    <button class="p-1 rounded mb-1">
                        <img class=" border p-1 rounded" onclick="previewImage(this)"
                            src="{{asset('storage/'. $product?->thumbnail)}}" width="60px" height="60px" alt="">
                    </button>
                    @foreach ($product->showcase as $images)
                    <button class="p-1 rounded mb-1">
                        <img width="60px" height="60px" class=" border p-1 rounded" onclick="previewImage(this)"
                            src="{{asset('storage/'. $images?->image)}}" width="60px" height="60px" alt="">
                    </button>
                    @endforeach
                </div>
                @endif

            </div>
        </div>

        <div class="p-3 w-full relative" style="min-width: 300px">
            <div id="zoomResult"></div>
            <div>
                {{-- Shop --}}
                <div class="text-green-900 w-full text-sm flex items-center gap-6" >
                    <x-nav-link class="px-2 rounded-xl bg-gray-50"
                            href="{{route('shops.visit', ['id' => $product?->owner?->resellerShop(), 'name' => $product?->owner?->resellerShop()->shop_name_en])}}">
                        <strong>{{$product?->owner?->resellerShop()->shop_name_en ?? "N/A"}}</strong>
                    </x-nav-link>

                    @php
                        $rawPhone = $product?->owner?->resellerShop()->phone;
                        $phone = $rawPhone ? '880' . ltrim(preg_replace('/\D/', '', $rawPhone), '0') : null;
                    @endphp

                    @if ($phone)
                        <a
                            href="https://wa.me/{{ $phone }}"
                            target="_blank"
                            class="px-2 py-1 rounded-xl bg-gray-50 inline-block hover:bg-green-50"
                            title="Chat on WhatsApp"
                        >
                            <i class="fab fa-whatsapp text-gree-600 mr-2" ></i>
                            <span>{{ $rawPhone }}</span>
                        </a>
                    @endif
                    {{-- <div class="px-2 py-1 rounded-xl bg-gray-50" >
                        {{$product?->owner?->resellerShop()->phone ?? "N/A"}}
                    </div> --}}
                </div>
                <div style="font-size: 28px; font-weight:bold;" class="capitalize">{{$product->title}}</div>
                <div class="flex justify-between items-center py-2" style="font-size: 14px">

                    <div class="flex items-center">
                        <i class="text_primary fas fa-star"></i>
                        <i class="text_primary fas fa-star"></i>
                        <i class="text_primary fas fa-star"></i>
                        <i style="color: #737272" class=" fas fa-star"></i>
                        <i style="color: #737272" class=" fas fa-star"></i>
                        <div class="px-1" style="color: #737272">
                            7/10
                        </div>
                    </div>

                    <div class="cursor-pointer flex items-center">
                        <i style="color:var(--brand-primary);" class="fas fa-heart mr-2"></i>
                        <div>save for later</div>
                    </div>

                </div>


            </div>


            {{-- category --}}
            <div class=" text-sm flex items-center">

                <div class="text_primary bold rounded">
                    <a wire:navigate href="{{route('category.products' , ['cat' =>$product->category?->slug])}}">
                        {{$product->category?->name ?? "Undefined"}}
                    </a>
                </div>
            </div>

            {{-- comments --}}
            <div class="bg-gray-50">
                <x-hr />
                <i class="fas fa-comments px-2"></i> {{$product->comments->count()}} Reviews.
                <x-hr />
            </div>
            {{-- comments --}}

            {{-- attr --}}
            @if ($product->attr?->value)
            <div class="py-2 my-3 ">
                <h4> {{ Str::ucfirst($product->attr?->name) }} </h4>
                @php
                $arrayOfAttr = explode(',', $product->attr?->value);
                @endphp
                <div class="flex flex-wrap justify-start items-center my-1" style="flex-wrap: wrap;gap: 10px;">
                    @foreach ($arrayOfAttr as $attr)
                    <div class="px-2 text-sm bg-indigo-300 text-white rounded mr-1 d-none @if($attr) d-block @endif"
                        style="align-content:center; text-align:center">
                        {{ Str::upper($attr) }}
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- optional delivery --}}
            @if ($product->shipping_note)
            <div class=" flex bg-gray-50 shadow rounded-lg p-1 bg-indigo-900">
                <i class="h-auto block rounded bg-gray-50 shadow-xl fas fa-bell p-2"></i>
                <p class="p-2 text-xs text-white">
                    {{$product->shipping_note}}
                </p>
            </div>
            @endif
            {{-- optional delivery --}}

            {{-- price --}}
            <div class="py-3">

                @if($product->offer_type)
                <div class="">
                    <div style="font-size:20px; margin-right:12px"> Price : <strong class="text_secondary bold">
                            {{$product->discount}} TK </strong></div>

                    <div class="flex justify-start items-baseline">
                        <del class="" style="font-size: 15px">
                            MRP: {{$product->price}} TK
                        </del>
                        @php
                        $originalPrice = $product->price;
                        $discountedPrice = $product->discount;
                        $discountPercentage = (($originalPrice - $discountedPrice) / $originalPrice) * 100;
                        @endphp
                        <div class="text-xs px-2">{{ round($discountPercentage, 0) }}% OFF</div>
                    </div>
                </div>
                @else
                <div style="font-weight:bold;font-size:22px; color:var(--brand-primary); margin-right:12px"> Price :
                    {{$product->price}} TK </div>
                @endif

            </div>
            <x-hr />

            <div class="purchase-info flex justify-start items-center w-full space-x-2">
                <x-primary-button wire:navigate
                    href="{{route('product.makeOrder', ['id' => $product->id, 'slug' => $product->slug])}}">
                    Buy Now<i class="fas fa-arrow-right ms-2"></i>
                </x-primary-button>

                @volt('cartAdd')
                <x-secondary-button wire:click="addToCart" type="button" class="option1 py-2 space-x-2">
                    <i class="fas fa-cart-plus"></i> <span class="hidden md:block">{{ __('Add to Cart') }}</span>
                </x-secondary-button>
                @endvolt

            </div>

            @php
                $shareUrl = route('products.details', [
                    'id' => $product->id,
                    'slug' => $product->slug
                ]);

                $socialMedia = [
                    [
                        'icon'  => 'facebook-f',
                        'name'  => 'Facebook',
                        'href'  => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($shareUrl),
                        'color' => '#1877F2', // Facebook blue
                    ],
                    [
                        'icon'  => 'twitter',
                        'name'  => 'Twitter',
                        'href'  => 'https://twitter.com/intent/tweet?url=' . urlencode($shareUrl),
                        'color' => '#1DA1F2', // Twitter blue
                    ],
                    [
                        'icon'  => 'whatsapp',
                        'name'  => 'WhatsApp',
                        'href'  => 'https://wa.me/?text=' . urlencode($shareUrl),
                        'color' => '#25D366', // WhatsApp green
                    ],
                ];
            @endphp
            <div class="mt-8 text-xs">
                <div class="font-semibold mb-2">SHARE WITH YOUR FRIENDS</div>
                <div class="flex items-center gap-3">
                    @foreach ($socialMedia as $social)
                         <a href="{{ $social['href'] }}"
                           title="{{ $social['name'] }}"
                           target="_blank"
                           style="background-color: {{ $social['color'] }};"
                           class="w-10 h-10 text-2xl flex items-center justify-center rounded-full
                                  text-white">
                            <i class="fab fa-{{ $social['icon'] }}"></i>
                        </a>
                    @endforeach
                </div>
                <div class="mt-8">
                    <button 
                        id="copyLinkBtn"
                        class="w-36 px-4 py-2 flex items-center justify-center text-white rounded transition"
                        style="background: #4f4f4f;" 
                        onclick="copyProductLink()">
                        <i class="fas fa-link mr-2"></i> Copy Link
                    </button>
                </div>
            </div>
        </div>

        {{-- @if (isset($relatedProduct))
        <div class="p-3 rounded hidden md:block" style="min-width: 300px;">
            <div class="">
                Related Products
            </div>

            @foreach ($relatedProduct as $item)
            <div class="flex py-2 border-b">
                <img src="{{asset('storage/'. $item->thumbnail)}}" style="width: 70px; height:70px" alt="">
                <div class="px-2 w-full">
                    <a wire:navigate href="{{route('products.details', ['id' => $item->id, 'slug' => $item->slug])}}">
                        {{$item->title}}
                    </a>
                    <div style="width:100%; display:flex; flex-direction:colums-reverse; align-items: center; font-size:14px; justify-content:space-between"
                        class="w-full py-1">
                        @if($item->offer_type)

                        <div class="text-md @if($item->offer_type ) @else align-self:center @endif"
                            style="font-weight: bold; text-align:right">
                            {{$item->discount}} TK
                        </div>

                        <div class="text-xs">
                            <del>
                                MRP {{$item->price}} TK
                            </del>
                        </div>

                        @else
                        <div class=" test-md @if($item->offer_type ) pr-2 @else align-self:center @endif"
                            style="font-weight: bold; text-align:right">
                            {{$item->price}} TK
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

            {{$relatedProduct->links()}}
        </div>
        @endif --}}
    </div>

    {{-- <div class="flex flex-wrap items-center p-3">
        @if ($product->cod)
        <div class="flex items-center p-2 w-full">
            <i class="fas fa-check-circle pr-2 m-0 w-6 h-6"></i>
            <div>

                <strong>Cash On Delivery.</strong>
                <p class="text-xs">
                    Get the product and pay.
                </p>
            </div>
        </div>
        <x-hr />
        @endif
        @if ($product->hand)
        <div class="text-green-200 flex items-center p-2 w-full">
            <i class="fas fa-check-circle pr-2 m-0 w-6 h-6"></i>

            <div>

                <strong>Hand-To-Hand</strong>
                <p class="text-xs">
                    Get the product directly from the shop, save shipping amount.
                </p>
            </div>
        </div>
        <x-hr />
        @endif
    </div> --}}

    @if (isset($relatedProduct))
    <hr>
    <div class="sm:w-full p-3 ">

        <div class="font-bold">
            Related Products
        </div>
        <br>
        <div class="product_section"
            style="display: grid; justify-content:start; grid-template-columns: repeat(auto-fill, 160px); grid-gap:10px">
            @foreach ($relatedProduct as $product)
            {{-- @component('client.product-cart', ['product' => $item], key($item->id)) --}}
            {{-- @includeIf('view.name', ['some' => 'data']) --}}
            <x-client.product-cart :$product :key="$product->id" />
            @endforeach
        </div>



    </div>
    @endif

    <script>
        function previewImage(element){
            const file = element.src;
            console.log(file);
            document.getElementById('preview').src = file;
            // const reader = new FileReader();
            // reader.onload = () => {
            //     const preview = document.getElementById('preview');
            //     preview.src = reader.result;
            // };
            // reader.readAsDataURL(file);
                
        }
    </script>

    <script>
        function copyProductLink() {
            const url = "{{ route('products.details', ['id' => $product->id, 'slug' => $product->slug]) }}";
            navigator.clipboard.writeText(url).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: 'Product link has been copied to clipboard.',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'bottom-start'
                });
            }).catch(err => {
                console.error('Failed to copy: ', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Failed to copy link.',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });
        }
    </script>

<script>
function initDarazZoom() {
    const img = document.getElementById("productImage");
    const lens = document.getElementById("lens");
    const result = document.getElementById("zoomResult");

    if (!img || !lens || !result) return;

    const zoom = 4;

    function setupZoom() {
        result.style.backgroundImage = `url('${img.src}')`;
        result.style.backgroundSize =
            (img.offsetWidth * zoom) + "px " +
            (img.offsetHeight * zoom) + "px";
    }

    setupZoom();

    /* SHOW preview when cursor ENTERS image */
    img.addEventListener("mouseenter", () => {
        lens.style.display = "block";
        result.style.display = "block";
    });

    /* HIDE preview when cursor LEAVES image */
    img.addEventListener("mouseleave", () => {
        lens.style.display = "none";
        result.style.display = "none";
    });

    /* MOVE lens while cursor MOVES */
    img.addEventListener("mousemove", moveLens);

    function moveLens(e) {
        const rect = img.getBoundingClientRect();

        let x = e.clientX - rect.left - lens.offsetWidth / 2;
        let y = e.clientY - rect.top - lens.offsetHeight / 2;

        x = Math.max(0, Math.min(x, img.offsetWidth - lens.offsetWidth));
        y = Math.max(0, Math.min(y, img.offsetHeight - lens.offsetHeight));

        lens.style.left = x + "px";
        lens.style.top = y + "px";

        result.style.backgroundPosition =
            `-${x * zoom}px -${y * zoom}px`;
    }
}

/* Thumbnail switch */
function previewImage(element) {
    const src = element.src;
    const img = document.getElementById("productImage");
    const result = document.getElementById("zoomResult");

    if (!img || !result) return;

    img.src = src;

    img.onload = () => {
        result.style.backgroundImage = `url('${src}')`;
        initDarazZoom();
    };
}

/* Init */
document.addEventListener("DOMContentLoaded", initDarazZoom);
document.addEventListener("livewire:load", initDarazZoom);
document.addEventListener("livewire:navigated", initDarazZoom);
</script>





</div>