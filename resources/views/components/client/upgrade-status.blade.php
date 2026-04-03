<div>
    <!-- If you do not have a consistent goal in life, you can not live it in a consistent way. - Marcus Aurelius -->
    <div>
        @php
        if ($upgrade == 'reseller') {
        $req = auth()->user()->requestsToBeReseller();
        }else{
        $req = auth()->user()->requestsToBeVendor();
        }

        if (isset($id)) {
            $authRequest = $req?->find($id);
        }else{
            $authRequest = $req?->latest();
        }
        @endphp
        @if ($authRequest->status == 'Pending')
        <div class="text-sm py-1 border-b border-t p-2 rounded" style="background-color: #fefcbf; color: #b45309;">
            <strong>Pending</strong>, Your account is under reveiw now. stay with patience.
        </div>
        @endif
        @if ($authRequest->status == 'Active')
        <div class="text-sm py-1 border-b border-t p-2 rounded" style="background-color: #bbf7d0; color: #166534;">
            Your Membership is now in <strong>{{$authRequest->status}}</strong> with
            <strong>{{$authRequest->system_get_comission ?? "0"}}%</strong> comission sharing . Now you can sell your
            products.
        </div>
        @endif
        @if ($authRequest->status == 'Disabled' || $authRequest->status == 'Suspended')
        <div class="text-sm py-1 border-b border-t p-2 rounded" style="background-color: #fee2e2; color: #b91c1c;">
            Your Membership is now <strong>{{$authRequest->status}}</strong> . <strong>{{ $authRequest->rejected_for ??
                "For unknown reason " }}</strong>
        </div>
        @endif

        @if ($authRequest->documents && $authRequest->documents?->deatline > carbon\Carbon::now())
        <div class="text-xs py-3">
            You are requested to fill your required document, with deatline of
            <strong>
                {{Carbon\Carbon::parse($authRequest->documents->deatline)->toFormattedDateString()}} *.
            </strong>
            After successfully authorize your document, you will be able to do your vendor daily jobs. Otherwise, you
            will be suspended.
        </div>
        @endif
    </div>

</div>