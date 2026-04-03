@extends("layouts.user.app")
@section('title')
    Product Review 
@endsection
@section('content')

@push('meta')
    <meta name='og:title' content="{{$product->meta_title ?? ""}}">
    <meta name='og:type' content="{{$product->category?->name ?? ""}}">
    <meta name='og:url' content={{url()->current()}}>
    <meta name='og:image' content="{{$product->meta_thumbnail ?? ""}}">
    <meta name='og:site_name' content="{{config('app.name')}}">
    <meta name='og:description' content="{{$product->meta_description}}">
    <meta name="keyword" content="{{$product->keyword}}"
    
    {{-- <meta name='fb:page_id' content='43929265776'>
    <meta name='application-name' content='foursquare'>
    <meta name='og:email' content='me@example.com'>
    <meta name='og:phone_number' content='650-123-4567'>
    <meta name='og:fax_number' content='+1-415-123-4567'> --}}
@endpush

<style>
    /* @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap'); */

    #taskPrev{
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

    #taskPrev .badges{
        position: absolute;
        top: -12px;
        left: 0px;
        padding: 1px 5px;
        background-color: green;
        color: white;
        font-size: 10px;
        border-radius: 25px;
    }

    .price-alert{
        position: fixed;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        z-index: 9999;

    }
</style>


<div class="container">
    <x-product-single :$product :key="$product->id" />
</div>
<hr>
<div class="container">
    <div class="row m-0">
        <div class="col-12">
            adsfasdf
        </div>
    </div>
</div>
<hr>
<div class="container">
    <div class="row m-0">
        <div class="col-lg-8 py-3">
            <div>
                <h4>Description</h4>
                {!! $product->description ?? "No Description Found !" !!}
            </div>
            <hr>
            <div>
                <h4>Reviews</h4>
                <div class="alert alert-info">
                    Review Not Available
                </div>
            </div>

        </div>

        <div class="py-3 col-lg-4 product_section">
            <h4>You May Also Like</h4>
            <div class="row p-0 m-0 mb-2">
                @foreach($relatedProduct as $product)
                <div class="col-6 p-1 mb-3">
                    <x-product-card :$product :key="$product->id" />
                </div>
                @endforeach
            </div>
        
            {{-- <x-product-card :$product :key="$product->id" /> --}}

        </div>
    </div>
{{-- 
    <div class="price-alert shadow  rounded-pill p-1 bg-white">
        <div class="border border-info rounded-pill w-100 p-2 px-5 d-flex justify-content-between align-items-center">
            <div>
                <div>
                    @if($product->offer_type == 'yes')
                        
                        <div class=" @if($product->offer_type == 'yes') pr-2 @else align-self:center @endif" style="font-size:17px; font-weight: bold; text-align:right">
                            {{$product->discount}} TK
                        </div>
        
                    @else
                        <div class=" @if($product->offer_type == 'yes') pr-2 @else align-self:center @endif" style="font-size:17px; font-weight: bold; text-align:right">
                            @if($product->price_in_usd != null) ${{$product->price_in_usd}} @else {{$product->price_in_bdt}} TK @endif 
                        </div>
                    @endif 
                </div>
            </div>
    
            <div>
                <a class="px-4 py-2 btn_secondary rounded-pill" href=""> <i class="fas fa-shopping-card me-2"></i> Order Now</a>
            </div>
        </div>
    </div> --}}
</div>

@auth
    
    @php    
        $task = session('data')['task'] ?? "";
        $vip_package = auth()->user()->vipPurchase;
    @endphp
@endauth

<x-vipCounter :$m_tasks />


@if(auth()->check() && $vip_package?->status && $task && $task != null)
    <div id="taskPrev" >
        <div class="badges "> Task</div>
        Done
    </div>
@else 
    <div id="taskPrev">
        {{-- @if(auth()->check() && auth()->user()->vipPurchase && auth()->user()->vipPurchase->task_type == 'monthly' && session('data')['task'] == null)
            <div class="badges"> Monthly </div>
        @else
        @endif --}}
        <div class="badges"> 0 MIN</div>
        <div id="min">
            00
        </div>
        :
        <div id="sec">
            00
        </div>
    </div>
@endif

{{-- @if(auth()->check() && auth()->user()->vipPurchase && auth()->user()->vipPurchase->task_type == 'monthly' && session('data')['task'] == null)
    
    <div id="taskPrev" >
        <div class="badges"> Monthly </div>
        <div id="min">
            00
        </div>
        :
        <div id="sec">
            00
        </div>
    </div>
@else
    <div id="taskPrev">
        <div class="badges"> 0 MIN</div>
        <div id="min">
            00
        </div>
        :
        <div id="sec">
            00
        </div>
    </div>
    
@endif --}}

{{-- @php
    
    print_r($m_tasks);
@endphp
     --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@if(auth()->check() )
    <script>
        // console.log({{auth()->user()->vipPurchase}});
        // console.log(count);
        
        function showTimer() {
            let sec = document.getElementById('sec');
            let min = document.getElementById('min');
            let prev = document.getElementById('taskPrev');
            let badge = document.getElementsByClassName('badges')[0];
            const timer = {{session('data')['countDown']?? "0"}}; // second
            badge.textContent = timer/60 + ' MIN';
            // show timer end up to 'hour' & 'min' based on timer
            let time = timer;
            // let h = Math.floor(time/3600);
            let m = Math.floor(time/60);
            let s = 60;
            // console.log(m, s);
            
            let counter = setInterval(() => {
                let ctime = localStorage.vip_{{auth()->user()->id}};
                let tm = Math.floor(ctime/60);
                let ts = ctime - (tm*60);
                // console.log(time, ctime);
                
                if(time == ctime) {
                    clearInterval(counter);
                    prev.style.display = 'none';
                    // window.location.reload();
                    // min.textContent = ('00');
                    // sec.textContent = ('00');
                    // console.log('final !');
                }
            
                
                min.textContent = (tm < 10 ? '0'+tm : tm);
                sec.textContent = (ts < 10 ? '0' +ts : ts);
                // console.log(tm, ts);


            }, 1000)

            // prev.style.display = 'block';
            // if (time <= 0) {
            //     prev.style.display = 'none';
            //     badge.style.display = 'none';
            // }
        }
            


        // @if (session('success'))
        //     toastr.success("{{ session('success') }}", 'Success', {
        //         positionClass: 'toast-top-right',
        //         timeOut: 3000
        //     });
        // @endif

        // @if (session('warning'))
        //     toastr.warning("{{ session('warning') }}", 'Warning', {
        //         positionClass: 'toast-top-right',
        //         timeOut: 3000
        //     });
        // @endif

        
        
        @if($vip_package?->valid_till > now())
            @if(auth()->check() && $vip_package?->status && $vip_package?->task_type == 'monthly')
                // localStorage.vip_{{auth()->user()->id}} = 1;
                // console.log({{auth()->user()->vipPurchase->month_task_date != today() }});
                let dtt = new Date();
                let toDat = dtt.getDate();
                // let task = "{{ auth()->user()->vipPurchase->month_task_date }}";
                let tdd = new Date("{{ $vip_package->month_task_date }}");
                let taskDat = tdd.getDate();
                // console.log(toDat == taskDat);

                if (toDat != taskDat) {
                    // trackVip();   
                    // alert('month task');
                    // console.log(toDat, taskDat);
                    showTimer();
                }
            @endif
            @if(auth()->check() && $vip_package?->status && $vip_package?->task_type == 'daily')
                // console.log('daily');
                
                showTimer();
            @endif
        @endif

    </script>
@endif
    





@endsection