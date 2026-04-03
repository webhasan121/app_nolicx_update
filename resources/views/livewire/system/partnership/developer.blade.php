<div>

  <x-dashboard.page-header>{{ __('Partnership - Developer Access') }}</x-dashboard.page-header>

  <x-dashboard.container>
    <x-dashboard.section>
      <x-dashboard.section.header>
        <x-slot name="title" >{{ __('Developer Access') }}</x-slot>
        <x-slot name="content"></x-slot>
      </x-dashboard.section.header>

      <x-dashboard.section.inner>
        <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm" >
          <table class="min-w-full divide-y divide-gray-200 text-sm" >
            <thead class="bg-gray-50" >
              <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-600" >{{ __('SL No.') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600" >{{ __('Name of User') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600" >{{ __('User Email') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600" >{{ __('Status') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600" >{{ __('Responded By') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600" width="100" >{{ __('A/C') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white" >
              @foreach($applications as $app)
                @php
                  $status = $app->status === 1 ? 'Approved' : ($app->status === 0 ? 'Rejected' : 'Pending');
                @endphp
                <tr class="hover:bg-gray-50 transition" >
                  <td class="px-4 py-3 font-medium text-gray-700" >{{ ($applications->total() - ($applications->firstItem() + $loop->index - 1)) . '.' }}</td>
                  <td class="px-4 py-3 text-gray-700" >{{ $app->user->name }}</td>
                  <td class="px-4 py-3 text-gray-700" >{{ $app->user->email }}</td>
                  <td class="px-4 py-3 text-gray-700" >{{ $status }}</td>
                  <td class="px-4 py-3 text-gray-700" >{{ $app->responder->name ?? '-' }}</td>
                  <td class="px-4 py-3 text-center space-x-2" >
                    @if ($app->status !== null)
                      @if($app->status === 1)
                        <button class="inline-flex justify-center items-center p-2 rounded-lg bg-green-500 text-white text-xs font-medium hover:bg-green-600 w-7 h-7 transition" disabled >
                          <i class="fas fa-check-circle" ></i>
                        </button>
                      @else
                        <button class="inline-flex justify-center items-center p-2 rounded-lg bg-red-500 text-white text-xs font-medium hover:bg-red-600 w-7 h-7 transition" disabled >
                          <i class="fas fa-circle-xmark" ></i>
                        </button>
                      @endif
                      <button wire:click="delete({{ $app->id }})" class="inline-flex justify-center items-center p-2 rounded-lg bg-red-500 text-white text-xs font-medium hover:bg-red-600 w-7 h-7 transition" >
                          <i class="fas fa-trash-alt" ></i>
                      </button>
                    @else
                      <button wire:click="accept({{ $app->id }})" class="inline-flex justify-center items-center p-2 rounded-lg bg-green-500 text-white text-xs font-medium hover:bg-green-600 w-7 h-7 transition" >
                        <i class="fas fa-check" ></i>
                      </button>
                      <button wire:click="reject({{ $app->id }})" class="inline-flex justify-center items-center p-2 rounded-lg bg-red-500 text-white text-xs font-medium hover:bg-red-600 w-7 h-7 transition" >
                        <i class="fas fa-times" ></i>
                      </button>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </x-dashboard.section.inner>
    </x-dashboard.section>
  </x-dashboard.container>
     
</div>
