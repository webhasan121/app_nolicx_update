<div>
    {{-- Success is as dangerous as failure. --}}
    <x-dashboard.page-header>
        <div class="md:flex items-center justify-between">
            <div class="mb-1">
                VIP Users
            </div>


        </div>
    </x-dashboard.page-header>

    <x-dashboard.container>

        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex flex-wrap justify-between items-start">
                        <div class="flex items-center gap-2">
                            <x-secondary-button @click="$dispatch('open-modal', 'filter-modal')">
                                <i class="fa-solid fa-filter"></i>
                            </x-secondary-button>
                            <select wire:model.live="nav" id="" class="rounded-md py-1">
                                <option value="All">All</option>
                                <option value="Pending">Pending</option>
                                <option value="Confirmed">Active</option>
                                <option value="Trash">Trash</option>
                            </select>
                        </div>
                        <div class="flex">
                            <x-primary-button wire:click='print'>
                                <i class="fas fa-print"></i>
                            </x-primary-button>
                            <input type="search" class="ms-2 rounded-lg border-gray-400 py-1"
                                placeholder="find name, id" wire:model.live="search" id="">
                        </div>
                    </div>
                </x-slot>
                <x-slot name="content">

                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <x-dashboard.foreach :data="$vip">

                    <x-dashboard.table>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>VIP</th>
                                <th>Wallet</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Validity</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($vip as $item)
                            <tr>
                                <td> {{$loop->iteration}} </td>
                                <td>
                                    {{$item->name ?? "N/A"}}
                                    <br>
                                    <div class="text-xs ">
                                        {{$item->user?->email ?? "N/A"}}
                                    </div>
                                </td>
                                <td>
                                    {{$item->package?->name ?? "N/A"}}
                                    <div class="text-xs"> {{$item->task_type ?? "N/A"}} </div>
                                </td>
                                <td> {{$item->user?->coin ?? "0"}} </td>

                                <td>
                                    @if ($item->status)
                                    Active
                                    @else
                                    @if($item->stauts == -1 || $item->deleted_at)
                                    Trash
                                    @else
                                    Pending
                                    @endif
                                    @endif
                                    <br>
                                    @if ($item->deleted_at)
                                    <span class="text-xs text-red-900 text-bold ">
                                        {{$item->deleted_at->toFormattedDateString()}}
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-nowrap">
                                        {{$item->created_at?->toFormattedDateString()}}
                                    </div>
                                </td>
                                <td>
                                    {{carbon\carbon::parse($item->valid_till)->toFormattedDateString()}}
                                    <div class="text-xs">
                                        {{carbon\carbon::parse($item->valid_till)->diffForHumans()}}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex space-x-3">
                                        <x-nav-link :href="route('system.vip.edit', ['vip' => $item->id])">View
                                        </x-nav-link>
                                        <x-nav-link>User</x-nav-link>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </x-dashboard.table>

                </x-dashboard.foreach>
            </x-dashboard.section.inner>
        </x-dashboard.section>

    </x-dashboard.container>

    <x-modal name="filter-modal">
        <div class="p-2">
            User Filter
        </div>
        <hr class="my-1" />
        <div class="p-3">

            <div class="md:flex justify-between items-start mb-2 border-b">

                <p class="">
                    Taks Type
                </p>

                <div class="md:flex items-center gap-2">
                    <div class="flex items-center mb-2 border rounded-md p-2">
                        <input type="radio" wire:model.live="type" class="w-4 h-4 rounded mr-2" id="daily"
                            value="daily" />
                        <p>Daily Taks</p>
                    </div>
                    <div class="flex items-center mb-2 border rounded-md p-2">
                        <input type="radio" wire:model.live="type" class="w-4 h-4 rounded mr-2" id="monthly"
                            value="monthly" />
                        <p>Monthly Taks</p>
                    </div>
                    <div class="flex items-center mb-2 border rounded-md p-2">
                        <input type="radio" wire:model.live="type" class="w-4 h-4 rounded mr-2" id="All" value="All" />
                        <p>Both</p>
                    </div>
                </div>
            </div>
            <div class="md:flex items-start justify-between gap-2 mb-2 border-b">
                <p>
                    Package Validity
                </p>
                <div class="md:flex items-center gap-2">
                    <div class="flex items-center mb-2 border rounded-md p-2">
                        <input type="radio" wire:model.live="validity" class="w-4 h-4 rounded mr-2" id="valid"
                            value="valid" />
                        <p>Only Valid</p>
                    </div>
                    <div class="flex items-center mb-2 border rounded-md p-2">
                        <input type="radio" wire:model.live="validity" class="w-4 h-4 rounded mr-2" id="invalid"
                            value="invalid" />
                        <p>Only Invalid</p>
                    </div>
                    <div class="flex items-center mb-2 border rounded-md p-2">
                        <input type="radio" wire:model.live="validity" class="w-4 h-4 rounded mr-2" id="All"
                            value="All" />
                        <p>Both</p>
                    </div>
                </div>
            </div>
            <div class="md:flex items-start justify-between gap-2">
                <p>
                    Between Date
                </p>
                <div class="flex text-xs gap-2">
                    <x-text-input type="date" wire:model.live='sdate' class="py-1" />
                    <x-text-input type="date" wire:model.live='edate' class="py-1" />
                </div>
            </div>
        </div>
        <hr class="my-1" />
        <div class="p-3">
            <x-secondary-button @click="$dispatch('close-modal', 'filter-modal')">
                Close
            </x-secondary-button>
        </div>
    </x-modal>

    <script>
        window.addEventListener('open-printable', (e) => {
                // console.log(e.detail[0].url);
                window.open(e.detail[0].url, '_blank');
            });
            
    </script>
</div>