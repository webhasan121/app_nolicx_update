<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <x-dashboard.container>
        <div class="mb-2 text-center">
            <h1>
                <x-application-name />
            </h1>
            <p>Deposit summery form {{carbon\carbon::parse($sdate)->format('d/m/Y')}}
                to {{carbon\carbon::parse($edate)->format('d/m/Y') }} </p>
        </div>
        <div id="pdf-content">
            <hr clas="my-1" />
            <x-dashboard.table :data="$history">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Trx ID</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>A/C</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($history as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{-- User Name --}}
                            <x-nav-link-btn href="{{route('system.users.edit', ['id' => $item->user?->id ?? ''])}}">
                                {{$item->user?->name ?? 'N/A'}}
                            </x-nav-link-btn>

                        </td>
                        <td>{{ $item->amount ?? 0 }}</td>
                        <td>
                            <div class="flex items-center">

                                {{ $item->senderAccountNumber }} <i class="fas fa-caret-right px-2"></i>
                                {{$item->paymentMethod}} <i class="fas fa-caret-right px-2"></i>
                                {{$item->receiverAccountNumber}}
                            </div>
                        </td>
                        <td>
                            {{ $item->transactionId ?? 'N/A' }}
                        </td>
                        <td>{{ $item->confirmed ? 'Confirmed' : 'Pending' }}</td>
                        <td>{{ $item->created_at->diffForHumans() }} </td>
                        <td>
                            <div class="flex">
                                <x-primary-button wire:click="confirmDeposit({{$item->id}})">
                                    <i class="fas fa-check"></i>
                                </x-primary-button>
                                <x-danger-button wire:click.prevent="denayDeposit({{$item->id}})">
                                    <i class="fas fa-times"></i>
                                </x-danger-button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-right font-bold">Total</td>
                        <td class="font-bold">{{$history?->sum('amount')}}</td>
                        <td colspan="5"></td>
                    </tr>
                </tfoot>
            </x-dashboard.table>

        </div>
    </x-dashboard.container>
</div>