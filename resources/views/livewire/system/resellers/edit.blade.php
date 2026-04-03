<div>
    {{-- Success is as dangerous as failure. --}}
    <x-dashboard.page-header>
        Resellers
        <br>
        <x-nav-link href="{{route('system.users.edit', ['id' => $resellers->user?->id ?? ''])}}">
            {{$resellers->user?->name ?? "N/A"}}
        </x-nav-link>
        - <span class="text-sm"> {{$resellers->shop_name_bn ?? "N/A"}} </span>
        <br>
        <span class="text-xs">Pending</span>
        <br>


        <div>
            <x-nav-link :active="$nav == 'user'" href="?nav=user">user</x-nav-link>
            <x-nav-link :active="$nav == 'documents'" href="?nav=documents">Documents</x-nav-link>
            <x-nav-link :active="$nav == 'products'" href="?nav=products">Products</x-nav-link>
            <x-nav-link :active="$nav == 'categories'" href="?nav=categories">Categories</x-nav-link>
            <x-nav-link :active="$nav == 'orders'" href="?nav=orders">Orders</x-nav-link>
        </div>
    </x-dashboard.page-header>
    
    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Reseller and Shops
                </x-slot>
                <x-slot name="content">
                    <div>
                         <div class="md:flex w-full flex-1 gap-10">
                            <div class="p-3 bg-gray-100 rounded-md shadow-sm w-full">
                                <hr>
                                <div class="text-md border-b w-full p-3">
                                    <div class="font-bold">Reseller ID: </div> 
                                    <div> {{$resellers->id ?? "N/A"}} </div>
                                </div>
                                <div class="text-md border-b w-full p-3">
                                    <div class="font-bold">Reseller Name: </div> 
                                    <div> {{$resellers->user?->name ?? "N/A"}} </div>
                                </div>
                                <div class="text-md border-b w-full p-3">
                                    <div class="font-bold">Reseller Email: </div> 
                                    <div> {{$resellers->user?->email ?? "N/A"}} </div>
                                </div>
                                <div class="text-md border-b w-full p-3">
                                    <div class="font-bold">Reseller Phone: </div> 
                                    <div> {{$resellers->user?->phone ?? "N/A"}} </div>
                                </div>
                                <div class="text-md  w-full p-3">
                                    <div class="font-bold">Shop Name: </div> 
                                    <div> {{$resellers->shop_name_en ?? "N/A"}}  </div>
                                </div>
                            </div>
                            <div class="p-3 bg-gray-100 rounded-md shadow-sm w-full">
                                <hr>
                                
                                <div class="text-md border-b w-full p-3">
                                    <div class="font-bold">Shop Email: </div> 
                                    <div> {{$resellers->email ?? "N/A"}} </div>
                                </div>
                                <div class="text-md border-b w-full p-3">
                                    <div class="font-bold">Shop Phone: </div> 
                                    <div> {{$resellers->phone ?? "N/A"}} </div>
                                </div>
                                <div class="text-md border-b w-full p-3">
                                    <div class="font-bold">Shop Address: </div> 
                                    <div> {{$resellers->address ?? "N/A"}}  </div>
                                </div>
                                <div class="text-md border-b w-full p-3">
                                    <div class="font-bold">Shop Location: </div>
                                    <div> {{$resellers->upazila ?? "N/A"}}, {{$resellers->district ?? "N/A"}}, {{$resellers->country ?? "N/A"}} </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-dashboard.section.header>
            
            <x-dashboard.section.inner>
                
                <x-hr />
                <div>
                    <form wire:submit.prevent='updateStatus'>
    
                        <div class="flex items-center justify-between">
    
                            <div >
                                <p class="text-sm">Current Status is : <strong> {{$resellers  ->status}} </strong>. Change status to - </p>
                                <select id="resStatus" wire:model.live="resArray.status" class="rounded-lg py-1" >
                                    <option value="Select Status">-- Select -- </option>
                                    <option value="Pending">Pending</option>
                                    <option value="Disabled">Disabled</option>
                                    <option value="Suspended">Suspended</option>
                                    <option value="Active">Active</option>
                                </select>
                                
                                {{-- <div class="mt-1" x-show="sd != 'Active'">
                                    <textarea class="rounded-lg" name="" id="" rows="2"></textarea>
                                </div> --}}
                                
                            </div>
                            <div class="text-end">
                                <p class="text-sm">
                                    update : {{$resellers ->updated_at->diffForHumans()}}
                                </p>
                                <x-primary-button class="ml-2">set</x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
    
                <x-hr/>
                <div>
                    <form wire:submit.prevent="setComission">
                        <div class="flex justify-between items-start">
                            <div>
                                <input type="text" wire:model.live="comission" class="rounded shadow" id="">
                                <div class='text-xs'>
                                    You take {{$resellers->system_get_comission ?? "0"}}% profit from this vendor revinew.
                                </div>
                            </div>
                        </div>
                        
                        <div class="my-2 rounded bg-gray-50 border-gray-200 p-3">
                            <div class="p-3 w-full flex justify-between items-center">
                                <div class="font-bold">Prevent adding unlimited product  : </div>
                                <div class="flex gap-10"> 
                                    <div class="flex items-center">
                                        <input type="radio" wire:model.live="resArray.allow_max_product_upload" id="allow_max" name="allow_max_product_upload" value="1" style="width:20px; height:20px">
                                        <div class="px-2">Yes</div>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio" wire:model.live="resArray.allow_max_product_upload" id="allow_max" name="allow_max_product_upload" value="0" style="width:20px; height:20px">
                                        <div class="px-2">No</div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-3 w-full flex justify-between items-center">
                                <div class="font-bold">Maximum Product : </div>
                                <div> 
                                    <x-text-input type="number" wire:model.live="resArray.max_product_upload" placeholder="100" class="w-20" />
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
                                        <input type="radio" wire:model.live="resArray.allow_max_resell_product" id="allow_max" name="allow_max_resell_product" value="1" style="width:20px; height:20px">
                                        <div class="px-2">Yes</div>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio" wire:model.live="resArray.allow_max_resell_product" id="allow_max" name="allow_max_resell_product" value="0" style="width:20px; height:20px">
                                        <div class="px-2">No</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="px-3 w-full flex justify-between items-center">
                                <div class="font-bold">Maximum Resel Product : </div>
                                <div> 
                                    <x-text-input type="number" wire:model.live="resArray.max_resell_product" placeholder="100" class="w-20" />
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 px-3">
                                If you allow the vendor to resell products, then the vendor will be able to resell products from other resellers.
                            </div>
                        
                        </div>

                        <div class="my-2 bg-gray-50 p-3">
                            <div class="px-3 w-full flex justify-between items-center">
                                <div class="font-bold">Define Fixed Amount : </div>
                                <div> 
                                    <x-text-input type="number" wire:model.live="resArray.fixed_amount" placeholder="100" class="w-20" />
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <x-primary-button>Update</x-primary-button>
                        </div>
                    </form>
                </div>
                
            </x-dashboard.section.inner>
        </x-dashboard.section>

        @if ($nav == 'documents')
    
            <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        Documents
                    </x-slot>
                    <x-slot name="content">
                        See the listed document submitted by the user
                    </x-slot>
                </x-dashboard.section.header>
                <x-dashboard.section.inner>
                    <x-input-file label="Document Submited Last Date" error="deatline">
                        <div class="border px-2 rounded shadow-sm">    
                            {{Carbon\Carbon::parse($resellers->documents->deatline)->toFormattedDateString()}} - {{Carbon\Carbon::parse($resellers->documents->deatline)->diffForHumans()}}
                        </div>
                    </x-input-file>
                    <x-hr />
                    <form wire:submit.prevent="updateDeatline">
                        <x-input-file label="set New Date" error="deatline">
                            <div class="flex">
    
                                <x-text-input wire:model.live="deatline" type="date" class="py-1" />
                                <x-primary-button type="button" wire:show="deatline" class="ms-2 py-1" type="submit">set</x-primary-button>
                            </div>
                        </x-input-file>
                    </form>
                </x-dashboard.section.inner>
            </x-dashboard.section>
      
            <x-dashboard.section>
                @php
                    $resellersDocument = $resellers->documents;
                @endphp
    
                <x-input-file label="Nid" error="nid">
                    <x-text-input type="number" class="form-control py-1" value="{{$resellersDocument->nid}}" label="NID No" name="nid" error="nid" />
                </x-input-file>
                <x-hr/>
    
                <x-input-file label="NID Image (front side)" error='nid_front'>
                    <img width="300px" height="200px" src="{{asset('storage/'.$resellersDocument->nid_front)}}" alt="">                
                </x-input-file>
                <x-hr/>
                
                <x-input-file label="NID Image (back side)" error='nid_back'>
                    <img width="300px" height="200px" src="{{asset('storage/'.$resellersDocument->nid_back)}}" alt="">                    
                </x-input-file>
                <x-hr />
            </x-dashboard.section>
            <x-dashboard.section>
                
                <x-input-file label="TIN No" error='tin'>
                    <x-text-input type="text" name="" value="{{$resellersDocument->shop_tin}}" id="" />
                </x-input-file>
                <x-hr/>
    
                <x-input-file label="TIN Image" error='shop_tin'>
                        <img width="300px" height="200px" src="{{asset('storage/'.$resellersDocument['shop_tin_image'])}}" alt="">                  
                </x-input-file>
            </x-dashboard.section>
    
            <x-dashboard.section>
                <x-input-file label="Shop Trade" error="shop_trade">
                    <x-text-input type="text" name="" value="{{$resellersDocument->shop_trade}}" id="" />
                </x-input-file>
                <x-hr/>
    
                <x-input-file label="Trade License Image" error='shop_trade_image'>
                    <img width="300px" height="200px" src="{{asset('storage/'.$resellersDocument['shop_trade_image'])}}" alt="">
                </x-input-file>
    
            
            </x-dashboard.section>
            
        @endif
        @if ($nav == 'products')
            <x-dashboard.section>
                <x-dashboard.section.inner>
                </x-dashboard.section.inner>
            </x-dashboard.section>
            
        @endif
        @if ($nav == 'orders')
            
            <x-dashboard.section>
                <x-dashboard.section.inner>
                </x-dashboard.section.inner>
            </x-dashboard.section>
        @endif
    </x-dashboard.container>
    @if ($nav == 'user')
        @livewire('system.users.edit', ['id' => $resellers->user?->id], key($resellers->user?->id))
    @endif
</div>
