<div class="max-w-3xl mx-auto p-6 bg-white shadow rounded-xl" >

    @if($hasApplied)
        <div class="text-center py-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
                Developer Application Submitted
            </h2>

            <p class="text-gray-600">
                You have already applied for developer access.
            </p>

            <div class="mt-4">
                <span class="px-4 py-2 rounded bg-yellow-100 text-yellow-800">
                    Status: {{ ucfirst($developerRequest->status ?? 'pending') }}
                </span>
            </div>

            @if($developerRequest->status === 'approved')
                <div class="mt-6 text-green-700 font-semibold">
                    🎉 Your developer access has been approved!
                </div>
            @elseif($developerRequest->status === 'rejected')
                <div class="mt-6 text-red-600">
                    ❌ Unfortunately your application was rejected.
                </div>
            @endif
        </div>
    @else
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Developer Partnership Form</h2>

        @if (session()->has('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        <form wire:submit.prevent="submit" class="space-y-5">

            <div>
                <label class="block text-sm font-semibold mb-1">Full Name</label>
                <input type="text" wire:model.defer="name" class="w-full border rounded px-4 py-2" readonly />
                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-5" >
                <div>
                    <label class="block text-sm font-semibold mb-1">Email</label>
                    <input type="email" wire:model.defer="email" class="w-full border rounded px-4 py-2" readonly />
                    @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Phone</label>
                    <input type="text" wire:model.defer="phone" class="w-full border rounded px-4 py-2" readonly />
                    @error('phone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Message</label>
                <textarea wire:model.defer="message" rows="4" class="w-full border rounded px-4 py-2"></textarea>
            </div>

            <button class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                Submit Application
            </button>

        </form>
    @endif
    
</div>
