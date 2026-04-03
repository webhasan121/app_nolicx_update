<div>
    <x-dashboard.page-header>
        Slider- {{$slider->name}}
    </x-dashboard.page-header>


    <x-dashboard.container>

        <x-dashboard.section>
            <x-dashboard.section.inner>

                <div class="w-full">

                    @foreach ($slides as $key => $item)
                    <div class="p-3 w-full mb-1 relative rounded border">
                        <div class="md:flex items-start jusitfy-between w-full p-3">

                            <div class="p-2">
                                {{-- {{$slides[$key]['main_title']}} --}}
                                @if ($image[$key]['image'])
                                <img src="{{$image[$key]['image']->temporaryUrl()}}" style="height:150px; width:100%;"
                                    alt="">
                                @else
                                <img src="{{asset('storage/'.$slides[$key]['image'])}}"
                                    style="height:150px; width:100%;" alt="">

                                @endif

                                <div class="relative">
                                    <input type="file" id="slider_image_{{$key}}" accept="jpg, jpeg, png" max="500"
                                        class="absolute hidden border p-1 w-full" wire:model="image.{{$key}}.image">
                                    <label for="slider_image_{{$key}}" class="p-1 rounded border shadow">
                                        <i class="fas fa-upload px-1"></i>
                                    </label>
                                </div>
                                <br>

                                <div class="flex items-center justify-between my-2 border-t border-b py-2">
                                    Background Color
                                    <input type="color" class="w-8 h-8 rounded shadow"
                                        wire:model.live='slides.{{$key}}.action_target'>
                                </div>
                            </div>
                            <div class="p-2 space-y-2">
                                <p class="text-end flex items-center justify-between text-xs">

                                    Title
                                    <input type="color" class="w-8 h-4 rounded mb-1"
                                        wire:model='slides.{{$key}}.title_color'>
                                </p>
                                <textarea rows="3" type="text" wire:model="slides.{{$key}}.main_title"
                                    class="rounded border border-gray-600 w-full" placeholder="Main Title"
                                    placeholder="Main Title"></textarea>
                                {{--
                                <x-text-input type="text" wire:model="slides.{{$key}}.main_title" class="w-full"
                                    placeholder="Main Title" /> --}}
                                {{--
                                <x-text-input type="text" wire:model="slides.{{$key}}.sub_title" class="w-full"
                                    placeholder="Sub Title" /> --}}
                                <p class="text-end flex items-center justify-between text-xs">

                                    Des
                                    <input type="color" class="w-8 h-4 rounded mb-1"
                                        wire:model='slides.{{$key}}.des_color'>
                                </p>
                                <textarea name="" id="" wire:model="slides.{{$key}}.description"
                                    class="w-full border border-gray-600 rounded" rows="3"
                                    placeholder="Description"></textarea>

                                <hr class="my-2" />
                                <p class="text-xs">Action Button</p>
                                <x-text-input type="text" wire:model="slides.{{$key}}.action_text" class="w-full"
                                    placeholder="Active Text" />
                                <x-text-input type="text" wire:model="slides.{{$key}}.action_url" class="w-full"
                                    placeholder="Active URL" />
                            </div>

                        </div>

                        <x-hr />
                        <div class="flex justify-start items-center space-x-2">

                            <x-danger-button class="" wire:click="deleteSlides({{$slides[$key]['id']}})">
                                <i class="fas fa-trash"></i>
                            </x-danger-button>
                            <x-primary-button :key="$key" class=""
                                wire:click="updateSlides({{$key}},{{$slides[$key]['id']}})">
                                <i class="fas fa-save pr-2 "></i> save
                            </x-primary-button>
                        </div>
                    </div>
                    @endforeach


                </div>
                <div class="flex justify-end space-x-2">
                    <x-primary-button wire:click="addNewSlides"> <i class="fas fa-plus pr-2"></i> Slides
                    </x-primary-button>
                    {{-- <x-primary-button wire:click="update"> update </x-primary-button> --}}
                </div>
            </x-dashboard.section.inner>
        </x-dashboard.section>

    </x-dashboard.container>
</div>