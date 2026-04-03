<div>
    <x-dashboard.page-header>
        User Update
        <br />
        <x-nav-link href="{{route('system.users.view')}}"> <i class="fa-solid fa-up-right-from-square me-2"></i> Users </x-nav-link>
    </x-dashboard.page-header>
    <x-dashboard.container x-data="{nav : 'profile'}">
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    {{$user->name}}
                </x-slot>
                <x-slot name="content">
                    
                    <div>
                        <x-nav-link @class="{nav == 'profile' ? 'active' : ''}" @click="nav = 'profile'">Profile</x-nav-link>
                        <x-nav-link @class="{nav == 'role' ? 'active' : ''}" @click="nav = 'role'" >Permission</x-nav-link>
                    </div>
                </x-slot>
            </x-dashboard.section.header>
        </x-dashboard.section>

        <x-dashboard.section x-show="nav == 'profile'">
            <x-dashboard.section.inner>
                
               @livewire('system.users.partials.update-profile-information', ['user' => $user], key($user->id))
                    
                <x-hr/>
                    <x-input-file label="User Coin" error="coin" name="coin" >
                        <div class="rounded-lg">
                            <x-text-input type="text"  class=" border-0 w-32" disabled wire:model.live="users.coin"/>

                            {{-- click button to show a livewire modal  --}}
                            <div class="p-2 bg-ref-900 rounded border inline-block">    
                                <div class="text-xs">Recharge</div>
                                <form wire:submit.prevent="rechargeUser">
                                    <x-text-input type="number" class="py-1 w-32" wire:model.live="rechargeAmount" />
                                    <x-primary-button>
                                        Apply
                                    </x-primary-button>
                                </form>
                            </div>
                        </div>
                    </x-input-file>
                <x-hr/>
                    
            </x-dashboard.section.inner>
        </x-dashboard.section>


        <x-dashboard.section x-show="nav == 'role'">
            <x-dashboard.section.inner>
                @livewire('system.users.partials.update-profile-role', ['user' => $user], key($user->id))
                <x-hr/>
                <div class="">
                    <x-input-label style="width:250px" class="mb-4">
                        User Permission
                    </x-input-label>
                    @livewire('system.users.partials.update-profile-permission', ['user' => $user], key($user->id))
                </div>
            </x-dashboard.section.inner>
        </x-dashboard.section>
       
        
    </x-dashboard.container>


    <x-modal name="confirmRechargeModal">
        <div class="p-4">
            <div class="text-lg">
                Confirm Recharge
            </div>
            <x-hr/>
            <p class="py-5">
                Are you sure to add {{$rechargeAmount}} TK amount to {{$user->name}}, {{$user->email}}
            </p>
            <x-hr/>
            <div class="flex">
                <x-secondary-button @click="$dispatch('close-modal', 'confirmRechargeModal')">Cancel</x-secondary-button>
                <x-primary-button wire:click="confirmRecharge">Recharge</x-primary-button>
                <x-danger-button wire:click="confirmRefund">Refund</x-danger-button>
            </div>
        </div>
    </x-modal>
   
</div>

