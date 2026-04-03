<div>
    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>

                <x-slot name="title">
                    VIP Ref Comission
                </x-slot>
                <x-slot name="content">
                    If your ref user purchase a vip package, then you will get the comissions.
                </x-slot>

            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <x-dashboard.table :data="$refs">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Comission</th>
                            <th>User</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($refs as $item)
                            <tr>
                                <td> {{$loop->iteration}} </td>
                                <td> {{$item->comission ?? 0}} </td>
                                <td> {{$item->user?->name ?? "N/A"}} </td>
                                <td> {{$item->created_at?->diffforHumans() ?? "N/A"}} </td>
                            </tr>
                        @endforeach

                    </tbody>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>
</div>
