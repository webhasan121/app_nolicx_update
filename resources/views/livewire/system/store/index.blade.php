<div>
    <x-dashboard.page-header>
        Coin Store
    </x-dashboard.page-header>

    <x-dashboard.container>

        <x-dashboard.section>
            <x-dashboard.section.inner>

                <div>
                    @livewire('system.store.coin-store', key('coin-store'))
                </div>

            </x-dashboard.section.inner>
        </x-dashboard.section>

        <div class="">
            <x-dashboard.section>
                @livewire('system.store.coast-store', key('coast-store'))
            </x-dashboard.section>

            <x-dashboard.section>
                @livewire('system.store.donation-store', key('donation-store'))
            </x-dashboard.section>

        </div>

    </x-dashboard.container>

</div>