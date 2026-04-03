<div>
    {{-- Be like water. --}}


    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex justify-between items-center">
                        <div>
                            {{auth()->user()->name}}
                        </div>
                        <div class="text-sm">
                            from {{auth()->user()?->isRider()?->created_at->toFormattedDateString()}}
                        </div>
                    </div>
                </x-slot>
                <x-slot name="content">
                    @if (auth()->user()->isRider()->is_reject)
                    <div class="px-3 py-1 bg-red-700 text-white inline-flex rounded shadow text-xs"> Rejected </div>
                    <div class="text-xs">
                        {{auth()->user?->isRider()?->reject_fo}}
                    </div>
                    @else
                    <div class="bg-gray-800 text-white px-3 py-1 inline-block rounded shadow text-xs">
                        {{auth()->user()->isRider()->status}}
                    </div>
                    @endif
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <div class="border-b mb-1 bg-green-100 md:flex justify-between items-center">
                    <div class="p-2">
                        Target Area
                    </div>
                    <div class="p-2 text-bold">
                        {{auth()->user()->isRider()->targeted_area}}
                    </div>
                </div>
                <hr />
                <div class="border-b mb-1 bg-gray-100 md:flex justify-between items-center">
                    <div class="p-2">
                        Name
                    </div>
                    <div class="p-2">
                        {{auth()->user()->name}}
                    </div>
                </div>
                <div class="border-b mb-1 bg-gray-100 md:flex justify-between items-center">
                    <div class="p-2">
                        Email
                    </div>
                    <div class="p-2">
                        {{auth()->user()->email}}
                    </div>
                </div>
                <div class="border-b mb-1 bg-gray-100 md:flex justify-between items-center">
                    <div class="p-2">
                        Phone
                    </div>
                    <div class="p-2">
                        {{auth()->user()->phone}}
                    </div>
                </div>
                <div class="border-b mb-1 bg-gray-100 md:flex justify-between items-center">
                    <div class="p-2">
                        Permanent Address
                    </div>
                    <div class="p-2">
                        {{auth()->user()->isRider()->fixed_address}}
                    </div>
                </div>
                <div class="border-b mb-1 bg-gray-100 md:flex justify-between items-center">
                    <div class="p-2">
                        Current Address
                    </div>
                    <div class="p-2">
                        {{auth()->user()->isRider()->current_address}}
                    </div>
                </div>
                <div class="border-b mb-1 bg-gray-100 md:flex justify-between items-center">
                    <div class="p-2">
                        NID
                    </div>
                    <div class="p-2">
                        {{auth()->user()->isRider()->nid}}
                        <hr>
                        <img src="{{asset('storage/' . auth()->user()?->isRider()?->nid_photo_front)}}"
                            class="w-12 h-12 rounded shadow" alt="">
                        <img src="{{asset('storage/' . auth()->user()?->isRider()?->nid_photo_back)}}"
                            class="w-12 h-12 rounded shadow" alt="">
                    </div>
                </div>

            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>
</div>