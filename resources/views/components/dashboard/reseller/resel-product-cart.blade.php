<?php 

use Livewire\Volt\Component;
use Livewire\Attributes\URL;
use App\Models\Reseller_like_product;
use App\Models\Reseller_resel_product;
use App\Models\Reseller_has_order;
use App\Models\Reseller_order_details;
use App\Models\Order;
use App\Models\CartOrder;
use Livewire\Attributes\On;

use App\Http\Controllers\ProductComissionController;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;


new class extends Component 
{
    public $pd, $alreadyLiked = false, $discountPercent, $haveDiscount = false, $rprice, $resellerOrderId = '', $needToSync = false, $myOrder = '';

    #[validate('required')]
    public $district, $upozila, $location, $area_condition, $delevery, $quantity = 1, $attr = '', $name, $phone, $house_no, $road_no;

    #[On('refresh')]
    public function mount() 
    {
        // dd($this->pd);
        if ($this->pd->offer_type) {
            $this->haveDiscount = true;
            $dis = $this->pd->price - $this->pd->discount;
            // make discount percent
            $this->discountPercent = ($dis / $this->pd->price) * 100;
        }
        // check this product alreay in liked list of reseller    
        $this->alreadyLiked = Reseller_like_product::where(['user_id' => auth()->user()->id, 'product_id' => $this->pd->id])->exists();
        $this->rprice = $this->pd->totalPrice();
    }

    public function like ()
    {
        if ($this->alreadyLiked) {
            Reseller_like_product::where(['user_id' => auth()->user()->id, 'product_id' => $this->pd->id])->delete();
            // $this->dispatch('refresh');
            $this->alreadyLiked = false;
            // $this->dispatch('success', 'Product removed from your favourite list');
        }else{

            Reseller_like_product::create(
                [
                    'user_id' => auth()->user()->id,
                    'product_id' => $this->pd->id,
                ]
            );
            $this->alreadyLiked = true;
            // $this->dispatch('refresh');
            // $this->dispatch('success', 'Product added to your favourite list');
        }

    }


    public function order()     
    {
        $this->validate();
        try {
            //code...
            $order = order::create(
                [
                    'user_id' => auth()->user()->id,
                    'user_type' => 'reseller',
                    'belongs_to' => $this->pd->user_id,
                    'belongs_to_type' => 'vendor',
                    
                    'quantity' => $this->quantity,
                    'total' => $this->quantity * $this->pd?->totalPrice(),
                    'status' => 'Pending',
                    
                    'name' => 'Purchase',
                    'district' => $this->district,
                    'upozila' => $this->upozila,
                    'location' => $this->location,
                    'house_no' => $this->house_no,
                    'road_no' => $this->road_no,
                    'area_condition' => $this->area_condition,
                    'delevery' => $this->delevery,
                    'number' => $this->phone,
                    'shipping' => $this->area_condition == 'Dhaka' ? $this->pd?->shipping_in_dhaka : $this->pd?->shipping_out_dhaka,
                    ]
                );
                
            $cor = CartOrder::create(
                [
                    'user_id' => Auth::id(),
                    'user_type' => 'reseller',
                    'belongs_to' => $this->pd->user_id,
                    'belongs_to_type' => 'vendor',
                    'order_id' => $order->id,
                    'product_id' => $this->pd->id,
                    'quantity' => $this->quantity,
                    'price' => $this->rprice ?? $this->pd->totalPrice(),
                    'size' => $this->attr,
                    'total' => $this->quantity * $this->rprice ?? $this->pd->totalPrice(),
                    'buying_price' => $this->pd->buying_price,
                    'status' => 'Pending',
                    ]
            );
            
            
            // $this->dispatch('refresh');
            $this->reset('name', 'rprice');
            $this->dispatch('refresh');
            $this->dispatch('success', 'Order Done');
            // $this->dispatch('close-modal', 'orderProduct_'.{{$pd->id}});
            if ($order->id && $cor->id) {
                # code...
                ProductComissionController::dispatchProductComissionsListeners($order->id);
            }
            
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            // throw $th;
            $this->dispatch('info', 'Have an Error');
        }
    }

    public function takeOrderInfoAndFill(){
        
        $this->myOrder = auth()->user()->orderToMe()->where('id', $this->resellerOrderId)->get();
        // if this->myOrder is empty, then make an input validation error
        if (empty($this->resellerOrderId)) {
            $this->addError('resellerOrderId', 'Requied !');
        }
        
        if ($this->myOrder->count() < 1) {
            $this->addError('resellerOrderId', 'No Order found !');
        }
        if($this->myOrder->count() > 0){
            $this->needToSync = true;
            $this->district = $this->myOrder[0]->district;
            $this->upozila = $this->myOrder[0]->upozila;
            $this->location = $this->myOrder[0]->location;
            $this->area_condition = $this->myOrder[0]->area_condition;
            $this->delevery = $this->myOrder[0]->delevery;
            $this->name = $this->myOrder[0]->user?->name;
            $this->phone = $this->myOrder[0]->number ?? $this->myOrder[0]->user?->phone;
            $this->house_no = $this->myOrder[0]->house_no;
            $this->road_no = $this->myOrder[0]->road_no;
        }
    }

    public function resetFindOrder()
    {
        $this->reset('resellerOrderId');
        $this->needToSync = false;
        $this->district = '';
        $this->upozila = '';
        $this->location = '';
        $this->area_condition = '';
        $this->delevery = '';
        $this->name = '';
        $this->phone = '';
        $this->house_no = '';
        $this->road_no = '';

    }
}

