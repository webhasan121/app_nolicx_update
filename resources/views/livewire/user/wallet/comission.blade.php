<div>
    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Comissions
                </x-slot>
                <x-slot name="content">
                    Your Products comissions list.
                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>
                <x-nav-link href="?nav=earn" :active="$nav=='earn'">Earn Comissions</x-nav-link>
                <x-nav-link href="?nav=system" :active="$nav=='system'">System Comissions</x-nav-link>
            </x-dashboard.section.inner>
        </x-dashboard.section>

        <x-nav-link href="?nav=earn&set=com" :active="$set=='com'" > Comissions </x-nav-link>
        <x-nav-link href="?nav=earn&set=prof" :active="$set == 'prof'" > Profits </x-nav-link>
        
        {{$data->links()}}
        @if ($nav == 'earn' && $set == 'com')
            <x-dashboard.section>
                <x-dashboard.table :data>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $earn)
                            <tr>
                                <td> {{$loop->iteration}} </td>
                                <td> {{$earn->id}} </td>
                                <td> {{$earn->product?->name ?? 0}}  </td>
                                <td> {{$earn->amount ?? 0}}  </td>
                                <td> {{$earn->updated_at?->toFormattedDateString() ?? 0}}  </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-dashboard.table>
            </x-dashboard.section>
        @endif
        @if ($set == 'prof')
            <x-dashboard.table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Profit</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $earn)
                        <tr>
                            <td> {{$loop->iteration}} </td>
                            <td> {{$earn->id}} </td>
                            <td> {{$earn->product?->name ?? 0}}  </td>
                            <td> {{$earn->profit ?? 0}}  </td>
                            <td> {{$earn->updated_at?->toFormattedDateString() ?? 0}}  </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-dashboard.table>
        @endif
        @if($nav == 'system') 
            <x-dashboard.table :data>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Amount</th>
                        <th>Product</th>
                        <th>Order</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $take)
                        <tr>
                            <td>{{ $take->id }}</td>
                            <td>{{ $take->take_comission }}</td>
                            <td>{{ $take->product?->name ?? "N/A" }}</td>
                            <td>{{ $take->order_id ?? "N/A" }}</td>
                            <td> {{ $take->updated_at?->toFormattedDateString()}} </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-dashboard.table>
        @endif
    </x-dashboard.container>
</div>
