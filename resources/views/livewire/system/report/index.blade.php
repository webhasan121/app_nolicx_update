<div>
    {{-- Stop trying to control. --}}
    <x-dashboard.page-header>
        Generate Reports
    </x-dashboard.page-header>
    <div class="flex justify-center items-center w-full">
        <div style="width: 350px" class="border rounded-md p-4 bg-white">
            <x-dashboard.section.inner>
                {{-- @if($nav == 'Deposit')
                @livewire('system.report.deposit-report')
                @elseif($nav == 'Withdraw')
                @livewire('system.report.withdraw-report')
                @elseif($nav == 'Sells')
                @livewire('system.report.sells-report')
                @elseif($nav == 'Vip')
                @livewire('system.report.vip-report')
                @elseif($nav == 'Product')
                @livewire('system.report.product-report')
                @endif --}}

                <div class="mb-2">
                    <p>Report For</p>
                    <select wire:model.live="nav" class="w-full rounded-md">
                        <option value=""> -- Select --</option>
                        <option value="Deposit">Deposit</option>
                        <option value="Withdraw">Withdraw</option>
                        <option value="Sells">Sells</option>
                        <option value="Vip">Vip</option>
                        <option value="Product">Products</option>
                    </select>
                </div>

                <div class="mb-2">
                    <p>From</p>
                    <input type="date" wire:model.live='sdate' class="w-full rounded-md" />
                    @error('sdate')
                    <strong class="text-red-900"> {{$message}} </strong>
                    @enderror
                </div>

                <div class="mb-2">
                    <p>To</p>
                    <input type="date" wire:model.live='edate' class="w-full rounded-md" />
                    @error('edate')
                    <strong class="text-red-900"> {{$message}} </strong>
                    @enderror
                </div>

                <div class="mb-3">
                    <p>ID</p>
                    <input type="text" wire:model.live="sid" placeholder="Optional" class="w-full rounded-md" />
                </div>

                <div class="w-ful text-end">
                    {{-- disabled button when wire:loading --}}
                    <x-primary-button wire:navigate.disabled type="button" wire:click.prevent='generateReport'>
                        Generate
                    </x-primary-button>
                </div>
            </x-dashboard.section.inner>
        </div>
    </div>
</div>