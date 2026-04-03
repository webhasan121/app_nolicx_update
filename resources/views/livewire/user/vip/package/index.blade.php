<div>
    <x-dashboard.page-header>
        VIP Packages
    </x-dashboard.page-header>


    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.inner>
                <div style="display: grid; grid-template-columns:repeat(auto-fit, 220px); grid-gap:20px; justify-content:center">
                    @foreach ($vips as $item)
                        <x-vip-cart :$item />
                    @endforeach
                </div>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>
   
</div>
