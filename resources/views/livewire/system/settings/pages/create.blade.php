<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">

    <x-dashboard.page-header>
        Create A New Page
        <br>
        <x-nav-link href="{{route('system.pages.index')}}" class=""> 
            <i class="fas fa-angle-left pr-2"></i> Back 
        </x-nav-link>
    </x-dashboard.page-header>

    <x-dashboard.container>
        <form wire:submit.prevent="createPage">
            
            <div class="flex justify-between items-start">

                <div>
                    <x-dashboard.section class="w-full">
                        <x-input-field inputClass="w-full" label="Page Name" wire:model.live="name" name="name" error="name" />
                        <div class="flex items-center">
                            Page URL : https://nolicx.com/pages/
                            <x-text-input wire:model.live="slug" name="slug" class="border-0" />
                        </div>
                    </x-dashboard.section>
                    
                    <x-dashboard.section >
                        <x-input-field inputClass="w-full" label="Page Title" wire:model="title" name="title" error="title" />
                        <x-input-field inputClass="w-full" label="Page Keyword" name="keyword" wire:model="keyword" error="keyword" />
                        <textarea name="description" id="description" placeholder="Description " class="w-full rounded" rows="3"></textarea>    
                        <hr>
                        
                        <div style="width: 300px; height:100px">
                            @if ($thumbnail)
                                @if ( Str::startsWith($thumbnail, 'pages') )
                                    <img src="{{asset('storage/'.$thumbnail) }}" style="width: 300px; height:100px" alt="" class="border rounded ">
                                @else
                                    <img src="{{$thumbnail->temporaryUrl()}}" style="width: 300px; height:100px" alt="" class="border rounded ">
                                @endif
                            @endif
                        </div>
                        <div class="relative w-full">
                            <p class="text-xs"> 300 x 100 thumbnail for social media share </p>
                            <input type="file" wire:model.live="thumbnail" name="" class="absolute hidden top-0" id="thumbnail">
                            <label for="thumbnail">
                                <i class="fas fa-upload p-2 mt-1 border rounded shadow"></i>
                            </label>
                        </div>
                        @error('thumbnail')
                            <p class="text-red-900"> {{$message}} </p>
                        @enderror
                        <x-hr/>
                    </x-dashboard.section>

                </div>
                
                <x-dashboard.section style="width: 300px">
                    <x-dashboard.section.header>
                        <x-slot name="title">
                            Other Pages
                        </x-slot>
                        <x-slot name="content">
                            Edit and Update other pages
                        </x-slot>
                    </x-dashboard.section.header>

                    <x-dashboard.section.inner>
                        @foreach ($pages as $item)
                      
                            <a href="{{route('system.pages.create', ['page' => $item->slug])}}" class="flex justify-between items-center border-b py-2 px-1" :active="$page == $item->slug" >
                                <div>
                                    <i class="fas fa-globe pr-2"></i>
                                    {{$item->name}}
                                </div>
                                <i class="fas fa-angle-right"></i>
                            </a>
                            
                        @endforeach
                    </x-dashboard.section.inner>
                    <x-nav-link-btn href="{{route('system.pages.create')}}">
                        <i class="fas fa-plus pr-2"></i> Page
                    </x-nav-link-btn>
                </x-dashboard.section>
            </div>

        
            <main wire:ignore>
                <trix-toolbar id="my_toolbar"></trix-toolbar>
                <div class="more-stuff-inbetween"></div>
                <input type="hidden" name="content" id="my_input" wire:model.live="products.description" value="{{$content}}" >
                <trix-editor toolbar="my_toolbar" input="my_input"></trix-editor>
            </main>
            <x-hr/>
            <x-primary-button>
                <i class="fas fa-save pr-2"></i> Save & Update
            </x-primary-button>
        </form>
    </x-dashboard.container>

    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    @script
    <script>
        document.querySelector("trix-editor").addEventListener('trix-change', ()=> {
            @this.set('content', document.querySelector("#my_input").value);            
        })
    </script>
    @endscript
</div>
