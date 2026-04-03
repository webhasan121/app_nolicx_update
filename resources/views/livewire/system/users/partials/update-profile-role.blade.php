<?php

use Livewire\Volt\Component;
use Spatie\Permission\Models\Role;


new class extends Component {
    public $user, $userRoles = [], $roles;


    public function mount() 
    {
        $this->userRoles  = $this->user->getRoleNames()->toArray();
        $this->roles = role::all();


        // dd($this->userRoles);
    }


    public function save() 
    {
        // dd($this->userRoles);
        // $this->user->removeRole($this->userRoles);
        $this->user->syncRoles($this->userRoles);    
        $this->dispatch('refres');
        $this->dispatch('success', 'Role Attached');
    }
    
    
}; 

?>

<div>
    <form wire:submit.prevent="save">
        <div>   
            <x-input-file label="User Role" error="role" name="role" >
                <div class="flex">
                    @foreach ($roles as $item)
                        <div class="flex items-center p-3 border shadow-sm">
                      
                            <x-text-input class="m-0" type="checkbox" wire:model.live="userRoles" value="{{$item->name}}" />
                            <x-input-label class="m-0 p-0 pl-3 text-md" value="{{$item->name}}" />
                            
                        </div>
                    @endforeach
                </div>
                <x-hr/>
                <x-primary-button>
                    save
                </x-primary-button>
            </x-input-file>
        </div>
    </form>
</div>
