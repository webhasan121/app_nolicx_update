<div>
    {{-- The best athlete wants his opponent at his best. --}}
    <x-dashboard.section >
        <x-dashboard.section.header>
            <x-slot name="title">
                <div class="flex justify-between">
                    <div>
                        Open {{Str::ucfirst($upgrade)}} Shop
                    </div>
                    <x-nav-link-btn href="{{route('upgrade.vendor.index', ['upgrade' => $upgrade])}}" class="">
                        <i class="pr-2 fas fa-list"></i> All
                    </x-nav-link-btn>
                </div>
            </x-slot>

            <x-slot name="content">
                Request to set-up a {{Str::ucfirst($upgrade)}} shop. Shop allows you to sell your products to other Users. It allows you to reach a wider audience and increase your sales potential.
            </x-slot>
        </x-dashboard.section.header>
    </x-dashboard.section>

    <form wire:submit.prevent="store" method="post">
        @csrf
        @include('user.pages.profile-upgrade.vendor.partials.basic')

        {{-- <x-dashboard.section>
            <x-dashboard.section.inner>
                <x-primary-button>
                    submit
                </x-primary-button>
            </x-dashboard.section.inner>
        </x-dashboard.section> --}}
    </form>
</div>
