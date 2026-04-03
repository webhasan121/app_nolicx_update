<div id="pdf-content">
    <style>
        /* Print-friendly adjustments */
        @page {
            size: {
                    {
                    $paper ?? 'A4'
                }
            }

                {
                    {
                    $orientation ?? 'portrait'
                }
            }

            ;

            margin: {
                    {
                    $margins ?? '10mm'
                }
            }

            ;
        }

        body {
            -webkit-print-color-adjust: exact;
            font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
        }


        /* Full width tables */
        .pdf-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pdf-table th,
        .pdf-table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            font-size: 12px;
        }


        @media print {

            html,
            body {
                width: 100%;
            }
        }
    </style>
    {{-- <style>
        @page {
            size: A4;
        }
    </style> --}}
    <x-dashboard.container>
        <div class="w-ful text-center">

            <div class="tex-xl">
                <x-application-name />
            </div>
            <div>
                <p class=""> Withdraw Summery form {{carbon\carbon::parse($sdate)->format('d/m/Y')}}
                    to {{carbon\carbon::parse($edate)->format('d/m/Y') }} </p>
            </div>
        </div>
        <br>
        <x-dashboard.table :data="$withdraws">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Details</th>
                    <th>Amount</th>
                    <th>Com</th>
                    <th>Payable</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($withdraws as $item)
                <tr @class(["bg-gray-200 font-bold"=> !$item->seen_by_admin])>
                    <td> {{$loop->iteration}} </td>
                    <td>
                        {{$item->created_at?->toFormattedDateString() }}
                    </td>
                    <td>
                        <div>
                            <div class="flex">
                                {{$item->user?->name}}
                                @if ($item->user?->subscription)
                                <span class="bg-indigo-900 text-white ms-1 px-1 rounded">
                                    vip
                                </span>
                                @endif
                                <span class="bg-gray-900 text-white ms-1 px-1 rounded-full">
                                    U
                                </span>
                            </div>

                            {{$item->user?->email}}
                        </div>
                    </td>
                    <td>
                        {{$item->amount ?? '0'}} TK
                    </td>

                    <td>
                        {{$item->total_fee ?? '0'}} TK
                    </td>
                    <td>
                        {{$item->payable_amount ?? '0'}} TK
                    </td>

                    <td>
                        @if (!$item->is_rejected)
                        {{$item->status ? "Accept" : 'Pending'}}
                        @else
                        <div class="p-1">Reject</div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-bold">
                    <td colspan="3" class="text-right font-bold">Total</td>
                    <td class="font-bold">{{$withdraws?->sum('amount')}}</td>
                    <td class="font-bold">{{$withdraws?->sum('total_fee')}}</td>
                    <td class="font-bold">{{$withdraws?->sum('payable_amount')}}</td>
                    <td colspan=""></td>
                </tr>
            </tfoot>
        </x-dashboard.table>
    </x-dashboard.container>

    <script>
        // If opened via browser printable flow, auto open print dialog
            setTimeout(() => window.print(), 500);
            // if (window.location.search.includes('autoPrint=1')) {
            // }
            
            // close the window, while close window.print()
            
            
    </script>
</div>