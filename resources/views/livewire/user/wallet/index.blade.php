<div>

    <x-dashboard.container>

        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Your Wallet
                </x-slot>>
                <x-slot name="content">
                    <div class="md:flex justify-between items-center">
                        <div class="text-2xl font-bold text-indigo-900"> Available Balance
                            {{auth()->user()->abailCoin()}} TK </div>

                        <div class="flex">

                            {{-- @if (auth()->user()->hasRole('reseller') || auth()->user()->hasRole('vendor') ||
                            auth()->user()->hasRole('system'))
                            <x-nav-link-btn
                                class="ring-1 bg-indigo-900 text-white px-2 rounded-lg border-0 uppercase font-bold"
                                href="{{route('user.wallet.diposit')}}">Deposit</x-nav-link-btn>
                            @endif --}}
                            {{-- <x-primary-button href="">Deposit</x-primary-button> --}}
                            {{-- <x-primary-button @click.prevent="$dispatch('open-modal', 'depositModal')">Deposit
                            </x-primary-button> --}}
                            <x-nav-link-btn class="ring-1 px-2 border-0 rounded-lg uppercase font-bold"
                                href="{{route('user.wallet.withdraw')}}">Withdraw</x-nav-link-btn>
                        </div>


                    </div>
                </x-slot>
            </x-dashboard.section.header>
        </x-dashboard.section>

        <x-dashboard.section>
            Todays Earning
            <x-dashboard.section.inner>
                <div class="flex items-start justify-start space-x-3 spacy-y-3 flex-wrap">

                    {{-- overview --}}
                    <div class="space-y-3 w-48">
                        <div class="rounded-lg p-3 shadow-md">
                            <div>
                                <div class=" ">Task</div>
                            </div>

                            <div class="text-lg pt-2 font-bold text-indigo-900">{{$task->coin ?? "0"}} TK</div>
                            <div class="text-xs">
                                <a wire:navigate href="{{route('user.wallet.tasks')}}" class="text-gray-600">View
                                    All</a>
                            </div>
                        </div>
                    </div>


                    <div class="space-y-3 w-48">
                        <div class="rounded-lg p-3 shadow-md">
                            <div>
                                <div class=" ">Earn Comission</div>
                            </div>

                            <div class="text-lg pt-2 font-bold text-indigo-900">{{$comission}} TK</div>
                            <div class="text-xs">
                                <a wire:navigate href="{{route('user.wallet.earn-comissions')}}"
                                    class="text-gray-600">View All</a>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 w-48">
                        <div class="rounded-lg p-3 shadow-md">
                            <div>
                                <div class=" ">Cut Comission</div>
                            </div>

                            <div class="text-lg pt-2 font-bold text-indigo-900">{{$cut}} TK</div>
                            <div class="text-xs">
                                <a wire:navigate href="{{route('user.wallet.earn-comissions', ['nav' => 'system'])}}"
                                    class="text-gray-600">View All</a>
                            </div>
                        </div>
                    </div>


                    <div class="space-y-3 w-48">
                        <div class="rounded-lg p-3 shadow-md">
                            <div>
                                <div class=" ">VIP Reffer</div>
                            </div>

                            <div class="text-lg pt-2 font-bold text-indigo-900">{{$reffer}} TK</div>
                            <div class="text-xs">
                                <a href="{{route('user.wallet.reffer')}}" class="text-gray-600">View All</a>
                            </div>
                        </div>
                    </div>
                </div>
            </x-dashboard.section.inner>
        </x-dashboard.section>


        <x-dashboard.section>
            Withdraws Requests
            <x-dashboard.section.inner>
                No Withdraw Info Found !
            </x-dashboard.section.inner>
        </x-dashboard.section>

    </x-dashboard.container>
</div>