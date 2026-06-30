<div>
    @props(['model', 'temp', 'src'])
    <div class="flex">
        @if ($model && !$temp)
            <x-image class="mb-2" style="width:150px; height:100px" :src="$src" alt="IMAGE" />
        @endif
        @if($temp) 
            <img class="mb-2" style="width:150px; height:100px" :src="$temp->temporaryUrl()" alt="IMAGE" />
        @endif
    </div>


     
    {{-- @if ($vendorDocument['nid_front'] && !$nid_front)
        <x-image src="{{asset('/storage/'.$vendorDocument['nid_front'])}}" alt="" />
    @endif
    @if($nid_front) 
        <img src="{{$nid_front->temporaryUrl()}}" alt="">
    @endif --}}
</div>