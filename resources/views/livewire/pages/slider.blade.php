<div>

  @if ($slides?->count())
  <div class="m_body">
    <div class="m_slider">
      <div class="m_slides">
        @foreach ($slides as $key => $item)
        <div class="m_slide {{ $key == 0 ? 'm_active' : '' }}">
          {{-- <img src="https://via.placeholder.com/800x400?text=Product+1" loading="lazy" /> --}}
          <a href="{{ $item->action_url ?? route('products.index') }}" wire:nvigation class="m_slide-link">
            {{-- <img src="https://placehold.co/600x400/orange/white" /> --}}
            <img src="{{asset('storage/' .$item->image)}}" />
          </a>
          @if ($item->main_title)
          <div class="hidden m_description md:block" style="background-color: {{$item->action_target}}!important">
            <div>
              <h1 style="color: {{$item->title_color}}">{{$item->main_title }}</h1>
              <p style="color:{{$item->des_color}}">{{$item->description }}</p>
            </div>
            <a class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-indigo-900 rounded-md shadow-sm hover:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25"
              href="{{ $item->action_url ?? route('products.index') }}">
              {{$item->action_text ?? "Shop Now"}}
            </a>
          </div>
          @endif
        </div>
        @endforeach
        {{-- <img src="https://placehold.co/600x400/orange/white" class="m_slide" /> --}}
        {{-- <img src="https://via.placeholder.com/800x400?text=Product+2" class="m_slide" />
        <img src="https://via.placeholder.com/800x400?text=Product+3" class="m_slide" /> --}}
      </div>

      @if ($slides->count() > 1)
      <button class="m_prev"><i class="fas fa-chevron-left"></i></button>
      <button class="m_next"><i class="fas fa-chevron-right"></i></button>
      <div class="m_dots">
        @foreach ($slides as $key => $item)
        <span class="m_dot {{ $loop->first ? 'm_active' : '' }}" data-index="{{$key}}"></span>
        {{-- <span class="m_dot" data-index="1"></span> --}}
        {{-- <span class="m_dot" data-index="2"></span> --}}
        @endforeach
      </div>
      @endif
    </div>
  </div>
  @endif

  {{-- <section class="m_splide" aria-label="Splide Basic HTML Example">
    <div class="m_splide__track">
      <ul class="m_splide__list">
        @foreach ($slides as $item)
        <li class="m_splide__slide">Slide 01</li>
        <li class="m_splide__slide">Slide 02</li>
        <li class="m_splide__slide">Slide 03</li>
        @endforeach
      </ul>
    </div>
  </section> --}}
  {{-- <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script> --}}
  @script
  <script>
    const slides = document.querySelectorAll(".m_slide");
    const dots = document.querySelectorAll(".m_dot");

    let current = 0;
    let interval = null;

    function showSlide(index) {
      if (index === current) return;

      const currentSlide = slides[current];
      const nextSlide = slides[index];

      // Start exit animation
      currentSlide.classList.add("m_exit");

      // After animation ends, clean up the old slide
      setTimeout(() => {
        currentSlide.classList.remove("m_active", "m_exit");
      }, 600); // match transition duration in CSS

      // Show the new slide
      nextSlide.classList.add("m_active");

      // Update dots
      dots.forEach((dot, i) => {
        dot.classList.toggle("m_active", i === index);
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
