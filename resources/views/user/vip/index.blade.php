@extends('layouts.user.dash.userDash')
@section('site_title')
    VIP | {{config('app.name')}}
@endsection
@section('content')
    


{{-- <div class="fs-3 d-flex justify-content-between align-items-start bold w-100"> --}}
<div class="fs-3">
    <x-daily-task />

    {{-- <a href="{{route('user.package.index')}}" class="d-block btn btn-sm btn-outline-info">All Package</a> --}}
</div> 

@if ( isset($ownerPackage))
{{-- @foreach ($ownerPackage as $item)
@endforeach      --}}


    <x-package-request/>

   
@else

    <div> 
        <div class="alert alert-info bold">
            No Active Package Found
            <br>
            <a href="{{route('user.package.index')}}" class="d-block btn btn-sm btn-outline-info">View All Package</a>

        </d>
    </div>
@endif


@endsection