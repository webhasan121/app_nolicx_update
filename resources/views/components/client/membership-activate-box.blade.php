<div>
    <!-- It is never too late to be what you might have been. - George Eliot -->
    @php
        $vendorActive = auth()->user()->requestsToBeVendor()->where(['status' => 'Active'])->first();
        $resellerActive = auth()->user()->requestsToBeReseller()->where(['status' => 'Active'])->first();
    @endphp
    @if($vendorActive)
        <div class="alert alert-success">
            <h6>Hello,</h6>
            <p>Your request for vendor, name of <strong class="px-3 py-1 mx-1 text-white bg-gray-800 rounded-lg shadow-sm">{{$vendorActive->shop_name_bn ?? "N/A"}} / {{$vendorActive->shop_name_en ?? "N/A"}} </strong> with <strong class="px-3 py-1 text-white bg-gray-800 rounded-lg shadow-sm">{{ $vendorActive->system_get_comission ?? "0" }}%</strong> comission share, is active now.</p>
            <x-nav-link href="{{route('dashboard')}}">Go To Dashboard</x-nav-link>
        </div>
    @endif


    @if($resellerActive)
        <div class="alert alert-success">
            <h6>Hello,</h6>
            <p>Your request for reseller, name of <strong class="px-3 py-1 mx-1 text-white bg-gray-800 rounded-lg shadow-sm">{{$resellerActive->shop_name_bn ?? "N/A"}} / {{$resellerActive->shop_name_en ?? "N/A"}} </strong> with <strong class="px-3 py-1 text-white bg-gray-800 rounded-lg shadow-sm">{{ $resellerActive->system_get_comission ?? "0" }}%</strong> comission share, is active now.</p>
        </div>
    @endif
</div>
