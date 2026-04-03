<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="">
        <div class="w-auto mx-auto px-2 space-y-6">
            <div class="sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    {{-- @include('profile.partials.update-profile-information-form') --}}
                    @livewire('profile.update-profile-information-form')
                </div>
            </div>

            <div class="sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    {{-- @include('profile.partials.update-password-form') --}}
                    @livewire('profile.update-password-form')
                </div>
            </div>

            <div class="sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    {{-- @include('profile.partials.delete-user-form') --}}
                    @livewire('profile.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
