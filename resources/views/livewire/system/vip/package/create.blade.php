<div>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">

    {{-- The Master doesn't talk, he acts. --}}
    <x-dashboard.page-header>
        VIP
        <br>
        <div>
            <x-nav-link :href="route('system.vip.index')" :active="request()->routeIs('system.vip.*')"> Package </x-nav-link>
            <x-nav-link :href="route('system.vip.users')" :active="request()->routeis('system.vip.')"> User </x-nav-link>
        </div>
    </x-dashboard.page-header>

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Add VIP Package
                </x-slot>
                <x-slot name="content">
                    add more vip package to your system with specific condition.
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <form wire:submit.prevent="store" >

                    <div>
                        <x-input-field label="Package Name" name="name" wire:model.lazy="name" class="md:flex" inputClass="w-full" error="name"  />
                        
                        <div class="md:flex">
                            <x-input-field label="Package Price" name="price" wire:model.lazy="price" type="number"  error="price" type="number"  />
                            <x-input-field label="Duration (Minute)" wire:model.lazy="countdown" error="countdown" name="countdown" />
                            {{-- <x-input-field label="Daily Reward" wire:model.lazy="coin" error="coin" name="coin" />
                            <x-input-field label="Montyly Reward" wire:model.lazy="m_coin" error="m_coin" name="m_coin" /> --}}

                        </div>
                        <div class="md:flex">
                            {{-- <x-input-field label="Package Price" name="price" wire:model.lazy="price" type="number"  error="price" type="number"  />
                            <x-input-field label="Duration (Minute)" wire:model.lazy="countdown" error="countdown" name="countdown" /> --}}
                            <x-input-field label="Daily Reward" wire:model.lazy="coin" error="coin" name="coin" />
                            <x-input-field label="Monthly Reward" wire:model.lazy="m_coin" error="m_coin" name="m_coin" />

                        </div>
                        <x-hr/>
                        <div class="md:flex">
                            <x-input-field label="By Referred Reward" wire:model.live="ref_owner_get_coin" name="ref_owner_get_coin" error="ref_owner_get_coin" />
                            {{-- <x-input-field label="Accept Referred Reward" wire:model.live="owner_get_coin" name="owner_get_coin" error="owner_get_coin" /> --}}
                        </div>
                        <x-hr/>

                    </div>
                   

                    <div class="p-0 my-4 mx-0 border p-2" x-data>
                        <div class="flex justify-between items-center">
                            <h4>Payment Option</h4>
                            <x-secondary-button type="button" wire:click="addPaymentOption" class="btn btn-sm btn-info">
                                <i class="fas fa-plus"></i>
                            </x-secondary-button>
                        </div>

                        <div class="paymentDiv">
                            @foreach ($paymentOptions as $index => $option)
                                <div class="p-2 rounded border my-2 bg-white">
                                    <div class="md:flex p-0 m-0">
                                        <div class="">
                                            <label class="py-1" for="pay_type_{{ $index }}">Payment Method</label>
                                            <input type="text" 
                                                class="form-control" 
                                                placeholder="Payment Method"
                                                wire:model="paymentOptions.{{ $index }}.pay_type"
                                                id="pay_type_{{ $index }}">
                                        </div>
                                        <div class="">
                                            <label class="py-1" for="pay_to_{{ $index }}">Payment Number/AC</label>
                                            <input type="text" 
                                                class="form-control" 
                                                placeholder="Payment To"
                                                wire:model="paymentOptions.{{ $index }}.pay_to"
                                                id="pay_to_{{ $index }}">
                                        </div>

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
                    </div>



                    <x-hr/>
                   
                    <div class="p-2 bg-white rounded">

                        <x-input-label class="py-1" for="desciption">Description</x-input-label>
                        {{-- <textarea wire:model="description" class="rounded border-0 shadow w-full" id="summernote" placeholder="write your package description" rows="10"></textarea> --}}
                         <main wire:ignore>
                            <trix-toolbar id="my_toolbar"></trix-toolbar>
                            <div class="more-stuff-inbetween"></div>
                            <input type="hidden" name="content" id="my_input" wire:model.live="description" value="{{$description}}" >
                            <trix-editor toolbar="my_toolbar" input="my_input"></trix-editor>
                        </main>
                    </div>
        

                    <x-hr/>
                    <x-primary-button>save</x-primary-button>

                </form>
            </x-dashboard.section.inner>

        </x-dashboard.section>
    </x-dashboard.container>

        <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    @script
    <script>
        document.querySelector("trix-editor").addEventListener('trix-change', ()=> {
            @this.set('description', document.querySelector("#my_input").value);            
        })
    </script>
    @endscript
</div>
