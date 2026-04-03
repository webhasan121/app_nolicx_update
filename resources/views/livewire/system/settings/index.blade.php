<div>
  {{-- The Master doesn't talk, he acts. --}}
  <x-dashboard.page-header>{{ __('Settings') }}</x-dashboard.page-header>

  <x-dashboard.container>
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6" >
      {{-- Page Setup --}}
      <x-dashboard.section>
        <x-dashboard.section.header>
          <x-slot name="title" >
            <div class="flex items-center justify-between" >
              <div>{{ __('Page Setup') }}</div>
            </div>
          </x-slot>

          <x-slot name="content" >
            <span>{{ __('Setup your necessary pages from here. add, edit and delete.') }}</span>
          </x-slot>
        </x-dashboard.section.header>

        <x-dashboard.section.inner>
          <x-nav-link-btn href="{{route('system.pages.index')}}" class="">
            <span>{{ __('Go To Page Setup') }}</span>
          </x-nav-link-btn>
        </x-dashboard.section.inner>
      </x-dashboard.section>

      {{-- Manage Branch --}}
      <x-dashboard.section>
        <x-dashboard.section.header>
          <x-slot name="title" >
            <div class="flex items-center justify-between" >
              <div>{{ __('Branch Management') }}</div>
            </div>
          </x-slot>

          <x-slot name="content" >
            <span>{{ __('Setup your necessary branches from here. add, edit and delete.') }}</span>
          </x-slot>
        </x-dashboard.section.header>

        <x-dashboard.section.inner>
          <x-nav-link-btn href="{{route('system.branches.index')}}" class="" >
            <span>{{ __('Manage Branch') }}</span>
          </x-nav-link-btn>
        </x-dashboard.section.inner>
      </x-dashboard.section>

      <x-dashboard.section>
        <x-dashboard.section.header>
          <x-slot name="title">
            <div class="flex items-center justify-between">
              <div>{{ __('Queue Setup') }}</div>
            </div>
          </x-slot>

          <x-slot name="content">
            <span>{{ __('Start your queue for your system. This will help you to manage your queue system.') }}</span>
          </x-slot>
        </x-dashboard.section.header>

        <x-dashboard.section.inner>
          @if ($isQueueRunning)
            <div class="text-green-500" >{{ __('Queue is running.') }}</div>
          @else
            <x-primary-button wire:click='startQueue' class="">
              <span>{{ __('Start Queue') }}</span>
            </x-primary-button>
          @endif
        </x-dashboard.section.inner>
      </x-dashboard.section>

      <x-dashboard.section>
        <x-dashboard.section.header>
          <x-slot name="title" >
            <div class="flex items-center justify-between" >
              <div>{{ __('Geolocation Setup') }}</div>
            </div>
          </x-slot>

          <x-slot name="content">
            <span>{{ __('Setup your rider targeted area from here. also edit and delete your gelolocation names. Country, State and City.') }}</span>
          </x-slot>
        </x-dashboard.section.header>

        <x-dashboard.section.inner>
          <x-nav-link-btn href="{{route('system.geolocations.index')}}" class="">
            {{ __('Go To Setup') }}
          </x-nav-link-btn>
          {{-- <div class="flex items-center gap-2" >
            <x-nav-link-btn href="{{route('system.geolocations.countries')}}" >
              <span>{{ __('Countries') }}</span>
            </x-nav-link-btn>
            <x-nav-link-btn href="{{route('system.geolocations.states')}}" >
              <span>{{ __('States') }}</span>
            </x-nav-link-btn>
            <x-nav-link-btn href="{{route('system.geolocations.cities')}}" >
              <span>{{ __('Cities') }}</span>
            </x-nav-link-btn>
            <x-nav-link-btn href="{{route('system.geolocations.area')}}" >
              <span>{{ __('Target Area') }}</span>
            </x-nav-link-btn> --}}
          </div>
        </x-dashboard.section.inner>
      </x-dashboard.section>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" >
      <x-dashboard.section>
        <x-dashboard.section.header>
          <x-slot name="title">
            <div class="flex items-center justify-between">
              <div>{{ __('Support Email') }}</div>
            </div>
          </x-slot>

          <x-slot name="content" >
            <span>{{ __('Update support email from here') }}</span>
          </x-slot>
        </x-dashboard.section.header>

        <x-dashboard.section.inner>
          <div class="relative" >
            <label>{{ __('Support Email') }}</label>
            <div class="flex justify-between items-center gap-4" >
              <input type="email" wire:model.defer="support_mail" class="border p-2 rounded-md w-full" />
              <button wire:click="updateEmail" class="px-4 py-2 bg-green-500 text-white rounded-md" >
                <span>{{ __('Save') }}</span>
              </button>
            </div>
          </div>
        </x-dashboard.section.inner>
      </x-dashboard.section>

      <x-dashboard.section>
        <x-dashboard.section.header>
          <x-slot name="title" >
            <div class="flex items-center justify-between" >
              <div>{{ __('WhatsApp') }}</div>
            </div>
          </x-slot>

          <x-slot name="content" >
            <span>{{ __('Update whatsapp number from here') }}</span>
          </x-slot>
        </x-dashboard.section.header>

        <x-dashboard.section.inner>
          <div class="relative" >
            <label>{{ __('WhatsApp Number') }}</label>
            <div class="flex justify-between items-center gap-4" >
              <input type="text" wire:model.defer="whatsapp_no" class="border p-2 rounded-md w-full" />
              <button wire:click="updateWhatsapp" class="x-4 py-2 bg-green-500 text-white rounded-md" >
                <span>{{ __('Save') }}</span>
              </button>
            </div>
          </div>
        </x-dashboard.section.inner>
      </x-dashboard.section>

      <x-dashboard.section>
        <x-dashboard.section.header>
          <x-slot name="title" >
            <div class="flex items-center justify-between" >
              <div>{{ __('DBID No.') }}</div>
            </div>
          </x-slot>

          <x-slot name="content" >
            <span>{{ __('Update DBID no. from here') }}</span>
          </x-slot>
        </x-dashboard.section.header>

        <x-dashboard.section.inner>
          <div class="relative" >
            <label>{{ __('DBID No.') }}</label>
            <div class="flex justify-between items-center gap-4" >
              <input type="text" wire:model.defer="dbid_no" class="border p-2 rounded-md w-full" />
              <button wire:click="updateDBIDNo" class="px-4 py-2 bg-green-500 text-white rounded-md" >
                <span>{{ __('Save') }}</span>
              </button>
            </div>
          </div>
        </x-dashboard.section.inner>
      </x-dashboard.section>

      <x-dashboard.section>
        <x-dashboard.section.header>
          <x-slot name="title" >
            <div class="flex items-center justify-between" >
              <div>{{ __('Trade License No.') }}</div>
            </div>
          </x-slot>

          <x-slot name="content" >
            <span>{{ __('Update trade license from here') }}</span>
          </x-slot>
        </x-dashboard.section.header>

        <x-dashboard.section.inner>
          <div class="relative" >
            <label>{{ __('Trade License No.') }}</label>
            <div class="flex justify-between items-center gap-4" >
              <input type="text" wire:model.defer="trade_license" class="border p-2 rounded-md w-full" />
              <button wire:click="updateTradeLicense" class="px-4 py-2 bg-green-500 text-white rounded-md" >
                <span>{{ __('Save') }}</span>
              </button>
            </div>
          </div>
        </x-dashboard.section.inner>
      </x-dashboard.section>

      <x-dashboard.section>
        <x-dashboard.section.header>
          <x-slot name="title" >
            <div class="flex items-center justify-between" >
              <div>{{ __('Playstore') }}</div>
            </div>
          </x-slot>

          <x-slot name="content" >
            <span>{{ __('Update playstore app url from here') }}</span>
          </x-slot>
        </x-dashboard.section.header>

        <x-dashboard.section.inner>
          <div class="relative" >
            <label>{{ __('Playstore App URL') }}</label>
            <div class="flex justify-between items-center gap-4" >
              <input type="url" wire:model.defer="playstore_link" class="border p-2 rounded-md w-full" />
              <button wire:click="updatePlaystoreLink" class="px-4 py-2 bg-green-500 text-white rounded-md" >
                <span>{{ __('Save') }}</span>
              </button>
            </div>
          </div>
        </x-dashboard.section.inner>
      </x-dashboard.section>
    </section>
  </x-dashboard.container>
</div>