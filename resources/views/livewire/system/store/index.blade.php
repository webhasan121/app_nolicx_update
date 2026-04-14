<div>
  <x-dashboard.page-header>{{ __('Coin Store - ') . now()->format('F Y') }}</x-dashboard.page-header>

  <x-dashboard.container>
    {{-- Store KPI Widgets --}}
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-6" >
      @foreach ($widgets as $widget)
        <x-dashboard.overview.div>
          <x-slot name="title">{{ $widget['label'] }}</x-slot>
          <x-slot name="content">{{ $widget['value'] }}</x-slot>
        </x-dashboard.overview.div>
      @endforeach
    </section>
  </x-dashboard.container>

  <x-hr />

  <x-dashboard.container>

    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6 mb-6" >
      <div class="relative bg-white rounded-md shadow-md p-6" >
        @livewire('system.store.coin-store', key('coin-store'))
      </div>
      <div class="grid grid-cols-2 gap-6" >
        <div class="relative bg-white rounded-md shadow-md p-6" >
          @livewire('system.store.coast-store', key('coast-store'))
        </div>
        <div class="relative bg-white rounded-md shadow-md p-6" >
          @livewire('system.store.donation-store', key('donation-store'))
        </div>
      </div>
    </section>

  </x-dashboard.container>

  <x-hr />

  <x-dashboard.container>

    <div class="flex gap-4 mt-6" >
      @foreach($tabs as $tab)
        <button wire:click="setTab('{{ $tab }}')" @class([ 'px-3 py-2 rounded-md', 'bg-blue-500 text-white' => $activeTab === $tab, 'bg-gray-200 text-gray-700' => $activeTab !== $tab ]) >{{ ucfirst($tab) }}</button>
      @endforeach
      {{-- <button wire:click="setTab('commissions')">Commissions</button>
      <button wire:click="setTab('withdrawals')">Withdrawals</button> --}}
    </div>

    @if($activeTab === 'commissions')
      <x-dashboard.section>
        <x-dashboard.section.header>
          <x-slot name="title" >
            <div class="flex justify-between items-center border-b pb-4" >
              <h4>{{ __('Distributed Commissions') }}</h4>
              <div class="relative" >
                @if($targetStore?->generate === false)
                  <button wire:click="distribute" wire:loading.attr="disabled" class="inline-block bg-blue-500 hover:bg-blue-600 rounded-md px-4 pb-1" >
                    <span class="text-sm text-white" >{{ __('Distribute') }}</span>
                  </button>
                @else
                  <div class="inline-block bg-blue-500 hover:bg-blue-600 rounded-md px-4 pb-1" >
                    <span class="text-sm text-white" >{{ __('Generated') }}</span>
                  </div>
                @endif
              </div>
            </div>
          </x-slot>
          <x-slot name="content"></x-slot>
        </x-dashboard.section.header>

        <x-dashboard.section.inner>
          <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm" >
            <table class="min-w-full divide-y divide-gray-200 text-sm" >
              <thead class="bg-gray-50" >
                <tr>
                  @foreach($columns1 as $key => $column)
                    <th class="px-4 py-3 text-left font-semibold text-gray-600" >
                      <strong>{{ $column }}</strong>
                    </th>
                  @endforeach
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 bg-white" >
                @forelse($commissions as $key => $commission)
                  @php
                    $store = \Carbon\Carbon::create(
                      $commission->storeInfo->year,
                      $commission->storeInfo->month
                    )->format('M-Y');
                  @endphp
                  <tr class="hover:bg-gray-50 transition" >
                    <td class="px-4 py-3 font-medium text-gray-700" >{{ $loop->iteration . __('.') }}</td>
                    <td class="px-4 py-3 font-medium text-gray-700" >
                      <strong>{{ $commission->user->name }}</strong>
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-700" >{{ $store }}</td>
                    <td class="px-4 py-3 font-medium text-gray-700" >{{ number_format($commission->amount, 2) . __('/-') }}</td>
                    <td class="px-4 py-3 font-medium text-gray-700" >{{ number_format($commission->range, 2) . __('%') }}</td>
                    <td class="px-4 py-3 font-medium text-gray-700" >{{ $commission->info }}</td>
                    <td class="px-4 py-3 font-medium text-gray-700" >
                      <span>-</span>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                      <span>{{ __('No histories found.') }}</span>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="mt-4" >{{ $commissions->links('livewire.pagination-links') }}</div>
        </x-dashboard.section.inner>
      </x-dashboard.section>
    @endif

    @if($activeTab === 'withdrawals')
      <x-dashboard.section>
        <x-dashboard.section.header>
          <x-slot name="title" >
            <h4>{{ __('Withdrawal History') }}</h4>
          </x-slot>
          <x-slot name="content"></x-slot>
        </x-dashboard.section.header>

        <x-dashboard.section.inner>
          <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm" >
            <table class="min-w-full divide-y divide-gray-200 text-sm" >
              <thead class="bg-gray-50" >
                <tr>
                  @foreach($columns2 as $key => $column)
                    <th class="px-4 py-3 text-left font-semibold text-gray-600" >
                      <strong>{{ $column }}</strong>
                    </th>
                  @endforeach
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 bg-white" >
                @forelse($withdrawals as $key => $withdraw)
                  <tr class="hover:bg-gray-50 transition" >
                    <td class="px-4 py-3 font-medium text-gray-700" >{{ $loop->iteration . __('.') }}</td>
                    <td class="px-4 py-3 font-medium text-gray-700" >{{ $withdraw->user->name }}</td>
                    <td class="px-4 py-3 font-medium text-gray-700" >{{ number_format($withdraw->store_req, 2) . '/-' }}</td>
                    <td class="px-4 py-3 font-medium text-gray-700" >{{ number_format($withdraw->maintenance_fee, 2) . '/-' }}</td>
                    <td class="px-4 py-3 font-medium text-gray-700" >{{ number_format($withdraw->server_fee, 2) . '/-' }}</td>
                    <td class="px-4 py-3 font-medium text-gray-700" >{{ $withdraw->pay_by }}</td>
                    <td class="px-4 py-3 font-medium text-gray-700" >{{ $withdraw->status === 1 ? 'Confirm' : 'Pending' }}</td>
                    <td class="px-4 py-3 font-medium text-gray-700" >{{ $withdraw->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3 font-medium text-gray-700" >-</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                      <span>{{ __('No histories found.') }}</span>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="mt-4" >{{ $withdrawals->links('livewire.pagination-links') }}</div>
        </x-dashboard.section.inner>
      </x-dashboard.section>
    @endif

  </x-dashboard.container>
</div>
