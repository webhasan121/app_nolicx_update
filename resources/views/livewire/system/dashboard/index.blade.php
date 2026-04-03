<div x-init="$wire.getOverview()">

    <x-dashboard.container>

        <div class="w-full text-md rounded-md mb-3 p-3">
            Welcome Back ! {{auth()->user()->name}}
            <p class="text-xs">
                Quick review what's goint on your store.
            </p>
        </div>

        <p class="mb-2 text-xs">
            Overall Details
        </p>
        <x-dashboard.overview.section>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Admins
                </x-slot>
                <x-slot name="content">

                    {{$adm}}

                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Vendors
                </x-slot>
                <x-slot name="content">

                    <div>
                        {{$vd}} / {{$avd}}
                    </div>

                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Resellers
                </x-slot>
                <x-slot name="content">

                    <div>
                        {{$rs}} / {{$ars}}
                    </div>

                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Riders
                </x-slot>
                <x-slot name="content">

                    <div>
                        {{$ri}} / {{$ari}}
                    </div>

                </x-slot>
            </x-dashboard.overview.div>
            <x-dashboard.overview.div>
                <x-slot name="title">
                    Users
                </x-slot>
                <x-slot name="content">

                    <div>
                        {{$userCount}}
                    </div>

                </x-slot>
            </x-dashboard.overview.div>

            <x-dashboard.overview.div>
                <x-slot name="title">
                    <div class="flex">
                        Products
                        <x-nav-link href="">
                            view
                        </x-nav-link>
                    </div>
                </x-slot>
                <x-slot name="content">

                    <div>
                        {{$vp}}
                    </div>

                </x-slot>
            </x-dashboard.overview.div>

            <x-dashboard.overview.div>
                <x-slot name="title">
                    Category
                </x-slot>
                <x-slot name="content">

                    <div>
                        {{$cat}}
                    </div>

                </x-slot>
            </x-dashboard.overview.div>
        </x-dashboard.overview.section>
        <x-hr />

        <div>
            {{-- <p class="text-xs mb-2">Todays Overview</p>
            <x-dashboard.overview.section>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Customer's
                    </x-slot>
                    <x-slot name="content">
                        23
                    </x-slot>
                </x-dashboard.overview.div>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Order
                    </x-slot>
                    <x-slot name="content">
                        5
                    </x-slot>
                </x-dashboard.overview.div>

                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Vendor
                    </x-slot>
                    <x-slot name="content">
                        23
                    </x-slot>
                </x-dashboard.overview.div>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Reseller
                    </x-slot>
                    <x-slot name="content">
                        234
                    </x-slot>
                </x-dashboard.overview.div>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Rider
                    </x-slot>
                    <x-slot name="content">
                        34
                    </x-slot>
                </x-dashboard.overview.div>
                <x-dashboard.overview.div>
                    <x-slot name="title">
                        Sales
                    </x-slot>
                    <x-slot name="content">
                        343452 TK
                    </x-slot>
                </x-dashboard.overview.div>
            </x-dashboard.overview.section> --}}

        </div>
    </x-dashboard.container>
</div>