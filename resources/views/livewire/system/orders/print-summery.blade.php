<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    <x-dashboard.container>
        <div class="mb-2 text-center">
            <h1>
                <x-application-name />
            </h1>
            <p class=""> Order Summery form {{carbon\carbon::parse($sd)->format('d/m/Y')}}
                to {{carbon\carbon::parse($ed)->format('d/m/Y') }} </p>
        </div>
        <div>
            <table class="border w-full">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Buyer</th>
                        <th>Flow</th>
                        <th>Seller</th>
                        <th>
                            Status
                        </th>
                        <th>
                            Amount
                        </th>
                        <th>
                            Comission
                        </th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                    $totalCom = 0;
                    @endphp
                    @foreach ($orders as $item)
                    <tr class="mb-3">
                        <td> {{$loop->iteration }} </td>
                        <td> {{$item->id ?? "N/A"}} </td>
                        <td>
                            @if ($item->user)
                            <p class="text-xs">
                                {{$item->user?->name ?? 'N/A'}}
                            </p>

                            @endif
                            <p class="text-xs">
                                {{$item->user?->phone ?? "N/A"}}| {{$item->user?->email ?? "N/A"}}
                            </p>
                        </td>
                        <td>
                            <div class="flex items-center text-xs">
                                <div>

                                    <span class="text-xs"></span>{{ $item->user_type }}
                                </div>
                                <i class="fas fa-caret-right px-2"></i>
                                {{ $item->belongs_to_type }}
                            </div>
                        </td>
                        <td>
                            <p class="text-xs">

                                @if ($item->seller)

                                {{$item->seller?->name ?? 'N/A'}}
                                {{-- <x-nav-link-btn
                                    href="{{route('system.users.edit', ['id' => $item->seller?->id ?? ''])}}">
                                </x-nav-link-btn> --}}

                                @endif
                                {{$item->seller?->phone ?? "N/A"}} | {{$item->seller?->email ?? "N/A"}}
                            </p>
                        </td>
                        <td class="text-xs">
                            {{-- {{$item->status ?? "N/A"}} --}}

                            <x-dashboard.order-status :status="$item->status" />
                        </td>
                        <td>
                            {{$item->total ?? 0}} TK
                        </td>
                        <td>
                            {{$item->comissionsInfo()->sum('take_comission') ?? 0}} TK

                            @php
                            $totalCom += $item->comissionsInfo()->sum('take_comission');
                            @endphp
                        </td>
                        <td>
                            {{$item->created_at?->toFormattedDateString()}}
                        </td>

                    </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="6">
                            {{count($orders)}} Item
                        </td>
                        <td>
                            {{$orders->sum('total')}}
                        </td>
                        <td>
                            {{$totalCom}}
                        </td>
                        <td></td>

                    </tr>
                </tfoot>

            </table>
        </div>
    </x-dashboard.container>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        // Trigger the print dialog after the content is loaded
        // window.dispatchEvent(new Event('open-printable', {
        // detail: [{
        // url: window.location.href
        // }]
        // }));
        setTimeout(() => {
        window.print();
        }, 1000);
        });
    </script>
</div>