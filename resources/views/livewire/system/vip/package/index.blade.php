<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <x-dashboard.page-header>
        VIP
        <br>
        <div>
            <x-nav-link :href="route('system.vip.index')" :active="request()->routeIs('system.vip.index')"> <i class="fa-solid fa-up-right-from-square me-2"></i> Package </x-nav-link>
            <x-nav-link :href="route('system.vip.users')" :active="request()->routeis('system.vip.users')"> <i class="fa-solid fa-up-right-from-square me-2"></i> User </x-nav-link>
        </div>
    </x-dashboard.page-header>

    <x-dashboard.container>
        <x-dashboard.section>

            <x-dashboard.section.header>
                <x-slot name="title">
                    <x-nav-link-btn :href="route('system.vip.crate')">
                        New
                    </x-nav-link-btn>
                </x-slot>
                <x-slot name="content">
                    <x-nav-link href="?nv=Active" :active="$nav == 'Active'" >Active</x-nav-link>
                    <x-nav-link href="?nav=Trash" :active="$nav == 'Trash'">Trash</x-nav-link>
                </x-slot>
            </x-dashboard.section.header>
    
            <x-dashboard.section.inner>
                <x-dashboard.foreach :data="$packages">
    
                    <x-dashboard.table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th >
                                    Name
                                </th>
                                <th>Price</th>
                                <th>Timer</th>
                                <th>Coin</th>
                                <th>Sell</th>
                                <th>Earn</th>
                                <th>Created</th>
                                <th>
                                    A/C
                                </th>     
                            </tr>
                        </thead>
        
                        <tbody>
                            @foreach ($packages as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td >
                                        <div class="position-relative">
        
                                            {{-- @if ($item->has_refer)
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                                    Ref
                                                </span>
                                            @else 
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                    <span class="visually-hidden"> </span>
                                                </span>
                                            @endif --}}
                                            {{$item->name }}
                                        </div>
                                    </td>
                                    <td>{{$item->price }} TK</td>
                                    <td>{{$item->countdown}} Minute</td>
                                    <td class="">
                                        <div class="">
                                            <div>D - </d>
                                            {{$item->coin }}
                                        </div>
                                        <div>
                                            <div>M - </d>
                                            {{$item->m_coin ?? "0" }}
                                        </div>
                                        <hr class="my-1">
                                        <div>
                                            <div>Ref - </d>
                                            {{$item->ref_owner_get_coin }}
                                        </div>
                                        
                                        {{-- <span class="px-2 bg-success bold text-white rounded-pill ">By Ref-</span> --}}
                                    </td>
                                    <td>
                                        {{
                                            $item->user->count() ?? "0"
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            $item->price * $item->user->count()
                                        }}
                                    </td>
                                    <td>
                                        <div>
                                            {{$item->created_at->diffForHumans()}}
                                        </div>
                                        <div class="text-xs">
                                            {{$item->created_at->toFormattedDateString()}}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex">
                                            <x-nav-link-btn href="{{route('system.package.edit', ['packages' => $item])}}" class="me-2" >View</x-nav-link-btn>
                                            <x-danger-button>Trash</x-danger-button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-dashboard.table>
    
                </x-dashboard.foreach>
            </x-dashboard.section.inner>

        </x-dashboard.section>

        {{-- edit modal  --}}
        <x-modal name="packageEditModal">
            <div class="p-3">
                <div class="border-b pb-2 text-md">
                    Vip Package Edit
                </div>
            </div>
        </x-modal>

    </x-dashboard.container>
</div>
