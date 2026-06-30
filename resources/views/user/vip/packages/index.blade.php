@extends('layouts.user.dash.userDash')
@section('content')

        {{-- @php
            $items = array (
                [
                    "id" => 11,
                    "label" => "Basic",
                    "Price" => 2000
                ],
                [
                    'id' => "10",
                    'label' => 'Premium',
                    'Price' => '2000',
                ]
            );
        @endphp --}}

<div class=" w-100">
    <style>
        .vip_cart{
            color: #000;
            overflow: hidden;
            transition: all linear .3s
        }
        .vip_cart:hover{
            box-shadow: 0px 5px 5px #d9d9d9;
            transition: all linear .3s
        }
        .vip_cart .head{
            /* box-shadow: 0px 0px 8px #d9d9d9;     */
            padding: 10px 8px 0px 8px;
            color: hsl(23, 100%, 65%);;
        }
        .vip_cart a {
            color: #000;
        }   
        .vip_item_info_box{
            height: 155px;
            /* border: 1px solid #c7c7c7; */
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            /* background-color:white ; */
            border-radius: 8px
        }
    </style>

    {{-- <div style="display: grid; grid-template-columns:repeat(auto-fit, minmax(220px, 250px)); grid-gap:20px; justify-content:start">
        <x-vip-cart :item="$package[0]" :active="11" />
    </div> --}}

    
    <div class="border-bottom my-3 py-2">  
        Buy Package 
    </div>
  
    <x-package-request />
  
  
    <div style="display: grid; grid-template-columns:repeat(auto-fit, 220px); grid-gap:20px; justify-content:center">

        {{-- <div class="vip_cart border text-center">
            <div class="head text-center">
                BASIC
            </div>
    
            <div class="px-3 pb-3">
                <hr>
                <div class="vip_price" style="font-size: 35px; font-weight:bolder; line-height:40px">
                    2000
                    <div class="vip_price_currency text_secondary rounded-circle d-inline-block" style="font-size: 15px; text-align:left; line-height:5px; font-weight:300;">
                        TK
                    </div>
                </div>
        
                <div class="vip_info py-4" style="font-weight: 300">
                    Per Month
                </div>
        
                <a class="vip_button btn_hover d-block text-center py-2 border" href="">Purchas Now <i class="fas fa-arrow-right ml-3"></i> </a>
            </div>
            
        </div>

        <div class="vip_cart border text-center">
            <div class="head text-center">
                BASIC
            </div>
    
            <div class="px-3 pb-3">
                <hr>
                <div class="vip_price" style="font-size: 35px; font-weight:bolder; line-height:40px">
                    2000
                    <div class="vip_price_currency text_secondary rounded-circle d-inline-block" style="font-size: 15px; text-align:left; line-height:5px; font-weight:300;">
                        TK
                    </div>
                </div>
        
                <div class="vip_info py-4" style="font-weight: 300">
                    Per Month
                </div>
        
                <a class="vip_button btn_hover d-block text-center py-2 border" href="{{route("user.vip.checkout", ['id'=> 11])}}">Purchas Now <i class="fas fa-arrow-right ml-3"></i> </a>
            </div>
            
        </div>

        <div class="vip_cart border text-center">
            <div class="head text-center">
                BASIC
            </div>
    
            <div class="px-3 pb-3">
                <hr>
                <div class="vip_price" style="font-size: 35px; font-weight:bolder; line-height:40px">
                    2000
                    <div class="vip_price_currency text_secondary rounded-circle d-inline-block" style="font-size: 15px; text-align:left; line-height:5px; font-weight:300;">
                        TK
                    </div>
                </div>
        
                <div class="vip_info py-4" style="font-weight: 300">
                    Per Month
                </div>
        
                <a class="vip_button btn_hover d-block text-center py-2 border" href="">Purchas Now <i class="fas fa-arrow-right ml-3"></i> </a>
            </div>
            
        </div> --}}
        @if(count($package))
            @foreach ($package as $item)
                <x-vip-cart :$item />
            @endforeach
        @else 
            <div class="alert alert-info w-100 bold">
                No Packge Found !
            </div>
        @endif
    </div>
</div>
@endsection