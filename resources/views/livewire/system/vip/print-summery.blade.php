<div>
    {{-- Stop trying to control. --}}
    <x-dashboard.container>
        <div class="text-center">
            <h1>
                <x-application-name />
            </h1>
            <p>
                User Summery @if ($sdate && $edate)
                From {{carbon\carbon::parse($sdate)->format('Y-d-m')}} To
                {{carbon\carbon::parse($edate)->format('Y-d-m')}}
                @endif
            </p>
        </div>
        <hr class="my-2" />

        <div>
            <x-dashboard.table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Package</th>
                        <th>Amount</th>
                        <th>Comission</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Validity</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                    $package_price = 0;
                    $com = 0;
                    @endphp
                    @foreach ($vip as $item)
                    <tr>
                        <td> {{$loop->iteration}} </td>
                        <td>
                            {{$item?->name ?? "N/A"}}
                            <br>
                            <div class="text-xs">
                                {{$item?->user?->email ?? "N/A"}}
                            </div>
                        </td>
                        <td>
                            {{$item?->package?->name ?? "N/A"}}
                            <div class="text-xs"> {{$item?->task_type ?? "N/A"}} </div>
                        </td>
                        <td>
                            {{$item?->package?->price ?? "0"}}
                            @php
                            $package_price += $item->package?->price ?? 0;
                            @endphp
                        </td>
                        <td>
                            {{$item?->comission ?? 0}}
                            @php
                            $com += $item->comission;
                            @endphp
                        </td>
                        <td>
                            @if ($item?->status)
                            Active
                            @else
                            @if($item?->stauts == -1 || $item?->deleted_at)
                            Trash
                            @else
                            Pending
                            @endif
                            @endif
                            <br>
                            @if ($item?->deleted_at)
                            <span class="text-xs text-red-900 text-bold ">
                                {{$item?->deleted_at->toFormattedDateString()}}
                            </span>
                            @endif
                        </td>
                        <td>
                            <div class="text-nowrap">
                                {{$item?->created_at?->toFormattedDateString()}}
                            </div>
                        </td>
                        <td>
                            {{carbon\carbon::parse($item?->valid_till)->toFormattedDateString()}}
                            <div class="text-xs">
                                {{carbon\carbon::parse($item?->valid_till)->diffForHumans()}}
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="3"> {{count($vip)}} Items </td>
                        <td class="font-bold">
                            {{$package_price}}
                        </td>
                        <td class="font-bold">
                            {{$com}}
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </x-dashboard.table>
        </div>
    </x-dashboard.container>
</div>