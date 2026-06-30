<div>
    <!-- Because you are alive, everything is possible. - Thich Nhat Hanh -->
    @props(['name'])

    @if (auth()->user()->hasRole($name))
        {{$slot}}
    @endif
</div>