<div>
    <!-- Simplicity is the ultimate sophistication. - Leonardo da Vinci -->
    <form action="{{route('multiple_role_to_single_user')}}" method="post">
        @csrf
        <div class="flex justify-between items-start w-full">
            <div @class(["p-2 rounded border", 'hidden' => $type == 'user'])>
                @php
                    $users = DB::table('users')->get();
                @endphp
                @foreach ($users as $item)      
                    <div class="flex items-center space-y-2">
                        @php
                            $chk = false; 
                            if ($type == 'user' && $id == $item->id) {
                                $chk = true;
                            }
                        @endphp
                        
                        <x-text-input :checked="$chk" type="checkbox" name="user[]" value="{{$item->id}}" />
                        <x-input-label class="pl-3 text-md" value="{{$item->name}}" />                
                    </div>
                @endforeach
            </div>
            {{-- <input type="hidden" name="target{{$type}}" value="{{$id}}"> --}}

            <div @class(["p-2 rounded border", 'hidden' => $type == 'role'])>
                @php
                    $userRoles= $user->getRoleNames();
                    $roles = DB::table('roles')->get();
                    // print_r($userRoles->contains('admin'));
                @endphp
                @foreach ($roles as $item)
                    <div class="flex items-center space-y-2">
                        @php
                            $chk = false;
                            if ($type == 'role' && $id == $item->id) {
                                $chk = true;
                            }else{
                                $chk = $userRoles->contains($item->name);
                            }
                        @endphp
                        <x-text-input :checked="$chk" type="checkbox" name="role[]" value="{{$item->name}}" />
                        <x-input-label class="pl-3 text-md" value="{{$item->name}}" />
                        
                    </div>
                @endforeach
            </div>
        </div>
        <x-hr />
        <x-primary-button>
            Save
        </x-primary-button>
    </form>
</div>