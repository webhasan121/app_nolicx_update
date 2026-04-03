<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <x-dashboard.page-header>  
        @include('auth.system.vendors.navigations')
    </x-dashboard.page-header>
    
    <x-dashboard.container>
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
                    {{-- <x-text-input type="date" /> --}}
                    <div class="border px-2 rounded shadow-sm">    
                        {{Carbon\Carbon::parse($vendor->documents->deatline)->toFormattedDateString()}} - {{Carbon\Carbon::parse($vendor->documents->deatline)->diffForHumans()}}
                    </div>
                    {{-- <input type="date" value="" id=""> --}}
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
    </x-dashboard.container>
    <x-dashboard.container>
        <x-dashboard.section>
             @php
                $vendorDocument = $vendor->documents;
             @endphp

            <x-input-file label="Nid" error="nid">
                <x-text-input type="number" class="form-control py-1" value="{{$vendorDocument->nid}}" label="NID No" name="nid" error="nid" />
            </x-input-file>
            <x-hr/>

            <x-input-file label="NID Image (front side)" error='nid_front'>
                <img width="300px" height="200px" src="{{asset('storage/'.$vendorDocument->nid_front)}}" alt="">                
            </x-input-file>
            <x-hr/>
            
            <x-input-file label="NID Image (back side)" error='nid_back'>
                <img width="300px" height="200px" src="{{asset('storage/'.$vendorDocument->nid_back)}}" alt="">                    
            </x-input-file>
            <x-hr />
        </x-dashboard.section>
        <x-dashboard.section>
            
            <x-input-file label="TIN No" error='tin'>
                <x-text-input type="text" name="" value="{{$vendorDocument->shop_tin}}" id="" />
            </x-input-file>
            <x-hr/>

            <x-input-file label="TIN Image" error='shop_tin'>
                    <img width="300px" height="200px" src="{{asset('storage/'.$vendorDocument['shop_tin_image'])}}" alt="">                  
            </x-input-file>
            {{-- <x-hr /> --}}
        </x-dashboard.section>
        {{-- <x-input-field wire:model="vendorDocument.shop_trade" label="business Trade Number" name="shop_trade" error="shop_trade" /> --}}

        <x-dashboard.section>
            <x-input-file label="Shop Trade" error="shop_trade">
                <x-text-input type="text" name="" value="{{$vendorDocument->shop_trade}}" id="" />
            </x-input-file>
            <x-hr/>

            <x-input-file label="Trade License Image" error='shop_trade_image'>
                <img width="300px" height="200px" src="{{asset('storage/'.$vendorDocument['shop_trade_image'])}}" alt="">
            </x-input-file>

           
        </x-dashboard.section>
    </x-dashboard.container>

</div>