?>


<div>
    <div class="bg-white rounded shadow overflow-hidden relative">

        @if ($pd->offer_type)

        <div class="discount-badge bg-orange-600 ">
            @php
            $dis = $pd->price - $pd->discount;
            @endphp
            {{ round(($dis / $pd->price) * 100, 1) ?? 0}}%
        </div>

        @endif

        <div class=" overflow-hidden shadow-md p-1">
            <img style="height: 120px" src="{{asset('storage/'. $pd->thumbnail)}}" class="w-full object-cover"
                alt="image">
        </div>

        <div class="p-2 bg-white h-34 flex flex-col justify-between">
            <x-nav-link href="{{route('reseller.resel-product.veiw', ['pd' => $pd->id])}}">
                <div class="text-sm">{{$pd->name ?? "N/A"}}</div>
            </x-nav-link>

            <div>

                <div class="text-md mb-3">
                    @if ($pd->offer_type)
                    <div class="bold">
                        {{$pd->discount ?? "0"}} TK
                    </div>
                    <div class="text-xs">
                        <del>
                            {{$pd->price ?? "0"}} TK
                        </del>
                    </div>
                    @else
                    <div class="bold">
                        {{$pd->price ?? "0"}} TK
                    </div>
                    @endif
                </div>
                {{-- <div class="text-right">
                    <button>clone</button>
                </div> --}}
                <div class="flex justify-center items-center text-sm">
                    <x-hr />
                    <x-primary-button class=" text-center w-full flex justify-between "
                        x-on:click.prevent="$dispatch('open-modal', 'orderProduct_{{$pd->id}}')"> Purchase <i
                            class="fas fa-angle-right pl-2"></i> </x-primary-button>
                    {{-- <button>To Cart</button> --}}
                </div>
            </div>
        </div>

        {{-- <i class="fa-thin fa-thumbs-up"></i> --}}
        {{-- @volt('pd')
        <button wire:click="like"
            class="absolute top-0 right-0 w-8 h-8 rounded-full shadow flex justify-center items-center bg-gray-100">
            <i wire:show="!alreadyLiked" wire:transition class="fa-regular fa-heart"> </i>
            <i wire:show="alreadyLiked" wire:transition class="fa-solid fa-heart"> </i>
        </button>
        @endvolt --}}
        {{-- <i class="fa-thin fa-circle-check"></i> --}}
    </div>

    <x-modal name="orderProduct_{{$pd->id}}" maxWidth="md">
        <div class="p-3 bold border-b flex justify-between items-center">
            <div>
                Purchase
            </div>
            <div class="bold text-lg">
                {{-- @php
                $pp = 0;
                if ($pd->offer_type && $pd->discount) {
                $pp = $pd->discount;
                }else{
                $pp = $pd->price;
                }
                @endphp --}}
                {{-- {{$pd?->totalPrice() ?? "0"}} * {{$quantity ?? 0}} = {{($pd?->totalPrice() ?? 0) *
                ($quantity ?? 0)}} TK --}}
                {{$pd->totalPrice()}} TK
            </div>
        </div>
        <div class="flex items-start justify-start mb-3 p-5 bg-gray-100">
            <div class="flex">
                <img src="{{asset('storage/'. $pd?->thumbnail)}}" class="w-12 h-12 rounded shadow mr-3" alt="">
            </div>
            <div>
                <div class="text-lg bold">
                    {{$pd->name ?? "N/A"}}
                </div>
                <div class="text-sm">

                    @if ($pd->offer_type)
                    <div class="flex items-baseline gap-2">
                        <div class="bold">
                            Price : {{$pd->totalPrice() ?? "0"}} TK
                        </div>
                        <div class="text-xs">
                            <del>
                                MRP : {{$pd->price ?? "0"}} TK
                            </del>
                        </div>
                        <div class="text-xs">
                            @php
                            $dis = $pd->price - $pd->discount;
                            @endphp
                            {{ round(($dis / $pd->price) * 100, 1) ?? 0}}% off
                        </div>
                    </div>

                    @else
                    <div class="bold">
                        Pirce : {{$pd->price ?? "0"}} TK
                    </div>

                    @endif
                    <div class="text-xs">
                        Available Stock: {{$pd->unit ?? "0"}}
                    </div>
                </div>
            </div>

        </div>
        @volt('order')
        <div class="p-5">


            {{-- <form wire:submit.prevent="takeOrderInfoAndFill" class="bg-gray-100 p-2 rounded">
                <x-input-field inputClass="w-full" type="number" class="md:flex" label="Order ID"
                    wire:model.live="resellerOrderId" name="resellerOrderId" error="resellerOrderId" />
                <div class="text-end flex justify-end gap-3">
                    <x-secondary-button type="button" wire:click.prevent="resetFindOrder">Reset</x-secondary-button>
                    <x-primary-button>Attach</x-primary-button>
                </div>
            </form> --}}

            <form wire:submit.prevent="order">
                <x-input-field inputClass="w-full" label="Name" wire:model.live="name" name="name" error="name" />
                <x-input-field inputClass="w-full" label="Phone" wire:model.live="phone" name="phone" error="phone" />
                <x-input-field inputClass="w-full" label="District" wire:model.live="district" name="district"
                    error="district" />
                <x-input-field inputClass="w-full" label="Upozila" wire:model.live="upozila" name="upozila"
                    error="upozila" />
                {{--
                <x-input-field inputClass="w-full" label="Full Address" name="location" wire:model.live="location"
                    error="location" /> --}}
                <textarea wire:model.live="location" id="full_address" cols="3" class="w-full rounded-md p-2 mb-2"
                    placeholder="Full Address"></textarea>
                @error('location')
                <span class="text-red-900">{{$message}}</span>
                @enderror
                <x-input-field inputClass="w-full" label="Road No" name="road_no" wire:model.live="road_no"
                    error="house_no" />
                <x-input-field inputClass="w-full" label="House No" name="house_no" wire:model.live="house_no"
                    error="house_no" />
                {{-- <div class="bg-gray-100 p-2 my-2 space-y-2 rounded shadow-lg">
                    <div class="w-full mb-2">
                        <div class="text-md">
                            @if ($pd->offer_type)
                            <div class="flex items-baseline gap-2">
                                <div class="bold">
                                    Price : {{$pd->totalPrice() ?? "0"}} TK
                                </div>
                                <div class="text-xs">
                                    <del>
                                        MRP : {{$pd->price ?? "0"}} TK
                                    </del>
                                </div>
                            </div>
                            @else
                            <div class="bold">
                                Pirce : {{$pd->price ?? "0"}} TK
                            </div>
                            @endif
                        </div>
                    </div>
                    <x-input-field inputClass="w-full py-1" class="md:flex" label="Reseller Price  "
                        wire:model.live="rprice" name="rprice" error="rprice" />

                    <div class="text-xs" wire:show='rprice'>
                        Profit: <strong> {{intVal($this->rprice) - intVal($this->pd->totalPrice())}} </strong>
                    </div>
                </div> --}}

                {{--
                <x-input-field inputClass="w-full" class="md:flex" label="Product Quantity" wire:model.live="quantity"
                    name="quantity" error="quantity" /> --}}
                <x-input-file label="Quantity" name="quantity" error="quantity">
                    {{-- {{$pd?->unit}} --}}
                    <select wire:model.live="quantity" id="" class="rounded-md py-1 border">
                        <option value="">Select Quantity</option>
                        @for ($i = 1; $i <= $this->pd?->unit; $i++) <option value="{{$i}}">{{$i}}</option>
                            @endfor
                    </select>
                </x-input-file>
                <div class="p-2 bg-indigo-100 ">

                    <div class="text-xs">
                        @if ($this->pd?->unit < 1) Stock Out @else You can order maximum {{$this->pd?->unit}} item
                            @endif
                    </div>

                    <div class="flex justify-between items-center">
                        <div>
                            Total
                        </div>
                        <div>
                            {{$this->quantity}} * {{$this->pd->totalPrice()}} = {{intVal($this->quantity) *
                            intVal($this->pd->totalPrice())}}
                        </div>
                    </div>
                </div>
                {{-- @error('quantity')
                <p class="text-red-400"> {{$message}} </p>
                @enderror --}}

                <x-input-file inputClass="w-full" class="md:flex" label="Product Size/Attribute" name="attr"
                    error="attr">
                    <select wire:model.live="attr" id="product_attr" class="rounded-md py-1 border">
                        <option value="">Select Size/Attribute</option>
                        @if ($this->pd?->attr?->value)
                        @php
                        $arrayOfAttr = explode(',', $this->pd?->attr?->value);
                        @endphp
                        @foreach ($arrayOfAttr as $attr)
                        <option value="{{$attr}}">{{$attr}}</option>
                        @endforeach
                        @else
                        <option value="N/A">N/A</option>
                        @endif
                    </select>
                </x-input-file>
                <x-hr />
                <x-input-file label="Area Condition" error='area_condition'>
                    <select wire:model.live="area_condition" id="" class="rounded py-1">
                        <option value="">Select Area</option>
                        <option value="Dhaka">Inside Dhaka</option>
                        <option value="Other">Out side of Dhaka</option>
                    </select>
                </x-input-file>
                <x-input-file label="Shipping " name="delevery" error="delevery">
                    <select wire:model.live="delevery" id="" class="rounded py-1">
                        <option value=""> Shipping Type</option>
                        <option value="Courier">Courier</option>
                        <option value="Home">Home Delivery</option>
                        <option value="Hand">Hand-To-Hand</option>
                    </select>
                </x-input-file>
                <x-primary-button>Order</x-primary-button>
            </form>
        </div>
        @endvolt
    </x-modal>
</div>