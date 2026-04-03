<div>
    {{-- The best athlete wants his opponent at his best. --}}

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex justify-between items-center">
                        <div>
                            {{ __('Categories') }} <span class="text-sm text-gray-500">({{ $categories->count() }})</span> 
                        </div>
                        <x-primary-button class="ml-2" wire:click="$dispatch('open-modal', 'category_create')">
                            <i class="fas fa-plus pr-2"></i>{{ __(' Category') }}
                        </x-primary-button>
                    </div>
                </x-slot>
                <x-slot name="content">
                    {{ __('Manage your categories and subcategories here.') }}
                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>   
                {{-- <div class="flex items-center justify-start items-center mb-4">
                    <input wire:model.live="collapse" type="checkbox" style="width:20px; height:20px" />
                    <label class="text-sm text-gray-600 ml-2">{{ __('Collase Open') }}</label>
                </div>
                <x-hr/> --}}
                @foreach ($categories as $item)
                    <x-dashboard.chr :item="$item" :key="$item->id" :loop="$loop->iteration" :collapse="$collapse" />
                @endforeach
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>

    <x-modal name="category_create" :title="__('Create Category')">
        <livewire:reseller.categories.create />
    </x-modal>
</div>
