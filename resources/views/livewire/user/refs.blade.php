<div>
    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Referred User
                </x-slot>
                <x-slot name="content">
                    <p>
                        You accept referrer by <strong> {{auth()->user()->getReffOwner?->owner?->name ?? "User Not Found"}} </strong>. And You have total {{count($refUser) ?? "0"}} referrer user. 
                    </p>
                </x-slot>
            </x-dashboard.section.header>
            

            <x-dashboard.section.inner>
                {{$refUser->links()}}
                <x-dashboard.table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Comission</th>
                            <th>Join</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($refUser as $key => $ru)
                            <tr>
                                <td>{{ $ru->id; }} </td>
                                <td> {{$ru->name}} </td>
                                <td>0</td>
                                <td>
                                    {{$ru->updated_at->toFormattedDateString()}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>
</div>
