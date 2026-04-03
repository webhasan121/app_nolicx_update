<x-dashboard.section>
    <x-dashboard.section.inner>
        {{-- <x-dashboard.section class="bg-gray-100"> --}}

            <x-input-field class="md:flex" inputClass="w-full" :data="$data??[]" label="Your Shop Name"
                wire:model.live="shop_name_en" name="shop_name_en" error="shop_name_en" />

            {{-- @if (auth()->user()->country == 'Bangladesh')
            <x-input-field class="md:flex" inputClass="w-full" :data="$data??[]" label="Your Shop Name bangla"
                wire:model.live="shop_name_bn" name="shop_name_bn" error="shop_name_bn" />
            @endif --}}

            <x-hr />

            <x-input-file label="Logo (Max 1Mb)" error="logo">
                <p>
                    100x100 logo
                </p>
                <div style="width:100px; height:100px" class="border rounded">
                    @if ($logo)
                    <img style="width:100px; height:100px" class="border rounded shadow" src="{{$logo->temporaryUrl()}}"
                        alt="100x100">
                    @endif
                </div>
                <div class="relative">
                    <x-text-input wire:model.live="logo" type="file" id="logo" class="absolute hidden" />
                    <label for="logo" class="p-2 shadow border rounded">
                        <i class="fas fa-upload"></i>
                    </label>
                </div>
            </x-input-file>

            <x-input-file label="Banner (Max 1Mb)" error="banner">
                <p>
                    100x300 banner image
                </p>
                <div style="width:300px;height:100px" class="border rounded">
                    @if ($banner)
                    <img style="width:300px; height:100px" class="border rounded shadow"
                        src="{{$banner->temporaryUrl()}}" alt="100x300">
                    @endif
                </div>
                <div class="relative">
                    <x-text-input wire:model.live="banner" type="file" id="banner" class="absolute hidden" />
                    <label for="banner" class="p-2 shadow border rounded">
                        <i class="fas fa-upload"></i>
                    </label>
                </div>
            </x-input-file>

            <x-input-file label="Description" error="description">
                <textarea wire:model="description" id="description" class="w-full rounded border " rows="5"
                    placeholder="Describe about your shop..."></textarea>
            </x-input-file>
            <x-hr />
            <x-input-field class="md:flex" inputClass="w-full" :data="$data??[]" type="number" label="Your Shop Phone"
                wire:model.live="phone" name="phone" error="phone" :value="auth()->user()->phone" />
            <x-input-field class="md:flex" inputClass="w-full" :data="$data??[]" type="email" label="Your Shop email"
                wire:model.live="email" name="email" error="email" :value="auth()->user()->email" />

    </x-dashboard.section.inner>
</x-dashboard.section>

<x-dashboard.section>
    <x-dashboard.section.inner>
        <p class="my-1">Shop Location</p>
        {{--
        <x-input-field class="md:flex" inputClass="w-full" :data="$data??[]" wire:model.live="country" label="Country"
            name="country" error="country" />
        <x-input-field class="md:flex" inputClass="w-full" :data="$data??[]" wire:model.live="district"
            label="Division / State" name="district" error="district" />
        <x-input-field class="md:flex" inputClass="w-full" :data="$data??[]" wire:model.live="upozila"
            label="District / City" name="upozila" error="upozila" /> --}}




        <div class="mt-4">
            <div style="width:350px">

                <x-input-label for="address" value='Give Full Address Of Your Shops'></x-input-label>

            </div>
            {{--
            <x-text-input wire:model="district" id="district" class="block mt-1 w-full" type="text" name="district" />
            --}}

            <div class="w-full">

                <textarea name="address" id="address" wire:model="address" class="w-full rounded " rows="1"
                    placeholder="Full Address"></textarea>
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>
        </div>
        <x-hr />

        <x-input-field class="md:flex" inputClass="w-full" :data="$data??[]" wire:model.live="village" label="Village"
            name="village" error="village" />
        <x-input-field class="md:flex" inputClass="w-full" :data="$data??[]" wire:model.live="zip" label="Zip Code"
            name="zip" error="zip" />
        <x-input-field class="md:flex" inputClass="w-full" :data="$data??[]" wire:model.live="road_no" label="Road No"
            name="road_no" error="road_no" />
        <x-input-field class="md:flex" inputClass="w-full" :data="$data??[]" wire:model.live="house_no" label="House No"
            name="house_no" error="house_no" />

      

        {{-- country field --}}
        <div class="mt-4 md:flex items-center">
            <div style="width:350px">
                <x-input-label for="country" value='Your Country'></x-input-label>
                <x-input-error :messages="$errors->get('country')" class="mt-2" />
            </div>
            {{-- <p class="text-sm text-gray-600">Please select your country.</p> --}}

            {{--
            <x-text-input type="search" list="countries" wire:model="country" id="countryjj" class="block mt-1 w-full"
                type="text" name="country" />
            <datalist id="countries">
                <option value="Bangladesh" data-con='BD' />
            </datalist> --}}
            <div class="w-full">

                <select wire:model="country" id="country" class="rounded border-0 ring-1 block mt-1 w-full">
                    <option value="Bangladesh">Bangladesh</option>
                </select>
            </div>

        </div>

        {{-- state field --}}
        <div class="mt-4 md:flex items-center" id="">
            <div style="width:350px">
                <x-input-label for="district" value='District'></x-input-label>
                <x-input-error :messages="$errors->get('district')" class="mt-2" />
            </div>

            {{--
            <x-text-input wire:model="district" id="district" class="block mt-1 w-full" type="text" name="district" />
            <div class="w-full"> --}}

                <select wire:model.live="district" id="discript" class="w-full rounded-md ">
                    <option value=""> -- Select Upozila --</option>
                    @foreach ($states as $state)
                    <option value="{{$state->name}}">{{$state->name}}</option>
                    @endforeach
                </select>
            </div>

        </div>

        {{-- state field --}}
        <div class="mt-4 md:flex">
            <div style="width:350px">

                <x-input-label for="upozila" value='Upozila'></x-input-label>
                <x-input-error :messages="$errors->get('upozila')" class="mt-2" />

            </div>


            <div class="w-full">
                <select wire:model.live="upozila" id="upozila" class="w-full rounded-md ">
                    <option value=""> -- Select Upozila --</option>
                    @foreach ($cities as $item)
                    <option value="{{$item->name}}">{{$item->name}}</option>
                    @endforeach
                </select>

            </div>
        </div>


        <br>
        {{-- add a wire navigating feature to button --}}
        {{-- <x-button wire:click="save" class="bg-blue-500 hover:bg-blue- 700 text-white font-bold py-2 px-4 rounded">
            Save</x-button> --}}
        <x-primary-button>
            Submit
        </x-primary-button>
    </x-dashboard.section.inner>
