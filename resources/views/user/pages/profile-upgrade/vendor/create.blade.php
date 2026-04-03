@extends('layouts.user.dash.userDash')

@section('site_title')
    Vendor Upgrade
@endsection

@section('content')
    <div>


        <x-dashboard.section >
            <x-dashboard.section.header>
                <x-slot name="title">
                    Vendor Request Form
                </x-slot>

                <x-slot name="content">
                    Request to be a vendor
                    <a href="{{route('upgrade.vendor.index')}}" class="">
                        previous request
                    </a>
                   
                </x-slot>
            </x-dashboard.section.header>
        </x-dashboard.section>
       
        <form action="{{route('upgrade.vendor.store')}}" method="post"> 
            @csrf
            @include('user.pages.profile-upgrade.vendor.partials.basic')

            {{-- <x-dashboard.section>
                <x-dashboard.section.inner>
                    <x-primary-button>
                        submit
                    </x-primary-button>
                </x-dashboard.section.inner>
            </x-dashboard.section> --}}
        </form>
    </div>

@endsection