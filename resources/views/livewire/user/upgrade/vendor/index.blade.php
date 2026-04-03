


<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    <x-dashboard.section>
        <x-dashboard.section.header>
            <x-slot name="title">    
                <h5>Profile Upgrade ({{$upgrade}})  </h5>
            </x-slot>
    
            <x-slot name="content">
                <div class="flex justify-between">
                    <div>
                        Upgrade your account to revenew money. To make a new request , click the 
                    </div>
                    
                </div>
                <x-primary-button x-on:click.prevent="$dispatch('open-modal', 'request-for-create')">
                    NEW REQUEST
                </x-primary-button>
                <x-hr/>
                <x-client.membership-activate-box />
                
            </x-slot>
        </x-dashboard.section.header>
        <x-hr/>
        <div>
            <x-nav-link :active="$upgrade == 'vendor'" href="?upgrade=vendor" > Vendor</x-nav-link>
            <x-nav-link :active="$upgrade == 'reseller'" href="?upgrade=reseller" > Reseller</x-nav-link>
            <x-nav-link :active="$upgrade == 'rider'" href="{{route('upgrade.rider.index')}}" > Rider</x-nav-link>
        </div>
        <x-dashboard.section.inner>
            <div  wire:show="upgrade == 'vendor'">
                @if (auth()->user()->requestsToBeVendor->count() > 0)
                    {{-- @if ($activeReqeust)
                    
                    @else
                    @endif --}}
                    <x-dashboard.section.inner>
                        <x-dashboard.table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
    
                            <tbody>
                                @foreach (auth()->user()->requestsToBeVendor()->orderBy('id', 'desc')->get() as $vr)
                                    <tr>
                                        <td> {{$loop->iteration}} </td>
                                        <td>
                                            
                                            <x-nav-link href="{{route('upgrade.vendor.edit', ['upgrade' => 'vendor' , 'id' => $vr->id])}}">
                                                {{$vr->shop_name_en}}
                                            </x-nav-link>
    
                                        </td>
                                        <td> {{$vr->created_at?->toFormattedDateString()}} </td>
                                        <td> {{$vr->status}} </td>
                                    
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-dashboard.table>
                    </x-dashboard.section.inner>
                @else
                    <div class="alert alert-info">
                        No Previous request found! Make a new request, instead. 
                    </div>
                @endif
            </div>
        </x-dashboard.section.inner>
        <x-dashboard.section.inner>
            <div  wire:show="upgrade == 'reseller'">

                @if (auth()->user()->requestsToBeReseller->count() > 0)
                    <x-dashboard.section.inner>
                        <x-dashboard.table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
    
                            <tbody>
                                @foreach (auth()->user()->requestsToBeReseller()->orderBy('id', 'desc')->get() as $vr)
                                    <tr>
                                        <td> {{$loop->iteration}} </td>
                                        <td>
                                            
                                            <x-nav-link href="{{route('upgrade.vendor.edit', ['upgrade' => 'reseller','id' => $vr->id])}}">
                                                {{$vr->shop_name_en}}
                                            </x-nav-link>
    
                                        </td>
                                        <td> {{$vr->created_at?->toFormattedDateString()}} </td>
                                        <td> {{$vr->status}} </td>
                                    
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-dashboard.table>
                    </x-dashboard.section.inner>
                @else
                    <div class="alert alert-info">
                        No Previous request found! Make a new request, instead. 
                    </div>
                @endif
            </div>
        </x-dashboard.section.inner>
    </x-dashboard.section>  

    <x-modal name="request-for-create" maxWidth="sm">
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Make sure your request
                </x-slot>
                <x-slot name="content">
                    please choose your expected link to reqeust.
                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>
                <x-nav-link href="{{route('upgrade.vendor.create', ['upgrade' => 'vendor'])}}">
                    <x-primary-button>
                        Request for Vendor
                    </x-primary-button>
                </x-nav-link>
                <br>
                <x-nav-link href="{{route('upgrade.vendor.create', ['upgrade' => 'reseller'])}}">
                    <x-primary-button>
                        Request for Reseller
                    </x-primary-button>
                </x-nav-link>
                <br>
                <x-nav-link href="{{route('upgrade.rider.create')}}">
                    <x-primary-button>
                        Request for Rider (Delevary Man)
                    </x-primary-button>
                </x-nav-link>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-modal>

</div>
