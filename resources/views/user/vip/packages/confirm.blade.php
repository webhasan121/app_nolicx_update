@extends('layouts.user.dash.userDash')

@section('content')

<div class="">

    <h3 >
        Confirm Purchase
    </h3>
    <hr>
    <div style="max-width: 300px">
        <x-vip-cart :item="$package" :active="$id" />
    </div>
    <hr>
    <div class="">
        <form action="{{route('user.package.request')}}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" value="{{$id}}" name="pid">
            <div class="p-3 shadow border rounded">
                <div class="row">

                    <div class="col-lg-6">
                        <div class="form-floating mb-3 ">
                            <label for="method">Payment Method </label>
                            <select name="payment_by" id="method" class="form-control @error('payment_by')is-invalid @enderror">
                                <option value="">Select an payment Method</option>
                                @foreach ($package->payOption as $item)
                                    <option value="{{$item->pay_type}}"> {{$item->pay_type}} - {{$item->pay_to}} </option>
                                @endforeach
                                {{-- <option value="Nogod">Nogod</option> --}}
                            </select>
                            @error('payment_by')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                          </div>
                    </div>
                    <div class="col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <label for="floatingInput">Transaction ID</label>
                            <input type="text" value="{{old('trx')}}" class="form-control @error('trx')is-invalid @enderror" id="floatingInput" name="trx" placeholder="AFASDF4574SD4S">
                            @error('trx')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                </div>

            </div>            
           
            <div class=" my-3 p-3 shadow border rounded">
                <div class="row">

                    <div class="col-lg-6">
                        <div class="form-floating mb-3">
                            <label for="name">Your Name </label>
                            <input type="text" value="{{old('name')}}" class="form-control @error('name')is-invalid @enderror" placeholder="John Doe" name="name" id="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                          </div>
                    </div>
                    <div class="col-sm-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <label for="phone">Phone Number</label>
                            <input type="text" value="{{old('phone')}}" class="form-control @error('phone')is-invalid @enderror" name="phone" placeholder="+880123456789">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

            <div class="mb-3 p-3 rounded shadow @error('task_type') border border-danger @enderror">
                <label for="package_type" class="my-2 fs-2">Task Type</label>
                
                <div class=" m-0 p-3 border  rounded">

                    <div class="col-6 p-2 flex d-flex m-0 align-items-center">
                        <input type="radio" name="task_type" value="daily" id="daily_task" class="m-0 border @error('task_type') is-invalid @enderror" style="width:20px; height:20px" id="">
                        <label for="daily_task" class="m-0 p-0 pl-5" > Daily Task </label>
                    </div>
                    <div class="form-text fs-6">
                        Daily task may be completed in 24 hours. Time has been fixed with package.
                    </div>
                </div>
                <hr>
                 
                <div class="row m-0 p-3 border   rounded">

                    <div class="col-6 p-2 flex d-flex m-0 align-items-center">
                        <input type="radio" name="task_type" value="monthly" id="monthly_task  " class="m-0 border @error('task_type') is-invalid @enderror" style="width:20px; height:20px" id="">
                        
                        <div>
                            <label for="monthly_task" class="m-0 p-0 pl-5" > Monthly Task </label>
                        </div>
                    </div>
                    <div class="form-text">
                        Monthly task may be completed to a day in a month.
                    </div>
                </div>

            </div>

            <div class="border p-3">
                
                <div class="row justify-content-center">
                    <div class="col-12">
                        <label for="nid">Your NID Number</label>
                        <input type="number" value="{{old('nid')}}" name="nid" id="nid" class="form-control @error('nid')is-invalid @enderror">
                        @error('nid')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="nid_img_wrapper row justify-content-center">
                    <div class="col-lg-6">
                        <div>
                            Front Side Of NID
                        </div>
                        <div class="w-100 nid_img_div">
                            <img id="front_image_prev" src="" height="200" style="width: 100%; object-fit:contain" alt="">
                        </div>

                        <div class="nid_img_file input-group mt-2">
                            <input onchange="previewImage(this, '#front_image_prev')" class="form-control border-0 image_file @error('nid_front')is-invalid @enderror" type="file" name="nid_front" id="front_image_file">
                            @error('nid_front')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <label class="input-group-text rounded bg-danger text-light cursor-pointer" for="" onclick="removeImage(this, '#front_image_prev')"> 
                                <i class="fas fa-minus"></i>
                            </label>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div>
                            Back Side of NID
                        </div>
                        <div class="w-100 nid_img_div">
                            <img id="back_image_prev" src="" height="200" style="width: 100%; object-fit:contain" alt="">
                        </div>

                        <div class="nid_img_file input-group mt-2">
                            <input onchange="previewImage(this, '#back_image_prev')" class="form-control border-0 image_file @error('nid_back')is-invalid @enderror" type="file" name="nid_back" id="back_image_file">
                            @error('nid_back')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <label class="input-group-text rounded bg-danger text-light cursor-pointer" for="" onclick="removeImage(this, '#back_image_prev')">
                                <i class="fas fa-minus"></i>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right mt-4">
                <button class="btn btn-lg bg_primary text-light">  Confirm <i class="fas fa-arrow-right mx-2"></i></button>
            </div>
        </form>
    </div>

</div>
<script>
    function previewImage(e, target)
    {
        if (e.files && e.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) { 
            document.querySelector(target).setAttribute("src",e.target.result);
            };
            reader.readAsDataURL(e.files[0]); 
        }
    }

    function removeImage(e, target_image)
    {
        e.previousElementSibling.value = "";
        document.querySelector(target_image).removeAttribute('src');
    }
</script>
@endsection
