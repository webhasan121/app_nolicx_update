<div >

    <x-dashboard.page-header>
        Request For A Withdraw
    </x-dashboard.page-header>

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Withdraw Request
                </x-slot>

                <x-slot name="content">
                    @if ( auth()->user()->abailCoin() > 1)
                        Able to Withdraw
                        : {{ auth()->user()->abailCoin()}}
                    @else
                        <span class="">You need to meet minimum balance to make a successful withdraw. Withdrawable balance : <strong class="text-red-900"> {{ auth()->user()->abailCoin()}}  </strong> TK </span>
                    @endif

                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>
                <form method="post" action="{{route('user.wallet.withdraw.store')}}" >
                    @csrf
                    <div class="mb-3">
                        <div class="mb-2 col-md-8">
                            <select  name="pay_by" id="bank_name" class=" ring-1 rounded border-0 shadow-0 form-control form-select @error('pay_by') is-invalid @enderror">
                                <option value="">Payment Method</option>
                                <option @selected(old('pay_by') == 'bkash') value="bkash">Bkash</option>
                                <option @selected(old('pay_by') == 'nogod') value="nogod">Nogod</option>
                                <option @selected(old('pay_by') == 'roket') value="roket">Roket</option>
                            </select>
                        </div>
                        @error('pay_by')
                            <div class="text-xs text-red-900 d-block">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="space-y-3">
                            <x-text-input type="number" value="{{old('amount')}}" name="amount" placeholder="Amount" />

                            <x-text-input type="number" value="{{old('pay_to')}}" name="pay_to" id="account" class="form-control @error('pay_to') is-invalid @enderror" placeholder="Enter Payment Number" />
                        </div>
                        @error('amount')
                            <div class="text-xs text-red-900 d-block">
                                {{ $message }}
                            </div>
                        @enderror
                        @error('pay_to')
                            <div class="text-xs text-red-900 d-block">
                                {{ $message }}
                            </div>
                        @enderror
                        <hr class="my-2"/>
                        <div>
                            <x-input-label value="Contact Number" />
                            <x-text-input type="number" value="{{old('phone')}}" name="phone" placeholder="Your Contact Number" />
                        </div>
                    </div>
                    @error('phone')
                        <div class="text-xs text-red-900 d-block">
                            {{ $message }}
                        </div>
                    @enderror


                    <x-hr/>
                    <div class="flex items-center justify-end space-x-2 text-end">
                        <x-nav-link href="{{route('user.wallet.withdraw')}}" > <i class="mr-2 fas fa-arrow-left"></i> Back</x-nav-link>
                        <x-primary-button >Submit</x-primary-button>
                    </div>
                </form>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>

</div>
