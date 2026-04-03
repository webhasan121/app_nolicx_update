@extends('layouts.user.dash.userDash')
@section('content')
<div class="container">
    <h4>Referred Comission</h4>
    <a class="btn btn-sm btn-info" href="{{route('user.ref.user')}}">View Your Referred User</a>


    <table class="table mt-3">
        <tr>
            <td colspan="3">Todal</td>
            <th>{{ $comissions->sum('amount') }}</th>
            <td></td>
        </tr>
    </table>

    <div class="overflow-x-scroll">
        @if(count($comissions) > 0)
            <table class="table">
                <thead>
                    <th>#</th>
                    <th>Date</th>
                    <th>User</th>
                    <th>Earn</th>
                    <th>Earn By</th>
                </thead>
                <tbody>
                    @foreach ($comissions as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ $item->created_at->diffForHumans()}}
                        </td>
                        <td>
                            {{$item->user?->name ?? "Not Found !"}}
                        </td>
                        <td>
                            {{ $item->amount }} TK <br>
                        </td>
                        <td>Referred user buy a vip package !</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else 
            <div class="alert alert-info">
                No Data Found !
            </div>
        @endif
    </div>
</div>
@endsection