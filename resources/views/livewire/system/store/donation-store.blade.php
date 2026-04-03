<div class="" x-init="$wire.getDeta()"> 
    <div class="rounded bg-white text-center" x-loading.disabled>
        <div class="border border-green-900 rounded md:flex justify-between items-center p-2">
            <div class="px-3 lg:p-3 bold text-start flex justify-between items-center md:block">
                <div class="fs-5 fw-bold text-start ">
                    <a href="" class= flex items-center"> 
                        <i class="fas fa-store fs-6 pe-2"></i>
                        Domain
                    </a>
                </div>
                <div class= "hidden flex items-center text-xs">
                    <div class="text-start text-red-900">
                        {{-- {{$withdraw ? $withdraw->count() : '0'}} --}}
                        <i class="fas fa-long-arrow-alt-up"></i>
                    </div>
                    <div class="px-3">
                        |
                    </div>
                    <div class="text-green-900">
                        <i class="fas fa-long-arrow-alt-down"></i>
                        {{-- {{$deposit ? $deposit->count() : '0'}} --}}
                    </div>
                </div>
            </div>
            <div class="px-3 py-1 lg:p-3 text-lg fw-bold text-green-900">
                {{-- {{$store->coin}} --}}
                {{$store}}
            </div>
            {{-- <div class="px-3 py-1">

                <button data-bs-toggle='modal' data-bs-target="#addCoinModal" class="btn btn-outline-default border px-3 py-1">
                    <i class="fa fa-plus"></i> Coin
                </button>
            </div>   --}}
        </div>
    </div>

</div>
