<div>
    <!-- An unexamined life is not worth living. - Socrates -->
    <div>

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
                height: 50px;
                /* border: 1px solid #c7c7c7; */
                text-align: center;
                display: flex;
                justify-content: space-between;
                align-items: center;
                /* background-color:white ; */
                border-radius: 8px;
                padding:0px 12px;
            }
        </style>
        @props(['isRequestedAccepted'])
        @isset($isRequestedAccepted)
            @foreach ($isRequestedAccepted as $req)
                @if ($req->status)
                    @if ($req->task_type == 'prevent')
                        <div class="alert alert-danger">
                            <strong>Warning !</strong> Your task has been <strong>PREVENTED</strong> by admin. <br> Now you are unable to earn by task. Please contact us for more information.
                        </div>
                        
                    @endif
                    {{-- <div @class(["alert alert-success rounded-0 border-bottom my-2 p-2 d-none", 'd-block' => $req->status])>

                        <div class="row justify-content-between w-100 align-items-start">
                            <div class="col-9 pb-1">
                                <h4>Active Packages</h4>
                            
                            </div>
                            <div class="col-3 w-100 text-right ">
                                <button class="btn btn-outline-info btn-md mx-lg-4 d-flex justify-content-between align-items-center">
                                    {{$req->package->name ?? ""}}
                                    <i class="fas fa-sort mx-2"></i>
                                </button>
                            </div>
                        </div>
                
                    </div> --}}
                
                    @if(request()->routeIs('user.vip.*'))
            
                        <div @class(['md:flex justify-between items-start m-0' => $req->status, 'block'])>
                    
                            <div class='mt-4'>
                                <div style="display: grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); grid-gap:20px;" >
                                    <a href="" class="block bold border vip_item_info_box">
                                        
                                        <div class="text-left"> 
                                            <div>
                                                Tasks
                                            </div>
                                            <span class="block text-dark" style="font-size: 10px">
                                                {{ DB::table('user_tasks')->where(['user_id' => Auth::id(), 'package_id' => $req->package_id])->count() ?? "0"}} taks completed
                                            </span>
                                        </div>
                                        <i class="fas fa-caret-right"></i>
                                    </a>
                    
                                    {{-- <a href="" class="block bold border vip_item_info_box">
                                        
                                        
                                        <div class="text-left"> 
                                            <div>
                                                Statistics
                                            </div>
                                            <span class="block text-dark" style="font-size: 10px">
                                                Earn {{$req->user?->coin ?? ""}}
                                            </span>
                                        </div>
                                        <i class="fas fa-caret-right"></i>
                                    </a> --}}
                    
                                    <x-hr/>
                                    <div>
                                        <div class="text-md">
                                            Active From 
                                        </div>
                                        <div class="text-xs">
                                            {{$req->package?->created_at->diffForHumans()}}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-md">
                                            Validity 
                                        </div>
                                        <div class="text-xs">
                                            {{\Carbon\Carbon::parse($req->valid_till)->diffForHumans() ?? 'Unlimited'}} at   {{\Carbon\Carbon::parse($req->valid_till)->toFormattedDateString()}}
                                        </div>
                                    </div>

                                    <x-hr/>
                                </div>
                            </div>
                    
                            {{-- <div @class(["py-4 px-2 "]) style="display: grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); grid-gap:20px;" > --}}
 
                            <div @class(["py-4 px-2  text-sm"]) >

                                <div class="mb-1 vip_item_info_box border bg-indigo-900 text-white">
                                    <div class="">
                                        Package
                                    
                                    </div>
                                    
                                    <div style="font-weight: 600; font-size:18px" class="">
                                        {{$req->package?->name ?? "Not Found !"}}
                                    </div>
                                </div>

                                {{-- <div class="mb-1 vip_item_info_box border">
                                    <div class="">
                                        Balance
                                    </div>
                                    
                                    <div style="font-weight: 600; font-size:18px" class="">
                                        {{$req->user?->coin ?? "0"}}
                                    </div>
                                </div> --}}
                        
                        
                                {{-- <div class="mb-1 vip_item_info_box border">
                                    <div>
                                        Price <i class="fas fa-check-circle mx-2"></i>
                                    </div>
                                    
                                    <div style="font-weight: 600; font-size:18px" class="">
                                        {{$ownerPackage->package->price}} TK
                                    </div>
                                </div>--}}
                        
                                <div class="mb-1 vip_item_info_box border">
                                    <div>
                                        Earning Rate
                                    </div>
                                    
                                    <div style="font-weight: 600; font-size:18px" class="">
                                        {{$req->package?->coin ?? "0"}} coin
                                    </div>
                                </div>
                        
                                <div class="mb-1 vip_item_info_box border">
                                    <div>
                                        Duration <i class="fas fa-clock mx-2"></i>
                                    </div>
                                    
                                    <div style="font-weight: 600; font-size:18px" class="">
                                        {{$req->package?->countdown ?? "0"}} Min
                                    </div>
                                </div>
                    
                                <div class="mb-1 vip_item_info_box border">
                                    <div>
                                        Task Type 
                                    </div>
                                    
                                    <div style="font-weight: 600; font-size:18px" class="">
                                        {{$req->task_type}}
                                    </div>
                                </div>

                                <div @class(["mb-1 vip_item_info_box border d-none", 'd-block' => $req->package?->refer_bonus_owner ?? "0"])>
                                    <div>
                                        Refer Bonus <i class="fas fa-link mx-2"></i>
                                    </div>
                                    
                                    <div style="font-weight: 600; font-size:18px" class="">
                                        {{$req->package?->ref_owner_get_coin ?? "0"}} TK
                                    </div>
                                </div>
                        
                                {{-- <div @class(["mb-1 vip_item_info_box border d-none", 'd-block' => $req->package?->refer_bonus_via_link ?? "0" ])>
                                    <div>
                                        Give Refer Bonus <i class="fas fa-link mx-2"></i>
                                    </div>
                                    
                                    <div style="font-weight: 600; font-size:18px" class="">
                                        {{$req->package?->refer_bonus_via_link ?? "0"}} Min
                                    </div>
                                </div> --}}
                        
                            </div>
                    
                            <div @class(['hidden lg:block mt-4'])>
                                <x-vip-cart :item="$req->package" type='owner' :active="$req->package->id??''" />
                            </div>
                        </div>

                    @endif
                @else 
                    {{-- if request is in progress --}}
                    
                    <div class="flex items-start p-3 shadow-lg border border-indigo-900 bg-white rounded-lg">
                        <i class="fas fa-info p-2 me-4"></i>
                        <div>
                            <div class="bold font-bold text-red-900">Request In Progress</div>
                            <div class="text-sm">Recently you purchage an package. Your purchage request is in porgress. </div>
                            <br>
                           
                            <div class=" rounded-lg p-3">
                                <div class="mb-2">
                                    <div class="text-md pb-1 text-indigo-900">
                                        Package
                                    </div>
                                    <a href="{{route('user.package.checkout', ['id' => $req->package_id])}}" class="text-sm">
                                        {{$req->package->name }}
                                    </a>
                                </div>
                                <x-hr/>
                                <div class="mb-2">
                                    <div class="bold text-md pb-1">
                                        Task Type
                                    </div>
                                    <div class="text-sm">
                                        {{$req->package->task_type ?? 'daily'}}
                                    </div>
                                </div>
                                <x-hr/>
                                <div class="text-xs">
                                    {{\Carbon\Carbon::parse($req->created_at)->diffForHumans()}}
                                </div>
                            </div>
                            {{-- <x-nav-link href="{{route('user.package.cancle', ['id' => $req->id])}}"> Cancle </x-nav-link> --}}
                        </div>
                    </div>
                @endif
                
            @endforeach
        @endisset

    </div>
</div>