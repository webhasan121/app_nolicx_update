@extends('layouts.user.dash.userDash')

@section('content')

@php
// $items = array (
// [
// "id" => 11,
// "label" => "Basic",
// "Price" => 2000
// ],
// [
// 'id' => 10,
// 'label' => 'Premium',
// 'Price' => '2000',
// ]
// );
// $active = request->query('id', '0')
@endphp
<style>
    .vip_item_info_box {
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

    .vip_item_info_box .label {}
</style>

<div class="" style="">
    {{-- <div
        style="display: grid; grid-template-columns:repeat(auto-fit, 225px); grid-gap:20px; justify-content:center"
        class="border p-2">
        @foreach ($items as $item)
        <x-vip-cart :$item :active="$id" />
        @endforeach
    </div> --}}


    <div>
        {{-- this package - {{$id}}
        owner - {{$package->user()->count()}} --}}
        {{--
        <pre>
            @php
                print_r($package)
            @endphp
       </pre> --}}
    </div>

    {{-- if the user owner --}}
    <div @class(["d-none d-flex border-bottom py-2 justify-content-between align-items-center input-group", 'd-block'=>
        $ownerPackage??""])>
        <div>
            Package
        </div>
    </div>


    <div @class(["row m-0 p-2 rounded d-none", 'd-block'=> $ownerPackage??""])>
        <div class="alert alert-success"> You Purchased This Package !</div>
    </div>

    {{-- <div @class(["row m-0 p-2 rounded", 'd-none'=> $ownerPackage??""])> --}}
        <div @class(["row m-0 p-2 rounded"])>

            <div class="col-md-5 mb-3" style="min-width:250px; max-width: 350px">
                <x-vip-cart :item="$package" :active="$id" />
            </div>

            <div class="col-lg-7 w-100 px-3">
                <div class="bg-white">
                    <div class="text-left">
                        <h4>Confirm Payment First</h4>
                        <p>Please send TK {{$package->price ?? "0"}} to bellow number. And collect your Tansactions ID
                            for further proccess. We need your Transactions ID to identify it's you.</p>

                        <div class="mt-2">

                            @if ($package->payOption)

                            @foreach ($package->payOption as $item)

                            <div class="p-2 rounded border mb-1">
                                <div class="" for="">{{ $item->pay_type }} </div>
                                <div class="input-group w-100">
                                    <div type="text" id="paymentTo_{{$item->id}}"
                                        class="border-0 form-control outline-0 shadow-0 bg-white py-2 text-dark bold">
                                        {{ $item->pay_to }} </div>
                                    <button class="btn btn-sm ml-5 py-0" id=""
                                        onclick="copyPaymentNumber(this, 'paymentTo_{{$item->id}}')">Copy</button>
                                </div>
                            </div>
                            @endforeach
                            @endif



                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{--
        <hr> --}}
        <div class="text-center">
            {{-- <a class="btn text-center btn_secondary d-inline-block" href="">View Details</a> --}}
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#packageDetailsModal">
                View Details
            </button>
        </div>

        <!-- Button trigger modal -->

        <!-- Modal -->
        <div class="modal fade" id="packageDetailsModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">{{ $package->name }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {!!

                        $package->description ?? "No Description Found !"

                        !!}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                    </div>
                </div>
            </div>
        </div>

        <div @class(['row m-0 d-none'=> $ownerPackage, 'block'])>
            <hr>

            <div @class(['col-lg-4 d-block'=> $ownerPackage, 'col-12 d-none mt-4'])>
                <x-vip-cart :item="$package" type='owner' :active="$package->id??''" />
            </div>

            <div @class(["py-4 px-2 col-12", 'col-lg-8'=> $ownerPackage]) style="display: grid;
                grid-template-columns:repeat(auto-fit, 155px); grid-gap:20px; justify-content:center" >

                <div class="vip_item_info_box shadow">
                    <div>
                        Package <i class="fas fa-check-circle mx-2"></i>
                    </div>
                    <hr class="w-100">
                    <div style="font-weight: 900; font-size:24px" class="">
                        {{$package->name}}
                    </div>
                </div>


                <div class="vip_item_info_box shadow">
                    <div>
                        Price <i class="fas fa-check-circle mx-2"></i>
                    </div>
                    <hr class="w-100">
                    <div style="font-weight: 900; font-size:24px" class="">
                        {{$package->price}} TK
                    </div>
                </div>


                <div class="vip_item_info_box shadow">
                    <div>
                        Daily TK
                    </div>
                    <hr class="w-100">
                    <div style="font-weight: 900; font-size:24px" class="">
                        {{$package->coin}}
                    </div>
                </div>

                <div class="vip_item_info_box shadow">
                    <div>
                        Daily Time <i class="fas fa-clock mx-2"></i>
                    </div>
                    <hr class="w-100">
                    <div style="font-weight: 900; font-size:24px" class="">
                        {{$package->countdown}} Min
                    </div>
                </div>

                <div @class(["vip_item_info_box shadow d-none", 'd-block'=> $package->refer_bonus_owner ])>
                    <div>
                        Refer Bonux <i class="fas fa-link mx-2"></i>
                    </div>
                    <hr class="w-100">
                    <div style="font-weight: 900; font-size:24px" class="">
                        {{$package->refer_bonus_owner}} Min
                    </div>
                </div>

                <div @class(["vip_item_info_box shadow d-none", 'd-block'=> $package->refer_bonus_via_link ])>
                    <div>
                        Give Refer Bonux <i class="fas fa-link mx-2"></i>
                    </div>
                    <hr class="w-100">
                    <div style="font-weight: 900; font-size:24px" class="">
                        {{$package->refer_bonus_via_link}} Min
                    </div>
                </div>

            </div>
        </div>

        <div class="text-center my-3">
            <a href="{{route(" user.package.confirm", ['id'=> $id])}}" @class(["btn btn-lg bg_primary shadow
                text-white", 'd-none' => $ownerPackage??""])>Procces to Purchase <i class="fas fa-arrow-right mx-2"></i>
            </a>

        </div>


        <!-- Button trigger modal -->
        {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
            Launch static backdrop modal
        </button> --}}

        <!-- Modal -->

    </div>
    @push('script')
    <script>
        // copyPaymentNumber('paymentTo')
    function copyPaymentNumber(e, elementId) 
    {
        const paymentNumberInput = document.getElementById(elementId);
        const tempTextarea = document.createElement("textarea");
        tempTextarea.value = paymentNumberInput.value || paymentNumberInput.textContent || paymentNumberInput.innerText;


          // Append the textarea to the DOM (off-screen)
        tempTextarea.style.position = "fixed";
        tempTextarea.style.opacity = "0";
        document.body.appendChild(tempTextarea);


          // Select the content of the textarea
        tempTextarea.select();
        tempTextarea.setSelectionRange(0, 99999); // For mobile devices

        // Copy the selected content to the clipboard
        try {
            document.execCommand("copy");
            // console.log("Content copied to clipboard!");
            // alert('copied !')
            e.innerText = 'copied';
            setTimeout(() => {
                e.innerText = 'copy';
            }, 2000);
        } catch (err) {
            console.error("Failed to copy content: ", err);
        }

        // Remove the temporary textarea
        document.body.removeChild(tempTextarea);

        // var refer = document.getElementById('refer_link_display');
        // paymentNumberInput.select();
        // refer.setSelectionRange(0,9999);
        // document.exceCommand('copy');
        // let ke = new keyboardEvent();
        // navigator.clipboard.writeText(refer.value);
        
    }
    </script>
    @endpush
    @endsection