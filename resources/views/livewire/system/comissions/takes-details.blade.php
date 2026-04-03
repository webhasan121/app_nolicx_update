<div>
    <x-dashboard.page-header>
        Take Comissions
    </x-dashboard.page-header>

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.table>

                <thead>
                    <th>ID</th>
                    <th>Order</th>
                    <th>Product</th>
                    <th>Buy</th>
                    <th>Sell</th>
                    <th>Profit</th>
                    <th>Rate</th>
                    <th>Take</th>
                    <th>Give</th>
                    <th>Store</th>
                    <th>Return</th>
                    <th>Confirmed</th>
                    <th>
                        A/C
                    </th>
                </thead>

                <tbody>

                    @foreach ($data as $item)
                    <tr>
                        <td> {{$item->id ?? "N/A"}} </td>
                        <td> {{$item->order_id ?? 0}} </td>
                        <td> {{$item->product_id ?? 0}} </td>
                        <td> {{$item->buying_price ?? 0}} </td>
                        <td> {{$item->selling_price ?? 0}} </td>
                        <td> {{$item->profit ?? "0"}} </td>
                        <td> {{$item->comission_range ?? "0"}} % </td>
                        <td> {{$item->take_comission ?? "0"}}</td>
                        <td> {{$item->distribute_comission ?? "0"}}</td>
                        <td> {{$item->store ?? "0"}}</td>
                        <td> {{$item->return ?? "0"}}</td>
                        <td>
                            @if ($item->confirmed == true)
                            <span class="p-1 px-2 rounded-xl bg-green-900 text-white">Confirmed</span>
                            <x-nav-link href="{{route('system.comissions.take.refund', ['id' => $item->id])}}"> Refund
                            </x-nav-link>
                            @else
                            <span class="p-1 px-2 rounded-xl bg-gray-900 text-white">Pending</span>
                            <form action="{{route('system.comissions.take.confirm', ['id' => $item->id])}}"
                                method="post">
                                @csrf
                                <button type="submit">Confirm</button>
                            </form>
                            {{-- <x-nav-link href="{{route('system.comissions.take.confirm', ['id' => $item->id])}}">
                                Confirm
                            </x-nav-link> --}}
                            @endif
                        </td>
                        <td>
                            <div class="flex space-x-2">
                                <x-nav-link href="{{route('system.comissions.distributes', ['id' => $item->id])}}">
                                    Details</x-nav-link>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                </tbody>

            </x-dashboard.table>
        </x-dashboard.section>
    </x-dashboard.container>
</div>