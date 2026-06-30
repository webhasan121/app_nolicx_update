<x-app-layout>
    
    <x-dashboard.page-header>
        Role Edit  
    </x-dashboard.page-header>

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <strong class="text-lg">
                        {{Str::upper($role->name) ?? 'Not Found'}}
                    </strong>
                </x-slot>
                <x-slot name="content">
                    Edit your {{$role->name}} role. add or remove permission from all ({{$role->permissions?->count()}}) Permissiions. 
                    <x-hr />
                    <div class=" space-x-2 space-y-2">

                        {{-- <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'role-add-modal')">
                            Add User
                        </x-primary-button> --}}

                        {{-- <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'permissions-add-modal')">
                            Give Permission
                        </x-primary-button> --}}
                       
                    </div>
                </x-slot>
            </x-dashboard.section.header>
        </x-dashboard.section>
    </x-dashboard.container>

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Permissions ({{$role->permissions?->count()}})
                </x-slot>
                <x-slot name="content">
                    <p>
                        Add or Remove permission from all ({{$permissions?->count()}}) Permissiions. 
                    </p>
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <form action="{{route('system.permissions.to-role', ['role' => $role->id])}}" method="post">
                    @csrf
                    @php
                        // $isPermit = $role->hasPermissionTo($perm->name) ? true : false;
                        $userPermissions = $role->getPermissionNames();
                    @endphp
                    <div x-init>
                        <x-permissions-to-user :$userPermissions />
                    </div>

                    {{-- <input type="hidden" name="role" value="{{$role}}"> --}}
                    {{-- <div style="display: grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap: 10px;">
                        @foreach ($permissions as $perm)
                            
                            <div class="flex items-center space-x-2">
                                <x-text-input :checked="$isPermit" class="m-0" type='checkbox' name='permissions[]' id="perm_{{$perm->id}}" value="{{$perm->name}}" />
                                <x-input-label class="text-md m-0 pl-3" id="perm_{{$perm->id}}"> {{$perm->name ?? "Not Found!"}} </x-input-label>
                            </div>
                        @endforeach
                    </div> --}}
                    
                    <x-hr />
                    @if($role->name != 'system')
                        <x-primary-button type="submit" class="mt-4 border-0">
                            Update
                        </x-primary-button>
                    @else
                        <x-danger-button class="border-0 text-danger">System Permission can't be omitted.</x-danger-button>
                    @endif
                </form>
            </x-dashboard.section.inner>

        </x-dashboard.section>
    </x-dashboard.container>

    {{-- <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Users
                </x-slot>
                <x-slot name="content">
                    <p>
                        add or remove User from all ({{$role->users?->count()}}) users. 
                    </p>
                </x-slot>

            </x-dashboard.section.header>
            <x-dashboard.section.inner>
                <form action="{{route('system.role.to-user')}}" method="post">
                    @csrf
                    <input type="hidden" name="role" value="{{$role->name}}">
                    <input type="hidden" name="force_delete" value="true">
                    <div style="display: grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap: 10px;">
                        @php
                            $offset = 0;
                            $limit = 20;
                            $usr = $role->users()->offset($offset * $limit)->limit(20)->get();;
                        @endphp
                        @foreach ($usr as $user)

                            <div class="flex items-center space-x-2">
                                <x-text-input type='checkbox' class="m-0" name="user[]" id="perm_{{$user->id}}" value="{{$user->id}}" />
                                <x-input-label class="text-md p-0 m-0 pl-3" id="perm_{{$user->id}}"> {{$user->name ?? "Not Found!"}} </x-input-label>
                            </div>

                        @endforeach
                    </div>
                    <div class="mt-2">

                    </div>
                    
                    <x-hr />
                    @if($role->name != 'system')
                        <x-danger-button type="submit" class="mt-4 border-0">
                            Remove User
                        </x-danger-button>
                    @else
                        <x-danger-button class="border-0 text-danger">System User can't be omitted.</x-danger-button>
                    @endif
                </form>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container> --}}


    {{-- role add modal  --}}
    <x-modal name="role-add-modal" focusable class="h-screen overflow-y-scroll">
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Assing user to {{$role->name}}
                </x-slot>
                <x-slot name="content">

                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner x-data="{offset:0, limit:20}">
                <form action="{{route('system.role.to-user')}}" method="post">
                    @csrf
                    <input type="hidden" name="role" value="{{$role->name}}">
                    
                    {{-- @foreach ($users as $user)
    
                        <div class="flex items-start border-b px-3 space-y-3 mb-2">
                            <x-text-input type='checkbox' id="perm_{{$user->id}}" name="user[]" value="{{$user->id}}" />
                            <x-input-label class="text-md p-0 m-0 pl-3" id="perm_{{$user->id}}"> 
                                 {{$user->name ?? "Not Found!"}} 
                                 <div class="font-sm font-gray-200">
                                    {{$user->email}}    
                                </div>    
                            </x-input-label>
                        </div>
    
                    @endforeach --}}
                    <x-primary-button type="submit"> 
                        Update
                    </x-primary-button>
                </form>

            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-modal>
    {{-- role add modal  --}}


    {{-- permission add to role modal  --}}
    <x-modal name="permissions-add-modal" focusable class="h-screen overflow-y-scroll">
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Give Permission to <strong> {{$role->name}} </strong> role
                </x-slot>
                <x-slot name="content">

                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner >
                <form action="" method="post">
                    @csrf 
                    <input type="hidden" name="role" value="{{$role->name}}">
                    
                    <div style="display: grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); grid-gap:5px">

                        @foreach ($permissions as $user)
        
                            <div class="flex items-start px-3">
                                <x-text-input type='checkbox' id="perm_{{$user->id}}" name="permissions[]" :value="$user->name" />
                                <x-input-label class="text-md p-0 m-0 pl-3" id="perm_{{$user->id}}"> 
                                     {{$user->name ?? "Not Found!"}} 
                                </x-input-label>
                            </div>
        
                        @endforeach
                    </div>
                    <x-primary-button type="submit">
                        Update
                    </x-primary-button>
                </form>

            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-modal>
    {{-- permission add to role modal  --}}

</x-app-layout>