<div>
    {{-- In work, do what you enjoy. --}}

    <x-dashboard.container>
        <div class="text-center">
            <h1>
                <x-application-name />
            </h1>
            <p class=""> Users Summery form {{carbon\carbon::parse($sd)->format('d/m/Y')}}
                to {{carbon\carbon::parse($ed)->format('d/m/Y') }} </p>
        </div>
        <hr class="my-2" />

        <x-dashboard.table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Ref & Reference</th>
                    <th>Role</th>
                    <th>Permissions</th>
                    <th>VIP</th>
                    <th>Order</th>
                    <th>Wallet</th>
                    <th>Created</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($users as $key => $user)
                <tr>
                    <td> {{$loop->iteration}} </td>
                    <td> {{$user->id ?? "N/A"}} </td>
                    <td>
                        {{$user->name ?? "N/A" }}
                        <br>
                        <b class="text-xs">{{$user ->email ?? "N/A" }}</b>
                    </td>
                    <td>
                        {{$user->myRef->ref ?? "N/A"}}
                        <br>
                        <span class="px-2 text-xs rounded border">
                            {{$user->reference ?? "Not Found" }} >
                            {{$user->getReffOwner?->owner?->name}}
                        </span>
                    </td>
                    <td>
                        @php
                        $uroles = $user?->getRoleNames();
                        @endphp
                        <div class="flex">

                            @foreach ($uroles as $role)
                            <div class="px-1 rounded border m-1 text-sm">{{$role}}</div>
                            @endforeach
                        </div>

                    </td>
                    <td> {{$user->permissions?->count() ?? "Not Found !"}} </td>
                    <td>
                        @if ($user->subscription)

                        @if ($user->subscription?->valid_till > now() &&
                        $user->subscription->status)
                        <div class="px-1 rounded inline-flex bg-green-200 text-xs">
                            {{$user->subscription?->package?->name ?? "N/A"}}
                        </div>
                        @elseif($user->subscription?->valid_till < now() && $user->
                            subscription->status)

                            <div class="px-1 rounded inline-flex bg-yellow-200 text-xs">
                                Expired
                            </div>

                            @elseif(!$user->subscription?->status)
                            <div class="px-1 rounded inline-flex bg-blue-200 text-xs">
                                Pending
                            </div>
                            @endif

                            @else
                            <div class="px-1 rounded inline-flex bg-red-200 text-xs">NO</div>
                            @endif
                    </td>
                    <td>
                        {{$user->myOrderAsUser()?->count() ?? "0"}}
                    </td>
                    <td> {{$user->coin ?? "0"}} </td>
                    <td>
                        {{$user->created_at?->toFormattedDateString() ?? ""}}
                    </td>

                </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="11">Total {{count($users)}} Items </td>
                </tr>
            </tfoot>
        </x-dashboard.table>

        </x-dshboard.container>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Trigger the print dialog after the content is loaded
                // window.dispatchEvent(new Event('open-printable', {
                //     detail: [{
                //         url: window.location.href
                //     }]
                // }));
                setTimeout(() => {
                    window.print();
                }, 1000);
            });
        </script>
</div>