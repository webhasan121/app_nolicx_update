<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
    <x-dashboard.page-header>
        Rider - Delevary Man
    </x-dashboard.page-header>

    <div>
        <x-dashboard.container>
            <x-dashboard.overview.section>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Total Rider
                    </x-slot>
                    <x-slot name="content">
                        {{$tri}}
                    </x-slot>
                </x-dashboard.overview.div>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Active Rider
                    </x-slot>
                    <x-slot name="content">
                        {{$ari}}
                    </x-slot>
                </x-dashboard.overview.div>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Pending Rider
                    </x-slot>
                    <x-slot name="content">
                        {{$pri}}
                    </x-slot>
                </x-dashboard.overview.div>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Suspended Rider
                    </x-slot>
                    <x-slot name="content">
                        {{$sri}}
                    </x-slot>
                </x-dashboard.overview.div>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Disabled Rider
                    </x-slot>
                    <x-slot name="content">
                        {{$dri}}
                    </x-slot>
                </x-dashboard.overview.div>
            </x-dashboard.overview.section>
        </x-dashboard.container>

        <x-dashboard.container>
            <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        Riders
                    </x-slot>
                    <x-slot name="content">
                        <div class="flex justify-between items-center">
                            <div>

                                <x-nav-link :active="$condition == 'all' " href="?condition=all"> All </x-nav-link>
                                <x-nav-link :active="$condition == 'Active' " href="?condition=Active"> Active
                                </x-nav-link>
                                <x-nav-link :active="$condition == 'Pending' " href="?condition=Pending"> Pending
                                </x-nav-link>
                                <x-nav-link :active="$condition == 'Disabled' " href="?condition=Disabled"> Disabled
                                </x-nav-link>
                                <x-nav-link :active="$condition == 'Suspended' " href="?condition=Suspended"> Suspended
                                </x-nav-link>
                            </div>

                            <div class="flex">
                                {{--
                                <x-text-input class="py-1 mr-1" wire:model.live="search" type="search"
                                    placeholder="Search" /> --}}
                                <x-secondary-button>Filter</x-secondary-button>
                            </div>
                        </div>
                        <div class="text-xs">
                            {{ count($riders) ?? '0' }} {{$condition}} Riders
                        </div>
                    </x-slot>
                </x-dashboard.section.header>

                <x-dashboard.section.inner>
                    <x-dashboard.foreach :data="$riders">
                        <x-dashboard.table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Join Data</th>
                                    <th>A/C</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($riders as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->user?->name ?? "N/A" }}</td>
                                    <td>{{ $item->status ?? "N/A" }}</td>

                                    <td>
                                        {{ $item->created_at->toFormattedDateString() }}
                                        <br>
                                        <span class="text-xs">
                                            {{ $item->created_at->diffForHumans() }}
                                        </span>

                                    </td>
                                    <td>
                                        <x-nav-link href="{{route('system.rider.edit', ['id' => $item->id])}}">edit
                                        </x-nav-link>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </x-dashboard.table>
                    </x-dashboard.foreach>
                </x-dashboard.section.inner>
            </x-dashboard.section>
        </x-dashboard.container>
    </div>
</div>