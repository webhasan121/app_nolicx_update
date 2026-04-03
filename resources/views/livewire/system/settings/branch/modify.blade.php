<div>

  <x-dashboard.page-header>
    {{ __('Update Branch') }}
    <br>
    <x-nav-link href="{{route('system.branches.index')}}" class="" >
      <i class="fas fa-angle-left pr-2" ></i>
      <span>{{ __('Back') }}</span> 
    </x-nav-link>
  </x-dashboard.page-header>

  <x-dashboard.container>
    <form wire:submit.prevent="modify" class="space-y-6" >
      <div class="flex flex-col lg:flex-row gap-8" >
        <div class="w-lg" >
          <x-dashboard.section class="w-full" >
            <div class="grid grid-cols-1 gap-5" >
              {{-- Branch Name --}}
              <div class="relative" >
                <x-input-label for="name" value="Branch Name" />
                <x-text-input id="name" type="text" wire:model.defer="name" class="w-full" placeholder="Test Branch" />
                <x-input-error :messages="$errors->get('name')" />
              </div>

              {{-- Slug --}}
              <div class="relative" >
                <x-input-label for="slug" value="Slug" />
                <x-text-input id="slug" type="text" wire:model.defer="slug" class="w-full" placeholder="test-branch" />
                <x-input-error :messages="$errors->get('slug')" />
              </div>
            </div>
          </x-dashboard.section>
        </div>
        <div class="w-full" >
          <x-dashboard.section class="w-full" >
            <div class="space-y-5" >
              <div class="grid lg:grid-cols-2 gap-5" >
                {{-- Email --}}
                <div class="relative" >
                  <x-input-label for="email" value="Email" />
                  <x-text-input id="email" type="email" wire:model.defer="email" class="w-full" placeholder="branch@example.com"
                  />
                  <x-input-error :messages="$errors->get('email')" />
                </div>

                {{-- Phone --}}
                <div class="relative" >
                  <x-input-label for="phone" value="Phone" />
                  <x-text-input id="phone" type="text" wire:model.defer="phone" class="w-full" placeholder="+8801XXXXXXXXX" />
                  <x-input-error :messages="$errors->get('phone')" />
                </div>
              </div>

              {{-- Address --}}
              <div class="relative" >
                <x-input-label for="slug" value="Address" />
                <x-text-input id="slug" type="text" wire:model.defer="address" class="w-full" placeholder="123 Main Street, Dhaka" />
                <x-input-error :messages="$errors->get('slug')" />
              </div>
            </div>
          </x-dashboard.section>
        </div>
      </div>

      {{-- Actions --}}
      <div class="flex items-center justify-start gap-4 pt-4 border-t" >
        <x-secondary-button type="button" onclick="window.location='{{ route('system.branches.index') }}'" >
          <span>{{ __('Cancel') }}</span>
        </x-secondary-button>

        <x-primary-button type="submit" wire:loading.attr="disabled" >
          <span wire:loading.remove >{{ __('Update Branch') }}</span>
          <span wire:loading >{{ __('Saving...') }}</span>
        </x-primary-button>
      </div>
    </form>
  </x-dashboard.container>
</div>
