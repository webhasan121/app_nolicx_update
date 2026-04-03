<div>
    {{--
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/bootstrap.min.css')}}" /> --}}

    <style>
        /* @media only screen and (max-width: 600px) { */
        @media (min-width: 767px) {
            .detail-box h1 {
                font-size: 3rem !important;
                margin-bottom: 0px;
            }
        }

        @media (min-width: 1199px) {
            .detail-box h1 {
                font-size: 4rem !important;
                margin-bottom: 0px;
            }
        }

        @media (max-width: 570px) {

            .slider_bg_box img {
                width: 100%;
                height: auto;
                /* -o-object-fit: cover; */
                /* object-fit: cover; */
                /* -o-object-position: top right;
                object-position: top right; */
                aspect-ratio: 16 / 9;
            }

            .detail-box h1 {
                font-size: 4rem !important;
                margin-bottom: 0px;
            }
        }

        @media (max-width: 767px) {

            .slider {
                /* height:200px!important; */
            }

            /* .slider_bg_box img {
                width: 100%;
                height: 100%;
                -o-object-fit: cover;
                object-fit: cover;
                -o-object-position: top right;
                object-position: top right;
                aspect-ratio: 16 / 9;
            }
            .slider_section {
                padding: 20px 10px;
            }
            .detail-box h1 {
                font-size: 1.5rem!important;
                margin-bottom: 0px;
            }
            .detail-box a {
                margin-top: 0px!important;
                padding: 10px!important;
                font-weight: 500!important;
            }
            .slider_section .detail-box,
            .about_section .detail-box {
                margin-bottom: 0px;
            }
            .slider_section .carousel-indicators li {
                background-color: #ffffff;
                width: 12px!important;
                height: 12px!important;
                border-radius: 100%;
                opacity: 1;
            } */

        }


        .body {
            margin: 0;
            font-family: sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            /* height: 100vh; */
        }

        .slider {
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

        .slides {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .slide {
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

        .slide.active {
            opacity: 1;
            transform: scale(1);
            visibility: visible;
            z-index: 2;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: unset;
            position: absolute;
            z-index: 0;
            top: 0;
            left: 0;
            /* aspect-ratio: 16/9; */
        }

        .description {
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

        .slide.active .description {
            opacity: 1;
            transform: translateX(0);
        }

        .description h1 {
            margin: 0 0 10px;
            font-size: 28px;
        }

        .description p {
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

        .dots {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 9;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(0, 0, 0, 0.4);
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .dot .active {
            background-color: #000;
        }

        .slide.exit {
            opacity: 0;
            transform: scale(0.95);
            visibility: hidden;
            z-index: 1;
        }
    </style>


    @livewire('pages.slider')


    <x-dashboard.container>

        @includeIf('components.client.display-category', ['categories' => $categories])

        @livewire('pages.new-product')
        @livewire('pages.todays-product')

        <div class="py-4">
            <div class="flex items-center justify-between px-2 py-4">
                <div class="text-xl font-bold">
                    Products
                </div>

                <div class="text-center">
                    <x-nav-link href="{{route('products.index')}}" class="px-3 py-2 rounded ">
                        View All
                    </x-nav-link>
                </div>
            </div>
            <div class="product_section" x-loading.disabled x-transition>
                <x-client.products-loop :$products />
            </div>

        </div>
        @livewire('pages.topSales')
    </x-dashboard.container>

    {{-- static slider --}}
    @if (count($ss) > 0)

    <div class="">

        <div class="body">

            <div class="slider">
                <div class="slides">
                    @foreach ($ss as $slider)
                    @foreach ($slider->slides as $key => $item)

                    <div class="slide {{ $key == 0 ? 'active' : '' }}">
                        {{-- <img src="https://via.placeholder.com/800x400?text=Product+1" loading="lazy" /> --}}
                        <a href="{{ $item->action_url ?? route('products.index') }}" wire:nvigation
                            class="w-full slide-link">
                            {{-- <img src="https://placehold.co/600x400/orange/white" /> --}}
                            <img src="{{asset('storage/' .$item->image)}}" class="w-full" />
                        </a>
                    </div>

                    @endforeach
                    @endforeach
                </div>

            </div>

        </div>
    </div>

    @endif

    {{-- recomended product --}}
    <x-dashboard.container>
        @livewire('pages.RecomendedProducts')
    </x-dashboard.container>
</div>




@script

{{-- <script>
    const slides = document.querySelectorAll(".slide");
    const prevBtn = document.querySelector(".prev");
    const nextBtn = document.querySelector(".next");

    let current = 0;

    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove("active"));
        slides[index].classList.add("active");
    }

    prevBtn.addEventListener("click", () => {
        current = (current - 1 + slides.length) % slides.length;
        showSlide(current);
    });

    nextBtn.addEventListener("click", () => {
        current = (current + 1) % slides.length;
        showSlide(current);
    });

</script> --}}

<script>
    const slides = document.querySelectorAll(".slide");
    const dots = document.querySelectorAll(".dot");

    let current = 0;
    let interval = null;

    function showSlide(index) {
    if (index === current) return;

    const currentSlide = slides[current];
    const nextSlide = slides[index];

    // Start exit animation
    currentSlide.classList.add("exit");

    // After animation ends, clean up the old slide
    setTimeout(() => {
    currentSlide.classList.remove("active", "exit");
    }, 600); // match transition duration in CSS

    // Show the new slide
    nextSlide.classList.add("active");

    // Update dots
    dots.forEach((dot, i) => {
    dot.classList.toggle("active", i === index);
    });

    current = index;
    }

    dots.forEach(dot => {
    dot.addEventListener("click", () => {
    const index = parseInt(dot.getAttribute("data-index"));
    showSlide(index);
    restartAutoplay();
    });
    });

    function nextSlide() {
    let next = (current + 1) % slides.length;
    showSlide(next);
    }

    function startAutoplay() {
    interval = setInterval(nextSlide, 5000);
    }

    function stopAutoplay() {
    clearInterval(interval);
    }

    function restartAutoplay() {
    stopAutoplay();
    startAutoplay();
    }

    startAutoplay();




</script>
@endscript



</div>
