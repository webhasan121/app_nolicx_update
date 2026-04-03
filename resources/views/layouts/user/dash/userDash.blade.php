<!DOCTYPE html>
<html>

<head>
   <meta charset="utf-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
   <meta name="keywords" content="" />
   <meta name="description" content="" />
   <meta name="author" content="" />
   <link rel="shortcut icon" href="{{asset('icon.png')}}" type="">
   <link rel="icon" href="{{asset('icon.png')}}" type="image/x-icon" />

   <x-site_title />

   <!-- Scripts -->
   @vite(['resources/css/app.css', 'resources/js/app.js'])
   @livewireStyles


   {{--
   <link rel="stylesheet" type="text/css" href="{{asset('assets/user/css/bootstrap.css')}}" /> --}}
   {{--
   <link href="{{asset('assets/user/css/font-awesome.min.css')}}" rel="stylesheet" /> --}}
   {{--
   <link href="{{asset('assets/user/css/style.css')}}" rel="stylesheet" /> --}}
   {{--
   <link href="{{asset('assets/user/css/responsive.css')}}" rel="stylesheet" /> --}}
   {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
   {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   {{--
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"> --}}
   {{--
   <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet"> --}}

   {{--
   <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/checkout/"> --}}
   {{--
   <link href="../../dist/css/bootstrap.min.css" rel="stylesheet"> --}}
   {{--
   <link href="form-validation.css" rel="stylesheet"> --}}


   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   <style>
      /* html{
            font-family: inherit !important;
         } */
      body {
         background-color: #f0f0f0 !important;
      }

      thead {
         background-color: rgb(238, 238, 238) !important;

      }

      th {
         vertical-align: middle !important;
         font-size: 14px;
      }

      tr:nth-child(even) {
         background-color: rgb(238, 238, 238);
      }

      #user_asside {
         width: 250px !important;
         height: auto;

      }

      #user_asside .asside_link {
         display: flex;
         padding: 15px;
         /* border-bottom: .5px solid #e5e5e5; */
         /* color: #000; */
         margin: 1px 0px;
         cursor: pointer;
      }

      #user_asside .asside_link:hover {
         /* background-color: #e5e5e5; */
         color: var(--brand-secondary);

      }

      #user_asside .asside_link .fas {
         width: 25px;
         text-align: center
      }

      .active {
         /* background-color: #e5e5e5!important; */
         /* border-left:5px solid hsl(23, 100%, 65%); */
         color: var(--brand-secondary) !important;
         font-weight: bold;
      }

      #user_asside .asside_link~.vip {
         /* font-size: 20p; */

      }

      @media (max-width: 767.98px) {
         #user_asside {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            width: 100% !important;
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            height: 50px;
            background-color: #fff !important;
            z-index: 99999;
         }

         #user_asside .asside_link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 0;
            margin: 0px !important;
            padding: 12px 5px !important;

         }

         /* .active{
               border: 0;
               border-bottom: 3px solid var(--brand-secondary)!important;
               font-weight: 900
            }   */
      }
   </style>
   @stack('style')
</head>

<body style="margin-bottom: 100px">


   <x-client.support-button />
   @include('layouts.user.dash.header')
   <x-dashboard.container>
      <div class="flex">

         {{-- left asside --}}

         <div id="user_asside" class="py-3 rounded position-sm-absolute col-md-3">
            <x-nav-link class="asside_link" :active="request()->routeIs('user.dash')" href="{{route('user.dash')}}">
               <i class="fas fa-home"></i>
               <span class="hidden pl-2 md:block">
                  Dashboard
               </span>
            </x-nav-link>
            {{-- <x-nav-link class="asside_link @if(request()->routeIs('cart.index')) active @endif" href="">
               <i class="pr-2 fas fa-shopping-cart"></i>
               <span class="hidden pl-2 md:block">
                  Cart
               </span>
            </x-nav-link> --}}
            <x-nav-link class="asside_link" :active="request()->routeIs('user.orders.view')"
               href="{{route('user.orders.view')}}">
               <i class="pr-2 fas fa-shopping-cart"></i>
               <span class="hidden pl-2 md:block">
                  Order ({{auth()->user()->myOrderAsUser()?->count() ?? "0"}})
               </span>
            </x-nav-link>
            <x-nav-link class="asside_link vip" :active="request()->routeIs('user.vip.*')"
               href="{{route('user.vip.index')}}">
               <i class="pr-2 fas fa-user-check"></i>
               <span class="hidden pl-2 md:block">
                  VIP
               </span>
            </x-nav-link>
            <x-nav-link class="asside_link wallet" :active="request()->routeIs('user.wallet.*')"
               href="{{route('user.wallet.index')}}">
               <i class="pr-2 fas fa-coins"></i>
               <span class="hidden pl-2 md:block">
                  Wallet
               </span>
            </x-nav-link>
            <x-nav-link class="asside_link wallet" :active="request()->routeIs('user.developer.*')"
               href="{{route('user.developer')}}">
               <i class="pr-2 fas fa-coins"></i>
               <span class="hidden pl-2 md:block">
                  Developer
               </span>
            </x-nav-link>
            <x-nav-link class="asside_link wallet" :active="request()->routeIs('user.management.*')"
               href="{{route('user.management')}}">
               <i class="pr-2 fas fa-coins"></i>
               <span class="hidden pl-2 md:block">
                  Management
               </span>
            </x-nav-link>

            @if ((auth()->user()?->hasRole('reseller') || auth()->user()?->hasRole('vendor')) &&
            !empty(auth()?->user()?->active_nav))
            <x-nav-link class="asside_link shop" href="{{route('my-shop', ['user' => auth()->user()->name])}}"
               :active="request()->routeIs('my-shop')">
               <i class="pr-2 fas fa-shop"></i>
               <span class="hidden pl-2 md:block">
                  My Shop
               </span>
            </x-nav-link>
            @endif
         </div>

         {{-- right content --}}
         <div id="user_content" class="col-md-9 py-2 p-lg-3 w-full mb-[50px]">
            {{$slot}}
         </div>
      </div>

   </x-dashboard.container>


   {{-- @include('layouts.user.footer') --}}
   {{-- <script src="{{asset('assets/user/js/jquery-3.4.1.min.js')}}"></script> --}}
   {{-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script> --}}
   {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script> --}}
   {{-- <script src="{{asset('assets/user/js/custom.js')}}"></script> --}}
   <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
   {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> --}}


