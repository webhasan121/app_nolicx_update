@extends('layouts.user.dash.userDash')

@section('site_title')
    Vendor Upgrade
@endsection

@section('content')

    <x-dashboard.section>
        <x-dashboard.section.header>
            <x-slot name="title">    
                <h5>Vendor Upgrade</h5>
            </x-slot>
    
            <x-slot name="content">
                Upgrade your account to venor to sell your product. To make a new request , click the button below. or check the status of your previous request.
                <div class="md:flex justify-between">
                    <a href="{{route('upgrade.vendor.create')}}">
                        <x-primary-button>
                            New REQUEST
                        </x-primary-button>
                    </a>
        
                    {{-- <a href="" class="mt-2 md:mt-0 block">
                        <x-secondary-button>
                            previous request
                        </x-secondary-button>
                    </a> --}}
                </div>
            </x-slot>
        </x-dashboard.section.header>
    </x-dashboard.section>

    <x-dashboard.section>
        <x-dashboard.section.header>
            <x-slot name="title">
                Previous Request 
            </x-slot>
            <x-slot name="content">
                view your previous vendor requests
            </x-slot>
        </x-dashboard.section.header>

        @if (auth()->user()->requestsToBeVendor->count() > 0)
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
                                    
                                    <x-nav-link href="{{route('upgrade.vendor.edit', ['id' => $vr->id])}}">
                                        {{$vr->shop_name_bn}}
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
        {{-- @php
            print_r(auth()->user()->requestsToBeVendor);
        @endphp --}}
    </x-dashboard.section>


@endsection