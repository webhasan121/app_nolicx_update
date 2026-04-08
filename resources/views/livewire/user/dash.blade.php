<div>

  <x-dashboard.container>

    <section class="grid grid-cols-1 gap-6 lg:grid-cols-2" >
      <div class="p-6 bg-white rounded-md shadow-md" >
        <div class="flex items-center justify-between gap-6" >
          <div class="relative" >
            <h5 class="text-lg" >{{ __('Welcome, back!') }}</h5>
            <h3 class="px-0" >
              <strong class="text-green-900" style="font-size: 22px" >{{ Str::upper(auth()->user()->name) }}</strong>
            </h3>
          </div>
          <div class="relative" >
            <p class="mb-2 text-xs text-right" >{{ __('Wallet Balance') }}</p>
            <x-nav-link href="{{route('user.wallet.index')}}" class="px-3 py-1 text-indigo-900 border rounded-lg shadow ring-1" >
              <span class="text-sm text-center" >{{ (auth()->user()->coin ?? "0") . __(' TK') }}</span>
            </x-nav-link>
          </div>
        </div>
        <p class="mt-1 text-sm text-gray-600">
          {{ __('We’re glad to see you again. Check your dashboard for updates, tasks, and rewards waiting for you today.') }}
        </p>
      </div>

      <div class="grid grid-cols-2 gap-6" >
        @foreach ($widgets as $key => $widget)
          @php
            $title = $loop->first ? __('Current Level') : __('Upcoming');
          @endphp
          <div class="relative p-6 bg-white rounded-md shadow-md" >
            <div class="flex items-center justify-between mb-2" >
              <h6 class="text-sm font-semibold text-gray-600" >{{ $title }}</h6>
                <div class="inline-block px-4 py-1 text-sm text-center text-white bg-blue-600 rounded-md hover:bg-blue-700" >
                  <span>{{ $widget['name'] }}</span>
                </div>
            </div>
            @if (isset($widget['data']['req_users'], $widget['data']['vip_users']))
              @if($loop->first)
                <p class="mt-2 mb-1 text-sm font-semibold text-gray-600" >{{ __('Achievement') }}</p>
              @endif
              <p class="flex items-center justify-between text-xs" >
                <strong>{{ __('Normal Users') }}</strong>
                <span>{{ $widget['data']['req_users'] }}</span>
              </p>
              <p class="flex items-center justify-between text-xs" >
                <strong>{{ __('VIP Users') }}</strong>
                <span>{{ $widget['data']['vip_users'] }}</span>
              </p>
              @if(isset($widget['rewards']))
                <p class="flex flex-col mt-2 text-xs text-gray-600" >
                  <strong>{{ __('Level-Up Rewards') }}</strong>
                  <span>{{ $widget['rewards'] }}</span>
                </p>
              @endif
            @endif
          </div>
        @endforeach
      </div>
    </section>

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
                            <input type="text" disabled readonly id="refID" class="rounded form-control"
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

        @if(!auth()->user()->created_at->diffInHours(Carbon\Carbon::now()) > 72)
            <x-dashboard.section>
                <div>
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
        @endif

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
