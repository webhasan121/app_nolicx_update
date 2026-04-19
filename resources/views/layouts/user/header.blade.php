@php
  $count = auth()->user() ? auth()->user()->myCarts()->count() : '0';
  $navigations = \App\Models\Navigations::with('links')->get();
  $categories = \App\Models\Category::getAll();
@endphp

<header x-data="{open:false}" >
  {{-- normal nav on desktop --}}
  <div class="text-center bg-white" >
    <div class="flex items-center justify-between w-full px-3 mx-auto max-w-8xl" id="desktop-nav" >

      <div class="flex items-center gap-4" >
        <button class="w-20 px-2 border-r" x-on:click="open = !open" >
          <i x-show="!open" class="text-lg fas fa-align-justify" ></i>
          <i x-show="open" class="text-lg fas fa-times" ></i>
        </button>
        {{-- logo --}}
        <a href="/" class="flex items-center" >
          <img height="50px" width="60px" src="{{asset('icon.png')}}" alt="" />
          <div class="text-lg font-bold ps-2" >
            <x-application-name />
          </div>
        </a>
      </div>

      {{-- search --}}
      <div class="items-center justify-between flex-1 hidden w-full px-4 md:flex" id="search_content" >
        <a href="{{route('shops.reseller')}}" class="block px-2"> Shops </a>

        <style>
          .nv-shop-item {
            height: auto;
            z-index: 9;
          }

          .nv-shop-btn:hover .nv-shop-item {
            display: block;
            transition: all linear .3s;
          }
        </style>

        {{-- <div class="relative max-w-md pe-4 nv-shop-btn" id="" style="width:200px">
          <div class="flex items-center justify-center cursor-pointer" >
            <div>{{ __('Category') }}</div>
            <i class="px-3 pb-2 fas fa-sort-down" ></i>
          </div>

          <div id="" class="absolute left-0 hidden w-auto bg-white border shadow nv-shop-item" style="top:100%; max-width:1100px;" >
            <div class="" >
              <div class="" style="display:grid; grid-template-columns:repeat(auto-fit, 150px); max-width:1400px" >
                @foreach ($categories as $item)
                  @if ($item->slug != 'default-category')
                    <div class="h-full p-2 space-x-2 text-start" style="" >
                      <x-nav-link class="font-bold text-gray-900 text-md" style="font-size: 16px" href="{{ route('category.products', ['cat' => $item->slug]) }}" >
                        <span>{{$item->name}}</span>
                      </x-nav-link>

                      <div class="block" >
                        @foreach ($item->children as $child)
                          <div>
                            <x-nav-link class="text-sm text-gray-900 " href="{{ route('category.products', ['cat' => $child->slug]) }}" >
                              <span>{{ $child->name }}</span>
                            </x-nav-link>
                          </div>

                          @foreach ($child->children as $sub)
                            <div class="ms-3" >
                              <x-nav-link class="text-sm text-gray-900 " href="{{ route('category.products', ['cat' => $sub->slug]) }}" >
                                <span>{{ $sub->name }}</span>
                              </x-nav-link>
                            </div>
                          @endforeach
                        @endforeach
                      </div>
                    </div>
                  @endif
                @endforeach
              </div>
            </div>
          </div>
        </div> --}}

        <div class="relative flex-1 max-w-xl" >
          <form action="{{route('search')}}" >
            <input type="search" name="q" value="{{request()->get('q')}}" placeholder="Search Product By Title or Tags"
            class="w-full border border-gray-200 rounded-md shadow-0 focus:border-0 focus:shadow-0" style="margin-bottom: 0px;" id="search" />
          </form>
          {{-- <button class="mx-2 rounded" @click="$dispatch('open-modal', 'search-modal')">
            <i class="p-2 fas fa-search text-md"></i>
          </button> --}}
        </div>
      </div>


      {{-- right content --}}
      <div>
        @auth
          <div class="flex items-center">
            <x-nav-link href="{{route('carts.view')}}" class="mr-3" >
              <button type="button" class="flex items-center btn" >
                <i class="fas fa-cart-plus" ></i>
                <span id="displayCartItem" class="pb-3 text-green" >
                  @auth
                    <div>{{ $count ?? '0' }}</div>
                  @endauth
                  @guest
                    {{ __('0') }}
                  @endguest
                </span>
              </button>
            </x-nav-link>

            <div class="flex" >
              <div class="flex sm:items-center sm:ms-6" >
                <x-dropdown align="right" width="64" >
                  <x-slot name="trigger" >
                    <button class="flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border rounded-md hover:text-gray-700 focus:outline-none">
                      <div>{{ Str::limit(Auth::user()->name ?? "Unauthorize" , 8, '...') }}</div>

                      <div class="ms-1">
                        <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" >
                          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                      </div>
                    </button>
                  </x-slot>

                  <x-slot name="content" >
                    @if (count(auth()->user()->getRoleNames()) > 1)
                      <x-dropdown-link class="bold" target="_blank" :href="route('dashboard')" >
                        <i class="pr-2 fas fa-home" ></i>
                        <span>{{ __('Dashboard') }}</span>
                      </x-dropdown-link>
                      <x-hr />
                    @endif

                    <x-dropdown-link :href="route('user.index')" >
                      <i class="pr-2 fas fa-gauge" ></i>
                      <span>{{ __('User Panel') }}</span>
                    </x-dropdown-link>

                    <x-dropdown-link :href="route('user.orders.view')" >
                      <i class="pr-2 fas fa-shopping-cart" ></i>
                      <span>{{ __('Order') }}</span>
                    </x-dropdown-link>

                    <x-dropdown-link :href="route('edit.profile')" >
                      <i class="pr-2 fas fa-user"></i>
                      <span>{{ __('Profile') }}</span>
                    </x-dropdown-link>

                    @if (empty(auth()->user()->active_nav))
                      <x-hr />

                      <x-dropdown-link :href="route('upgrade.vendor.create', ['upgrade' => 'vendor'])" >
                        <i class="pr-2 fas fa-shop" ></i>
                        <span>{{ __('Request Vendor') }}</span>
                      </x-dropdown-link>

                      <x-dropdown-link :href="route('upgrade.vendor.create', ['upgrade' => 'reseller'])">
                        <i class="pr-2 fas fa-shop" ></i>
                        <span>{{ __('Request Reseller') }}</span>
                      </x-dropdown-link>

                      {{-- <x-dropdown-link :href="route('user.orders.view')" >
                        <i class="pr-2 fas fa-truck" ></i>
                        <span>{{ __('Request Rider') }}</span>
                      </x-dropdown-link> --}}

                      <x-hr />
                    @endif

                    @php
                      $get = auth()->user()->active_nav;
                    @endphp

                    @if ((auth()->user()->hasRole('admin') || auth()->user()?->hasRole('system')))
                      {{-- vendor primary nav --}}
                      <hr>
                      {{-- @includeif('layouts.responsive_navigation') --}}
                      <hr>
                    @endif

                    @if (auth()->user()->hasRole('vendor') && $get == 'vendor')
                      {{-- vendor primary nav --}}
                      <hr>
                      {{-- @includeif('layouts.vendor.navigation.responsive') --}}
                      <hr>
                    @endif

                    @if (auth()->user()->hasRole('reseller') && $get == 'reseller')
                      {{-- reseller primary nav --}}
                      <hr>
                      {{-- @includeif('layouts.reseller.navigation.responsive') --}}
                      <hr>
                    @endif

                    {{-- <hr>
                    <x-dropdown-link :href="route('profile.edit')">
                      <span>{{ __('Change Password') }}</span>
                    </x-dropdown-link>
                    <hr>  --}}

                    <x-hr />
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" >
                      <i class="pr-2 fas fa-sign-out" ></i>
                      <span>{{ __('Log Out') }}</span>
                    </x-dropdown-link>
                  </x-slot>
                </x-dropdown>
              </div>
            </div>
          </div>
        @endauth
        @guest
          <x-nav-link class="px-3 uppercase text-md" :href="route('login')" >
            <i class="pr-2 fas fa-sign-in" ></i>
            <span>{{ __('Login') }}</span>
          </x-nav-link>
        @endguest
      </div>
    </div>
  </div>

  {{-- sticky nav --}}
  <div class="fixed top-0 left-0 z-50 w-full py-1 bg-white" id="sticky-nav" >
    <div class="flex items-center justify-between w-full px-3 mx-auto max-w-8xl" x-data="{search:false}" >
      {{-- logo --}}
      <div class="flex items-center gap-4" >
        <button class="w-20 px-2 border-r" x-on:click="open = !open" >
          <i x-show="!open" class="text-lg fas fa-align-justify" ></i>
          <i x-show="open" class="text-lg fas fa-times" ></i>
        </button>
        <div class="flex items-center">
          <a href="/" class="flex items-center" >
            <x-application-logo style="width:40px" />
            {{-- app name --}}
            <div class="text-lg font-bold ps-2" >
              <x-application-name />
            </div>
          </a>
        </div>
      </div>

      {{-- right content --}}
      <div>
        <div class="flex items-center justify-between" >
          {{-- search --}}
          <button class="mx-2 rounded" @click="$dispatch('open-modal', 'search-modal')">
            <i class="p-2 fas fa-search text-md"></i>
          </button>
          @auth
            <x-nav-link href="{{route('carts.view')}}" class="mr-3" >
              <button type="button" class="flex items-center btn" >
                <i class="fas fa-cart-plus" ></i>
                <span id="displayCartItem" class="pb-3 text-green" >
                  @auth
                    <div>{{ $count ?? '0' }}</div>
                  @endauth
                  @guest
                    {{ __('0') }}
                  @endguest
                  {{-- <span class="visually-hidden">unread messages</span> --}}
                </span>
              </button>
            </x-nav-link>

            <div class="flex" >
              <div class="flex sm:items-center sm:ms-6" >
                <x-dropdown align="right" width="48" >
                  <x-slot name="trigger" >
                    <button class="flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border rounded-md hover:text-gray-700 focus:outline-none">
                      <div> {{ Str::limit(auth()->user()->name, 8,'..') }}</div>

                      <div class="ms-1" >
                        <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" >
                          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                      </div>
                    </button>
                  </x-slot>

                  <x-slot name="content" >

                    <x-dropdown-link :href="route('user.index')" >
                      <span>{{ __('User Panel') }}</span>
                    </x-dropdown-link>

                    {{-- <hr>
                    <x-dropdown-link :href="route('profile.edit')">
                      <span>{{ __('Change Password') }}</span>
                    </x-dropdown-link>
                    <hr> --}}

                    <x-dropdown-link :href="route('upgrade.vendor.create', ['upgrade' => 'vendor'])" >
                      <i class="pr-2 fas fa-shop" ></i>
                      <span>{{ __('Request Vendor') }}</span>
                    </x-dropdown-link>

                    <x-dropdown-link :href="route('upgrade.vendor.create', ['upgrade' => 'reseller'])" >
                      <i class="pr-2 fas fa-shop" ></i>
                      <span>{{ __('Request Reseller') }}</span>
                    </x-dropdown-link>

                    @if (count(auth()->user()->getRoleNames()) > 1)
                      <x-dropdown-link class="bold" target="_blank" :href="route('dashboard')">
                        <span>{{ __('Dashboard') }}</span>
                      </x-dropdown-link>
                    @endif

                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" >
                      <span>{{ __('Log Out') }}</span>
                    </x-dropdown-link>
                  </x-slot>
                </x-dropdown>
              </div>
            </div>
          @endauth
          @guest
            <x-nav-link class="px-3 uppercase text-md" href="/login" >
              <i class="pr-2 fas fa-sign-in" ></i>
              <span>{{ __('Login') }}</span>
            </x-nav-link>
          @endguest
        </div>
      </div>
    </div>

    <x-modal name="search-modal" >
      <div class="p-3" >
        <form action="{{route('search')}}" >
          <input type="search" name="q" value="{{request()->get('q')}}" style="margin-bottom: 0px;" id="search"
            placeholder="Search Product By Title or Tasgs" class="w-full border rounded-md" />
          <hr class="my-2" />
          <x-primary-button type="submit" >{{ __('Search') }}</x-primary-button>
        </form>
      </div>
    </x-modal>
  </div>

  {{-- other side nav --}}
  <aside class="fixed top-0 left-0 z-50 hidden h-screen overflow-y-scroll bg-white shadow-lg" :class="{'hidden' : !open}" x-transition style="width:275px;" >
    <div class="flex items-center gap-4 py-2" >
      <button class="w-20 px-2 border-r" x-on:click="open = !open" >
        <i class="text-lg fas fa-times" ></i>
      </button>
      <div class="flex items-center">
          <a href="/" class="flex items-center" >
            <x-application-logo style="width:40px" />
            {{-- app name --}}
            <div class="text-lg font-bold ps-2" >
              <x-application-name />
            </div>
          </a>
        </div>
    </div>
    <a href="{{route('shops.reseller')}}" class="flex items-center justify-between w-full p-3 py-4 mb-4 bg-indigo-200 border rounded" >
      <span>{{ __('Shops') }}</span>
      <i class="fas fa-caret-right" ></i>
    </a>

    <div>
      @foreach ($categories as $item)
        <div class="p-3 mb-1 bg-gray-100 border-b" x-data="{display:false}" >
          {{-- btn --}}
          <button class="flex items-center justify-between w-full" x-on:click="display = !display" >
            <span>{{ $item->name ?? "N/A" }}</span>
            <i x-show="display" class="fas fa-sort-up" ></i>
            <i x-show="!display" class="fas fa-sort-down" ></i>
          </button>

          {{-- content --}}
          <div x-show="display" >
            <x-client.cat-loop :item="$item" :key="$item->ids" />
            {{-- @foreach ($item->links as $il)
              <div class="py-1" >
                <x-nav-link class="block" >{{ $il->name ?? "N/A" }}</x-nav-link>
              </div>
            @endforeach
            <div class="py-1" >
              <x-nav-link class="block">Home</x-nav-link>
            </div>
            <div class="py-1" >
              <x-nav-link class="block">Home</x-nav-link>
            </div>
            <div class="py-1" >
              <x-nav-link class="block">Home</x-nav-link>
            </div> --}}
          </div>
        </div>
      @endforeach
    </div>
  </aside>
</header>





<script>
    /**
 * code for show and hide the sticky nav
 */
// let desktopNav = document.getElementById('desktop-nav');
// let stickyNav = document.getElementById('sticky-nav');

document.getElementById('sticky-nav').style.opacity = 0;
document.getElementById('sticky-nav').style.display = 'none';
document.addEventListener('scroll', (e) =>
{
   if(document.documentElement.scrollTop > 150 && document.getElementById('desktop-nav') && document.getElementById('sticky-nav')) {
      // console.log('scrolled');
      // console.log(document.getElementById('sticky-nav'));

      document.getElementById('desktop-nav').style.dispaly = 'none';
      document.getElementById('sticky-nav').style.display = 'block';
      document.getElementById('sticky-nav').style.opacity = 1;
   }else{
      document.getElementById('desktop-nav').style.dispaly = 'block';
      document.getElementById('sticky-nav').style.display = 'none';
      document.getElementById('sticky-nav').style.opacity = 0;
   }

});

</script>
