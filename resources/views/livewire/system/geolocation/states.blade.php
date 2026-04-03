<div>
  {{-- Be like water. --}}
  <x-dashboard.page-header>{{ __('Geolocation - States') }}</x-dashboard.page-header>

  <x-dashboard.container>
    <div class="flex items-center gap-2">
      <x-nav-link-btn href="{{route('system.geolocations.countries')}}">Countries</x-nav-link-btn>
      <x-nav-link-btn href="{{route('system.geolocations.states')}}">States</x-nav-link-btn>
      <x-nav-link-btn href="{{route('system.geolocations.cities')}}">Cities</x-nav-link-btn>
      <x-nav-link-btn href="{{route('system.geolocations.area')}}">Areas</x-nav-link-btn>
    </div>
  </x-dashboard.container>

  <x-dashboard.container>
    <x-dashboard.section>
      <x-dashboard.section.header>
        <x-slot name="title" >
          <div class="flex justify-between items-center" >
            <h2>{{ __('States') }}</h2>
            <x-primary-button wire:click="create" >
              <i class="fas fa-plus mr-2" ></i>
              <span>{{ __('Add New') }}</span>
            </x-primary-button>
          </div>
        </x-slot>
        <x-slot name="content">
        </x-slot>
      </x-dashboard.section.header>

      <x-dashboard.section.inner>
        <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm" >
          <table class="min-w-full divide-y divide-gray-200 text-sm" >
            <thead class="bg-gray-50" >
              <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-600" >{{ __('SL No.') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600" >{{ __('Name of State') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600" >{{ __('Country Name') }}</th>
                <th class="px-4 py-3 text-center font-semibold text-gray-600" >{{ __('ISO2') }}</th>
                <th class="px-4 py-3 text-center font-semibold text-gray-600" >{{ __('ISO3') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600" width="100" >{{ __('A/C') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white" >
              @forelse($states as $key => $state)
                <tr class="hover:bg-gray-50 transition" >
                  <td class="px-4 py-3 font-medium text-gray-700" >{{ ($states->total() - ($states->firstItem() + $loop->index - 1)) . '.' }}</td>
                  <td class="px-4 py-3 text-gray-700" >{{ $state->name }}</td>
                  <td class="px-4 py-3 text-gray-700" >{{ $state->country->name }}</td>
                  <td class="px-4 py-3 text-center" >
                    <span class="px-2 py-1 rounded bg-blue-50 text-blue-700 text-xs font-semibold" >{{ $state->iso2 }}</span>
                  </td>
                  <td class="px-4 py-3 text-center" >
                    <span class="px-2 py-1 rounded bg-purple-50 text-purple-700 text-xs font-semibold" >{{ $state->iso3166_2 }}</span>
                  </td>
                  <td class="px-4 py-3 text-center space-x-2" >
                    <button wire:click="edit({{ $state->id }})"
                      class="inline-flex justify-center items-center p-2 rounded-lg bg-indigo-600 text-white text-xs font-medium hover:bg-indigo-700 w-7 h-7 transition">
                      <i class="fas fa-edit" ></i>
                    </button>

                    <button wire:click="delete({{ $state->id }})" onclick="confirm('Delete this state?') || event.stopImmediatePropagation()"
                      class="inline-flex justify-center items-center p-2 rounded-lg bg-red-600 text-white text-xs font-medium hover:bg-red-700 w-7 h-7 transition">
                      <i class="fas fa-trash-alt" ></i>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                    <span>{{ __('No countries found.') }}</span>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="mt-4" >{{ $states->links('livewire.pagination-links') }}</div>
      </x-dashboard.section.inner>
    </x-dashboard.section>
  </x-dashboard.container>

  <x-modal name="stateModal" maxWidth="md" >
    <div class="p-3" >{{ ($isEdit ? __('Update') : 'Add New') . __(' State') }}</div>
    <hr class="my-2" >
    <div class="p-4" >
      <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="space-y-6" >
        <div class="relative" >
          <x-input-label value="Name of State" />
          <x-text-input type="text" wire:model.live="name" class="w-full" placeholder="Enter state Name" />
          @error('name')
            <span class="text-red-500 text-sm">{{ $message }}</span>
          @enderror
        </div>
        <div class="relative" >
          <x-input-label value="Country Name" />
          <select wire:model.live="country_id" class="py-1 rounded-md" id="selectCountry" >
            <option value=""> -- Country -- </option>
            @foreach ($countries as $item)
              <option value="{{$item->id}}"> {{$item->name}} </option>
            @endforeach
          </select>
        </div>
        <div class="grid grid-cols-2 gap-6" >
          <div class="relative" >
            <x-input-label value="Code (ISO2)" />
            <x-text-input type="text" wire:model.live="iso2" class="w-full" placeholder="Enter iso2 code" />
            @error('iso2')
              <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
          </div>
          <div class="relative" >
            <x-input-label value="Code (ISO3)" />
            <x-text-input type="text" wire:model.live="iso3166_2" class="w-full" placeholder="Enter iso3 code" />
            @error('iso3166_2')
              <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="flex justify-end" >
          <x-primary-button type="submit" >{{ __('Save State') }}</x-primary-button>
        </div>
      </form>
    </div>
  </x-modal>
</div>
