<div>
    <x-dashboard.page-header>
        User Update
        <br>
    </x-dashboard.page-header>
    
    
    
    <x-dashboard.container x-data="{nav : 'profile'}">
        
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Update User Profile
                </x-slot>
                <x-slot name="content">
                    
                    <div>
                        <x-nav-link @click="nav = 'profile'">Profile</x-nav-link>
                        <x-nav-link @click="nav = 'role'" >Role and Permission</x-nav-link>
                        <x-nav-link @click="nav = 'vip'" >vip</x-nav-link>
                    </div>
                </x-slot>
            </x-dashboard.section.header>
        </x-dashboard.section>
        
        <x-dashboard.section x-show="nav == 'profile'">
            <x-dashboard.section.inner>
                {{-- <x-input-label value="Phone" />
                <x-text-input name="phone" value="{{$user->phone}}" autofocus class="w-full" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        
                <x-input-label value="Reference" />
                <x-text-input name="reference" value="{{$user->reference}}" autofocus class="w-full " />
                <x-input-error class="mt-2" :messages="$errors->get('reference')" />
        
                <x-input-label value="Coin" />
                <x-text-input name="coin" value="{{$user->coin}}" autofocus class="w-full " />
                <x-input-error class="mt-2" :messages="$errors->get('coin')" /> --}}

                <form action="{{ route('system.users.update', $user->id) }}" method="POST">
                    @csrf
                    <div class="flex m-0">
                        
                        <div class="w-1/2 ">
                            <div class="bg-white p-2 rounded">
            
                                <div class="mb-2">
                                    <x-input-label for="name">Name</x-input-label>
                                    <x-text-input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required />
                                </div>
                                <div class="mb-2">
                                    <x-input-label for="email">Email</x-input-label>
                                    <x-text-input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required />
                                </div>
                                <div class="mb-2">
                                    <x-input-label for="password">Password (leave blank to keep current password)</x-input-label>
                                    <x-text-input type="password" class="form-control" id="password" name="password" />
                                </div>
                                <div class="mb-2">
                                    <x-input-label for="password_confirmation">Confirm Password</x-input-label>
                                    <x-text-input type="password" class="form-control" id="password_confirmation" name="password_confirmation" />
                                </div>
                            </div>
                        </div>
                        <div class="w-1/2 p-1">
                            <div class="bg-white p-2 rounded ">
                                <h4>Referred</h4>
                                <hr>
            
                                @if ($user->reference)
                                    Accept ref by <strong> {{$user->getReffOwner?->owner?->name ?? "Not Found"}} </strong>
                                @endif
            
                                <div class="">
                                    {{-- <x-input-label class="input-group-text">USER REF</x-input-label> --}}
                                    <div>
                                        {{$user->reference ?? "No Ref" }}
                                    </div>
                                    {{-- <x-text-input type="text" class="w-full" disabled readonly value="" /> --}}
                                </div>
                                <hr>
                                <div class=" items-center my-2 border p-2 rounded ">
                                    <x-input-label for="new_ref">Custom Ref</x-input-label>
                                    <x-text-input type="text" class="w-full"  placeholder="Write custom referred code " id="new_ref" name="reference" />
                                </div>
                                <hr>
                                <div class="flex items-start my-2">
                                    <x-text-input type="checkbox" id="reference" name="reference" value="{{config('app.ref')}}" style="width:25px; height:25px; margin-right:25px" id="" />
                                    <div>
                                        <p class="bold font-bold fw-bold m-0" for="reference">Set Default Admin Ref</p>
                                        <h6>
                                            In case of set the admin default ref, please check the box.
                                        </h6>
                                    </div>
                                </div>
                            
                            </div>
                            <x-primary-button>
                                Update User
                            </x-primary-button>
                        </div>
                    </div>
                    {{-- <button type="submit" class="btn btn-primary">Update User</button> --}}
                </form>
                    
            </x-dashboard.section.inner>
        </x-dashboard.section>

        <x-dashboard.section x-show="nav == 'vip'">
            <x-dashboard.section.header>
                <x-slot name="title">
                    Vip Package
                </x-slot>

                <x-slot name="content">
                    If user purchase a vip package. you can control over here
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                @if ($user->vipPurchase?->package)
                    <form action="{{route('admin.vip.destroy', ['id' => $user->vipPurchase?->id])}}" method="post">
                        @csrf
                        <button class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                    {{-- <h4 class="alert alert-success">{{ $user->vipPurchase->package->name }}</h4> --}}
                    <div style="max-width: 300px" class="my-3">
                        {{-- <x-vip-cart :item="$package" :active="$id" /> --}}
                        <x-vip-cart :item="$user->vipPurchase?->package" :active="$user->vipPurchase?->package?->id" />
                    </div>
                @else 
                    <div class="alert alert-danger">No Package Found !</div>
                @endif
            </x-dashboard.section.inner>
        </x-dashboard.section>
        
        <x-dashboard.section x-show="nav == 'role'">
            <x-dashboard.section.header>
                <x-slot name="title">
                    Profile Upgradetion
                </x-slot>
                <x-slot name="content">
                    Define user role and given permissions for certain tasks
                </x-slot>
            </x-dashboard.section.header>
            
            <x-dashboard.section.inner>
                
                <div class="lg:flex">
                    <div class="border rounded p-2 mb-3">
                        <x-dashboard.section.header>
                            <x-slot name="title">
                                Role
                            </x-slot>
                            <x-slot name="content">
                                
                            </x-slot>
                        </x-dashboard.section.header>
                        <x-dashboard.section.inner>
                            @php
                                
                                $id = $user->id;
                                $type = 'user';
                            @endphp
                            @include('components.dashboard.role-to-user', ['id' => $id, 'type' => $type])
                        </x-dashboard.section.inner>
                    </div>

                    <div class="border rounded p-2 mb-3">
                        <x-dashboard.section.header>
                            <x-slot name="title">
                                Permissions
                            </x-slot>
                            <x-slot name="content">
                            </x-slot>
                        </x-dashboard.section.header>
                        <form action="{{route('system.permissions.to-user', ['user' => $user->id])}}" method="post">
                            @csrf
                            <x-text-input type="hidden" name="user" value="{{$user->id}}" />
                            
                            @php
                                $userPermissions = $user->getPermissionNames();
                            @endphp
                        
                            <x-permissions-to-user :$userPermissions />
                            {{-- @include('components.permissions-to-user') --}}
                            <x-hr/>
                            <x-primary-button>
                                save
                            </x-primary-button>
                        </form>
                    </div>
                </div>

            </x-dashboard.section.inner>
        </x-dashboard.section>

    </x-dashboard.container>
</div>