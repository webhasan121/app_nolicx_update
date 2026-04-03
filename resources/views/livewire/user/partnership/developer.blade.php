<div class="max-w-3xl p-6 mx-auto bg-white shadow rounded-xl" >

    @if($hasApplied)
        <div class="py-10 text-center">
            <h2 class="mb-2 text-2xl font-bold text-gray-800">
                Developer Application Submitted
            </h2>

            <p class="text-gray-600">
                You have already applied for developer access.
            </p>

            <div class="mt-4">
                <span class="px-4 py-2 text-yellow-800 bg-yellow-100 rounded">
                    Status: {{ ucfirst($developerRequest->status ?? 'pending') }}
                </span>
            </div>

            @if($developerRequest->status === 'approved')
                <div class="mt-6 font-semibold text-green-700">
                    🎉 Your developer access has been approved!
                </div>
            @elseif($developerRequest->status === 'rejected')
                <div class="mt-6 text-red-600">
                    ❌ Unfortunately your application was rejected.
                </div>
            @endif
        </div>
    @else
        <h2 class="mb-6 text-2xl font-bold text-gray-800">Developer Partnership Form</h2>

        @if (session()->has('success'))
            <div class="px-4 py-2 mb-4 text-green-800 bg-green-100 rounded">
                {{ session('success') }}
            </div>
        @endif
        <form wire:submit.prevent="submit" class="space-y-5">

            <div>
                <label class="block mb-1 text-sm font-semibold">Full Name</label>
                <input type="text" wire:model.defer="name" class="w-full px-4 py-2 border rounded" readonly />
                @error('name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-5" >
                <div>
                    <label class="block mb-1 text-sm font-semibold">Email</label>
                    <input type="email" wire:model.defer="email" class="w-full px-4 py-2 border rounded" readonly />
                    @error('email') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold">Phone</label>
                    <input type="text" wire:model.defer="phone" class="w-full px-4 py-2 border rounded" readonly />
                    @error('phone') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block mb-1 text-sm font-semibold">Message</label>
                <textarea wire:model.defer="message" rows="4" class="w-full px-4 py-2 border rounded"></textarea>
            </div>

            <button class="px-6 py-2 text-white bg-indigo-600 rounded hover:bg-indigo-700">
                Submit Application
            </button>

        </form>
    @endif

</div>