</body>
{{-- @if (session('success'))
<script>
   toastr.success("{{ session('success') }}", 'Success', {
              positionClass: 'toast-top-right',
              timeOut: 3000
          });
</script>
@endif

@if (session('warning'))
<script>
   toastr.warning("{{ session('warning') }}", 'warning', {
         positionClass: 'toast-top-right',
         timeOut: 3000
      });
</script>
@endif --}}
<script>
   document.addEventListener('DOMContentLoaded', function () {
      Livewire.on('cart', (data) => {
         Swal.fire({
            title: 'Done!',
            text: 'Cart Updated',
            icon: 'Info',
            confirmButtonText: 'OK'
         })
         document.getElementById('displayCartItem').innerHTML = data;
      });
      Livewire.on('info', (data) => {
         Swal.fire({
            title: 'Look At!',
            text: data,
            icon: 'Info',
            confirmButtonText: 'OK'
         })
      });
      Livewire.on('success', (data) => {
         Swal.fire({
            title: 'Congrass !',
            text: data,
            icon: 'success',
            confirmButtonText: 'OK'
         })
      });
      Livewire.on('warning', (data) => {
         Swal.fire({
            title: 'Alart !',
            text: data,
            icon: 'warning',
            confirmButtonText: 'OK'
         })
      });
      Livewire.on('error', (data) => {
         Swal.fire({
            title: 'Attention !',
            text: data,
            icon: 'error',
            confirmButtonText: 'OK'
         })
      });
   });

</script>

@if($message = session('warning'))
<script>
   Swal.fire({
            title: 'Warning!',
            text: '{{$message}}',
            icon: 'warning',
            confirmButtonText: 'Understood'
         })
</script>
@endif

@if($message = session('success'))
<script>
   Swal.fire({
            title: 'Success',
            text: '{{$message}}',
            icon: 'success',
            confirmButtonText: 'Done'
         })
</script>
@endif

@if($message = session('error'))
<script>
   Swal.fire({
            title: 'Error !',
            text: '{{$message}}',
            icon: 'error',
            confirmButtonText: 'Close'
         })
</script>
@endif

@if($message = session('info'))
<script>
   Swal.fire({
            title: 'Info !',
            text: '{{$message}}',
            icon: 'info',
            confirmButtonText: 'Understood'
         })
</script>
@endif

@stack('script')

</html>
