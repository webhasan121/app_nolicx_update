<div>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">

    {{-- Success is as dangerous as failure. --}}
    <x-dashboard.page-header>
        VIP Package Update 
        <br>
        <x-nav-link href="{{route('system.vip.index')}}">  <i class="fa-solid fa-up-right-from-square me-2"></i> Back To Packages</x-nav-link>
    </x-dashboard.page-header>

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Package Basic Info
                </x-slot>
                <x-slot name="content"></x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <form action="">
                    <div class="st">
                        {{$st}}
                    </div>
                    <div class="flex flex-wrap space-x-3">
                        <div class="w-md py-2 border-b">
                            <div class="text-sm">
                                Package Name
                            </div>
                            <div class="text-md">
                                <x-text-input wire:model.live="data.name" class="py-1 border-0 w-full sahdow-0" />
                            </div>
                        </div>
                        <div class="w-md py-2 border-b">
                            <div class="text-sm">
                                Package Price
                            </div>
                            <div class="text-md">
                                <x-text-input min="10" type='number' wire:model.live="data.price" class="py-1 border-0 w-full sahdow-0" />
                            </div>
                        </div>
                        <div class="w-md py-2 border-b">
                            <div class="text-sm">
                                Package Task Duration (Minute)
                            </div>
                            <div class="text-md">
                                <x-text-input min="1" type="number" max="60" wire:model.live="data.countdown" class="py-1 border-0 w-full sahdow-0" />
                            </div>
                        </div>
                        <div class="w-md py-2 border-b">
                            <div class="text-sm">
                                Package Daily Coin
                            </div>
                            <div class="text-md">
                                <x-text-input min="1" type="number" wire:model.live="data.coin" class="py-1 border-0 w-full sahdow-0" />
                            </div>
                        </div>
                        <div class="w-md py-2 border-b">
                            <div class="text-sm">
                                Package Monthly Coin
                            </div>
                            <div class="text-md">
                                <x-text-input min="1" type="number" wire:model.live="data.m_coin" class="py-1 border-0 w-full sahdow-0" />
                            </div>
                        </div>
                        <div class="w-md py-2 border-b">
                            <div class="text-sm">
                                Referrer Coin 
                            </div>
                            <div class="text-md">
                                <x-text-input min="1" type="number" wire:model.live="data.ref_owner_get_coin" class="py-1 border-0 w-full sahdow-0" />
                            </div>
                        </div>
                    </div>
                    <br/>
                </form>
            </x-dashboard.section.inner>
        </x-dashboard.section>

       <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex justify-between items-center">
                        <h4>Payment Option</h4>
                        <x-secondary-button type="button" wire:click="addPaymentOption" class="btn btn-sm btn-info">
                            <i class="fas fa-plus"></i>
                        </x-secondary-button>
                    </div>

                </x-slot>
                <x-slot name="content">
                    Manage your package payment options
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <div class="flex flex-wrap ">

                    @foreach ($payoption as $index => $option)
                        <div class="p-2 rounded border my-2 bg-white shadow border-sky-800 space-x-2">
                            <div class="p-0 m-0">
                                <div class="py-2 border-b">
                                    <x-input-label class="py-1" for="pay_type_{{ $index }}" value="Payment Method" />
                                    <x-text-input type="text" 
                                        class="border-0 py-1" 
                                        placeholder="Payment Method"
                                        wire:model="payoption.{{ $index }}.pay_type"
                                        id="pay_type_{{ $index }}" />
                                </div>
                                <div class="">
                                    <x-input-label class="py-1" for="pay_to_{{ $index }}" value="Payment Number" />
                                    <x-text-input type="text" 
                                        class="border-0 py-1" 
                                        placeholder="Payment To"
                                        wire:model="payoption.{{ $index }}.pay_to"
                                        id="pay_to_{{ $index }}" />
                                </div>
                                <br>
                                {{-- <x-input-field name="abc" label="Payment Method" wire:model="paymentOptions.{{$index}}" />
                                <x-input-field name="abc" label="Payment Number / AC " wire:model="paymentOptions.{{$index}}" /> --}}
                                <x-danger-button type="button" 
                                        class="btn border btn-sm" 
                                        wire:click="removePaymentOption({{ $index }})">
                                    <i class="fas fa-trash"></i>
                                </x-danger-button>
                            </div>

                        </div>
                    @endforeach
                
                </div>
                <br>
                 <main wire:ignore>
                    <trix-toolbar id="my_toolbar"></trix-toolbar>
                    <div class="more-stuff-inbetween"></div>
                    <input type="hidden" name="content" id="my_input" wire:model.live="data.description" value="{{$data['description']}}" >
                    <trix-editor toolbar="my_toolbar" input="my_input"></trix-editor>
                </main>
                <br>
                <x-primary-button wire:click="store">Update</x-primary-button>
            </x-dashboard.section.inner>
       </x-dashboard.section>

       <x-dashboard.section>
            <x-nav-link href=''> <i class="fa-solid fa-up-right-from-square me-2"></i> Task Statatistics</x-nav-link>
            <x-nav-link href=''> <i class="fa-solid fa-up-right-from-square me-2"></i> VIP Users</x-nav-link>
            <x-nav-link href=''> <i class="fa-solid fa-up-right-from-square me-2"></i> Earnings</x-nav-link>
            <x-nav-link href=''> <i class="fa-solid fa-up-right-from-square me-2"></i> </x-nav-link>
       </x-dashboard.section>
    </x-dashboard.container>


    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    @script
    <script>
        document.querySelector("trix-editor").addEventListener('trix-change', ()=> {
            @this.set('data.description', document.querySelector("#my_input").value);            
        })
    </script>
    @endscript
</div>
