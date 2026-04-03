<div>
    <x-dashboard.page-header>
        Navigations
    </x-dashboard.page-header>

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex justify-between items-center">
                        <div>
                            Menus
                        </div>
                        <x-secondary-button wire:click="openAddMenuForm"> <i class="fa-solid fa-plus pe-2"></i> New </x-secondary-button>
                        {{-- <x-secondary-button x-on:click="$dispatch('open-modal', 'add-new-menu')"> <i class="fa-solid fa-plus pe-2"></i> New </x-secondary-button> --}}
                    </div>
                </x-slot>
                <x-slot name="content">
                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>
                <div wire:show='isOpenAddMenuForm' class="p-3 mx:w-48 border rounded shadow-lg">
                    <form wire:submit.prevent="addNewMenu('')">
                       
                        <x-input-label value="Menu Name" />
                        <div class="flex flex-wrap">
                            <x-text-input wire:model.live="newMenu" placeholder="Menu Name" class="py-1" />
                            <x-primary-button class="m-1">Save</x-primary-button>
                        </div>
                        
                    </form>
                </div>
            </x-dashboard.section.inner>
        </x-dashboard.section>

        <x-dashboard.section>
            <x-dashboard.table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Menu</th>
                        <th>Items</th>
                        <th>A/C</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($menus as $item)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>
                                {{$item->name ?? "N/A"}}
                            </td>
                            <td> 
                                {{count($item->links) ?? "0"}}
                            </td>
                            <td> 
                                <div class="flex space-x-2">
                                    <x-danger-button wire:click='destroyMenu({{$item->id}})'>
                                        <i class="fas fa-trash"></i>
                                    </x-danger-button>
                                    <x-secondary-button wire:click="openMenu({{$item->id}})">
                                        View
                                    </x-secondary-button>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-dashboard.table>
        </x-dashboard.section>
    </x-dashboard.container>


    <x-modal name='add-new-menu'>
        <div class="p-4 overflow-y-scroll h-100%">
            <div class="border-b py-2">
                
                <form wire:submit.prevent="updateRenameMenu">
                    <x-text-input wire:model="renameMenu" />
                    <button type="submit">Update</button>
                </form>
            </div>
            <div class="py-2">
                <div class="flex justify-between items-center mb-2">
                   
                </div>
                <div class="space-y-3" wire:show="selectedMenuItems">
                    
                        @foreach ($selectedMenuItems as $key => $item)
                          
                            <div class="p-2 rounded shadow space-y-2">
                                <x-text-input placeholder="Menu Item Name" wire:model.live="selectedMenuItems.{{$key}}.name" class=" rounded-0 text-sm py-1" />
                                <div>
                                    <x-text-input placeholder="Menu Item URL" wire:model.live="selectedMenuItems.{{$key}}.url" class=" rounded-0 text-sm py-1 w-full" />
                                </div>
                                <x-danger-button wire:click="destroyMenItems({{$key}}), this">
                                    <i class="fas fa-trash"></i>
                                </x-danger-button>
                            </div>
                        @endforeach
                    
                </div>

                <div class="text-end space-x-2 mt-2 flex justify-between">
                    <x-secondary-button x-on:click="$dispatch('close-modal', 'add-new-menu')" >close</x-secondary-button>

                    <div>

                        <x-primary-button wire:click="addNewMenuItems">
                            <i class="fas fa-plus"></i>
                        </x-primary-button>
                        <x-primary-button wire:click="updateMenuItems">
                            update
                        </x-primary-button>
                    </div>

                    
                </div>
            </div>
        </div>
    </x-modal>
</div>
