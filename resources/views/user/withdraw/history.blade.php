@extends('layouts.user.dash.userDash')
@section('content')
<div class="d-flex justify-content-between align-items-center">
    <h3 class="p-0 m-0">Withdraws</h3>
    <a href="{{route('user.withdraw.request')}}" class=" @if(auth()->user()->coin > 500) d-none @endif block bold shadow rounded btn btn-success border px-4 py-1"> <i class="fas fa-coins pr-3"></i> Request</a>

</div>

<table class="table">
    @php
        $totalWithdraw = 0
    @endphp
    <tr>
        <td>Total withdraw</td>
        <th> {{ $withdraw->where('status', 1)->whereNull('is_rejected')->sum('amount')}} TK </th>
    </tr>
</table>

<div>
    @foreach ($withdraw as $wtd)
    
        {{-- @php
            if ($wtd->is_rejected === NULL && $wtd->status == 1) {
                $totalWithdraw += $wtd->amount;
            }
        @endphp --}}

    <div class="accordion rounded " id="accordion_{{$wtd->id}}">
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="border p-2 w-100 bg-white accordion-button @if($wtd->status) alert alert-success @endif @if($wtd->status == 0) alert alert-info @endif @if($wtd->is_rejected) alert alert-danger @endif " type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{$wtd->id}}" aria-expanded="true" aria-controls="collapse_{{$wtd->id}}">
                <div style="font-size: 14px" class="w-100 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="pr-3">{{$loop->iteration }} </span>
                        {{ $wtd->created_at->diffForHumans() }}
                    </div>

                    <div>
                        <span> {{ $wtd->amount }} </span> TK
                    </div>
                    <div>
                        @if($wtd->is_rejected)
                            <span class="badge badge-danger"> 
                                Rejected
                            </span>
                            {{-- <br>
                            - <span style="font-size: 12px">{{$wtd->reject_for ?? "Have an wrong information!" }}</span> --}}
                        @else
                            @if($wtd->status == 0)
                                <span class="badge badge-info" >Pending</span>
                            @else
                                <span class="badge badge-success" >Accepted</span>
                            @endif
                        @endif
                    </div>
                </div>
            </button>
          </h2>

          <div id="collapse_{{$wtd->id}}" class="px-2 accordion-collapse collapse" data-bs-parent="#accordion_{{$wtd->id}}">
            <div class="accordion-body">
                <div class="py-3">
                    <div class="row m-0 border border-info rounded text-left">
                        <div class="col-md-12 py-2 px-3 border-bottom">
                            @if($wtd->is_rejected)
                                <span class="badge badge-danger">rejected</span>
                            @else
                                @if($wtd->status)
                                    <span class="badge badge-success">Accepted - {{$wtd->updated_at->diffForHumans()}}</span>
                                @else 
                                    <span class="badge badge-info" >Pending</span>
                                @endif
                            @endif

                            @if ($wtd->status && !$wtd->is_rejected && $wtd->comissions)    
                                <div>
                                    You received {{ $wtd->amount - $wtd->transactions->vat }} TK by <strong> {{$wtd->transactions->send_by}} </strong>, from <strong> {{$wtd->transactions->send_from}} </strong>
                                </div>
                            @endif
                            @if($wtd->is_rejected)
                                <div class="alert alert-danger">
                                    {{$wtd->reject_for}}
                                </div>
                            @endif
                        </div>

                        @if ($wtd->status && !$wtd->is_rejected)    
                            <div class="col-md-6 py-2 px-3">                            
                                @if($wtd->status) 
                                    <table class="table  w-100">
                                        <tr>
                                            <td>Withdraw</td>
                                            <th class="text-right">{{ $wtd->amount }} TK</th>
                                        </tr>
                                        <tr>
                                            <td>Vat</td>
                                            <th class="text-right"> - {{$wtd->transactions->vat}}  TK</th>
                                        </tr>
                                        <tr>
                                            <td> Received </td>
                                            <th class="text-right"> {{ $wtd->amount - $wtd->transactions->vat }} TK </th>
                                        </tr>
                                    </table>
                                @else 
                                    <p>{{$wtd->amount}} TK</p>
                                @endif
                            </div>
                            <div class="col-md-6 py-2 px-3 ">
                                {{-- <p> {{$wtd->pay_by}} </p>
                                <p class="m-0">
                                    A/C: {{$wtd->pay_to}}
                                </p> --}}
                                <table class="w-100 table">
                                    <tr>
                                        <td>Payment</td>
                                        <th>
                                            <div class=""> {{$wtd->transactions->send_by}} </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            Sender/From
                                        </td>
                                        <th>
                                            <div class=""> {{$wtd->transactions->send_from}} </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            Receiver/To
                                        </td>
                                        <th>
                                            <div class=""> {{$wtd->transactions->send_to}} </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            TRNX ID
                                        </td>
                                        <th>
                                            <div class=""> {{$wtd->transactions->trnx}} </div>
                                        </th>
                                    </tr>
                                </table>

                            </div>
                        @endif
                        @if($wtd->is_rejected)
                            <div class="col-md-3 px-3 py-2">
                                <h6>Amount</h6>
                                {{ $wtd->amount }} TK <span class="px-2"> > </span> {{$wtd->pay_by}} Request
                            </div>
                            <div class="col-md-3 px-3 py-2">
                                <h6>Payment</h6>
                                </span> {{$wtd->pay_by}}
                                <br>
                                A/C : {{$wtd->pay_to}}
                            </div>
                        @endif

                        @if(!$wtd->status && !$wtd->is_rejected)
                            <div class="p-3">
                                You requested <strong>{{ $wtd->amount }}</strong>  TK withdraw by  <strong>{{$wtd->pay_by}} - ({{$wtd->pay_to}})</strong> received.
                            </div>
                        @endif

                        <div class="col-12 p-3 border-top">
                          
                            <p> {{$wtd->created_at->toDayDateTimeString()}} <br>- {{ $wtd->created_at->diffForHumans() }} </p>
                            <form @class(['d-none' => $wtd->is_rejected || $wtd->status]) action="{{route('user.withdraw.destroy')}}" method="post">
                                <input type="hidden" name="wid" value="{{$wtd->id}}">
                                @csrf
                                <button type="submit" class="btn btn-danger">Cancel</button>
                            </form>
                            {{-- <a href="" class="btn btn-danger">Cancel</a> --}}
                        </div>
                        
                    </div>
                </div>
            </div>
          </div>

        </div>
    </div>

    @endforeach
</div>

@endsection