</x-dashboard.section>

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.5.0/axios.min.js"></script>
@script
<script>
    document.getElementById('state_alt').style.display = 'none';
    let countryCode = '';
            function getCountryStateCity() {
                const countrySelectElement = document.getElementById('country');
                const stateSelectElement = document.getElementById("division");
                const citySelectElement = document.getElementById("district");


                // get country name and set to country select element
                axios.get("https://api.countrystatecity.in/v1/countries", {
                        headers: {
                            "X-CSCAPI-KEY": "eldObUl5V0Q4MWpiaXFQeEpNSEVVSTlBU1R5ZlU5OE5ORmRra1dxRg==",
                        }
                    })
                    .then(res => {
                        res.data.forEach(cntry => {
                            let option = document.createElement("option");
                            option.value = cntry.name;
                            option.setAttribute('data-iso2', cntry.iso2);
                            option.textContent = cntry.name;
                            countrySelectElement.appendChild(option);
                        })
                    })
                    .then(error => {
                        // console.log(error);
                    })

                //if country change call api for state data
                countrySelectElement.addEventListener('change', function () {
                    const sop = this.options[this.selectedIndex];
                    countryCode = sop.getAttribute('data-iso2');
                    // set the country code to the hidden input
                    
                    // countryCode2 = sop.getAttribute('data-iso2');
                    // console.log('code is ' + countryCode);
                    
                    if (countryCode == "BD") {
                        // console.log("Bangladesh selected");
                        stateSelectElement.style.display = 'none';
                        document.getElementById('state_main').style.display = 'none';
                        document.getElementById('state_alt').style.display = 'flex';
                    } else {
                        stateSelectElement.style.display = 'flex';
                        document.getElementById('state_main').style.display = 'flex';
                        document.getElementById('state_alt').style.display = 'none';   
                        
                    }
                    // console.log("https://api.countrystatecity.in/v1/countries/" + countryCode + "/states");
                    axios.get("https://api.countrystatecity.in/v1/countries/" + countryCode + "/states", {
                            headers: {
                                "X-CSCAPI-KEY": "eldObUl5V0Q4MWpiaXFQeEpNSEVVSTlBU1R5ZlU5OE5ORmRra1dxRg==",
                            }
                        })
                        .then(res => {
                            let htmlOption = '';
                            let ifBd = "";
                            res.data.forEach(state => {
                                if (countryCode == "BD") {
                                    // console.log(state);
                                    console.log(countryCode);
                                    if (state.iso2.length > 0) {
                                        //get name without  'District' from state.name
                                        var str = state.name;
                                        var newstr = str.replace(/ District$/, "");

                                        ifBd +=
                                            `
                                            <option value="${newstr}">${newstr}</option>
                                        
                                            `;
                                    }
                                } else {
                                    
                                    htmlOption +=
                                        `
                                        <option value="${state.iso2}">${state.name}</option>
                                        
                                        `;
                                }
                                htmlOption +=
                                    `
                                    <option value="${state.iso2}">${state.name}</option>
                                    
                                    `;
                            })
                            stateSelectElement.innerHTML = htmlOption;
                            citySelectElement.innerHTML = ifBd;
                        })
                        .then(error => {
                            console.log(error);
                        })
                })


                //if change state call api for city
                stateSelectElement.addEventListener("input", (e) => {
                    // let countryCode = document.getElementById('select_country').getAttribute('data-iso2');
                    let cityCode = e.target.value;
                    console.log(countryCode, cityCode);
                    axios.get("https://api.countrystatecity.in/v1/countries/" + countryCode + "/states/" + cityCode + "/cities", {
                            headers: {
                                "X-CSCAPI-KEY": "eldObUl5V0Q4MWpiaXFQeEpNSEVVSTlBU1R5ZlU5OE5ORmRra1dxRg==",
                            }
                        })
                        .then(res => {
                            let htmlOption = "";
                            // console.log(res.data[0]);
                            res.data.forEach(ct => {
                                htmlOption +=
                                    `
                                    <option value="${ct.name}">${ct.name}</option>
                                    `;
                            })
                            citySelectElement.innerHTML = htmlOption;
                        })
                        .then(error => {
                            console.log(error);
                        })
                })
            }


            document.getElementById('country').addEventListener('click', (e) =>{
                // let countryCode = e.getAttribute('data-iso2');
                if(e.target.children.length == 1){
                    getCountryStateCity();
                };
                
            });

            // document.getElementById('country').addEventListener('change', (e) => {
            //     console.log(e.target.data);
                
            // })

            getCountryStateCity();
</script>
@endscript --}}
</form>