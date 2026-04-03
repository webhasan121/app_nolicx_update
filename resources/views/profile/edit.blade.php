<div>
  {{-- Because she competes with no one, no one can compete with her. --}}
  <x-dashboard.container>
    <x-dashboard.section>
      <x-dashboard.section.header>
        <x-slot name="title" >{{ __('Profile Update') }}</x-slot>
        <x-slot name="content" >
          <p class="text-sm md:text-base" >{{ __('Update your account\'s profile information and email address.') }}</p>
        </x-slot>
      </x-dashboard.section.header>

      <x-dashboard.section.inner>
        <form wire:submit="updateProfile" class="mt-6 space-y-6" >
          <div class="flex flex-col lg:flex-row justify-between gap-8" >
            {{-- Left Block --}}
            <div class="relative w-full lg:w-1/3 bg-green-50 rounded-md shadow-md p-4 space-y-4 lg:space-y-6" >
              {{-- Block Header --}}
              <div class="mb-4 border-b border-gray-200 pb-2">
                <h3 class="text-lg font-semibold text-gray-700" >{{ __('Personal Information') }}</h3>
                <p class="text-sm text-gray-500 mt-1" >{{ __('Update your basic profile details.') }}</p>
              </div>

              {{-- Name --}}
              <div class="relative" >
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input wire:model="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
              </div>

              {{-- Email --}}
              <div class="relative" >
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input wire:model="email" type="email" class="mt-1 block w-full" required autocomplete="email" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                  <div>
                    <p class="text-sm mt-2 text-gray-800" >
                      <span>{{ __('Your email address is unverified.') }}</span>

                      <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" >
                        <span>{{ __('Click here to re-send the verification email.') }}</span>
                      </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                      <p class="mt-2 font-medium text-sm text-green-600" >
                        <span>{{ __('A new verification link has been sent to your email address.') }}</span>
                      </p>
                    @endif
                  </div>
                @endif
              </div>

              {{-- Phone --}}
              <div class="relative" >
                <x-input-label for="phone" :value="__('Phone')" />
                <x-text-input wire:model="phone" type="text" class="mt-1 block w-full" required autocomplete="phone" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
              </div>

              {{-- Date of Birth --}}
              <div class="relative" >
                <x-input-label for="dob" :value="__('Date of Birth')" />
                <x-text-input wire:model="dob" type="date" class="mt-1 block w-full" autocomplete="dob" />
                <x-input-error class="mt-2" :messages="$errors->get('dob')" />
              </div>

              {{-- Gender --}}
              <div class="relative">
                <x-input-label for="gender" :value="__('Gender')" />
                <select wire:model="gender" class="rounded border-0 ring-1 block mt-1 w-full">
                  <option value="">{{ __('Select Gender') }}</option>
                    @foreach($genders as $key => $value)
                      <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('gender')" />
              </div>
            </div>

            {{-- Right Block --}}
            <div class="relative w-full lg:w-2/3 bg-green-50 rounded-md shadow-md p-4 space-y-4" >
              {{-- Biography --}}
              <div class="relative" >
                <x-input-label for="bio" :value="__('Bio')" />
                <textarea wire:model="bio" rows="6" class="mt-1 block w-full rounded border-0 ring-1 ring-gray-300 focus:ring-indigo-500 focus:outline-none resize-none" placeholder="Write something about yourself..." ></textarea>
                <x-input-error class="mt-2" :messages="$errors->get('bio')" />
              </div>
              <x-hr />

              {{-- Address --}}
              <div class="relative" >
                <x-input-label for="line1" :value="__('Address')" />
                <x-text-input wire:model="line1" type="text" class="mt-1 block w-full" autocomplete="line1" />
                <x-text-input wire:model="line2" type="text" class="mt-4 block w-full" autocomplete="line2" />
                <x-input-error class="mt-2" :messages="$errors->get('line1')" />
                <x-input-error class="mt-2" :messages="$errors->get('line2')" />
              </div>
              <x-hr />

              <div class="grid md:grid-cols-2 gap-4" >
                {{-- Country --}}
                <div class="relative" >
                  <x-input-label for="country" :value="__('Country')" />
                  <select wire:model.live="country" class="rounded border-0 ring-1 block mt-1 w-full" >
                    <option value="" >{{ __('Select Country') }}</option>
                    @foreach($countries as $country)
                      <option value="{{ $country->id }}" >{{ $country->name }}</option>
                    @endforeach
                  </select>
                  <x-input-error class="mt-2" :messages="$errors->get('cocuntry')" />
                </div>

                {{-- State --}}
                <div class="relative" >
                  <x-input-label for="state" :value="__('State')" />
                  <select wire:model.live="state" class="rounded border-0 ring-1 block mt-1 w-full" >
                    <option value="" >{{ __('Select State') }}</option>
                    @foreach($states as $stateItem)
                      <option value="{{ $stateItem->id }}">{{ $stateItem->name }}</option>
                    @endforeach
                  </select>
                </div>

                {{-- City --}}
                <div class="relative" >
                  <x-input-label for="city" :value="__('City')" />
                  <select wire:model.live="city" class="rounded border-0 ring-1 block mt-1 w-full" >
                    <option value="" >{{ __('Select City') }}</option>
                    @foreach($cities as $city)
                      <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                  </select>
                </div>

                {{-- Zip Code --}}
                <div class="relative" >
                  <x-input-label for="zip" :value="__('Zip Code')" />
                  <x-text-input wire:model="zip" type="text" class="mt-1 block w-full" autocomplete="zip" />
                  <x-input-error class="mt-2" :messages="$errors->get('zip')" />
                </div>
              </div>
            </div>
          </div>
            
          <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            <x-action-message class="me-3" on="profile-updated" >{{ __('Saved.') }}</x-action-message>
          </div>
        </form>
      </x-dashboard.section.inner>
    </x-dashboard.section>

    <x-dashboard.section>
      <x-dashboard.section.header>
        <x-slot name="title" >{{ __('Update Password') }}</x-slot>
        <x-slot name="content" >
          <p class="text-sm md:text-base" >{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
        </x-slot>
      </x-dashboard.section.header>

      <x-dashboard.section.inner>
        <div class="grid lg:grid-cols-2 gap-6 mt-6" >
          <div class="relative" >
            <form wire:submit="updatePassword" class="p-4 bg-green-50 rounded-md shadow-md space-y-4">
              {{-- Current Password --}}
              <div class="relative" >
                <x-input-label for="update_password_current_password" :value="__('Current Password')" />
                <x-text-input wire:model="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
              </div>

              {{-- New Password --}}
              <div class="relative" >
                <x-input-label for="update_password_password" :value="__('New Password')" />
                <x-text-input wire:model="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
              </div>

              {{-- Confirm Password --}}
              <div class="relative" >
                <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
                <x-text-input wire:model="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
              </div>

              <div class="flex items-center gap-4" >
                <x-primary-button>{{ __('Save') }}</x-primary-button>
                <x-action-message class="me-3" on="password-updated" >{{ __('Saved.') }}</x-action-message>
              </div>
            </form>
          </div>
          <div class="relative" >
            <div class="p-4 bg-red-50 rounded-md shadow-md space-y-6" >
              {{-- Block Header --}}
              <div class="border-b border-blue-200 pb-2 mb-2">
                <h3 class="text-lg font-semibold text-blue-700">{{ __('Password Rules') }}</h3>
                <p class="text-sm text-blue-600">{{ __('Follow these rules when setting a new password.') }}</p>
              </div>

              {{-- Requirements --}}
              <div class="mt-4">
                <h3 class="font-medium text-gray-700 mb-2">{{ __('Password Requirements:') }}</h3>
                <ul class="list-disc list-inside text-sm lg:text-base text-gray-600 space-y-3">
                  @foreach($passwordRules as $rule)
                    <li>{{ $rule }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        </div>
      </x-dashboard.section.inner>
    </x-dashboard.section>

    <x-dashboard.section>
      <x-dashboard.section.header>
        <x-slot name="title" >{{ __('Delete Account') }}</x-slot>
        <x-slot name="content" >
          <p class="text-sm md:text-base" >{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>
        </x-slot>
      </x-dashboard.section.header>

      <x-dashboard.section.inner></x-dashboard.section.inner>
    </x-dashboard.section>
  </x-dashboard.container>
</div>
