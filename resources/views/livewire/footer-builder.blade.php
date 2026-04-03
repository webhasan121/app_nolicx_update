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
                <div class="border p-4 bg-gray-50 rounded">
                    <input type="text" wire:model="layout.sections.{{ $sIndex }}.title" 
                        class="border px-2 py-1 w-full mb-2"
                        placeholder="Section Title"/>

                    <div class="grid grid-cols-{{ count($section['columns']) }} gap-4">
                        @foreach($section['columns'] as $cIndex => $col)
                            <div class="border p-2 bg-white rounded">
                                <h4 class="font-semibold mb-2">Column {{ $cIndex+1 }}</h4>

                                @foreach($col['widgets'] as $wIndex => $widget)
                                    <div class="border p-2 mb-2 rounded">
                                        @if($widget['type'] === 'text')
                                            <textarea wire:model="layout.sections.{{ $sIndex }}.columns.{{ $cIndex }}.widgets.{{ $wIndex }}.content" 
                                                    class="w-full border px-2 py-1" placeholder="Text..."></textarea>
                                        @elseif($widget['type'] === 'link')
                                            <input wire:model="layout.sections.{{ $sIndex }}.columns.{{ $cIndex }}.widgets.{{ $wIndex }}.label" 
                                                placeholder="Link Label" class="border w-full px-2 py-1 mb-1"/>
                                            <input wire:model="layout.sections.{{ $sIndex }}.columns.{{ $cIndex }}.widgets.{{ $wIndex }}.url" 
                                                placeholder="Link URL" class="border w-full px-2 py-1"/>
                                        @elseif($widget['type'] === 'icon')
                                            <input wire:model="layout.sections.{{ $sIndex }}.columns.{{ $cIndex }}.widgets.{{ $wIndex }}.icon" 
                                                placeholder="Icon name (e.g., facebook)" class="border w-full px-2 py-1 mb-1"/>
                                            <input wire:model="layout.sections.{{ $sIndex }}.columns.{{ $cIndex }}.widgets.{{ $wIndex }}.url" 
                                                placeholder="Icon URL" class="border w-full px-2 py-1"/>
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

        <x-primary-button wire:click="save" class="bg-green-600 text-white px-4 py-2 rounded mt-4">Save Footer</x-primary-button>

        @if(session()->has('success'))
            <div class="text-green-600 mt-2">{{ session('success') }}</div>
        @endif
    </div>

</div>
