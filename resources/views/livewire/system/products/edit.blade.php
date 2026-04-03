<div>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">

    {{-- Stop trying to control. --}}
    <x-dashboard.page-header>
        Product Edit
        <br>
        {{--
        <x-dashboard.vendor.products.navigations :nav="$nav" /> --}}
        @include('components.dashboard.vendor.products.navigations')
    </x-dashboard.page-header>


    <x-dashboard.container>

        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex justify-between text-xs">
                        <div>

                            @if ($products['deleted_at'])
                            <div style="color: red; font-weight:bolder">
                                Trashed
                            </div>
                            @else
                            {{$products['status'] ? "Active" : "Drafted"}} | {{
                            Carbon\Carbon::parse($products['created_at'])->diffForHumans()}}
                            @endif

                        </div>
                        <div>
                            @if ($products['deleted_at'])
                            <x-secondary-button type="button" wire:click.prevent="restoreFromTrash">
                                <i class="fa-solid fa-sync mr-2"></i> Restore
                            </x-secondary-button>
                            @else
                            <x-secondary-button type="button" wire:click.prevent="moveToTrash">
                                <i class="fa-solid fa-trash mr-2"></i> Trash
                            </x-secondary-button>
                            @endif
                        </div>
                    </div>
                </x-slot>

                <x-slot name="content">
                    <div class="flex justify-between">
                        <div>

                            <div>
                                <x-image src="{{asset('storage/'.$products['thumbnail'])}}" />
                            </div>
                            <div>
                                {{$products['title'] ?? "N/A"}}
                            </div>

                            <div class="text-sm">
                                Category : <strong> {{$data['category']?->name ?? "N/A"}} </strong>
                            </div>
                        </div>
                        <div>

                            <div class="text-sm">
                                Type :
                                @if ($products['is_resel'])
                                <span class="bg-indigo-900 text-md text-white rounded-lg px-2">
                                    Resel
                                </span>
                                @else
                                <span class="bg-indigo-900 text-md text-white rounded-lg px-2"> Owner </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-dashboard.section.header>
        </x-dashboard.section>

        <form wire:submit.prevent="save">
            <div class="md:flex jusfity-between">
                <x-dashboard.section>
                    <x-dashboard.section.header>
                        <x-slot name="title">
                            Product Basic Info
                        </x-slot>
                        <x-slot name="content">

                        </x-slot>
                    </x-dashboard.section.header>
                    <x-dashboard.section.inner>

                        <x-input-field error='products.name' wire:model.live="products.name" labelWidth="350px"
                            label="Products Name" name="name" inputClass="w-full">
                            {{-- <textarea wire:model.live="products.name" rows="2" id="" class="w-full"></textarea>
                            --}}
                        </x-input-field>
                        <x-input-file labelWidth="200px" class="block" error='products.title' label="Products title"
                            name="title" inputClass="w-full">
                            <textarea wire:model.live="products.title" rows="3" id="" class="w-full rounded"></textarea>
                        </x-input-file>

                        <x-hr />
                        <x-input-file labelWidth="250px" class="md:flex" label="Products Category" error="category">
                            <div class="text-xs">
                                Category : <strong>{{ $data['category']?->name ?? "N/A" }}</strong>. Change to another
                            </div>
                            <select wire:modal="products.category_id" id="">
                                <option value=""> -- Select Category -- </option>
                                @foreach ($categories as $children)
                                <option value="{{$children->id}}"> {{$children->name}} </option>

                                @if (count($children->children) > 0)
                                @foreach ($children->children as $child)
                                <option value="{{$child->id}}"> --{{$child->name}} </option>

                                @if (count($child->children) > 0)
                                @foreach ($child->children as $grandChild)
                                <option value="{{$grandChild->id}}"> ---- {{$grandChild->name}} </option>
                                @endforeach
                                @endif
                                @endforeach

                                @endif
                                @endforeach
                            </select>
                        </x-input-file>
                        <x-hr />
                    </x-dashboard.section.inner>
                </x-dashboard.section>


                <x-dashboard.section>
                    <x-dashboard.section.header>
                        <x-slot name="title">
                            Product Price
                        </x-slot>
                        <x-slot name="content"></x-slot>
                    </x-dashboard.section.header>
                    <x-dashboard.section.inner>

                        <div>
                            <x-input-field class=" mx-1" labelWidht="100px" label="Product Buying Price"
                                wire:model.live="products.buying_price" name="products.buying_price"
                                error="products.buying_price" />
                            <x-input-field class=" mx-1" labelWidht="100px" label="Product Sell Price"
                                wire:model.live="products.price" name="products.price" error="products.price" />
                            <x-input-field class=" mx-1" labelWidht="100px" type="number" label="Product Unite"
                                wire:model.live="products.unit" name="products.unit" error="products.unit" />
                        </div>
                        <x-hr />
                        <div>
                            {{--
                            <x-input-field class="mx-1" label="Product Buying Price"
                                wire:model.live="products.buying_price" name="products.buying_price"
                                error="products.buying_price" /> --}}
                            <x-input-file label="Wish to sell with Discount" name="offer_type" error="offer_type  ">
                                <input type="checkbox" @checked($products['offer_type'])
                                    wire:model.live="products.offer_type" style="width:25px; height:25px" />
                            </x-input-file>
                            <x-input-field wire:transition wire:show="products.offer_type" class="" labelWidth="250px"
                                label="Product Discount Price" wire:model.live="products.discount"
                                name="products.discount" error="products.discount" />
                            {{--
                            <x-input-field class="mx-1" type="number" label="Product Unite"
                                wire:model.live="products.unite" name="products.unite" error="products.unite" /> --}}
                        </div>
                        <x-hr />
                        <div>
                            <x-input-file label="Set to Recomended Products" name="display_at_home"
                                error="display_at_home">
                                <input type="checkbox" @checked($products['display_at_home'])
                                    wire:model.live="products.display_at_home" style="width:25px; height:25px" />
                            </x-input-file>
                        </div>

                    </x-dashboard.section.inner>
                </x-dashboard.section>
            </div>


            {{-- delevery --}}
            <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        Product Delevery
                    </x-slot>
                    <x-slot name="content">
                        Define your product delevery option and charge from here.
                    </x-slot>
                </x-dashboard.section.header>

                <x-dashboard.section.inner>
                    <div class="md:flex justify-between  ">
                        <div>

                            <x-input-file error='products.cod' label="Available Cash-On-Delevery" class="lg:flex"
                                name="cod" inputClass="w-full">
                                <input @checked($products['cod']) wire:model.live="products.cod" type="checkbox"
                                    style="width:25px; height:25px" />
                            </x-input-file>
                            <x-hr />
                            <x-input-file error='products.courier' label="Available Couried Delivery" class="lg:flex"
                                name="courier" inputClass="w-full">
                                <input @checked($products['courier']) wire:model.live="products.courier" type="checkbox"
                                    style="width:25px; height:25px" />
                            </x-input-file>
                            <x-hr />
                            <x-input-file error='products.hand' label="Available Hand-To-Hand Delevery" class="lg:flex"
                                name="hand" inputClass="w-full">
                                <input @checked($products['hand']) wire:model.live="products.hand" type="checkbox"
                                    style="width:25px; height:25px" />
                            </x-input-file>
                        </div>
                        <div>
                            <x-input-field label="Delevery Amount Inside Dhaka"
                                wire:model.live="products.shipping_in_dhaka" name="products.shipping_in_dhaka"
                                class="lg:flex" labelWidth="250px" error="products.shipping_in_dhaka" />
                            <x-hr />
                            <x-input-field label="Normal Delevery Amount" class="lg:flex"
                                wire:model.live="products.shipping_out_dhaka" name="products.shipping_out_dhaka"
                                labelWidth="250px" error="products.shipping_out_dhaka" />
                            <x-hr />
                            <x-input-file label="Shipping Note" error="products.shipping_note"
                                name="products.shipping_note" labelWidth="250px">
                                <textarea wire:model.live="products.shipping_note" id="psn" rows="3"
                                    class="w-full rounded" placeholder="write your shipping note ... "></textarea>
                            </x-input-file>
                        </div>
                    </div>
                </x-dashboard.section.inner>
            </x-dashboard.section>


            {{-- seo --}}
            <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        SEO
                    </x-slot>
                    <x-slot name="content">
                        Setup your product seo from here.
                    </x-slot>
                </x-dashboard.section.header>

                {{-- seo section --}}
                <x-dashboard.section.inner>

                    <x-input-field error='products.keyword' wire:model.live="products.keyword" label="Meta Keyword"
                        name="keyword" class="lg:flex" inputClass="w-full" />
                    <x-input-field error='products.meta_title' wire:model.live="products.meta_title" label="Meta Title"
                        name="title" class="lg:flex" inputClass="w-full" />
                    <x-input-field error='products.meta_tags' wire:model.live="products.meta_tags" label="Meta Tags"
                        name="tags" class="lg:flex" inputClass="w-full" />
                    <x-input-file error='meta.meta_description' label="Meta Description" name="description"
                        class="lg:flex" inputClass="w-full">
                        <textarea wire:model.live="meta.meta_description" class="rounded-md p-2 shadow w-full" rows="4"
                            placeholder="Meta Description ...."></textarea>
                    </x-input-file>
                    <x-input-file error="products.meta_thumbnail" label="Meta Thumbnail" name="thumbnail">
                        <div>
                            @if ($newseothumb)
                            <img src="{{$newseothumb->temporaryUrl()}}" width="100px" height="200px" alt="">
                            @else
                            <img src="{{asset('storage/'.$products['meta_thumbnail'])}}" width="100px" height="200px"
                                alt="">
                            @endif
                        </div>
                        <div class="relative">
                            <p>
                                100 x 200 meta thumbnail
                            </p>
                            <input type="file" wire:model.live="newseothumb" id="newseothumb" class="absolute hidden">
                            <label for="newseothumb">
                                <i class="fas fa-upload px-2"></i>
                            </label>
                        </div>
                    </x-input-file>
                </x-dashboard.section.inner>
            </x-dashboard.section>

            <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        Image Attributes
                    </x-slot>
                    <x-slot name="content">
                        Give your products attributes, product different types, different product color package and
                        quantity.
                    </x-slot>
                </x-dashboard.section.header>
                <x-dashboard.section.inner>
                    <div class="md:flex">
                        <input type="text" wire:model.live='attr.name' placeholder="Name" />
                        <input type="text" wire:model.live='attr.value' placeholder="Value" />
                    </div>
                </x-dashboard.section.inner>
            </x-dashboard.section>

            <x-dashboard.section>
                <div class="md:flex flex-rowreverse justify-between">
                    <x-dashboard.section.header>
                        <x-slot name="title">
                            Image Thumbnail
                        </x-slot>
                        <x-slot name="content">
                            Provide a mendatory thumbnail image for your products. This image consider for the thumbnail
                            for social media platform.

                            <div class="relative">
                                <p>
                                    600 x 600 image thumbnail
                                </p>
                                <x-text-input id="prod_thumb" type="file" wire:model.live="thumb"
                                    class="absolute hidden border p-1" />
                                <label for="prod_thumb" class="p-2 rounded border">
                                    <i class="fas fa-upload"></i>
                                </label>
                            </div>
                        </x-slot>
                    </x-dashboard.section.header>

                    <x-dashboard.section.inner>
                        @if ($products['thumbnail'] && !$thumb)
                        <x-image src="{{asset('storage/'. $products['thumbnail'])}}" />
                        @endif
                        @if ($thumb)
                        <img src="{{$thumb->temporaryUrl()}}" width="100px" height="200px" alt="">
                        @endif
                    </x-dashboard.section.inner>
                </div>
            </x-dashboard.section>

            <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        {{-- <div class="flex"></div> --}}
                        Other Image
                    </x-slot>
                    <x-slot name="content">
                        Other product image that showcase your product. other image mainly display at product details
                        page.
                    </x-slot>
                </x-dashboard.section.header>

                <x-dashboard.section.inner>

                    <div style="display: grid; grid-template-columns:repeat(auto-fit,100px); grid-gap:10px">
                        @foreach ($relatedImage as $item)
                        <div class="p-2 border">
                            <x-image src="{{asset('storage/'. $item['image'])}}" />
                            <button type="button" wire:click="erageOldImage({{$item['id']}})">
                                Erage
                            </button>
                        </div>
                        @endforeach
                    </div>

                    <x-hr />
                    <div style="display: grid; grid-template-columns:repeat(auto-fit,50px); grid-gap:10px">
                        @foreach ($newImage as $ni)

                        <div class="p-2 border rounded">
                            <img src="{{$ni->temporaryUrl()}}" width="50px" height="50px" alt="">
                        </div>

                        @endforeach
                    </div>

                    <div class="relative">
                        <x-text-input type="file" wire:model.live="newImage" id="multi_prod_img"
                            class="absolute hidden border p-1" multiple />
                        <label for="multi_prod_img" class="p-2 border rounded">
                            <i class="fas fa-upload"></i>
                        </label>
                    </div>
                    <div class="text-xs">
                        Please choose all image at once, if you plan to upload multiple image.
                    </div>

                </x-dashboard.section.inner>
            </x-dashboard.section>

            <x-dashboard.section>
                <x-dashboard.section.header>
                    <x-slot name="title">
                        Description
                    </x-slot>
                    <x-slot name="content">
                        Descrive your product as you need.
                    </x-slot>
                </x-dashboard.section.header>
                <x-dashboard.section.inner>
                    <x-input-file label="Description" labelWidth="250px" error="products.description">
                        {{-- <div class="flex flex-wrap items-center p-3 border-b bg-gray-50 gap-2">
                            <button type="button" onclick="format('bold')" class="px-2 py-1 hover:bg-gray-200 rounded"
                                title="Bold"><b>B</b></button>
                            <button type="button" onclick="format('italic')"
                                class="px-2 py-1 hover:bg-gray-200 rounded italic" title="Italic">I</button>
                            <button type="button" onclick="format('underline')"
                                class="px-2 py-1 hover:bg-gray-200 rounded underline" title="Underline">U</button>
                            <button type="button" onclick="format('strikeThrough')"
                                class="px-2 py-1 hover:bg-gray-200 rounded line-through" title="Strike">S</button>
                            <button type="button" onclick="format('insertOrderedList')"
                                class="px-2 py-1 hover:bg-gray-200 rounded" title="Ordered List">OL</button>
                            <button type="button" onclick="format('insertUnorderedList')"
                                class="px-2 py-1 hover:bg-gray-200 rounded" title="Unordered List">UL</button>
                            <button type="button" onclick="format('formatBlock', 'H1')"
                                class="px-2 py-1 hover:bg-gray-200 rounded text-xl" title="Heading 1">H1</button>
                            <button type="button" onclick="format('formatBlock', 'H2')"
                                class="px-2 py-1 hover:bg-gray-200 rounded text-lg" title="Heading 2">H2</button>
                            <button type="button" onclick="addLink()"
                                class="px-2 py-1 hover:bg-gray-200 rounded text-blue-600"
                                title="Insert Link">🔗</button>
                            <button type="button" onclick="removeFormatting()"
                                class="px-2 py-1 hover:bg-gray-200 rounded text-red-600"
                                title="Clear Formatting">🧹</button>
                        </div> --}}
                        {{-- <div id="editor" class="rounded border min-h-[200px] p-4 focus:outline-none"
                            contenteditable="true" placeholder="Write Here ...">
                            <p class="text-gray-700">Start writing here...</p>
                        </div> --}}

                        {{-- <div wire:ignore ">
                            <div id=" editor" class="min-h-[200px] border rounded p-3 bg-white" contenteditable="true"
                            x-init="
                                    $el.innerHTML = @js($description);
                                    $el.addEventListener('input', () => {
                                        Livewire.emit('editorUpdated', $el.innerHTML);
                                    });
                                "></div>
</div> --}}
{{-- <textarea wire:model.live="products.description" class="w-full rounded border-gray-30o"
    placeholder="Describe your own" id="editor" rows="10"></textarea> --}}
<main wire:ignore>
    <trix-toolbar id="my_toolbar"></trix-toolbar>
    <div class="more-stuff-inbetween"></div>
    <input type="hidden" name="content" id="my_input" wire:model.live="description" value="{{$description}}">
    <trix-editor toolbar="my_toolbar" input="my_input"></trix-editor>
</main>


{{-- {!! $description !!} --}}

</x-input-file>
</x-dashboard.section.inner>
</x-dashboard.section>

<x-primary-button>save</x-primary-button>
</form>

</x-dashboard.container>

<script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

@script
<script>
    document.querySelector("trix-editor").addEventListener('trix-change', ()=> {
            @this.set('description', document.querySelector("#my_input").value);            
        })
</script>
@endscript
</div>