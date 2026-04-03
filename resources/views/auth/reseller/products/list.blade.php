<x-app-layout>
    <x-dashboard.page-header>
        Your Products
    </x-dashboard.page-header>

    <x-dashboard.container>

        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Products List
                </x-slot>
                <x-slot name="content">
                    Product those have insert in the system or resell form vendor
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                    @php
                        $nav = request('nav') ?? 'own';
                    @endphp
                    <x-nav-link href="{{url()->current()}}/?nav=own" :active="$nav == 'own'">
                        Your Product
                    </x-nav-link>
                    <x-nav-link href="{{url()->current()}}/?nav=resel" :active="$nav == 'resel'">
                        Resel Product
                    </x-nav-link>
            </x-dashboard.section.inner>
        </x-dashboard.section>   
    
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title" class="float-right clearfix">
                    <x-primary-button>
                        New
                    </x-primary-button>
                </x-slot>
                <x-slot name="content">
                    <div class="flex justify-between items-center">
                        <div>
                            <x-nav-link href="" :active="true">
                                Active
                            </x-nav-link>
                            <x-nav-link href="">
                                In Active
                            </x-nav-link>
                            <x-nav-link href="">
                                Trash
                            </x-nav-link>
                        </div>

                        <div class="flex items-center">
                            <x-text-input type="search" placeholder="Search by name" class="mx-2 hidden lg:block py-1"></x-text-input>
                            <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'filter-modal')" >Filter</x-primary-button>
                        </div>
                    </div>
                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>
                <x-dashboard.table>
                    <thead>
                        <tr>
                            <th>#</th>
                        </tr>
                    </thead>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>

        {{-- filter model  --}}
        <x-modal name="filter-modal" maxWidth="xl" focusable class="h-screen overflow-y-scroll">
            <div class="p-3">
                <x-dashboard.section.header>
                    <x-slot name="title">
                        Filter Your Own
                    </x-slot>
                    <x-slot name="content">
                        
                    </x-slot>
                </x-dashboard.section.header>
                <x-dashboard.section.inner>
                    <form action="" method="get">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3>Filter by Create date</h3>
                                <ul class="ms-4 mt-2">
                                    <li>
                                        <div class="flex items-center mb-2">
                                            <x-text-input class="p-0 m-0 mr-3" type="radio" name="" value="today" />
                                            <x-input-label class="p-0 m-0">Today</x-input-label>
                                        </div>
                                        <div class="flex items-center mb-2">
                                            <x-text-input class="p-0 m-0 mr-3" type="radio" name="" value="today" />
                                            <x-input-label class="p-0 m-0">Today</x-input-label>
                                        </div>
                                        <div class="flex items-center mb-2">
                                            <x-text-input class="p-0 m-0 mr-3" type="radio" name="" value="today" />
                                            <x-input-label class="p-0 m-0">Today</x-input-label>
                                        </div>
                                        <div class="flex items-center mb-2">
                                            <x-text-input class="p-0 m-0 mr-3" type="radio" name="" value="today" />
                                            <x-input-label class="p-0 m-0">Today</x-input-label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            
                            
                            <div>
                                <h3>Filter by Status</h3>
                                <ul class="ms-4 mt-2">
                                    <li>
                                        <div class="flex items-center mb-2">
                                            <x-text-input class="p-0 m-0 mr-3" type="radio" name="" value="today" />
                                            <x-input-label class="p-0 m-0">Active</x-input-label>
                                        </div>
                                        <div class="flex items-center mb-2">
                                            <x-text-input class="p-0 m-0 mr-3" type="radio" name="" value="today" />
                                            <x-input-label class="p-0 m-0">Disable</x-input-label>
                                        </div>
                                        <div class="flex items-center mb-2">
                                            <x-text-input class="p-0 m-0 mr-3" type="radio" name="" value="today" />
                                            <x-input-label class="p-0 m-0">Trash</x-input-label>
                                        </div>
                                      
                                    </li>
                                </ul>
                            </div>
                            <div></div>
    
                        </div>
                    </form>
                </x-dashboard.section.inner>
            </div>
        </x-modal>
</x-app-layout>