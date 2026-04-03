<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <form wire:submit.prevent="save">
        <x-dashboard.container>
            <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        Category
                    </x-slot>
                    <x-slot name="content">
                        Get a new category.
                    </x-slot>
                </x-dashboard.section.header>

                <x-dashboard.section.inner>
                    <x-input-field name="name" class="md:flex" labelWidth="250px" wire:model.live="name" error="name" label="Your Category Name" />
                    <x-hr/>
                    <x-input-file label="Category Image" error="image" >
                        <input type="file" wire:model.live="image" id="">
                    </x-input-file>
                    <x-primary-button>
                        save
                    </x-primary-button>
                </x-dashboard.section.inner>
            </x-dashboard.section>
        </x-dashboard.container>
    </form>
</div>
