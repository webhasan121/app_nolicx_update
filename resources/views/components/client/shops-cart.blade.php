@props(['shop'])
<div>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="relative">
            <img class="w-full bg-indigo-900" style="height:100px" src="{{asset('storage/'. $shop->banner)}}" alt="">
            <img class="rounded-full  absolute left-0 top-0 bg-white m-2" style="height: 50px; width:50px"
                src="{{asset('storage/'.$shop->logo)}}" alt="">
        </div>
        <div class="p-3">
            <div class="">
                {{$shop->shop_name_en}}
                {{-- Lorem ipsum dolor sit amet. --}}
            </div>
            <p class="text-xs">
                {{$shop->village}}, {{$shop->upozila}}, {{$shop->district}}
            </p>
            {{-- <div class="py-3">
                <div class="flex items-center">
                    <i class="fas fa-star text-indigo-900"></i>
                    <i class="fas fa-star text-indigo-900"></i>
                    <i class="fas fa-star text-indigo-900"></i>
                    <i class="fas fa-star text-indigo-900"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div> --}}
            <div class="mt-2 flex justify-between items-center space-x-2 space-y-2">

                {{-- <div class="px-2 bg-gray-900 text-white rounded-lg inline-block text-xs">
                    Eruhi Choise
                </div> --}}
            </div>
            <x-hr />
            <div class="flex justify-between">
                <div>
                    {{-- <i class="fas fa-heart"></i> --}}
                </div>
                <x-nav-link
                    href="{{route('shops.visit', ['id' => $shop->id, 'name'=>$shop->shop_name_en ?? 'not_found'])}}">
                    Visit Shops <i class="fas fa-caret-right px-2"></i>
                </x-nav-link>
            </div>
        </div>
    </div>
</div>