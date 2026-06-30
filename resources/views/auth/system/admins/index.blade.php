<x-app-layout>
    <x-dashboard.page-header>
        Admins
    </x-dashboard.page-header>

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Your system admins
                </x-slot>

                <x-slot name="content">
                    You have {{ $admins->count() ?? "N/A" }} admin with different permissions
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <x-dashboard.table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Permissions</th>
                                <th>Assign At</th>
                                <th>
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($admins as $item)     
                                <tr>
                                    <td>
                                        {{$loop->iteration}}
                                    </td>
                                    <td>
                                        {{$item->name ?? "N/A"}}
                                    </td>
                                    <td>
                                        {{
                                            $item->getPermissionNames()?->count() ?? "N/A"
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            $item->updated_at->toFormattedDateString();
                                        }}
                                    </td>
                                    <td>
                                        <form action="{{route('system.users.edit', ['id' => $item->id])}}" method="get">
                                            <x-primary-button type="submit">
                                                Edit
                                            </x-primary-button>
                                        </form>
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>
</x-app-layout>