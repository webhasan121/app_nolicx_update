@props(['item', 'style','active','type'])
@php
if (!isset($active)) {
# code...
$active = '';
}
$isActive = $active == $item['id'] ? true : false;
$active = '';

// $href = $isActive ? route('user.vip.index', ['id' => $item['id']]) : route('user.package.checkout', ['id' =>
$item['id'];

$href = route('user.package.checkout', ['id' => $item['id']]);
@endphp
<div>

    <style>
        .vip_cart {
            color: #000;
            overflow: hidden;
            transition: all linear .3s
        }

        .vip_cart:hover {
            box-shadow: 0px 5px 5px #d9d9d9;
            transition: all linear .3s
        }

        .vip_cart:hover .vip_button {
            background-color: var(--brand-primary);
            transition: all linear .3s;
            color: var(--brand-white)
        }

        .vip_cart .head {
            /* box-shadow: 0px 0px 8px #d9d9d9;     */
            padding: 10px 8px 0px 8px;
            color: hsl(23, 100%, 65%);
            ;
        }

        .vip_cart a {
            color: #000;
        }

        .selected {
            border: 3px solid rgb(31, 118, 80) !important;
        }

        .unSelected {
            opacity: 4;
        }

        .selected_btn {
            background-color: rgb(31, 118, 80);
            color: white !important;
        }
    </style>


    @isset($item)
    <div style="{{$style??"" }}" {{$attributes->class(['selected' => $isActive, 'unSelected' =>
        !$isActive])->merge(['class' => 'rounded-md shadow-lg vip_cart border br_primary text-center']) }}>
        <div class="head text-center bolder">
            {{
            Str::upper($item->name)
            }}
            {{-- <div class="vip_price_currency text_secondary rounded-circle d-inline-block"
                style="font-size: 15px; text-align:left; line-height:5px; font-weight:300;">
                TK
            </div> --}}
        </div>

        <div class="px-3 pb-3">
            <x-hr />
            <div class="vip_price text_secondary" style="font-size: 35px; font-weight:bolder; line-height:40px">
                {{
                $item->price
                }}
                <div class="inline-block" style="font-size: 15px; text-align:left; line-height:5px; font-weight:300;">
                    TK
                </div>
            </div>

            <div class="vip_info py-4 text_sm" style="font-weight: 300">
                <div>
                    {{$item->countdown}} Minute daily time
                </div>
                <div>
                    {{$item->coin}} TK per day
                </div>
                {{-- <div>
                    {{$item->m_coin}} coin per month.
                </div> --}}
            </div>

            @isset(request()->id)
            <div class="p-2 rounded rounded-md w-full text-md uppercase font-bold bg-green-900 text-white" type="buton"
                disabled>
                selected
                <i class="fas fa-check-circle mx-2"></i>
            </div>
            @else
            {{-- <a {{$attributes->class(['selected_btn disable' => $isActive])->merge(["class" => 'vip_button d-block
                text-center py-2 border'])}} href="{{$href}}"> {{$isActive ? 'Purchase' : 'View Details'}}
                <i {{$attributes->class(['fa-check-circle'=> $isActive, 'fa-arrow-right' => !$isActive])->merge(['class'
                    => 'fas mx-2'])}}></i>
            </a> --}}

            {{-- <x-nav-link {{$attributes->class(['selected_btn disable' => $isActive])->merge(["class" => 'vip_button
                d-block text-center py-2 border'])}} href="{{$href}}"> View Details
                <i {{$attributes->class(['fa-check-circle'=> $isActive, 'fa-arrow-right' => !$isActive])->merge(['class'
                    => 'fas mx-2'])}}></i>
            </x-nav-link> --}}
            <x-nav-link-btn href="{{route('user.package.checkout', ['id' => $item->id])}}">View Details</x-nav-link-btn>
            @endisset
        </div>

    </div>
    @else
    <div class="alert alert-info text-center">
        No Data Found !
    </div>
    @endisset


    {{-- modal for refer info --}}
    <style>
        .position-fixed {
            position: fixed !important;
            top: 0;
            left: 0;
            z-index: 99;
            opacity: 1;
            transition: all linear .3s;
        }

        .position-hidden {
            position: fixed;
            top: -500%;
            left: 0;
            z-index: -99;
            opacity: 0;
            transition: all linear .3s;
        }
    </style>

    <script>
        function showModal() 
        {
            var cm = document.getElementById('customModal');
            cm.classList.remove('position-hidden')
            cm.classList.add('position-fixed')

        }

        function hideModal() 
        {
            var cm = document.getElementById('customModal');
            cm.classList.add('position-hidden')
            cm.classList.remove('position-fixed')

        }

        function copy()
        {
            try {

                // navigator.permissions.query({ name: "write-on-clipboard" }).then((result) => {
                //     if (result.state == "granted" || result.state == "prompt") {
                //         alert("Write access granted!");
                //     }
                // });
                
                var refer = document.getElementById('refer_link_display');
                refer.select();
                // refer.setSelectionRange(0,9999);
                document.exceCommand('copy');
                navigator.clipboard.writeText(refer.value);
                
                alert('copied !')
            } catch (error) {
                alert("faild to copy !")       
            }
        }
    </script>


</div>