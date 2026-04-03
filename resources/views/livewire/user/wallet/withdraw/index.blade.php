<div class="p-2">
    <x-dashboard.container>

        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex items-start justify-between">
                        <div>
                            Your Wallet
                        </div>
                    </div>
                </x-slot>>
                <x-slot name="content">
                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold text-indigo-900"> Available Balance
                            {{auth()->user()->abailCoin()}}
                            TK </div>
                    </div>
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>

                <ul>
                    <li>
                        To make a withdrawal, your balance must be at least 500 TK. If you're a new user, you'll need to
                        reach a minimum balance of 500 TK before you can withdraw.
                    </li>
                    <li>
                        To make a withdrawal, VIP and VIP Package users must first complete a product purchase.
                    </li>
                </ul>

                <x-hr />
                <div class="mt-2 space-x-2 text-end">
                    {{-- <x-nav-link -on:click="$dispatch('open-modal', 'request-payment-modal')">Request A Payment
                    </x-nav-link> --}}
                    <x-nav-link-btn href="{{route('user.wallet.withdraw.create')}}">Request A Payment</x-nav-link-btn>
                </div>
            </x-dashboard.section.inner>
        </x-dashboard.section>

        <x-dashboard.section>
            <div class="flex items-center justify-between">
                <div>
                    Last Activity
                </div>
                <x-nav-link href=""> History </x-nav-link>

            </div>
            <div class="mt-2">
                <div class="m-0 row">
                    @foreach ($withdraw as $wtd)
                    @if($wtd->status == 0 && $wtd->is_rejected == NULL)
                    <div class="w-48 py-3">
                        <div class="text-left border rounded">
                            <div class="px-3 py-2 border-bottom">
                                <h6>Status</h6>
                                <p class="font-bold text-red-900">Pending</p>
                            </div>
                            <div class="px-3 py-2 border-b">
                                <h6>Amount</h6>
                                <p class="font-bold">{{$wtd->amount}} TK</p>
                            </div>
                            <div class="px-3 py-2 border-b">
                                <p> {{$wtd->pay_by}} </p>
                                <p class="font-bold">
                                    A/C: {{$wtd->pay_to}}
                                </p>
                            </div>
                            <div class="p-3">
                                <h6>Date</h6>
                                <p> {{$wtd->created_at?->toFormattedDateString()}} <br>- {{
                                    $wtd->created_at?->diffForHumans() }} </p>
                                <x-hr />
                                <form @class(['d-block', 'd-none'=> $wtd->is_rejected]) action="" method="post">
                                    <input type="hidden" name="wid" value="{{$wtd->id}}">
                                    @csrf
                                    <button type="submit" class="px-2 bg-red-900 border rounded ">Cancel</button>
                                </form>
                                {{-- <a href="" class="btn btn-danger">Cancel</a> --}}
                            </div>

                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </x-dashboard.section>
    </x-dashboard.container>

</div>
