<?php

use Livewire\Volt\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

new class extends Component {
    public $user, $permissions, $userPermissions = [], $permissionsViaRole = [];

    public function mount() 
    {
        $this->userPermissions = $this->user->getPermissionNames()->toArray();
        $this->permissionsViaRole = $this->user->getPermissionsViaRoles()->toArray();
        $this->permissions = permission::all();    
        // dd($this->permissionsViaRole);
    }
    
    public function save() 
    {
        $this->user->syncPermissions($this->userPermissions);
        $this->dispatch('success', 'Permission Synced !');
        $this->dispatch('refresh');
    }
    
}; ?>

<div>
    <form wire:submit.prevent="save">
        <p>
            User has {{count($this->permissionsViaRole)}} Permissions via Role. <br> <x-secondary-button x-on:click.prevent="$dispatch('open-modal', 'permission-via-role')" class="py-1">check</x-secondary-button>
        </p>
        <x-hr/>
            <div style="display: grid; grid-template-columns:repeat(auto-fit, minmax(230px, 1fr)); gap: 10px;">
                <div>
                    <x-input-label>
                        Role
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'role_'))

                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="userPermissions" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>
                
                {{-- permission  --}}
                <div>
                    <x-input-label>
                        Permission
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'permission'))

                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="userPermissions" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- access  --}}
                <div>
                    <x-input-label>
                        Access
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'access'))

                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="userPermissions" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- sync  --}}
                <div>
                    <x-input-label>
                        Sync
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'sync'))

                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="userPermissions" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>


                {{-- admin  --}}
                <div>
                    <x-input-label>
                        Admin
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'admin'))

                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="userPermissions" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- vendors  --}}
                <div>
                    <x-input-label>
                        Vendors
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'vendors'))

                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="userPermissions" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- resellers  --}}
                <div>
                    <x-input-label>
                        Resellers
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'reseller'))

                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="userPermissions" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- riders  --}}
                <div>
                    <x-input-label>
                        Riders
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'riders'))

                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="userPermissions" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- users  --}}
                <div>
                    <x-input-label>
                        Users
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'users'))

                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="userPermissions" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- product  --}}
                <div>
                    <x-input-label>
                        Product
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'product'))

                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="userPermissions" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- category  --}}
                <div>
                    <x-input-label>
                        Category
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'category'))

                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="userPermissions" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>

            </div>
        <x-hr />
        <x-primary-button>
            save
        </x-primary-button>
    </form>

    <x-modal name="permission-via-role">
        <div class="p-3">
            <p>Permissions</p>
            <x-hr />
            <div style="display: grid; grid-template-columns:repeat(auto-fit, minmax(230px, 1fr)); gap: 10px;">
                <div>
                    <x-input-label>
                        Role
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'role_'))
    
                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="permissionsViaRole" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>
                
                {{-- permission  --}}
                <div>
                    <x-input-label>
                        Permission
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'permission'))
    
                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="permissionsViaRole" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>
    
                {{-- access  --}}
                <div>
                    <x-input-label>
                        Access
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'access'))
    
                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="permissionsViaRole" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>
    
                {{-- sync  --}}
                <div>
                    <x-input-label>
                        Sync
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'sync'))
    
                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="permissionsViaRole" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>
    
    
                {{-- admin  --}}
                <div>
                    <x-input-label>
                        Admin
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'admin'))
    
                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="permissionsViaRole" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>
    
                {{-- vendors  --}}
                <div>
                    <x-input-label>
                        Vendors
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'vendors'))
    
                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="permissionsViaRole" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>
    
                {{-- resellers  --}}
                <div>
                    <x-input-label>
                        Resellers
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'reseller'))
    
                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="permissionsViaRole" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>
    
                {{-- riders  --}}
                <div>
                    <x-input-label>
                        Riders
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'riders'))
    
                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="permissionsViaRole" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>
    
                {{-- users  --}}
                <div>
                    <x-input-label>
                        Users
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'users'))
    
                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="permissionsViaRole" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>
    
                {{-- product  --}}
                <div>
                    <x-input-label>
                        Product
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'product'))
    
                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="permissionsViaRole" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>
    
                {{-- category  --}}
                <div>
                    <x-input-label>
                        Category
                    </x-input-label>
                    @foreach ($permissions as $permission)
                        
                        @if (Str::startsWith($permission->name, 'category'))
    
                            <div>
                                <x-text-input class="m-0" type="checkbox" wire:model.live="permissionsViaRole" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                                <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                            </div>
                        @endif
                    @endforeach
                </div>
    
            </div>
        </div>
    </x-modal>

</div>
