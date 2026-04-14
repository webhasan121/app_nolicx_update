<div x-init="$wire.getDeta">

  <div class=" rounded w-full bg-white text-center" x-loading.disabled >
    <div class="border border-green-900 rounded md:flex justify-between items-start p-2" >
      <div class="px-3 py-1 lg:p-3 bold text-center flex justify-between items-center md:block" >
        <div class="fs-5 fw-bold text-start w-full" >
          <a href="javascript:void(0);" class="flex items-center" >
            <i class="fas fa-store fs-6 p-2" ></i>
            <span>{{ __('Comission Store') }}</span>
          </a>
        </div>
        <div class="relative mt-2" >
          <button wire:click="withdraw1" class="inline-block bg-blue-500 hover:bg-blue-600 rounded-md px-3 py-1">
            <span class="text-sm text-white font-bold" >{{ __('Withdraw') }}</span>
          </button>
        </div>
      </div>

      <div class="px-3 text-center py-1 lg:p-3  text-lg fw-bold text-green-900">
        {{-- {{$store->coin}} --}}
        <div class="font-bold px-2 border rounded" >{{ $store }}</div>

        <div class="py-2" >
          <div class="flex justify-center items-center text-xs" >
            <div class="text-start text-red-900" style="color:red" >
              {{-- {{$withdraw? $withdraw->sum('amount') : '0'}} --}}
              {{$give}}
              <i class="fas fa-long-arrow-alt-up" ></i>
            </div>
            <div class="px-3" >{{ __('|') }}</div>
            <div class="text-green-900" style="color:green" >
              <i class="fas fa-long-arrow-alt-down" ></i>
              {{ $take }}
              {{-- {{$deposit ? $deposit->sum('amount') : "0"}} --}}
            </div>
          </div>
        </div>
      </div>

      <div class="px-3 py-1 lg:p-3 text-end" >
        <div class="flex items-center text-xs" >
          <div class="text-start text-red-900" style="color:red" >
            {{-- {{$withdraw? $withdraw->count() : '0'}} --}}
            <i class="fas fa-long-arrow-alt-up" ></i>
          </div>
          <div class="px-3" >{{ __('|') }}</div>
          <div class="text-green-900" style="color:green" >
            <i class="fas fa-long-arrow-alt-down" ></i>
            {{-- {{$deposit ? $deposit->count() : "0"}} --}}
          </div>
        </div>
      </div>

    </div>
  </div>

  <x-modal name="withdrawModal1" maxWidth="md" >
    <div class="p-3" >{{ __('Withdraw') }}</div>
    <hr class="my-2" >
    <div class="p-4" >
      <form wire:submit.prevent="submit" >
        <div class="grid grid-cols-2 gap-6" >
          <div class="relative w-full" >
            <x-input-label value="Payment Method" />
            <select wire:model.live="method" class="py-2 rounded-md w-full" >
              <option value=""> -- Choose -- </option>
              @foreach($paymentMethod as $item)
                <option value="{{ $item }}" >{{ $item }}</option>
              @endforeach
            </select>
          </div>

          <div class="relative" >
            <x-input-label value="Withdraw Amount" />
            <x-text-input type="number" wire:model.live="amount" class="w-full" placeholder="Enter withdraw amount" />
            @error('amount')
              <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
          </div>
        </div>

        @if($method === 'Bank')
          <div class="relative my-4" >
            <x-input-label value="Bank Account" />
            <x-text-input type="text" wire:model.live="bankAccount" class="w-full" placeholder="Enter bank account" />
            @error('bankAccount')
              <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
          </div>

          <div class="relative my-4" >
            <x-input-label value="Account Holder Name" />
            <x-text-input type="text" wire:model.live="accountholder" class="w-full" placeholder="Account holder name" />
            @error('accountholder')
              <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
          </div>

          <div class="grid grid-cols-2 gap-6 my-4" >
            <div class="relative" >
              <x-input-label value="Bank Branch" />
              <x-text-input type="text" wire:model.live="bankBranch" class="w-full" placeholder="Enter bank branch" />
              @error('bankBranch')
                <span class="text-red-500 text-sm">{{ $message }}</span>
              @enderror
            </div>

            <div class="relative" >
              <x-input-label value="Swift Code" />
              <x-text-input type="text" wire:model.live="swiftCode" class="w-full" placeholder="Enter swift code" />
              @error('swiftCode')
                <span class="text-red-500 text-sm">{{ $message }}</span>
              @enderror
            </div>
          </div>

          <div class="relative my-4" >
            <x-input-label value="Account Number" />
            <x-text-input type="text" wire:model.live="accountNumber" class="w-full" placeholder="Enter account number" />
            @error('accountNumber')
              <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
          </div>
        @else
          <div class="relative my-4" >
            <x-input-label value="Phone Number" />
            <x-text-input type="number" wire:model.live="phone" class="w-full" placeholder="Enter phone number" />
            @error('phone')
              <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
          </div>
        @endif

        <div class="relative my-4" >
          <x-input-label value="Remarks" />
          <textarea wire:model.live="remarks" rows="3" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" ></textarea>
        </div>

        <div class="flex justify-end" >
          <x-primary-button type="submit" >{{ __('Submit') }}</x-primary-button>
        </div>
      </form>
    </div>
  </x-modal>

</div>
