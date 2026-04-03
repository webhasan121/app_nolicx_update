<div>


    <x-slot name="title">
        {{$product->meta_title ?? $product->title}}
    </x-slot>

    @push('seo')
    <meta name="title" content="{{$product->seo_title ?? $product->name}}" />
    <meta name="description" content="{{$product->meta_description ?? $product->title}}" />
    <meta name="keyword" content="{{$product->keyword ?? ''}}" />
    <meta name="twitter:card" content="summary_large_image" />

    <meta name="twitter:title" content="{{ $product->meta_title ?? $product->title }}" />
    <meta name="twitter:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />

    <meta name="twitter:image:alt" content="" />

    <meta name="twitter:image" content="{{ asset('storage/'. $product->meta_thumbnail ?? $product->thumbnail) }}" />

    <meta property="og:type" content="og:product" />

    <meta property="og:title" content="{{ $product->meta_title ?? $product->title }}" />

    <meta property="og:image" content="{{ asset('storage/'. $product->meta_thumbnail ?? $product->thumbnail) }}" />

    <meta property="og:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />

    @endpush

    {{-- The Master doesn't talk, he acts. --}}
    <style>
        #taskPrev {
            position: fixed;
            bottom: 10px;
            right: 20px;
            width: 70px;
            height: 30px;
            border: 1px solid rgb(25, 78, 46);
            border-radius: 25px;
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #ffffff;
            /* box-shadow: 0px 0px 5px gray; */
            font-size: 18px;
            /* display: none; */

        }

        #taskPrev .badges {
            position: absolute;
            top: -12px;
            left: 0px;
            padding: 1px 5px;
            background-color: green;
            color: white;
            font-size: 10px;
            border-radius: 25px;
        }

        .price-alert {
            position: fixed;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            z-index: 9999;

        }
    </style>

    <x-dashboard.container>
        <div class=" ">
            <div class="">
                @includeIf('components.client.product-single')

            </div>
        </div>


        {{-- summery and specifications --}}
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Shop Details
                </x-slot>
                <x-slot name="content">
                    this product belongs to bellow shop. see about the shop.
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                @if (auth()?->user()?->id == $product->user_id)
                <strong class="p-2 rounded border bg-sky-900 text-white">It's your product </strong>
                @else
                <hr class="my-2" />
                <div class="flex flex-wrap">
                    <div class=" border-b w-48 m-2 p-2">
                        <div class="text-sm font-normal">
                            Shop Name
                        </div>
                        <div class="text-md font-bold">
                            {{$product?->owner?->resellerShop()->shop_name_en ?? "N/A"}}
                        </div>
                    </div>
                    <div class=" border-b w-48 m-2 p-2">
                        <div class="text-sm font-normal">
                            Shop Owner
                        </div>
                        <div class="text-md font-bold">
                            {{$product?->owner?->name ?? "N/A"}}
                        </div>
                    </div>
                    <div class=" border-b w-48 m-2 p-2">
                        <div class="text-sm font-normal">
                            Shop Location
                        </div>
                        <div class="text-md font-bold">
                            {{$product?->owner?->resellerShop()->address ?? "N/A"}}
                        </div>
                    </div>
                    <div class=" border-b w-48 m-2 p-2">
                        <div class="text-sm font-normal">
                            Shop Address
                        </div>
                        <div class="text-md font-bold">
                            {{$product?->owner?->resellerShop()->address ?? "N/A"}}
                        </div>
                    </div>
                </div>
                <br>
                <div class="flex flex-wrap space-x-2 ">
                    <x-nav-link-btn
                        href="{{route('shops.visit', ['id' => $product?->owner?->resellerShop()->id, 'name' => $product?->owner?->resellerShop()->shop_name_en])}}">
                        Visit Shop</x-nav-link-btn>
                    {{-- <x-nav-link-btn href="" class="space-x-2 space-y-2">Other Products
                    </x-nav-link-btn>
                    <x-nav-link-btn href="">Report Incorrect Information</x-nav-link-btn> --}}
                </div>
                @endif
            </x-dashboard.section.inner>
        </x-dashboard.section>


        <x-dashboard.section>

            <x-dashboard.section.inner>
                <div>
                    <div class=" p-2 w-full">
                        {!! $product->description ?? "No Description Found !" !!}
                    </div>
                </div>
            </x-dashboard.section.inner>

        </x-dashboard.section>
        <x-dashboard.section>

            <x-dashboard.section.header>
                <x-slot name="title">
                    Comments
                </x-slot>
                <x-slot name="content">

                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>
                @foreach ($product->comments as $item)

                <div class="px-2 py-3 bg-gray-100 mb-1">
                    <div class="flex justify-between">
                        <div class="text-xs"> <span class="text-indigo-900">{{$item->user?->name}} </span> at
                            {{$item->created_at?->diffForHumans()}} </div>
                        @if (Auth::user()?->can('users_manage') || Auth::id() == $item->user_id)
                        <form action="{{route('user.comment.destroy', ['id' => $item->id])}}" method="post">
                            @csrf
                            <button class="mt-1 rounded bg-white border text-xs"> <i class="fas fa-trash"></i> </button>
                        </form>
                        @endif

                    </div>
                    <div class="ps-2 text-md">
                        {{$item?->comments}}
                    </div>
                </div>
                @endforeach
            </x-dashboard.section.inner>

            <x-hr />
            @if (Auth::check())

            <x-dashboard.section.inner>
                <form action="{{route('user.comment.store')}}" method="post">
                    @csrf
                    <div class="">
                        <div>
                            <div class="text-xs">
                                @error('comments')
                                {{$message}}
                                @enderror
                            </div>
                            <x-text-input class="" name="comments" placeholder="write your comments" />
                        </div>
                        <input type="hidden" name="product_id" value="{{$product->id}}">
                        <x-primary-button>submit</x-primary-button>
                    </div>
                </form>
            </x-dashboard.section.inner>
            @else
            <div>
                Log In to add comment
            </div>
            @endif

        </x-dashboard.section>



        {{-- Product Q/A --}}
        {{-- <x-dashboard.section x-data={show:false}>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex justify-between items-center">
                        Product Q/A
                        <button x-html="show ? 'collapse' : 'expand'" class="border text-xs p-2 rounded-lg"
                            x-on:click="show = !show">

                        </button>
                    </div>
                </x-slot>
                <x-slot name="content">
                    Have any question regarding this products?
                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>
                <div class="mb-2">
                    <div class="py-2 ">
                        <div class="flex items-center text-xs mb-2">
                            <div class="">Your Qestions</div>
                        </div>

                        <div class="p-2 border-l mb-3">
                            <div class="text-sm text-bold text-gray-600">July 16, 2025</div>
                            <div class="p-2">
                                Lorem, ipsum dolor sit amet consectetur adipisicing elit. A quaerat labore voluptatem
                                rerum delectus vero iusto quia culpa voluptatum quae.
                                <div class="text-gray-600 font-normal">
                                    <i class="fa-solid fa-sync"></i>
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Facilis, similique.
                                    <i>seller</i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div x-show="show" x-transition>
                    <x-hr />
                    No More Question Found !
                    <x-hr />
                </div>
                @auth
                <div class="border rounded p-2 text-end">
                    <textarea name="" id="" cols="3" class="w-full rounded border-0 mb-2"
                        placeholder="ask a question "></textarea>
                    <x-primary-button>ask</x-primary-button>
                </div>
                @else
                <div class="border rounded p-2 text-center">
                    login to ask question
                </div>
                @endauth
            </x-dashboard.section.inner>
        </x-dashboard.section> --}}

        @livewire('pages.RecomendedProducts')
    </x-dashboard.container>


    {{-- daily task counter --}}
    @if (!$taskNotCompletYet)
    <div id="taskPrev">
        <div class="badges "> Task</div>
        Done
    </div>
    @else
    <div id="taskPrev">
        <div class="badges"> {{$package->countdown ?? 0}} MIN</div>
        <div id="min">
            {{$min}}
        </div>
        :
        <div id="sec">
            {{$sec}}
        </div>
    </div>
    @endif



    @auth

    @script
    <script>
        let task = {{$taskNotCompletYet ?? ''}};
                let duration = {{$countdown}} * 60;            
                
                if (task && duration > 0) {
                    let min = 0, sec = 0;
                    let ct = {{$currentTaskTime}} ?? 0;
                    let counterLoop = setInterval(() => {

                        if (ct > duration) {
                            clearInterval(counterLoop);
                            console.log('finished  : ' + ct, duration);
                            window.location.reload();
                        }else{

                            $wire.dispatch("count-task");
                            console.log('continue : ' +ct, duration);
                        }
                        
                    }, 1000);
                };

    </script>
    @endscript

    {{-- <script>
        let token = "{{csrf_token()}}";
            let req = new XMLHttpRequest();

            req.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(JSON.parse(this.response));
                    
                }
            };
            req.open("get", "http://eruhi.local/http/test", true);
            req.send();

    </script> --}}
    @endauth
</div>