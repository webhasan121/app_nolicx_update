<?php
 
use Livewire\Volt\Component;
use App\Models\reseller;

 
new class extends Component {
    public $trs,$ars, $prs, $srs, $drs;

    public function mount()
    {
        $qy = reseller::query();
        $this->trs = $qy->count();
        $this->ars =$qy->active()->count();
        $this->prs = $qy->Pending()->count();
        $this->srs = $qy->suspended()->count();
        $this->drs = $qy->disabled()->count();
    }

} ?>

<div>
    {{-- Do your work, then step back. --}}
    <x-dashboard.page-header>
        Resellers
    </x-dashboard.page-header>



    <x-dashboard.container>

        <x-dashboard.section>
            <x-dashboard.overview.section>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Resellers
                    </x-slot>
                    <x-slot name="content">
                        @volt('trs')
                            <div>
                                {{$this->trs}}
                            </div>
                        @endvolt
                    </x-slot>
                </x-dashboard.overview.div>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Active
                    </x-slot>
                    <x-slot name="content">
                        @volt('ars')
                            <div>
                                {{$this->ars}}
                            </div>
                        @endvolt
                    </x-slot>
                </x-dashboard.overview.div>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Pending
                    </x-slot>
                    <x-slot name="content">
                        @volt('prs')
                            <div>
                                {{$this->prs}}
                            </div>
                        @endvolt
                    </x-slot>
                </x-dashboard.overview.div>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Disabled
                    </x-slot>
                    <x-slot name="content">
                        @volt('drs')
                            <div>
                                {{$this->drs}}
                            </div>
                        @endvolt
                    </x-slot>
                </x-dashboard.overview.div>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Suspended
                    </x-slot>
                    <x-slot name="content">
                        @volt('srs')
                            <div>
                                {{$this->srs}}
                            </div>
                        @endvolt
                    </x-slot>
                </x-dashboard.overview.div>
            </x-dashboard.overview.section>

        </x-dashboard.section>
       
       
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name='title'>
                    <div class="flex justify-between items-start">
                        <div>
                            {{-- <x-nav-link href="?filter=*" class="px-2 mb-2" :active="$filter == '*' " >All</x-nav-link> --}}
                            <x-nav-link href="?filter=Active" class="px-2 mb-2" :active="$filter == 'Active' " >Active </x-nav-link>
                            <x-nav-link href="?filter=Pending" class="px-2 mb-2" :active="$filter == 'Pending' " >Pending</x-nav-link>
                            <x-nav-link href="?filter=Disabled" class="px-2 mb-2" :active="$filter == 'Disabled' " >Disabled</x-nav-link>
                            <x-nav-link href="?filter=Suspended" class="px-2 mb-2" :active="$filter == 'Suspended' " >Suspended</x-nav-link>
                        </div>
        
                        <div>
                            <x-text-input wire:model.live="find" type="search" placeholder="Search Vendor" class="my-1 py-1" />
                            <x-primary-button type="button" x-on:click.prevent="$dispatch('open-modal', 'vendor-filter-modal')">Filter</x-primary-button>
                        </div>
                                    
                    </div>
        
                </x-slot>
                <x-slot name='content'>
                    
                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>
                <x-dashboard.foreach :data="$resellers">
                    <x-dashboard.table>
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Commission</th> 
                                <th>Category</th>
                                <th>Product</th>
                                <th>Join</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <x-dashboard.foreach :data="$resellers">
                                @foreach ($resellers as $item)
                                    <tr>
                                        <td> {{$loop->iteration}} </td>
                                        <td>
                                            {{$item->user?->name ?? "N/A"}}
                                            <br>
                                            <span class="text-xs">
                                                {{$item->shop_name_bn ?? "N/A"}}
                                            </span>
                                        </td>
                                        <td> {{$item->status ?? "N/A"}} </td>
                                        <td> {{$item->system_get_comission ?? "N/A"}} </td>
                                        <td> 0 </td>
                                        <td> 0 </td>
                                        <td>
                                            {{$item->created_at->diffForHumans()}}
                                            <br>
                                            <span class="text-xs">
                                                {{$item->created_at->toFormattedDateString()}}    
                                            </span>     
                                        </td>
                                        <td>
                                            <x-nav-link href="{{route('system.reseller.edit', ['id' => $item->id, 'filter' => $filter])}}">
                                                edit
                                            </x-nav-link>
                                        </td>
                                    </tr>
                                    
                                @endforeach
                            </x-dashboard.foreach>
                        </tbody>
                    </x-dashboard.table>
                </x-dashboard.foreach>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>
</div>
