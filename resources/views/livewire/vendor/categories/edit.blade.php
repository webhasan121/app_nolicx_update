<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <x-dashboard.page-header>
        Category Update
    </x-dashboard.page-header>

    <form wire:submit.prevent="update">
        <x-dashboard.container>
            <x-dashboard.section>

                <x-dashboard.section.inner>
                    <x-input-field wire:model.live="targettedForEdit.name" name="targettedForEdit.name" class="md:flex" labelWidth="250px" error="name" label="Your Category Name" />
                    <x-hr/>
                    <x-input-file label="Category Image" error="image" >
                        @if (!$image && $targettedForEdit['image'])
                            <img width="100px" height="100px" src="{{asset('storage/'. $targettedForEdit['image'])}}" alt="">          
                            @endif
                        @if ($image)
                            
                            <img width="100px" height="100px" src="{{$image->temporaryURL()}}" alt="">          
                        @endif
                        <input type="file" wire:model.live="image" id="">
                    </x-input-file>
                    <x-primary-button>
                        update
                    </x-primary-button>
                </x-dashboard.section.inner>
            </x-dashboard.section>
        </x-dashboard.container>
    </form>
</div>
