<div>
    {{-- The best athlete wants his opponent at his best. --}}
    <x-dashboard.container>
         <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Deposit To Wallet
                </x-slot>>
                <x-slot name="content">
                    <div class="md:flex justify-between items-center">
                        <div class="text-2xl font-bold text-indigo-900"> {{auth()->user()->coin}} TK </div> 

                        <div class="flex">
                            {{-- <x-nav-link-btn class="ring-1 bg-indigo-900 text-white px-2 rounded-lg border-0 uppercase font-bold" href="{{route('user.wallet.diposit')}}">Deposit</x-nav-link-btn> --}}
                            {{-- <x-primary-button href="" >Deposit</x-primary-button> --}}
                            {{-- <x-primary-button @click.prevent="$dispatch('open-modal', 'depositModal')" >Deposit</x-primary-button> --}}
                            <x-nav-link-btn class="ring-1 px-2 border-0 rounded-lg uppercase font-bold" href="{{route('user.wallet.withdraw')}}">Withdraw</x-nav-link-btn>
                        </div>
                    </div>
                </x-slot>
            </x-dashboard.section.header>
        </x-dashboard.section>


        <x-dashboard.section>
            <p class="text-sm">
                Deposit amount to your wallet. To make confirm your deposit, you are requested to send your expected amout to our Mobile Bank Account (Bkash, Nogod, Roket). 
                @if ($payNumber)
                    @foreach ($payNumber as $type => $pay)
                        <div class="inline-flex p-2 rounded border mb-1 ">
                            <span class="font-bold pr-2">{{$type}}:</span> {{$pay}}
                        </div>
                    @endforeach
                @endif
            </p>
            <x-hr/>
            <x-primary-button @click.prevent="$dispatch('open-modal', 'depositModal')" > <i class="fas fa-plus px-2"></i> Deposit</x-primary-button>
        </x-dashboard.section>


        <x-dashboard.section>
            <div>History</div>

            <x-dashboard.section.inner>
                <x-dashboard.table :data="$history">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Trx ID</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->amount ?? 0 }}</td>
                                <td>
                                    <div class="flex items-center">

                                        {{ $item->senderAccountNumber }} <i class="fas fa-caret-right px-2"></i> {{$item->paymentMethod}} <i class="fas fa-caret-right px-2"></i> {{$item->receiverAccountNumber}}
                                    </div>
                                </td>
                                <td>
                                    {{ $item->transactionId ?? 'N/A' }}
                                </td>
                                <td>{{ $item->confirmed ? 'Confirmed' : 'Pending' }}</td>
                                <td>{{ $item->created_at->diffForHumans() }} </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>

           

    </x-dashboard.container>
    <x-modal name="depositModal" maxWidth="md" >
        <div class="p-4">
            <div class="text-lg">
                Deposit 
            </div>
            
            <x-hr/>

            <form wire:submit.prevent="confirmDeposit">
                
                <div class="mb-4">
                    <x-input-label value="Amount" />
                    <x-text-input type="number" wire:model="amount" class="w-full" placeholder="Enter amount" />
                    @error('amount')
                        <div class="text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <x-input-label value="Payment Method" />
                    <select class="rounded w-full" wire:model="paymentMethod">

                        <option value="">Select Payment Method</option>
                        <option value="bkash">Bkash</option>
                        <option value="nagad">Nagad</option>
                        <option value="rocket">Rocket</option>
                        <option value="Bank">Bank</option>
                    </select>
                    @error('paymentMethod')
                        <div class="text-red-500">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <x-input-label value="Receiver Account Number" />
                    <x-text-input type="text" wire:model="receiverAccountNumber" class="w-full" placeholder="Enter account number" />
                    @error('receiverAccountNumber')
                        <div class="text-red-500">{{ $message }}</div>
                    @enderror
                    <div class="text-xs">
                        If you send throught the bank, your are requested to write Bank Name first.  Then Back Account Number.
                    </div>
                </div>

                <x-hr/>
                <div class="text-xs">
                    Sender Info
                </div>
                <div class="mb-4">
                    <x-input-label value="Sender Account Number" />
                    <x-text-input type="text" wire:model="senderAccountNumber" class="w-full" placeholder="Enter account number" />
                    @error('senderAccountNumber')
                        <div class="text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <x-input-label value="Sender Name" />
                    <x-text-input type="text" wire:model="senderName" class="w-full" placeholder="Enter sender name" />
                    @error('senderName')
                        <div class="text-red-500">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <x-input-label value="Transaction ID" />
                    <x-text-input type="text" wire:model="transactionId" class="w-full" placeholder="Enter transaction ID" />
                    @error('transactionId')
                        <div class="text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                <x-hr/>
                
                <x-primary-button>Submit</x-primary-button>

            </form>
        </div>
    </x-modal>
</div>
