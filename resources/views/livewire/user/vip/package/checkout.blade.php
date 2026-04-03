<div>
    <x-dashboard.page-header>
        VIP Package
    </x-dashboard.page-header>


    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.inner>

                <div class="flex justify-start">

                    <div class="mb-3 col-md-5" style="min-width:250px; max-width: 350px">
                            <x-vip-cart :item="$package" :active="$id" />
                    </div>

                    <div class="px-3 col-lg-7 w-100">
                        <div class="bg-white">
                            <div class="text-left">
                                <div class="text-lg font-bold bold" >Confirm Payment First</div>
                                <div class="text-sm">Please send TK {{$package->price ?? "0"}} to bellow number. And collect your Tansactions ID for further proccess. We need your Transactions ID to identify it's you.</div>
                                <x-hr />
                                <div class="mt-2">

                                    @if ($package->payOption)

                                        @foreach ($package->payOption as $item)

                                            <div class="p-2 mb-1 border rounded">
                                                <div class="uppercase" for="">{{ $item->pay_type }} </div>
                                                <div class="flex items-center justify-between w-full">
                                                    <div type="text" id="paymentTo_{{$item->id}}" class="py-2 bg-white border-0 form-control outline-0 shadow-0 text-dark bold"> {{ $item->pay_to }} </div>
                                                    <x-primary-button class="py-0 ml-5 btn btn-sm" id="" onclick="copyPaymentNumber(this, 'paymentTo_{{$item->id}}')" >Copy</x-primary-button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="text-center">
                    <x-secondary-button x-on:click="$dispatch('open-modal', 'package-details-modal')" data-bs-target="#packageDetailsModal">
                        View Details
                    </x-secondary-button>
                </div>
            </x-dashboard.section.inner>
        </x-dashboard.section>

        <x-dashboard.section>
            <x-dashboard.section.inner>
                <div>
                    <style>
                        .vip_item_info_box{
                            height: 155px;
                            /* border: 1px solid #c7c7c7; */
                            text-align: center;
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            align-items: center;
                            /* background-color:white ; */
                            border-radius: 8px
                        }
                        .vip_item_info_box .label {

                        }
                    </style>

                    <div style="display: grid; grid-template-columns:repeat(auto-fit, 155px); grid-gap:20px; justify-content:center" >

                        <div class="shadow vip_item_info_box">
                            <div>
                                Package <i class="mx-2 fas fa-check-circle"></i>
                            </div>
                            <hr class="w-100">
                            <div style="font-weight: 900; font-size:24px" class="">
                                {{$package->name}}
                            </div>
                        </div>


                        <div class="shadow vip_item_info_box">
                            <div>
                                Price <i class="mx-2 fas fa-check-circle"></i>
                            </div>
                            <hr class="w-100">
                            <div style="font-weight: 900; font-size:24px" class="">
                                {{$package->price}} TK
                            </div>
                        </div>


                        <div class="shadow vip_item_info_box">
                            <div>
                                Daily TK
                            </div>
                            <hr class="w-100">
                            <div style="font-weight: 900; font-size:24px" class="">
                                {{$package->coin}}
                            </div>
                        </div>

                        <div class="shadow vip_item_info_box">
                            <div>
                                Daily Time <i class="mx-2 fas fa-clock"></i>
                            </div>
                            <hr class="w-100">
                            <div style="font-weight: 900; font-size:24px" class="">
                                {{$package->countdown}} Min
                            </div>
                        </div>

                    </div>
                    <x-hr/>

                    <div class="my-3 text-center">
                        <x-primary-button x-on:click="$dispatch('open-modal', 'purchase-modal')" @class(["btn btn-lg bg_primary shadow text-white", 'd-none' => $ownerPackage??""])>Procces to Purchase <i class="mx-2 fas fa-arrow-right"></i> </x-primary-button>
                    </div>
                </div>


            </x-dashboard.section.inner>
        </x-dashboard.section>

        <x-modal name="package-details-modal" maxWidth="xl">
            <div class="p-3 bg-white border-b">
                Package Description
            </div>
            <div class="p-3">
               {!!

                $package->description ?? "No Description Found !"

                !!}
            </div>
        </x-modal>


        <x-modal name="purchase-modal" maxWidth="lg">
            <div class="p-3 border-b">
                Purchase Package
            </div>

            <div class="p-3">
                <div>

                    {{-- Payment Section --}}
                    <div class="p-3 border rounded">

                        <div class="mb-3">
                            <x-input-label for="method">Payment Method</x-input-label>

                            <select
                                wire:model.live="payment_by"
                                id="method"
                                class="w-full rounded @error('payment_by') is-invalid @enderror"
                            >
                                <option value="">Select a payment method</option>
                                @foreach ($package->payOption as $item)
                                    <option value="{{ $item->pay_type }}">
                                        {{ $item->pay_type }} - {{ $item->pay_to }}
                                    </option>
                                @endforeach
                            </select>

                            @if ($errors->has('payment_by'))
                                <div class="text-xs text-red-600">
                                    {{ $errors->first('payment_by') }}
                                </div>
                            @endif
                        </div>

                        <div class="mb-3 form-floating">
                            <x-input-label for="trx">Transaction ID</x-input-label>

                            <x-text-input
                                id="trx"
                                type="text"
                                class="w-full"
                                wire:model.live="trx"
                                placeholder="AFASDF4574SD4S"
                            />

                            <div class="text-xs">Write down the transaction ID.</div>

                            @if ($errors->has('trx'))
                                <div class="text-xs text-red-600">
                                    {{ $errors->first('trx') }}
                                </div>
                            @endif
                        </div>

                    </div>

                    {{-- User Info --}}
                    <div class="p-3 my-3 border rounded">

                        <div class="mb-3">
                            <x-input-label for="name">Your Name</x-input-label>

                            <x-text-input
                                id="name"
                                type="text"
                                wire:model.live="name"
                                class="w-full"
                                placeholder="John Doe"
                            />

                            @if ($errors->has('name'))
                                <div class="text-xs text-red-600">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <x-input-label for="phone">Phone Number</x-input-label>

                            <x-text-input
                                id="phone"
                                type="text"
                                wire:model.live="phone"
                                class="w-full"
                                placeholder="+880123456789"
                            />

                            @if ($errors->has('phone'))
                                <div class="text-xs text-red-600">
                                    {{ $errors->first('phone') }}
                                </div>
                            @endif
                        </div>

                    </div>

                    {{-- Task Type --}}
                    <div @class([
                        'mb-3 p-3 rounded shadow',
                        'border border-danger' => $errors->has('task_type'),
                    ])>

                        <x-input-label class="my-2 fs-2">Task Type</x-input-label>

                        <div class="p-3 border rounded">
                            <div class="flex align-items-center">
                                <input
                                    type="radio"
                                    id="daily_task"
                                    value="daily"
                                    wire:model.live="task_type"
                                    style="width:20px;height:20px"
                                />
                                <x-input-label for="daily_task" class="pl-3 m-0">
                                    Daily Task
                                </x-input-label>
                            </div>
                            <div class="text-xs">
                                Daily task may be completed within 24 hours.
                            </div>
                        </div>

                        <hr>

                        <div class="p-3 border rounded">
                            <div class="flex align-items-center">
                                <input
                                    type="radio"
                                    id="monthly_task"
                                    value="monthly"
                                    wire:model.live="task_type"
                                    style="width:20px;height:20px"
                                />
                                <x-input-label for="monthly_task" class="pl-3 m-0">
                                    Monthly Task
                                </x-input-label>
                            </div>
                            <div class="text-xs">
                                Monthly task may be completed once per month.
                            </div>
                        </div>

                    </div>

                    @if ($errors->has('task_type'))
                        <div class="text-xs text-red-600">
                            {{ $errors->first('task_type') }}
                        </div>
                    @endif

                    {{-- NID Section --}}
                    <div class="p-3 border">

                        <div class="mb-3">
                            <x-input-label for="nid">Your NID Number</x-input-label>

                            <x-text-input
                                id="nid"
                                type="number"
                                wire:model.live="nid"
                                class="w-full"
                            />

                            @if ($errors->has('nid'))
                                <div class="text-xs text-red-600">
                                    {{ $errors->first('nid') }}
                                </div>
                            @endif
                        </div>

                        <x-hr />

                        <div class="row">

                            {{-- Front NID --}}
                            <div class="col-lg-6">
                                <div>Front Side of NID</div>

                                @if ($nid_front)
                                    <img
                                        src="{{ $nid_front->temporaryUrl() }}"
                                        class="object-contain w-24 h-24 my-2"
                                        alt="NID Front"
                                    />
                                @endif

                                <x-text-input
                                    type="file"
                                    wire:model.live="nid_front"
                                    class="form-control"
                                />

                                @if ($errors->has('nid_front'))
                                    <div class="text-xs text-red-600">
                                        {{ $errors->first('nid_front') }}
                                    </div>
                                @endif
                            </div>

                            {{-- Back NID --}}
                            <div class="col-lg-6">
                                <div>Back Side of NID</div>

                                @if ($nid_back)
                                    <img
                                        src="{{ $nid_back->temporaryUrl() }}"
                                        class="object-contain w-24 h-24 my-2"
                                        alt="NID Back"
                                    />
                                @endif

                                <x-text-input
                                    type="file"
                                    wire:model.live="nid_back"
                                    class="form-control"
                                />

                                @if ($errors->has('nid_back'))
                                    <div class="text-xs text-red-600">
                                        {{ $errors->first('nid_back') }}
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="mt-4 text-right">
                        <x-primary-button wire:click.prevent="purchase">
                            Confirm <i class="mx-2 fas fa-arrow-right"></i>
                        </x-primary-button>
                    </div>

                </div>


            </div>
        </x-modal>
    </x-dashboard.container>

    @push('script')
        <script>
            // copyPaymentNumber('paymentTo')
            function copyPaymentNumber(e, elementId)
            {
                const paymentNumberInput = document.getElementById(elementId);
                const tempTextarea = document.createElement("textarea");
                tempTextarea.value = paymentNumberInput.value || paymentNumberInput.textContent || paymentNumberInput.innerText;


                // Append the textarea to the DOM (off-screen)
                tempTextarea.style.position = "fixed";
                tempTextarea.style.opacity = "0";
                document.body.appendChild(tempTextarea);


                // Select the content of the textarea
                tempTextarea.select();
                tempTextarea.setSelectionRange(0, 99999); // For mobile devices

                // Copy the selected content to the clipboard
                try {
                    document.execCommand("copy");
                    // console.log("Content copied to clipboard!");
                    // alert('copied !')
                    e.innerText = 'copied';
                    setTimeout(() => {
                        e.innerText = 'copy';
                    }, 2000);
                } catch (err) {
                    console.error("Failed to copy content: ", err);
                }

                // Remove the temporary textarea
                document.body.removeChild(tempTextarea);

                // var refer = document.getElementById('refer_link_display');
                // paymentNumberInput.select();
                // refer.setSelectionRange(0,9999);
                // document.exceCommand('copy');
                // let ke = new keyboardEvent();
                // navigator.clipboard.writeText(refer.value);

            }

             function previewImage(e, target)
            {
                if (e.files && e.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                    document.querySelector(target).setAttribute("src",e.target.result);
                    };
                    reader.readAsDataURL(e.files[0]);
                }
            }

            function removeImage(e, target_image)
            {
                e.previousElementSibling.value = "";
                document.querySelector(target_image).removeAttribute('src');
            }
        </script>
    @endpush
</div>
