<?php

use Livewire\Volt\Component;
use App\Molel\user_has_refs;


new class extends Component {
    //
    public $users, $user, $cref;

    public function mount()
    {
        $this->users = $this->user->toArray();
    }
    public function save()
    {
        $this->user->update([
            'name' => $this->users['name'],
            'email' => $this->users['email'],
        ]);

        if ($this->cref) {
            $reffArray = user_has_refs::all('ref', 'user_id');
            $reference = $this->cref;
            $reff = $reffArray->where('ref', $reference)->first();

            if ($reff) {
                # code...
                $this->user->reference_accepted_at = Carbon::now();
                $this->user->reference = $this->cref;
                $this->user->save();
            }
        }

        $this->reset(['cref']);
        // $this->getData();
        $this->dispatch('success', "Updated!");
    }
}; 
?>

<div>
    {{-- {{$users->name}} --}}
    <form wire:submit.prevent="save">

        <div class=" m-0">
            
 
            <x-input-file label="User Name" error="name" name="name" >
                <x-text-input type="text" class="w-full" wire:model.live="users.name"/>
            </x-input-file>
            <x-hr/>
            <x-input-file label="User Email" error="email" name="email" >
                <x-text-input type="text" class="w-full" wire:model.live="users.email"/>
            </x-input-file>
            <x-hr/>
            <x-input-file label="User Reference" error="reference" name="reference" >
                @if (!empty($users['reference']))
                    Accept ref by <strong> {{$user->getReffOwner?->owner?->name ?? "Not Found"}} </strong>
                @endif
                <x-text-input :disabled="true" type="text" class="w-full" wire:model.live="users.reference"/>
                <div class="p-2 rounded border border-slate-600 mt-3 shadow-sm">
                    {{-- <x-primary-button wire:click="addReference" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Add Reference</x-primary-button> --}}
                    <div class=" items-center my-2 border p-2 rounded ">
                        <x-input-label for="new_ref">Custom Ref</x-input-label>
                        <x-text-input type="text"  placeholder="Write custom referred code" id="new_ref" wire:model.live="cref" />
                    </div>
                    <hr>
                    <div class="flex items-start my-2">
                        <x-text-input type="checkbox" id="reference" wire:model.live="cref" value="{{config('app.ref')}}" style="width:25px; height:25px; margin-right:25px" />
                        <div>
                            <p class="bold font-bold fw-bold m-0" for="reference">Set Default Admin Ref</p>
                            <h6>
                                In case of set the admin default ref, please check the box.
                            </h6>
                        </div>
                    </div>
                </div>
                <x-hr/>
                <x-primary-button>
                    Update User
                </x-primary-button>
            </x-input-file>

        </div>
        {{-- <button type="submit" class="btn btn-primary">Update User</button> --}}
    </form>
</div>
