<div x-init="$wire.getDeta()" x-loading.disabled>
    {{-- Success is as dangerous as failure. --}}
    <x-dashboard.page-header>
        Withdraws
        <br>
        <div class="text-2xl">
            {{$withdraw?->user?->currency_sing}}{{$withdraw?->amount}}
            <div class="text-sm text-gray-400">
                @if (!$withdraw?->is_rejected)
                {{$withdraw?->status ? "Accept" : 'Pending'}}
                @else
                <div class="p-1">Reject</div>
                @endif
            </div>
        </div>

    </x-dashboard.page-header>

    <x-dashboard.container>
        <div
            style="display: grid; grid-template-columns:repeat(auto-fit, 170px); grid-gap:10px; justify-content:start; align-items:start">
            <div class="p-3 w-full bg-white rounded-lg shadow-sm">
                Withdrawal
                <x-hr />
                <div class="text-5xl font-bold my-3">
                    {{$withdraw?->amount}} <span class="text-sm"> {{$withdraw?->user?->currency_sing}} </span>
                </div>
                User Balance
                <x-hr />
                <div class=" text-xl font-bold">
                    {{$withdraw?->user?->abailCoin()}}
                </div>
            </div>

            <div class="p-3 w-full bg-white rounded-lg shadow-sm">
                Payable
                <x-hr />
                <div class="text-5xl font-bold my-3">
                    {{$withdraw?->payable_amount ?? 0}} <span class="text-sm"> {{$withdraw?->user?->currency_sing}}
                    </span>
                </div>
                Range
                <x-hr />
                <div class=" text-xl font-bold">
                    {{$withdraw?->fee_range?? "0"}} %
                </div>
            </div>

            <div class="p-3 w-full bg-white rounded-lg shadow-sm">
                VAT
                <x-hr />
                <div class="text-5xl font-bold my-3">
                    {{$withdraw?->total_fee ?? 0}} <span class="text-sm"> {{$withdraw?->user?->currency_sing}} </span>
                </div>
                deduction
                <x-hr />
                <div class=" text-xl font-bold">
                    {{$withdraw?->server_fee?? 0}} + {{$withdraw?->maintenance_fee ?? 0}}
                </div>
            </div>
        </div>

        {{-- <div class="md:flex border rounded-lg items-start shadow">
        </div> --}}

        <div @class(['p-3 bg-red-600 text-white rounded-lg ',' hidden ' => !$withdraw?->is_rejected])>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <span class="text-white" >Request Rejected !</span>
                </x-slot>
                <x-slot name="content">
                    <span class="text-white">
                        {{$withdraw?->reject_for ?? ' Unknown reason'}} </span>
            </x-slot>
            </x-dashboard.section.header>
        </div>

        <x-dashboard.section @class(['bg-indigo-900 text-white','hidden'=> !$withdraw?->status])>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="text-white">
                        Request Confirmed !
                    </div>
                </x-slot>
                <x-slot name="content">
                    Confirm by {{$withdraw?->confirm_by ?? 'N/A'}} at
                    {{\Carbon\Carbon::parse($withdraw?->updated_at)->format('m:s')}}
                </x-slot>
            </x-dashboard.section.header>
        </x-dashboard.section>

        @if (!$withdraw?->status && !$withdraw?->is_rejected)
        <x-dashboard.section x-loading.disabled @class(["mt-2"])>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Payment Confirmation
                </x-slot>
                <x-slot name='content'>
                    Confirm payment and make a track to your payment history.
                </x-slot>
                <x-hr />
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <div class="">

                    <div style="" class="inline-block mt-2">
                        <div class="border bg-indigo-900 text-white overflow-hidden rounded-lg w-full">
                            <div class="p-3">
                                User Request
                            </div>
                            <div class=" p-3">
                                <strong>
                                    {{$withdraw?->pay_by}}
                                </strong>
                                <br>
                                {{$withdraw?->pay_to}}
                                <x-hr />
                            </div>
                            <div class=" p-3">
                                <strong>
                                    {{$withdraw?->bank_account}}
                                </strong>
                                {{$withdraw?->account_holder_name}}
                                {{$withdraw?->account_humber}}
                            </div>
                        </div>
                    </div>

                    <div class="p-2 rounded-lg mt-2 border">
                        <form wire:submit.prevent="confirmPayment">


                            {{-- <div class="md:flex justify-between items-center py-1 my-1">
                                <div>
                                    Payment Method
                                </div>
                                <x-text-input class="" placeholder="Who make this payment" />
                            </div>
                            <x-hr /> --}}
                            <div class="md:flex justify-between items-center py-1 my-1">
                                <div>
                                    Payment From
                                </div>
                                <x-text-input wire:model.lazy="paid_from" class=""
                                    placeholder="From where the payment has been done" />
                            </div>
                            <div class="text-xs">
                                Bank Account or Mobile Banking Number that user receive the payment.
                            </div>
                            @error('paid_from')
                            <div class="text-red-900">
                                {{$message}}
                            </div>
                            @enderror
                            <x-hr />
                            <div class="md:flex justify-between items-center py-1 my-1">
                                <div>
                                    TRX ID
                                </div>
                                <x-text-input wire:model.lazy="trx" class="" placeholder="TRX ID" />
                            </div>
                            @error('trx')
                            <div class="text-red-900">
                                {{$message}}
                            </div>
                            @enderror
                            <x-hr />

                            <x-primary-button>Confirm Payment</x-primary-button>

                        </form>
                    </div>

                </div>
            </x-dashboard.section.inner>
        </x-dashboard.section>
        @endif

        <x-dashboard.section @class(['hidden'=> $withdraw?->status || $withdraw?->is_rejected ])>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Payment Reejction
                </x-slot>
                <x-slot name="content">
                    Have an unprocessable payment? Wish to reject the payment requeest. Payment rejection process done
                    by following procedures.
                </x-slot>
            </x-dashboard.section.header>
            <x-hr />
            <form wire:submit.prevent="rejectPayment">

                <x-input-label value="Rejection Message" />
                <textarea wire:model="rMessage" id="" class="w-full rounded-lg"
                    placeholder="Write Your Rejection Message .......... " rows="4"></textarea>
                @error('rMessage')
                <div class="text-red-900">
                    {{$message}}
                </div>
                @enderror
                <x-danger-button type="submit">Confirm Reject</x-danger-button>

            </form>
        </x-dashboard.section>
    </x-dashboard.container>
</div>