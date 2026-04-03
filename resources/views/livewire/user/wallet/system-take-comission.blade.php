<div>
    {{-- Success is as dangerous as failure. --}}
    <x-dashboard.page-header>
        System Take Comissions
    </x-dashboard.page-header>

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.table :data="comissions" >
                <thead>
                    <tr>
                        <th>  </th>
                        <th> Order ID </th>
                        <th> Product ID </th>
                        <th> Profit </th>
                        <th> System Take </th>
                        <th> Confirmed </th>
                        <th> Date </th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach ($comissions as $item)
    
                        <tr>
                            <td> {{$item->id}} </td>
                            <td> {{$item->order_id}} </td>
                            <td> {{$item->product_id}} </td>
                            <td> {{$item->take_comission}} </td>
                            <td> {{$item->confirmed}} </td>
                            <td> {{$item->created_at?->toFormattedDateString()}} </td>
                        </tr>

                        @endforeach
                </tbody>
            </x-dashboard.table>
        </x-dashboard.section>
    </x-dashboard.container>
</div>
