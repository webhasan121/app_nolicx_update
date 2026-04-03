<div>
    <x-dashboard.page-header>
        Users
    </x-dashboard.page-header>
    <div>
        <x-dashboard.container>
            <x-dashboard.section>
             <div class="grid grid-cols-6 gap-6" >
                 @foreach ($widgets as $widget)
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        {{ $widget['head'] }}
                    </x-slot>
                    <x-slot name="content">
                        {{ $widget['data'] }}
                    </x-slot>
                </x-dashboard.overview.div>
              @endforeach
             </div>
            </x-dashboard.section>
            <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        {{-- <x-nav-link href="" :active="true">Any Role</x-nav-link>
                        <x-nav-link href="">Admin Role</x-nav-link>
                        <x-nav-link href="">Vendor Role</x-nav-link>
                        <x-nav-link href="">Reseller Role</x-nav-link>
                        <x-nav-link href="">Rider Role</x-nav-link> --}}

                    </x-slot>
                    <x-slot name="content">
                        <div class="flex justify-between items-center gap-2">
                            <div>
                                <x-primary-button wire:click='print'>
                                    <i class="fas fa-print"></i>
                                </x-primary-button>
                            </div>
                            <div class="flex gap-2">
                                {{-- <x-primary-button class="mx-1" @click="$dispatch('open-modal', 'filter-modal')">
                                    <i class="fa-solid fa-filter"></i>
                                </x-primary-button> --}}
                                <x-text-input type="date" wire:model.live='sd' class="py-1" />
                                <x-text-input type="date" wire:model.live='ed' class="py-1" />
                                <x-text-input wire:model.live="search" type="search" placeholder="search"
                                    class="py-1" />
                            </div>
                        </div>
                    </x-slot>
                </x-dashboard.section.header>

                <x-dashboard.section.inner>
                    {{$users->links()}}

                    <x-dashboard.foreach :data="$users">
                        {{-- <div x-data="{ users: @js($users) }"> --}}
                            <div>
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
                                            <th>A/C</th>
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
                                            <td>
                                                <div class="flex">
                                                    <x-nav-link
                                                        href="{{route('system.users.edit', ['id' => $user->id])}}">
                                                        <i class="fa-solid fa-pen mr-2"></i> Edit
                                                    </x-nav-link>
                                                    <x-nav-link>
                                                        <i class="fa-solid fa-eye mr-2"></i> view
                                                    </x-nav-link>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </x-dashboard.table>

                            </div>
                    </x-dashboard.foreach>
                </x-dashboard.section.inner>
            </x-dashboard.section>
        </x-dashboard.container>
    </div>

    <x-modal name='filter-modal'>
        <x-slot name="title">
            Filter Users
        </x-slot>
        <x-slot name="content">
            <div class="flex flex-col gap-2">

            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button @click="$dispatch('close-modal', 'filter-modal')">Close</x-secondary-button>
        </x-slot>
    </x-modal>
    {{--
    @script
    <script sec="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
    <script>
        let table = new DataTable('#myTable');
    </script>
    @endscript --}}


    <script>
        window.addEventListener('open-printable', (e) => {
                // console.log(e.detail[0].url);
                window.open(e.detail[0].url, '_blank');
            });
            
    </script>
</div>