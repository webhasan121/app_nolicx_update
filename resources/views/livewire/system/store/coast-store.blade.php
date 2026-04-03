<div x-init="$wire.getDeta()" >
    <div class="rounded bg-white text-center" x-loading.disabled>
        <div class="border border-green-900 rounded md:flex justify-between items-center p-2">
            <div class="px-3 py-1 p-lg-3 bold text-start flex justify-between items-center md:block">
                <div class="fs-5 fw-bold text-start ">
                    <a href="" class="flex items-center"> 
                        <i class="fas fa-store text-md pe-2"></i>
                        Server Cost
                    </a>
                </div>
                <div class="hidden flex items-center text-xs">
                    <div class="text-start text-danger" style="color: red">
                        {{-- {{$withdraw ? $withdraw->count() : "0"}}  --}}
                        <i class="fas fa-long-arrow-alt-up"></i>
                    </div>
                    <div class="px-3">
                        |
                    </div>
                    <div class="" style="color:green">
                        <i class="fas fa-long-arrow-alt-down"></i>
                        {{-- {{$deposit ? $deposit->count() : "0"}}  --}}
                    </div>
                </div>
            </div>
            <div class="px-3 py-1 lg:p-3 text-lg fw-bold " style="color:green">
                {{-- {{$store->coin}} --}}
                {{$store}}
            </div>
         
        </div>
    </div>


</div>
