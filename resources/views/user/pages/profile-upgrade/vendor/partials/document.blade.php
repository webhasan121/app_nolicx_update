<form action="{{route('upgrade.vendor.updateDocument', ['id' => $data->documents?->id ?? "0"])}}" method="post">
    {{-- <input type="text" name="" value="{{$data->documents->id}}" id=""> --}}
    @csrf
    <x-dashboard.section>
        <x-dashboard.section.inner>
            <x-input-field :data="$data->documents??[]" label="Your NID No" name="nid" error="nid" :required='true' />
            <div class="md:flex">
                <div style="width:250px">
                    <x-input-label> Your NID Front Image </x-input-label>
                </div>
                <div>
                    <img src="" width="300px" height="200px" alt="">
                    <x-text-input type="file" name="nid_front" />
                </div>
            </div>
            <div class="md:flex">
                <div style="width:250px">
                    <x-input-label> Your NID Back Image </x-input-label>
                </div>
                <div>
                    <img src="" width="300px" height="200px" alt="">
                    <x-text-input type="file" name="nid_back" />
                </div>
            </div>
    
            <x-hr/>
            <x-input-field :data="$data->documents??[]" label="Your TIN No" name="shop_tin" error="tin" :required='true' />
            <div class="md:flex">
                <div style="width:250px">
                    <x-input-label> Your TIN Image </x-input-label>
                </div>
                <div>
                    <img src="" width="300px" height="200px" alt="">
                    <x-text-input type="file" name="shop_tin_image" />
                </div>
            </div>
    
            <x-hr/>
            {{-- <x-input-field label="Your Business Trade No" name="trade" error="trade" :required='true' />
            <x-input-field type="file" label="Your Business Trade Image" name="trade_image" erro="trade_image"/> --}}
            
            {{-- <x-input-field label="Your Business Trade No" name="trade" error="trade" :required='true' />
            <x-input-field type="file" label="Your Business Trade Image" name="trade_image" erro="trade_image"  /> --}}
    
            @if (auth()->user()->id == $data?->user_id)     
                <x-primary-button>
                    submit
                </x-primary-button>
            @endif
    
        </x-dashboard.section.inner>
    </x-dashboard.section>
</form>