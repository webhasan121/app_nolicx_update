<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    <x-dashboard.page-header>
        @include('auth.system.vendors.navigations')
    </x-dashboard.page-header>
    
    <x-dashboard.container>
        <form wire:submit.prevent="update">
            

            <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        Settings
                    </x-slot>
                    <x-slot name="content">
                        Set up your vendor membership status and important things.
                    </x-slot>
                </x-dashboard.section.header>
                <x-dashboard.section.inner>

                    <div class="md:flex w-full flex-1 gap-10">
                        <div class="p-3 bg-gray-100 rounded-md shadow-sm w-full">
                            <hr>
                            <div class="text-md border-b w-full p-3">
                                <div class="font-bold">Vendor ID: </div> 
                                <div> {{$vendor->id ?? "N/A"}} </div>
                            </div>
                            <div class="text-md border-b w-full p-3">
                                <div class="font-bold">Vendor Name: </div> 
                                <div> {{$vendor->user?->name ?? "N/A"}} </div>
                            </div>
                            <div class="text-md border-b w-full p-3">
                                <div class="font-bold">Vendor Email: </div> 
                                <div> {{$vendor->user?->email ?? "N/A"}} </div>
                            </div>
                            <div class="text-md border-b w-full p-3">
                                <div class="font-bold">Vendor Phone: </div> 
                                <div> {{$vendor->user?->phone ?? "N/A"}} </div>
                            </div>
                            <div class="text-md  w-full p-3">
                                <div class="font-bold">Shop Name: </div> 
                                <div> {{$vendor->shop_name_en ?? "N/A"}}  </div>
                            </div>
                        </div>
                        <div class="p-3 bg-gray-100 rounded-md shadow-sm w-full">
                            <hr>
                            
                            <div class="text-md border-b w-full p-3">
                                <div class="font-bold">Shop Email: </div> 
                                <div> {{$vendor->email ?? "N/A"}} </div>
                            </div>
                            <div class="text-md border-b w-full p-3">
                                <div class="font-bold">Shop Phone: </div> 
                                <div> {{$vendor->phone ?? "N/A"}} </div>
                            </div>
                            <div class="text-md border-b w-full p-3">
                                <div class="font-bold">Shop Address: </div> 
                                <div> {{$vendor->address ?? "N/A"}}  </div>
                            </div>
                            <div class="text-md border-b w-full p-3">
                                <div class="font-bold">Shop Location: </div>
                                <div> {{$vendor->upazila ?? "N/A"}}, {{$vendor->district ?? "N/A"}}, {{$vendor->country ?? "N/A"}} </div>
                            </div>
                        </div>
                    </div>
                    @csrf
                    <div class=" mt-2 p-3 border rounded bg-gray-50 shadow-sm">
                        <div class="w-full bg-gray-50 p-3 rounded-md shadow-sm">
                            <div>
                                <x-hr/>
                                <div class="flex">
                                    <div class="flex items-center p-2 ">
                                        <x-text-input type="radio" class="m-0 mr-2" wire:model.live="varray.status" name="status" value="Active" id="active_check" />
                                        <x-input-label class="m-0" >Active</x-input-label>
                                    </div>
                                    <div class="flex items-center p-2 ">
                                        <x-text-input type="radio" class="m-0 mr-2" wire:model.live="varray.status" name="status" value="Pending" id="Pending_check" />
                                        <x-input-label class="m-0" >Pending</x-input-label>
                                    </div>
                                    <div class="flex items-center p-2 ">
                                        <x-text-input type="radio" class="m-0 mr-2" wire:model.live="varray.status" name="status" value="Disabled" id="Disabled_check" />
                                        <x-input-label class="m-0" >Disabled</x-input-label>
                                    </div>
                                    <div class="flex items-center p-2 ">
                                        <x-text-input type="radio" class="m-0 mr-2" wire:model="varray.status" name="status" value="Suspended" id="Suspended_check" />
                                        <x-input-label class="m-0" >Suspended</x-input-label>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500" wire:show="varray.status == 'Pending'">
                                    Pending vendor membership can update own information.
                                </div>
                                <x-hr/>
                                <div class="text-xs text-gray-500">
                                    <span class="font-bold">Note: </span> 
                                    If you change the vendor status to "Pending" or "Disabled", then the vendor will not be able to access the dashboard.
                                    <br>
                                    If you change the vendor status to "Active", then the vendor will be able to access the dashboard and manage their products.
                                </div>
                                <x-hr />
                                <div class="flex justify-between items-center gap-2">
                                    
                                    <div>Comission Rate (%) </div>
                                    {{-- <x-text-input name="system_get_comission" wire:model="varray.system_get_comission" placeholder="10" /> --}}
                                    <div class='text-xs'>
                                        <input type='number' wire:model="varray.system_get_comission" class="form-control rounded-md shoadow-sm" max="100" />
                                        <div>
                                            You take {{$vendor->system_get_comission ?? "0"}}% profit from this vendor revinew.
                                        </div>
                                    </div>
        
                                </div>
        
                                
                            </div>
        
                        </div>
        
                       {{-- allow to add product  --}}
                        <div class="my-2 rounded bg-gray-50 border-gray-200 p-3">
                            <div class="p-3 w-full flex justify-between items-center">
                                <div class="font-bold">Prevent adding unlimited product  : </div>
                                <div class="flex gap-10"> 
                                    <div class="flex items-center">
                                        <input type="radio" wire:model.live="varray.allow_max_product_upload" id="allow_max" name="allow_max_product_upload" value="1" style="width:20px; height:20px">
                                        <div class="px-2">Yes</div>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio" wire:model.live="varray.allow_max_product_upload" id="allow_max" name="allow_max_product_upload" value="0" style="width:20px; height:20px">
                                        <div class="px-2">No</div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-3 w-full flex justify-between items-center">
                                <div class="font-bold">Maximum Product : </div>
                                <div> 
                                    <x-text-input type="number" wire:model.live="varray.max_product_upload" placeholder="100" class="w-20" />
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 px-3">
                                If you set the maximum product, then the vendor will not be able to upload more than this number of products.
                            </div>
                        
                        </div>
                        
                        <div class="my-2 bg-gray-50 p-3">
                            <div class="px-3 w-full flex justify-between items-center">
                                <div class="font-bold">Allow to resell products : </div>
                                {{-- <div> 
                                    <input wire:model.live="can_resell_products" type="checkbox" name="" style="width:20px; height:20px" id="">
                                </div> --}}

                                <div class="flex gap-10"> 
                                    <div class="flex items-center">
                                        <input type="radio" wire:model.live="varray.can_resell_products" id="allow_max" name="can_resell_products" value="1" style="width:20px; height:20px">
                                        <div class="px-2">Yes</div>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio" wire:model.live="varray.can_resell_products" id="allow_max" name="can_resell_products" value="0" style="width:20px; height:20px">
                                        <div class="px-2">No</div>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="text-xs text-gray-500 px-3">
                                If you allow the vendor to resell products, then the vendor will be able to resell products from other resellers.
                            </div>
                        
                        </div>
                        
                        <x-hr />
                        <x-primary-button>
                            Update Settings
                        </x-primary-button>

                        {{-- <div wire:show="varray.status == 'Active'">
                            <x-nav-link href="{{route('system.users.edit', ['id' => $vendor->user?->id])}}">
                                <x-primary-button>
                                    Updat User
                                </x-primary-button>
                            </x-nav-link>
                        </div> --}}
                    </div>


                </x-dashboard.section.inner>
            </x-dashboard.section>

           

        </form>



        <div class="md:flex justify-between items-start">
            <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        Rejection
                    </x-slot>
                    <x-slot name="content">
                        If you wish to reject the vendor membership request, <br> Follow the bellow rejection projess. <br> first check to the checkbox, the give a rejection causes message.
                    </x-slot>
                </x-dashboard.section.header>
                <x-dashboard.section.inner>
                    <form wire:submit.prevent="update">
                        
                        <div class="flex mb-3">
                            <x-text-input type="checkbox" wire:model.live="varray.is_rejected" value="1" style="width:25px; height:25px; margin-right:10px" />
                            <x-input-label>Rejecte the request!</x-input-label>
                        </div>
    
                        <textarea wire:model.live="varray.rejected_for" id=""  rows="8" class="p-3" placeholder="Describe why you wish to reject .... "></textarea>
                        <x-hr />
    
                        <x-primary-button>
                            submit
                        </x-primary-button>
                    </form>
                </x-dashboard.section.inner>
            </x-dashboard.section>


            {{-- <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        Permissions
                    </x-slot>

                    <x-slot name='content'>
                        Give or Remove permission for certain task
                    </x-slot>
                </x-dashboard.section.header>

                <x-dashboard.section.inner>
                    @php
                        $userPermissions = $vendor->user->getPermissionNames();
                    @endphp
                    <form action="{{route('system.permissions.to-user', ['user' => $vendor->user->id])}}" method="post">
                        @csrf
                        <x-text-input type="hidden" name="user" value="{{$vendor->user->id}}" />
                    
                        <x-permissions-to-user :$userPermissions />
                        <x-primary-button>
                            save
                        </x-primary-button>
                    </form>

                </x-dashboard.section.inner>
            </x-dashboard.section> --}}
        </div>


    </x-dashboard.container>
</div>
