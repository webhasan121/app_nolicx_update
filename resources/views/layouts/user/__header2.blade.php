<?php 

 
use App\Models\cart;
use App\Models\Navigations;
use App\Models\Category;
use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;
use Livewire\Attributs\On;
use function Livewire\Volt\{computed};
 
// $count = computed(function () {
//     return auth()->user() ? auth()->user()->myCarts()->count() : "0";
// });


new class extends Component {

    public $count = 0, $navigations = [], $categories = [];
    protected $listeners = ['$refresh'];

    public function mount() 
    {
        $this->count();
        $this->navigations = Navigations::with('links')->get();
        // $this->categories = Category::where(['belongs_to' => false, 'slug' => 'default-category'])->orWhereNull('belongs_to')->get();
        $this->categories = Category::getAll();
    }
    
    #[On('cart')]
    public function count() 
    {
        $this->count = auth()->user() ? auth()->user()->myCarts()->count() : "0";
        // $this->count ++;
    }
    
    /**
     * Log the current user out of the application.
     */
     public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    public function login() 
    {
        $this->redirect('/login', navigate:true);    
    }
    
}
?>

<div class="bg-white text-center">

    {{-- normal nav on desktop --}}
    <div class="w-full px-3 max-w-8xl mx-auto flex justify-between items-center" id="desktop-nav" x-data="{open:false}" >

        <div class="flex items-center gap-6" >
            <button class="border-r px-2" x-on:click="open = !open">
                <i x-show="!open" class="fas fa-align-justify text-lg"></i>
                <i x-show="open" class="fas fa-times text-lg"></i>
            </button>
            <a wire:navigate href="/" class="flex items-center">
                <img height="50px" width="60px" src="{{asset('icon.png')}}" alt="">
                <div class="ps-2 text-lg font-bold">
                    {{-- app name --}}
                    <x-application-name />
                </div>
            </a>
        </div>

        {{-- logo --}}
        {{-- <a wire:navigate href="/" class="flex items-center">
            <img height="50px" width="60px" src="{{asset('icon.png')}}" alt="">
            <div class="ps-2 text-lg font-bold">
                <x-application-name />
            </div>
        </a> --}}

        {{-- search --}}
        <div class="hidden md:flex justify-between items-center flex-1 w-full px-4" id="search_content">
            <a wire:navigate href="{{route('shops.reseller')}}" class="block px-2"> Shops </a>

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

            <div class="pe-4 max-w-md nv-shop-btn relative" id="" style="width:200px">
                <div class="flex items-center justify-center cursor-pointer">
                    <div>Category</div>
                    <i class=" px-3 pb-2 fas fa-sort-down"></i>
                </div>

                <div id="" class="w-auto nv-shop-item hidden absolute left-0 border shadow bg-white"
                    style="top:100%; max-width:1100px;">
                    <div class="">
                        @volt()
                        <div class=""
                            style="display:grid; grid-template-columns:repeat(auto-fit, 150px); max-width:1400px">

                            @foreach ($categories as $item)
                            {{--
                            <x-client.cat-loop :item="$item" :key="$item->ids" /> --}}
                            @if ($item->slug != 'default-category')
                            <div class="space-x-2 text-start h-full p-2 " style="">
                                <x-nav-link class=" text-gray-900 text-md font-bold" style="font-size: 16px"
                                    href="{{ route('category.products', ['cat' => $item->slug]) }}">
                                    {{-- {{ Str::limit( Str::ucfirst( $item->name), 8,'..') }} --}}
                                    {{$item->name}}
                                    {{-- <i class="fas fa-chevron-right"></i> --}}
                                </x-nav-link>

                                <div class="block">
                                    @foreach ($item->children as $child)
                                    <div>
                                        {{-- <x-nav-link class="block">{{$child->name ?? "N/A"}}</x-nav-link> --}}
                                        <x-nav-link class=" text-gray-900 text-sm"
                                            href="{{ route('category.products', ['cat' => $child->slug]) }}">
                                            {{-- <i class="fas fa-chevron-right"></i> --}}
                                            {{-- {{ Str::limit( Str::ucfirst( $child->name), 10,'..') }} --}}
                                            {{$child->name}}
                                        </x-nav-link>
                                    </div>


                                    @foreach ($child->children as $sub)
                                    <div class="ms-3">
                                        <x-nav-link class=" text-gray-900 text-sm"
                                            href="{{ route('category.products', ['cat' => $sub->slug]) }}">
                                            {{-- <i class="fas fa-chevron-right"></i> --}}
                                            {{-- {{ Str::limit( Str::ucfirst( $sub->name), 10,'..') }} --}}
                                            {{$sub->name}}
                                        </x-nav-link>
                                    </div>
                                    @endforeach
                                    @endforeach

                                </div>
                            </div>
                            @endif

                            @endforeach
                        </div>
                        @endvolt
                    </div>
                </div>
            </div>

            <div class="relative  flex-1">
                <form action="{{route('search')}}">
                    <input type="search" name="q" value="{{request()->get('q')}}"
                        placeholder="Search Product By Title or Tags"
                        class="border-0 rounded-md shadow-0 focus:border-0 focus:shadow-0 w-full"
                        style="margin-bottom: 0px;" id="search">
                </form>
                {{-- <button class="rounded mx-2" @click="$dispatch('open-modal', 'search-modal')">
                    <i class="fas fa-search text-md p-2"></i>
                </button> --}}
            </div>
        </div>


        {{-- right content --}}
        <div>
            @auth
            <div class="flex items-center">
                <x-nav-link href="{{route('carts.view')}}" class="mr-3">
                    <button type="button" class="btn flex items-center">
                        <i class="fas fa-cart-plus"></i>
                        <span id="displayCartItem" class="pb-3 text-green">
                            @auth
                            @volt('cart')
                            <div>
                                {{$this->count ?? "0"}}
                            </div>
                            @endvolt
                            @endauth
                            @guest
                            0
                            @endguest
                            {{-- <span class="visually-hidden">unread messages</span> --}}
                        </span>
                    </button>
                </x-nav-link>

                <div class="flex">
                    <div class="flex sm:items-center sm:ms-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="flex items-center px-3 py-2 border border text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Str::limit(Auth::user()->name ?? "Unauthorize" , 8, '...') }}</div>

                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                @if (count(auth()->user()->getRoleNames()) > 1)
                                <x-dropdown-link wire:navigate class="bold" target="_blank" :href="route('dashboard')">
                                    <i class="fas fa-home pr-2"></i> Dashboard
                                </x-dropdown-link>
                                <x-hr />
                                @endif

                                <x-dropdown-link :href="route('user.index')">
                                    <i class="fas fa-gauge pr-2"></i> {{ __('User Panel') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('user.orders.view')">
                                    <i class="fas fa-shopping-cart pr-2"></i>
                                    {{ __('Order') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('edit.profile')">
                                    <i class="fas fa-user pr-2"></i>
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                @if (empty(auth()->user()->active_nav))

                                <x-hr />

                                <x-dropdown-link :href="route('upgrade.vendor.create', ['upgrade' => 'vendor'])">
                                    <i class="fas fa-shop pr-2"></i> {{ __('Request Vendor') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('upgrade.vendor.create', ['upgrade' => 'reseller'])">
                                    <i class="fas fa-shop pr-2"></i> {{ __('Request Reseller') }}
                                </x-dropdown-link>


                                {{-- <x-dropdown-link :href="route('user.orders.view')">
                                    <i class="fas fa-truck pr-2"></i> {{ __('Request Rider') }}
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

                                {{--
                                <hr>
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Change Password') }}
                                </x-dropdown-link>
                                <hr> --}}


                                <x-hr />
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                    <i class="fas fa-sign-out pr-2"></i> {{ __('Log Out') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>
            @endauth
            @guest
            <x-nav-link class=" px-3 text-md uppercase " :href="route('login')">
                <i class="fas fa-sign-in pr-2"></i> login
            </x-nav-link>
            @endguest
        </div>
    </div>
</div>

{{-- sticky nav --}}
<div class="bg-white w-full fixed z-50 top-0 left-0" id="sticky-nav" x-data="{open:false}">

    <div class="w-full px-3 max-w-8xl mx-auto flex justify-between items-center" x-data="{search:false}">
        {{-- logo --}}
        <div class="flex items-center">
            <button class="border-r px-2" x-on:click="open = !open">
                <i x-show="!open" class="fas fa-align-justify text-lg"></i>
                <i x-show="open" class="fas fa-times text-lg"></i>
            </button>
            <div class="flex items-center">
                <a wire:navigate href="/" class="flex items-center">
                    <x-application-logo style="width:40px" />
                    <div class="ps-2 text-lg font-bold">
                        {{-- app name --}}
                        <x-application-name />
                    </div>
                </a>
            </div>
        </div>

        {{-- search --}}
        {{-- <div x-show="search" x-transition
            class="absolute border rounded shadow flex justify-between items-center flex-1 pr-3"
            style="left: 30px; width:300px" id="search_content">
            <div class="relative w-full flex-1 px-2">
                <form action="{{route('search')}}">
                    <input type="search" name="q" autofocus placeholder="Search Product By Title or Tasgs "
                        class="mb-0 border-0 rounded-md shadow-0 blur:border-0 blur:shadow-0 " id="" autofocus
                        style="margin-bottom:0px">
                </form>
            </div>
            <button x-on:click="search = !search">
                <i class="fas fa-times"></i>
            </button>
        </div> --}}


        {{-- right content --}}
        <div>

            <div class="flex items-center justify-between">
                <button class="rounded mx-2" @click="$dispatch('open-modal', 'search-modal')">
                    <i class="fas fa-search text-md p-2"></i>
                </button>
                @auth
                <x-nav-link href="{{route('carts.view')}}" class="mr-3">
                    <button type="button" class="btn flex items-center">
                        <i class="fas fa-cart-plus"></i>
                        <span id="displayCartItem" class="pb-3 text-green">
                            @auth
                            @volt('cart')
                            <div>
                                {{$this->count ?? "0"}}
                            </div>
                            @endvolt
                            @endauth
                            @guest
                            0
                            @endguest
                            {{-- <span class="visually-hidden">unread messages</span> --}}
                        </span>
                    </button>
                </x-nav-link>

                <div class="flex">
                    <div class="flex sm:items-center sm:ms-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="flex items-center px-3 py-2 border border text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div>
                                        {{ Str::limit(auth()->user()->name, 8,'..')}}
                                    </div>

                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">

                                <x-dropdown-link :href="route('user.index')">
                                    {{ __('User Panel') }}
                                </x-dropdown-link>


                                {{--
                                <hr>
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Change Password') }}
                                </x-dropdown-link>
                                <hr> --}}

                                <x-dropdown-link :href="route('upgrade.vendor.create', ['upgrade' => 'vendor'])">
                                    <i class="fas fa-shop pr-2"></i> {{ __('Request Vendor') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('upgrade.vendor.create', ['upgrade' => 'reseller'])">
                                    <i class="fas fa-shop pr-2"></i> {{ __('Request Reseller') }}
                                </x-dropdown-link>

                                @if (count(auth()->user()->getRoleNames()) > 1)
                                <x-dropdown-link wire:navigate class="bold" target="_blank" :href="route('dashboard')">
                                    Dashboard
                                </x-dropdown-link>
                                @endif



                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
                @endauth
                @guest
                <x-nav-link class=" px-3 text-md uppercase " href="/login">
                    <i class="fas fa-sign-in pr-2"></i> login
                </x-nav-link>
                @endguest
            </div>
        </div>


    </div>



    {{-- other side nav --}}
    <div class="fixed left-0 h-screen bg-white shadow-lg overflow-y-scroll hidden" :class="{'hidden' : !open}"
        x-transition style="top:40px;width:250px;">
        <a wire:navigate href="{{route('shops.reseller')}}"
            class="w-full p-3 bg-indigo-200 py-4 border rounded flex justify-between items-center mb-4"> Shops <i
                class="fas fa-caret-right"></i> </a>

        @volt()
        <div>
            @foreach ($categories as $item)
            <div class="p-3 border-b bg-gray-100 mb-1" x-data="{display:false}">
                {{-- btn --}}
                <button class="flex justify-between items-center w-full" x-on:click="display = !display">
                    {{$item->name ?? "N/A"}}
                    <i x-show="display" class="fas fa-sort-up"></i>
                    <i x-show="!display" class="fas fa-sort-down"></i>
                </button>

                {{-- content --}}
                <div x-show="display">
                    <x-client.cat-loop :item="$item" :key="$item->ids" />
                    {{-- @foreach ($item->links as $il)

                    <div class="py-1">
                        <x-nav-link class="block"> {{$il->name ?? "N/A"}} </x-nav-link>
                    </div>

                    @endforeach --}}
                    {{-- <div class="py-1">
                        <x-nav-link class="block">Home</x-nav-link>
                    </div>
                    <div class="py-1">
                        <x-nav-link class="block">Home</x-nav-link>
                    </div>
                    <div class="py-1">
                        <x-nav-link class="block">Home</x-nav-link>
                    </div> --}}
                </div>
            </div>
            @endforeach
        </div>
        @endvolt

    </div>



    <x-modal name="search-modal">
        <div class="p-3">
            <form action="{{route('search')}}">
                <input type="search" name="q" value="{{request()->get('q')}}"
                    placeholder="Search Product By Title or Tasgs" class="border rounded-md w-full"
                    style="margin-bottom: 0px;" id="search">
                <hr class="my-2" />
                <x-primary-button type="submit"> Search </x-primary-button>
            </form>
        </div>
    </x-modal>

</div>



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