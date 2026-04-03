<div>
    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex items-center justify-between">
                        <div>
                            VIP Package
                        </div>
                        <x-primary-button x-on:click="$dispatch('open-modal', 'purchase-package-modal')" >Purchase</x-primary-button>
                    </div>
                </x-slot>
                <x-slot name="content">
                    your vip package, visit packages and purchase one.
                </x-slot>
            </x-dashboard.section.header>
        </x-dashboard.section>


        <div>
            <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        Your Subscription
                    </x-slot>
                    <x-slot name="content">
                        You has subscribe our bellow package. To veiw details click on 'VIEW DETAILS' button, on package cart.  </strong>
                    </x-slot>
                </x-dashboard.section.header>
                <x-dashboard.section.inner>
                    @includeIf('components.package-request', ['isRequestedAccepted' => $vip])
                </x-dashboard.section.inner>
            </x-dashboard.section>
            {{-- <x-package-request :isRequestedAccepted="$vip/> --}}

            @if (empty($vip) || !$vip)
                <div class="bold">
                    No Active Package Found
                </div>
            @endif

        </div>

        <x-modal name="purchase-package-modal">
            <div class="">
                @livewire('user.vip.package.index')
            </div>
        </x-modal>
    </x-dashboard.container>
</div>
