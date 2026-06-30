<!DOCTYPE html>
<html>

<head>
   <meta charset="utf-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
   <meta name="author" content="" />
   <meta name="token" content="{{csrf_token()}}">

   @stack('seo')


   <x-site_icon />

   <link rel="shortcut icon" href={{ asset("icon.png")}} type="">

   {{--
   <x-site_title /> --}}

   <title>
      @isset($title)
      {{$title}} -
      {{config('app.name', 'nolicx')}}
      @else
      {{config('app.name', 'nolicx')}}
      @endisset
   </title>


   {{-- google font --}}
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet">


   {{--
   <link rel="stylesheet" type="text/css" href="{{asset('assets/css/bootstrap.min.css')}}" /> --}}
   {{--
   <link rel="stylesheet" type="text/css" href="{{asset('assets/user/css/bootstrap.css')}}" /> --}}
   <link href="{{asset('assets/user/css/font-awesome.min.css')}}" rel="stylesheet" />
   <link href="{{asset('assets/user/css/style.css')}}" rel="stylesheet" />
   <link href="{{asset('assets/user/css/responsive.css')}}" rel="stylesheet" />
   <link href="{{ asset('assets/css/inner-image-zoom.min.css') }}" type="text/css" rel="stylesheet" >

   {{--
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
   {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script> --}}

   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   @vite(['resources/css/app.css', 'resources/js/app.js'])

   {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
   {{--
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"> --}}
   {{--
   <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/checkout/"> --}}
   {{--
   <link href="../../dist/css/bootstrap.min.css" rel="stylesheet"> --}}
   {{--
   <link href="form-validation.css" rel="stylesheet"> --}}

   <style>
      body {
         background-color: #f0f0f0 !important;
      }

      th {
         vertical-align: middle !important;
         font-size: 14px;
      }

      .discount-badge {
         position: absolute;
         top: 0;
         left: 0;
         color: white;
         font-weight: bold;
         padding: 3px 8px;
         clip-path: polygon(0px 0px, 85px 0px, 0px 75px);
         width: 100px;
         height: 100px;
         text-align: center;
         display: flex;
         font-size: 18px;
      }

      .m_body {
         margin: 0;
         font-family: sans-serif;
         background: #f4f4f4;
         display: flex;
         justify-content: center;
         align-items: center;
         /* height: 100vh; */
      }

      .m_slider {
         position: relative;
         width: 100%;
         height: auto;
         max-height: 400px;
         overflow: hidden;
         /* border-radius: 10px; */
         /* box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); */
         background: #fff;
         aspect-ratio: 16/9;
      }

      .m_slides {
         width: 100%;
         height: 100%;
         position: relative;
      }

      .m_slide {
         width: 100%;
         height: 100%;
         position: absolute;
         top: 0;
         left: 0;
         opacity: 0;
         transform: scale(0.95);
         visibility: hidden;
         transition: opacity 0.6s linear, transform 0.6s linear;
         display: flex;
         align-items: center;
      }

      .m_slide.m_active {
         opacity: 1;
         transform: scale(1);
         visibility: visible;
         z-index: 2;
      }

      .m_slide img {
         width: 100%;
         height: 100%;
         object-fit: unset;
         position: absolute;
         z-index: 0;
         top: 0;
         left: 0;
         /* aspect-ratio: 16/9; */
      }

      .m_description {
         position: relative;
         z-index: 1;
         width: 100%;
         max-width: 400px;
         /* background: #002c3e09; */
         background-color: #ffffffe8;
         padding: 30px;
         margin-left: 40px;
         opacity: 0;
         transform: translateX(-50px);
         transition: opacity 0.6s linear, transform 0.6s linear;
         /* filter: blur(10px); */
         backdrop-filter: blur(8px);
         border-radius: 10px;
         overflow: hidden;
      }

      .m_slide.m_active .m_description {
         opacity: 1;
         transform: translateX(0);
      }

      .m_description h1 {
         margin: 0 0 10px;
         font-size: 28px;
      }

      .m_description p {
         margin: 0 0 15px;
         font-size: 16px;
      }

      /* .description .btn {
      display: inline-block;
      padding: 10px 20px;
      background: #22c55e;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      transition: background 0.3s;
      }

      .description .btn:hover {
      background: #16a34a;
      } */

      .m_dots {
         position: absolute;
         bottom: 15px;
         left: 50%;
         transform: translateX(-50%);
         display: flex;
         gap: 8px;
         z-index: 9;
      }

      .m_dot {
         width: 12px;
         height: 12px;
         border-radius: 50%;
         background-color: rgba(0, 0, 0, 0.4);
         cursor: pointer;
         transition: background-color 0.3s;
      }

      .m_dot .m_active {
         background-color: #000;
      }

      .slide.exit {
         opacity: 0;
         transform: scale(0.95);
         visibility: hidden;
         z-index: 1;
      }

      .mask_bg {
         -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
         mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
      }
   </style>


   @stack('style')

</head>

<body>
   {{--
   <x-vipCounter /> --}}

   <x-client.support-button />
   <div>
      @includeIf('layouts.user.header')
   </div>

   <div class="relative">

      {{$slot}}

   </div>


   @includeIf('layouts.user.footer')

</body>

<script src="{{ asset('assets/js/inner-image-zoom.min.js') }}" ></script>

<script src="https://unpkg.com/js-image-zoom@0.4.1/js-image-zoom.js"></script>

<script>
   document.addEventListener('DOMContentLoaded', function () {

      // const slides = document.querySelectorAll(".slide");
      // const prevBtn = document.querySelector(".prev");
      // const nextBtn = document.querySelector(".next");

      // let current = 0;

      // function showSlide(index) {
      //    slides.forEach(slide => slide.classList.remove("active"));
      //    slides[index].classList.add("active");
      // }

      // prevBtn.addEventListener("click", () => {
      //    current = (current - 1 + slides.length) % slides.length;
      //    showSlide(current);
      // });

      // nextBtn.addEventListener("click", () => {
      //    current = (current + 1) % slides.length;
      //    showSlide(current);
      // });


      if (window.Livewire && typeof window.Livewire.on === 'function') {
         Livewire.on('cart', (data) => {
            const cartCount = document.getElementById('displayCartItem');

            if (cartCount) {
               cartCount.innerHTML = data;
            }
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
      }
   });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.product-zoom-container').forEach(function (el) {
        if (!el.dataset.zoomed) {
            new ImageZoom(el, {
                width: el.offsetWidth,
                zoomWidth: 450
            });
            el.dataset.zoomed = true;
        }
    });
});
</script>

{{-- <script>
document.addEventListener('DOMContentLoaded', function () {

    if (document.querySelector('.product-image-zoom')) {
        new InnerImageZoom('.product-image-zoom', {
            zoomType: 'hover',
            zoomScale: 1.8
        });
    }

});
</script> --}}




@stack('script')

</html>
