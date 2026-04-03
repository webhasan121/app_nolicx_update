@php
  $branches = \App\Models\Branch::get();
  $widgets = [
    [
      'head' => 'Menu',
      'menu' => [
        [ 'title' => 'About Us', 'route' => route('web.pages', 'about-us')  ],
        [ 'title' => 'Contact Us', 'route' => route('web.pages', 'about-us') ],
        [ 'title' => 'Products', 'route' => route('products.index')  ],
        [ 'title' => 'Categories', 'route' => route('category.index') ],
      ]
    ],

    [
      'head' => 'Links',
      'menu' => [
        [ 'title' => 'Earn', 'route' => route('web.pages', 'how-to-earn') ],
        [ 'title' => 'Privacy Policy', 'route' => route('web.pages', 'privacy-policy') ],
        [ 'title' => 'Return & Refund', 'route' => route('web.pages', 'return-refund') ],
        [ 'title' => 'Terms & Conditions', 'route' => route('web.pages', 'terms-conditions') ],
      ]
    ],

    auth()->check()
    ? [
        'head' => 'Account',
        'menu' => [
          [ 'title' => 'Dashboard', 'route' => route('dashboard') ],
          [ 'title' => 'Profile', 'route' => route('profile.edit') ],
          // [ 'title' => 'Logout', 'route' => 'logout' ],
        ]
      ]
    : [
        'head' => 'Account',
        'menu' => [
          [ 'title' => 'Login', 'route' => route('login') ],
          [ 'title' => 'Register', 'route' => route('register') ],
        ]
      ],
  ];
@endphp


<footer>
  <section class="px-6 pt-16 pb-8 mx-auto mb-8 border-b max-w-7xl" >
    <div class="flex flex-col gap-8 lg:flex-row lg:gap-16" >
      <div class="flex flex-row lg:flex-col items-center lg:items-start md:w-[25%]" >
        <a wire:navigate href="/" class="flex items-center w-full" >
          <img height="50px" width="60px" src="{{asset('icon.png')}}" alt="" />
          <div class="text-4xl font-bold ps-2">
            <x-application-name />
          </div>
        </a>
        <a href="{{ config('app.playstore_link') }}" class="w-[150px] md:w-[225px] lg:w-full" target="_blank" >
          <img src="{{ asset('playstore.png') }}" class="w-sm" />
        </a>
      </div>
      <div class="grid w-full grid-cols-2 gap-6 lg:grid-cols-4" >
        @foreach($widgets as $widget)
          <div class="block" >
            @if(!empty($widget['head']))
              <h5 class="mb-4 text-lg font-bold border-b" >{{ $widget['head'] }}</h5>
            @endif
            <ul class="mb-4" >
              @if(!empty($widget['menu']))
                @foreach($widget['menu'] as $link)
                  <li>
                    <x-nav-link class="py-1 mb-1 d-block" href="{{ $link['route'] }}" >
                      <span>{{ $link['title'] }}</span>
                    </x-nav-link>
                  </li>
                @endforeach
              @endif
            </ul>
            @if($loop->last)
              <a href="javascript:void(0);" onclick="openMail(event)" class="p-2 px-4 rounded-md btn_outline_secondary bold" >
                <i class="mr-2 fa-solid fa-paper-plane" ></i>
                <span>Mail Us</span>
              </a>
            @endif
          </div>
        @endforeach
        <div class="block" >
          <h5 class="mb-4 text-lg font-bold border-b" >{{ __('Information') }}</h5>
          <div class="space-y-4" >
            <p>
              <strong>DBID No</strong>
              <span>:</span>
              <span>{{ config('app.dbid_no') }}</span>
            </p>
            <p>
              <strong>Trade License</strong>
              <span>:</span>
              <span>{{ config('app.trade_license') }}</span>
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="mb-16 max-w-7xl" >
    <div class="grid gap-8 lg:grid-cols-3 ml-14" >
      @foreach($branches as $key => $branch)
        <div class="block" >
          <h4 class="mb-4 text-2xl font-bold text-blue-600 uppercase" >{{ $branch->name }}</h4>
          <p>
            <i class="w-6 mr-2 fa-solid fa-map-marker-alt" ></i>
            <span>:</span>
            <span>{{ $branch->address }}</span>
          </p>
          <p class="my-2" >
            <i class="w-6 mr-2 fa-solid fa-phone" ></i>
            <span>:</span>
            <span>{{ $branch->phone }}</span>
          </p>
          <p>
            <i class="w-6 mr-2 fa-solid fa-envelope" ></i>
            <span>:</span>
            <span>{{ $branch->email }}</span>
          </p>
        </div>
      @endforeach
    </div>
  </section>
  <section class="px-6 py-4 text-center bg-gray-800" >
    <p class="text-base text-white">© 2025 All Rights Reserved</p>
  </section>
</footer>

<script>
const email = @json(config('app.support_mail'));

function openMail(e) {
    e.preventDefault();

    if (/Mobi|Android|iPhone/i.test(navigator.userAgent)) {
        window.location.href = `mailto:${email}`;
    } else {
        window.open(
            `https://mail.google.com/mail/?view=cm&fs=1&to=${email}`,
            "_blank"
        );
    }
}
</script>
