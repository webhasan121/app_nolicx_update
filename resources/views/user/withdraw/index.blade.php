@extends('layouts.user.dash.userDash')
@section('content')
    <div>
        <h4>Withdraws</h4>
    </div>
    <hr>
    <div class="text-center">
        Your Earnings
        <x-user.display-user-coin/>
        {{-- <div>
            <div @class(['d-none', 'd-block alery alert-danger' => auth()->user()->coin < 500])> Min Withdraw balance 500 TK </div>
            <div class="d-flex justify-content-center align-items-center">
                <a href="{{route('user.withdraw.index')}}"  @class(['py-2 px-2 rounded', 'd-block' => auth()->user()->coin > 500])>Withdraw <i class="ml-3 fas fa-arrow-right"></i> </a>
            </div>
        </div> --}}

        <div class="p-3 text-left border rounded">
            <div class="text-left ">
               <ul class="">
                    <li class="py-2 m-0 border-bottom">
                        You can't withdraw, unless your balance is 500 TK or more.
                    </li>

                    <li class="py-2 m-0 border-bottom">
                        If you are a new user, you can't withdraw amount ulness you have 500 TK or more.
                    </li>
                    <li class="py-2 m-0 border-bottom">
                        VIP User<span class="badge badge-info">VIP Package User</span>  can't withdraw amount, untill purchase a product.
                    </li>
               </ul>
            </div>

            <div class="">
                <a href="{{route('user.withdraw.request')}}" class="rounded bold btn btn_secondary"> <i class="pr-3 fas fa-coins"></i> Request A Withdraw</a>
            </div>
            <a href="{{route('user.withdraw.history')}}" class="mt-2 rounded btn btn_outline_primary" > <i class="pr-3 fas fa-sync"></i> History</a>
        </div>


        <div class="m-0 row">
            @foreach ($withdraws as $wtd)
                @if($wtd->status == 0 && $wtd->is_rejected == NULL)
                    <div class="py-3 col-sm-6 col-lg-4">
                        <div class="text-left border rounded border-info">
                            <div class="px-3 py-2 border-bottom">
                                <h6>Status</h6>
                                <p class="text-info">Pending</p>
                            </div>
                            <div class="px-3 py-2 border-bottom">
                                <h6>Amount</h6>
                                <p>{{$wtd->amount}} TK</p>
                            </div>
                            <div class="px-3 py-2 border-bottom">
                                <h6>Method</h6>
                                <p> {{$wtd->pay_by}} </p>
                                <p>
                                    A/C: {{$wtd->pay_to}}
                                </p>
                            </div>
                            <div class="p-3">
                                <h6>Date</h6>
                                <p> {{$wtd->created_at}} <br>- {{ $wtd->created_at->diffForHumans() }} </p>
                                <form @class(['d-block', 'd-none' => $wtd->is_rejected]) action="{{route('user.withdraw.destroy')}}" method="post">
                                    <input type="hidden" name="wid" value="{{$wtd->id}}">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Cancel</button>

                                </form>
                                {{-- <a href="" class="btn btn-danger">Cancel</a> --}}
                            </div>

                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endsection
