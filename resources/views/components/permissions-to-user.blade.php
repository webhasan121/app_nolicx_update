<div>
    <!-- Simplicity is the ultimate sophistication. - Leonardo da Vinci -->

    @php
        // $permissions = DB::table('permissions')->get();
    @endphp
        <div style="display: grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <div>
                <x-input-label>
                    Role
                </x-input-label>
                @foreach ($permissions as $permission)
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'role_'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                            <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                        </div>
                    @endif
                @endforeach
            </div>
            
            {{-- permission  --}}
            {{-- <div>
                <x-input-label>
                    Permission
                </x-input-label>
                @foreach ($permissions as $permission)
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'permission'))     
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                            <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                        </div>
                    @endif
                @endforeach
            </div> --}}

            {{-- access  --}}
            <div>
                <x-input-label>
                    Access
                </x-input-label>
                @foreach ($permissions as $permission)
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'access'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
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
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'sync'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
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
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'admin'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
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
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'vendors'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
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
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'reseller'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
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
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'riders'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                            <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                        </div>
                    @endif
                @endforeach
            </div>
            
            {{-- shop  --}}
            <div>
                <x-input-label>
                    Shops
                </x-input-label>
                @foreach ($permissions as $permission)
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'shop'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
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
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'users'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
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
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'product'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
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
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'category'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                            <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                        </div>
                    @endif
                @endforeach
            </div>
            
            
            {{-- comission  --}}
            <div>
                <x-input-label>
                    Comission
                </x-input-label>
                @foreach ($permissions as $permission)
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'comission'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                            <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                        </div>
                    @endif
                @endforeach
            </div>
            
            {{-- withdraw  --}}
            <div>
                <x-input-label>
                    Withdraw
                </x-input-label>
                @foreach ($permissions as $permission)
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'withdraw'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                            <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                        </div>
                    @endif
                @endforeach
            </div>
           
            {{-- vip  --}}
            <div>
                <x-input-label>
                    VIP Package
                </x-input-label>
                @foreach ($permissions as $permission)
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'vip'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                            <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                        </div>
                    @endif
                @endforeach
            </div>
            
            
            {{-- vip  --}}
            <div>
                <x-input-label>
                    VIP User
                </x-input-label>
                @foreach ($permissions as $permission)
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'vip_user'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                            <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                        </div>
                    @endif
                @endforeach
            </div>
           
           
            {{-- vip  --}}
            <div>
                <x-input-label>
                    Store
                </x-input-label>
                @foreach ($permissions as $permission)
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'store'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                            <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                        </div>
                    @endif
                @endforeach
            </div>
           
            {{-- order  --}}
            <div>
                <x-input-label>
                    Order
                </x-input-label>
                @foreach ($permissions as $permission)
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'order'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                            <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                        </div>
                    @endif
                @endforeach
            </div>
           
            {{-- slider  --}}
            <div>
                <x-input-label>
                    Slider
                </x-input-label>
                @foreach ($permissions as $permission)
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'slider'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                            <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                        </div>
                    @endif
                @endforeach
            </div>
           
            {{-- slider  --}}
            <div>
                <x-input-label>
                    Deposit
                </x-input-label>
                @foreach ($permissions as $permission)
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'deposit'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                            <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- partnership  --}}
            <div>
                <x-input-label>
                    Partnership
                </x-input-label>
                @foreach ($permissions as $permission)
                    @php
                        $chk = false;
                        if (isset($userPermissions)){
                            if($userPermissions->contains($permission->name)){
                                $chk = true;
                            }
                        }
                    @endphp
                        
                    @if (Str::startsWith($permission->name, 'partnership'))
                        {{-- {{$permission->name}} --}}
                        
                        
                        <div>
                            <x-text-input class="m-0" :checked="$chk" type="checkbox" name="permissions[]" id="perm_{{$permission->id}}" value="{{$permission->name}}" />
                            <label class="pl-3 text-sm" for="perm_{{$permission->id}}">{{$permission->name}}</label>     
                        </div>
                    @endif
                @endforeach
            </div>

        </div>
</div>