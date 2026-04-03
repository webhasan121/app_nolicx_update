<div>
    {{-- In work, do what you enjoy. --}}
    <p>
        {{count($shops)}} shops found !
    </p>
    <div
        style="display: grid; grid-template-columns:repeat(auto-fit, 300px); justify-content:start; align-items:start; grid-gap:10px">

        @foreach ($shops as $shop)
        <x-client.shops-cart :$shop :key="$shop->id" />
        @endforeach

    </div>
</div>