@extends('layouts.user.dash.userDash')

@section('site_title')
    wallet | {{config('app.name')}}
@endsection

@section('content')
<h3>
    Your Wallet
</h3>
<hr>

<div class="container p-3 text-center" >
    Wallet have
    <x-user.display-user-coin/>


    @php
        $url = url()->current();
        $nav = $_GET['nav'] ?? 'comission';
    @endphp

    <div>
        {{-- <div @class(['d-none', 'd-block alery alert-danger' => auth()->user()->coin < 500])> Min Withdraw balance 500 TK </div> --}}
        <div class="d-flex justify-content-center align-items-center">
            <a href="{{route('user.withdraw.index')}}"  @class(['py-2 px-4 btn btn_secondary '])>Withdraw <i class="fas fa-arrow-right ml-3"></i> </a>
            {{-- <a href="{{route('user.withdraw.request')}}" class="rounded border px-4 py-1">Request A Withdraw</a> --}}
        </div>
    </div>
</div>

<div class="p-3">

    {{-- <div class="d-fle"></div> --}}
    <div class="d-flex align-items-center my-2">
        <a @class(["px-2 py-1 mx-1 nav-link rounded", 'border border-success text-success'=> $nav == 'comission']) href="{{$url}}?nav=comission">
            Comission
        </a>
        <a @class(["px-2 py-1 mx-1 nav-link rounded ",'border border-success text-success'=> $nav == 'task']) href="{{$url}}?nav=task">
            Tasks
        </a>
        <a @class(["px-2 py-1 mx-1 nav-link rounded", 'border border-success text-success'=> $nav == 'referred']) href="{{$url}}?nav=referred">
            Referred
        </a>
    </div>

    @if ($nav == 'comission')

        {{-- <div class="d-flex p-2 mb-1 alert alert-success" >
            <div class="me-4">
                {{ $st->created_at->diffForHumans() }}
                <br>
                {{ $st->created_at }}
                
            </div>
            
            <div >
                You received {{$st->comission}} TK comission <br> - {{$st->info}}
            </div>
            
            
        </div>   --}}
        <div class="alert alert-info">
            <table class="table w-100">
                <tr>
                    <td> 
                        <strong>Today,</strong> ({{ $statement->count() }}) Comission
                        <br>
                    </td>
                    <td></td>
                    <td></td>
                    <th>{{ $statement->sum('comission') }} TK</th>
                </tr>
            </table>
                
                <a href="{{route('user.coin.purchase')}}" class="btn btn-sm btn-outline-info">History <i class="fas fa-arrow-right ml-2"></i> </a>
                <p>
                    Users can earn commissions by <strong>purchasing products</strong>. The commission will be credited to their wallet. Additionally, users can earn commissions when <strong>referred users</strong> make a purchase.                    
                </p>

        </div>

        @if (count($statement) > 0)
            <table class="table w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Comission</th>
                        <th>Info</th>

                    </tr>
                </thead>

                <tbody>
                    @foreach ($statement as $st)
                        
                        <tr>
                            <td> {{$loop->iteration}} </td>
                            <td>
                                {{ $st->created_at->diffForHumans() }} <br>
                                <span style="font-size: 12px">
                                    {{ $st->created_at }} 
                                </span>

                            </td>
                            <td> {{ $st->comission }} TK </td>
                            <td> {{ $st->info }} </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-info">No Comission Accept <strong>Today</strong> !  </div>  
        @endif
    @endif

    @if ($nav == 'task')
        <div class="alert alert-info">
            <table class="table w-100">
                <tr>
                    <td> 
                        <strong>{{Carbon\Carbon::now()->format('F')}} </strong>, Daily task ({{$statement->count()}})
                        <br>
                        
                    </td>
                    <td>- </td>
                    <td>Earn {{  $statement->sum('coin') }} TK</td>
                </tr>
            </table>
            
            <a href="{{route('user.coin.task')}}" class="btn btn-sm btn-outline-info">History <i class="fas fa-arrow-right ml-2"></i> </a>
            <p>
                <strong>Today's Task</strong>, User may earn by completing task. To earn, user must complete task within the given time. By Purchasing<strong> VIP Package</strong> user able to complete task. <a href="{{route('user.package.index')}}">view vip package</a>
            </p>
        </div>
        
        @if (count($statement) > 0)
            <x-daily-task />
            <div class="overflow-x-scroll">
                <table class="table">
                    <thead>
                        <th>#</th>
                        <th>Date</th>
                        <th>Earn</th>
                        <th>Earn By</th>
                    </thead>
                    <tbody>
                        @foreach ($statement as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $item->created_at->diffForHumans()}}
                                </td>
                                <td>
                                    {{ $item->coin }} TK <br>
                                </td>
                                <td>Complete Daily Task !</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
               No Task Found!
            </div>
        @endif
    @endif

    @if ($nav == 'referred')

        <div class="alert alert-info">
            
            <table class="table w-100">
                <tr>
                    <td>                 
                        ({{ $statement->count() }}) Referred comission
                        <br>
                    </td>
                    <td></td>
                    <td></td>
                    <th>{{ $statement->sum('amount') }} TK</th>
                </tr>
            </table>
            <div class="d-flex">
                
                <a href="{{route('user.coin.referred')}}" class="btn btn-sm btn-outline-info mx-1"> History <i class="fas fa-arrow-right ml-1"></i></a>
                <a class="btn btn-sm btn-info" href="{{route('user.ref.user')}}">View Your Referred User</a>
            </div>
            <p>
                You may earn <strong>Referred Comission</strong> if any user join with your <strong>Referrer code</strong> and purchase a <strong>VIP</strong> package.
            </p>
        </div>
        <div class="overflow-x-scrol">
            @if(count($statement) > 0)
                <table class="table">
                    <thead>
                        <th>#</th>
                        <th>Date</th>
                        <th>User</th>
                        <th>Earn</th>
                        <th>Earn By</th>
                    </thead>
                    <tbody>
                        @foreach ($statement as $item)
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
    @endif

    {{-- <x-display-user-comission-statistics /> --}}

</div>

@endsection