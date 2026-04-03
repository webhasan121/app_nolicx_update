<div>
    {{-- The Master doesn't talk, he acts. --}}
    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex justify-between items-start w-full" >

                        {{ __('Edit Category') }}
                        <x-primary-button class="ml-2" wire:click="$dispatch('open-modal', 'category_create')">
                            <i class="fas fa-plus pr-2"></i>{{ __(' Category') }}
                        </x-primary-button>
                    </div>
                </x-slot>
                <x-slot name="content">
                    {{ __('Modify the details of the selected category.') }}
                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>
                <form wire:submit.prevent="updateCategory" class="space-y-4 p-3 border rounded-md" style="max-width: 350px; margin: auto;">
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Category Name') }}</label>
                        <input type="text" id="name" wire:model.live="category.name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        @error('category.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Category Name') }}</label>
                        <input type="text" id="name" wire:model.defer="category.slug" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        @error('category.slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        @if ($newImage)
                            <img src="{{$newImage->temporaryUrl()}}" width="100" height="100" class="border shadow rounded" alt="">
                        @else
                            <img src="{{asset('storage/'.$category['image'])}}" width="100" height="100" class="border shadow rounded" alt="">
                        @endif
                        
                        <label for="image" class=" rounded border p-2 inline-block text-sm font-medium text-end text-gray-700"> <i class="fas fa-upload pr-2"></i> Upload </label>
                        <input type="file" id="image" wire:model.live="newImage" class="hidden mt-1 block w-full border rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        
                        @error('category.slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <x-hr/>

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Parent') }} </label>
                        <select wire:model.defer="category.belongs_to" id="parent_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">{{ __('None') }}</option>
                            @foreach ($categories as $children)
                                <option @if( $children->id == $category['belongs_to']) selected @endif value="{{ $children->id }}">{{ $children->name }}</option>
                                
                                @if ($children->children()->count() > 0)
                                    @foreach ($children->children as $child)
                                        <option @if( $child->id == $category['belongs_to']) selected @endif value="{{ $child->id }}">-- {{ $child->name }}</option>


                                        @if ($child->children()->count() > 0)
                                            @foreach ($child->children as $subChild)
                                                <option @if( $subChild->id == $category['belongs_to']) selected @endif value="{{ $subChild->id }}">---- {{ $subChild->name }}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                        @error('category.parent_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <x-hr/>

                    <div class="flex justify-end">
                        <x-primary-button type="submit">
                            <i class="fas fa-save pr-2"></i>{{ __('Save Changes') }}
                        </x-primary-button>
                        {{-- <x-secondary-button class="ml-2" wire:click="$dispatch('close-modal', 'category_edit')">
                            <i class="fas fa-times pr-2"></i>{{ __('Cancel') }}
                        </x-secondary-button> --}}
                    </div>

                </form>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>

    <x-modal name="category_create" :title="__('Create Category')">
        <livewire:reseller.categories.create />
    </x-modal>
</div>
