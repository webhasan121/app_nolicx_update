@extends('layouts.user.dash.userDash')
@section('content')
    <div class="d-flex justify-content-between align-items-center border-bottom">
        <h5 class="m-0 p-0">Withdraw Request</h5>
        <p class="p-0 m-0">
            {{-- <a href="{{ route('user.dash') }}">Dashboard</a> --}}
            <a href="" class="mt-2 btn rounded border" > <i class="fas fa-sync mr-2"></i> History </a>
        </p>
    </div>
    {{-- <hr> --}}
    <a href="{{route('user.coin.store')}}" class=" fs-1 text-bold" >
        <x-user.display-user-coin />
    </a>
    <h5>Request for a withdraw</h5>
    <p>
        To make a successfull withdraw request, fill the require fill carefully. Give us your payment details and we will process your withdraw request. it may takes 72 working hours to successfull your request. Stay tuned for updates.
    </p>
    <a href="" class="mt-2 btn rounded text-light" > <i class="fas fa-sync pr-3"></i> History</a>
    <hr>

    <div>
        <form action="{{ route('user.withdraw.store') }}" method="POST">
            @csrf
            <div class="row m-0">

                <label for="amount" class="col-md-4">Amount</label>
                <input type="number" name="amount" min="200" max="{{auth()->user()->coin - 50}}" value="{{old('amount')}}" class="col-md-8 form-control @error('amount') is-invalid @enderror" id="amount" placeholder="Enter Your Amount">
            </div>
            @error('amount')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror


            <div class="row m-0">

                <label for="phone" class="col-md-4">Phone</label>
                <input type="text" name="phone" class="col-md-8 form-control @error('phone') is-invalid @enderror" id="phone" placeholder="Your Own Number">
            </div>
            @error('phone')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

            <div class="border p-3 rounded">
                <h5>Payment Details</h5>
                <div class="row m-0">
                    <label for="bank_name" class="col-md-4">Pyment Method</label>
                    <select name="pay_by" id="bank_name" class="col-md-8 form-control form-select @error('pay_by') is-invalid @enderror ">
                        <option selected value="bkash">Bkash</option>
                        <option value="nogod">Nogod</option>
                        <option value="roket">Roket</option>
                        {{-- <option value="bank">Bank Transfer</option> --}}
                    </select>

                </div>
                @error('pay_by')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
                <hr>
                <div class="row m-0">
                    <label for="account" class="col-md-4">Number</label>
                    <input type="text" name="pay_to" class="col-md-8 form-control @error('pay_to') is-invalid @enderror" id="account" placeholder="Enter Your Account Number">
                </div>
                @error('pay_to')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

                <div id="back" class="d-none">
                    <div class="form-group">
                        <label for="account_number">Account Number</label>
                        <input type="number" name="account_number" class="form-control" id="account _number" placeholder="Enter account number">
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="account_name">Account Name</label>
                        <input type="text" name="account_name" class="form-control" id="account _name" placeholder="Enter account name">
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="swift_code">Swift Code</label>
                        <input type="text" name="swift_code" class="form-control" id="swift _code" placeholder="Enter swift code">
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="iban">IBAN</label>
                        <input type="text" name="iban" class="form-control" id="iban" placeholder="Enter IBAN">
                    </div>
                </div>

            </div>
            <hr>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

@endsection