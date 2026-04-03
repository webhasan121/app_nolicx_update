<div>

    {{--
    <?php
    use App\Models\vendor;
    use function Livewire\Volt\{state};
    state(['latestVndor' =>0, 'data' => vendor::latest()]);
    ?> --}}

    <x-dashboard.container>
        <x-dashboard.section>
            <div class="my-2 rounded">
                <div>
                    <div class="flex justify-between align-center">
                        <div>
                            Welcome, back!
                        </div>
                        {{-- <div>
                            {{auth()->user()->created_at->diffForHumans()}}
                        </div> --}}
                    </div>
                    <div class="flex justify-between p-0 m-0 align-center">
                        <div class="px-0">
                            <b class="text-green-900" style="font-size: 20px">
                                {{ Str::upper(auth()->user()->name)}}
                            </b>
                        </div>
                        {{-- <div class="px-0 col-md-2 co"></div> --}}
                        <x-nav-link href="{{route('user.wallet.index')}}"
                            class="px-3 text-indigo-900 border rounded-lg shadow ring-1">
                            <div>
                                Wallet
                            </div>
                            <div class="py-1 pl-3 d-block ">
                                {{auth()->user()->coin ?? "0"}} TK
                            </div>
                        </x-nav-link>
                    </div>

                </div>
            </div>
        </x-dashboard.section>

        <div class="items-start justify-between m-0 my-2 lg:flex">
            <x-dashboard.section>
                <div class="">
                    <x-dashboard.section.header>
                        <x-slot name="title">
                            Refer and Claim
                        </x-slot>
                        <x-slot name="content">
                            Refer your friends and get 5% of every purchase!
                        </x-slot>
                    </x-dashboard.section.header>
                    <x-dashboard.section.inner>

                        <div class=" w-100">
                            <input type="text" disabled readonly id="refID" class="rounded  form-control"
                                value="{{auth()->user()->myRef->ref ?? ""}}">
                        </div>

                        <div>
                            <x-primary-button onclick="copyPaymentNumber(this, 'refID')"
                                class="my-1 text-right btn btn-success btn-sm PX-3"> <i class="mr-1 fas fa-copy"></i>
                                COPY</x-primary-button>
                            <x-nav-link wire:navigate class="text-xs" href="{{route('user.ref.view')}}">View Your
                                Referred User</x-nav-link>
                        </div>
                    </x-dashboard.section.inner>

                </div>
            </x-dashboard.section>

            <x-dashboard.section>
                <div @class(['hidden'=> auth()->user()->created_at->diffInHours(Carbon\Carbon::now()) > 72])>
                    <x-dashboard.section.header>
                        <x-slot name="title">
                            Claim Your Reward
                        </x-slot>
                        <x-slot name="content">
                            Your friend may give you a referral code.
                        </x-slot>
                    </x-dashboard.section.header>
                    <x-dashboard.section.inner>
                        <div class=" position-relative">

                            {{-- <form action="{{route('user.set.ref')}}" method="post"> --}}
                                <form wire:submit.prevent="checkRef">
                                    <div class="input-group w-100">
                                        {{-- @php
                                        $rffer = '';
                                        if(auth()->user()->reference_accepted_at ||
                                        auth()->user()->created_at->diffInHours(Carbon\Carbon::now()) > 72)
                                        {
                                        $rffer = auth()->user()->reference;
                                        }

                                        @endphp --}}
                                        <input type="text" class="w-full border rounded" wire:model.live="newRef"
                                            @disabled(auth()->user()->reference_accepted_at ? true : false)
                                        placeholder="Give Referred Code" >
                                        @error('reference')
                                        <strong>{{$message}}</strong>
                                        @enderror
                                    </div>


                                    <div class="flex justify-between mt-1 align-center">
                                        <x-primary-button>Apply</x-primary-button>

                                        {{-- <button class="px-3 py-1 mt-1 text-white bg-gray-800 rounded" type="submit"
                                            for="" @disabled(auth()->user()->reference_accepted_at ? true : false) >
                                            Apply</button> --}}
                                        <div class="text-xs">
                                            <span class="" id="timeleft"> </span>
                                            {{auth()->user()->created_at->diffForHumans()}}
                                        </div>
                                    </div>

                                </form>
                        </div>
                    </x-dashboard.section.inner>
                </div>
            </x-dashboard.section>
        </div>



        <x-dashboard.section>
            <x-client.membership-activate-box />

            @push('style')
            <style>
                .add:hover>.wrapAdd {
                    translate: all linear .3s;
                    /* position: absolute;
                        bottom: 12px;
                        right: 12px!important; */
                    width: 10px;
                    height: 10px;
                    translate: all linear .3s;
                    /* border: 1px solid green!important; */

                }

                .wrapAdd {
                    translate: all linear .3s;
                    margin-top: 10px;
                    /* position: absolute;
                        bottom: 12px;
                        right: 20px; */
                    /* padding: 5px; */
                    width: 15px;
                    height: 15px;
                    /* border-radius: 3px; */
                    border-top: 1px solid gray;
                    ;
                    border-right: 1px solid gray;
                    ;
                    transform: rotate(45deg);
                }
            </style>
            @endpush
            <div
                style="color:black; display: grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); grid-gap:10px">
                <a wire:navigate href="{{route('upgrade.vendor.index', ['upgrade' => 'vendor'])}}"
                    class="add p-3 rounded shadow my-2 border; "
                    style="position:relative;background:linear-gradient(135deg, rgb(235, 235, 235), lightgreen, rgb(235, 235, 235))">
                    <div class="text-lg" style="font-weight:600; color:green"> Be a Vendor</div>
                    <div class="text-sm">
                        Upgrade your account to <strong>VENDOR</strong>, sell product and earn comission.
                    </div>
                    <div class="wrapAdd"></div>
                </a>
                <a wire:navigate href="{{route('upgrade.vendor.index', ['upgrade' => 'reseller'])}}"
                    class="add p-3 rounded shadow my-2 border; "
                    style="position:relative;background:linear-gradient(135deg, rgb(235, 235, 235), lightgreen, rgb(235, 235, 235))">
                    <div class="text-lg" style="font-weight:600; color:green;"> Be Reseller</div>
                    <div class="text-sm">
                        Upgrade your account to <strong>Reseller</strong> now. Chose product and sel as yours.
                    </div>
                    <div class="wrapAdd"></div>
                </a>
                <a wire:navigate href="{{route('upgrade.rider.index')}}" class="add p-3 rounded shadow my-2 border; "
                    style="position:relative;background:linear-gradient(135deg, rgb(235, 235, 235), lightgreen, rgb(235, 235, 235))">
                    <h6 style="font-weight:600; color:green;">Be a Rider</h6>
                    <div class="text-sm">
                        Be a <strong>Delevary Man</strong>, collect product and shipped to destination.
                    </div>
                    <div class="wrapAdd"></div>
                </a>
            </div>
        </x-dashboard.section>

    </x-dashboard.container>


    @push('script')

    <script>
        function copyRef() {
            var copyText = document.getElementById("refID");
            navigator.clipboard.writeText(copyText.value)
            .then(function() {
                alert("Copied the text: " + copyText.value);
            })
            .catch(function(err) {
                console.error('Async: Could not copy text: ', err);
            });
        }

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
    </script>

    @endpush
</div>
