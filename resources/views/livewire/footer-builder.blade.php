<div>
    {{-- The best athlete wants his opponent at his best. --}}
    <x-dashboard.page-header>
        Footer Builder
    </x-dashboard.page-header>


    <div>
        <x-secondary-button wire:click="addSection">
            + Add Section
        </x-secondary-button>

        <div class="space-y-6">
            @foreach($layout['sections'] as $sIndex => $section)
                <div class="p-4 border rounded bg-gray-50">
                    <input type="text" wire:model="layout.sections.{{ $sIndex }}.title"
                        class="w-full px-2 py-1 mb-2 border"
                        placeholder="Section Title"/>

                    <div class="grid grid-cols-{{ count($section['columns']) }} gap-4">
                        @foreach($section['columns'] as $cIndex => $col)
                            <div class="p-2 bg-white border rounded">
                                <h4 class="mb-2 font-semibold">Column {{ $cIndex+1 }}</h4>

                                @foreach($col['widgets'] as $wIndex => $widget)
                                    <div class="p-2 mb-2 border rounded">
                                        @if($widget['type'] === 'text')
                                            <textarea wire:model="layout.sections.{{ $sIndex }}.columns.{{ $cIndex }}.widgets.{{ $wIndex }}.content"
                                                    class="w-full px-2 py-1 border" placeholder="Text..."></textarea>
                                        @elseif($widget['type'] === 'link')
                                            <input wire:model="layout.sections.{{ $sIndex }}.columns.{{ $cIndex }}.widgets.{{ $wIndex }}.label"
                                                placeholder="Link Label" class="w-full px-2 py-1 mb-1 border"/>
                                            <input wire:model="layout.sections.{{ $sIndex }}.columns.{{ $cIndex }}.widgets.{{ $wIndex }}.url"
                                                placeholder="Link URL" class="w-full px-2 py-1 border"/>
                                        @elseif($widget['type'] === 'icon')
                                            <input wire:model="layout.sections.{{ $sIndex }}.columns.{{ $cIndex }}.widgets.{{ $wIndex }}.icon"
                                                placeholder="Icon name (e.g., facebook)" class="w-full px-2 py-1 mb-1 border"/>
                                            <input wire:model="layout.sections.{{ $sIndex }}.columns.{{ $cIndex }}.widgets.{{ $wIndex }}.url"
                                                placeholder="Icon URL" class="w-full px-2 py-1 border"/>
                                        @endif
                                    </div>
                                @endforeach

                                <div class="space-x-2">
                                    <button wire:click="addWidget({{ $sIndex }}, {{ $cIndex }}, 'text')" class="text-sm text-blue-500">+ Text</button>
                                    <button wire:click="addWidget({{ $sIndex }}, {{ $cIndex }}, 'link')" class="text-sm text-green-500">+ Link</button>
                                    <button wire:click="addWidget({{ $sIndex }}, {{ $cIndex }}, 'icon')" class="text-sm text-purple-500">+ Icon</button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <x-secondary-button wire:click="addColumn({{ $sIndex }})" class="mt-2 text-green-600">+ Add Column</x-secondary-button>
                </div>
            @endforeach
        </div>

        <x-primary-button wire:click="save" class="px-4 py-2 mt-4 text-white bg-green-600 rounded">Save Footer</x-primary-button>

        @if(session()->has('success'))
            <div class="mt-2 text-green-600">{{ session('success') }}</div>
        @endif
    </div>

</div>
