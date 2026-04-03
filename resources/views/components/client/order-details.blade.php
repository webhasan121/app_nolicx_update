<div>

    <form wire:submit.prevent="confirm">

        <div class="md:flex">
            <div>
                @if (!empty($product->attr->value))
                    <div class="">
                        @php
                            $arrayOfAttr = explode(',', $product->attr?->value);
                        @endphp
                        <x-input-label style="width: 250px" for="size">{{ $product->attr?->name }}</x-input-label>
                        <select wire:model.live="size" class="rounded border-gray-300" required>
                            
                                    <option value="Size Less" selected disable>select size</option>
                            @if (count($arrayOfAttr) > 0)     
                                @foreach ($arrayOfAttr as $attr)
                                    <option value="{{$attr ?? "Size Less"}}"  disable>{{ $attr ?? "Size Less" }}</option>
                                @endforeach
                            @endif
                            
                        </select>
                        @error('size')
                            <strong>{{$message}}</strong>
                        @enderror
                    </div>
                @endif
                
                <x-input-field  wire:model.live="name" label="Your Name" error="name" name="name" />
                <div>
                    <x-input-field type="number" wire:model.live="quantity" min="1" label="Quantity" error="quantity" name="quantity" />
                </div>
            </div>

            <div>
                <div>
                    <x-input-label>Your Full Address</x-input-label>
                    @if ($errors->has('location'))
                        <div class="text-sm text-red-600">{{ $errors->first('location') }}</div>
                    @endif
                    <textarea wire:model.live="location" id="" class="w-full rounded" cols="5" placeholder="Address"></textarea>
                </div>
                <x-input-field  wire:model.live="phone" label="Your Active Phone" error="phone" name="phone" />
            </div>
        </div>
    
        <x-primary-button >Confirm Order</x-primary-button>
    </form>

</div>