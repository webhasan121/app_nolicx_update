<div>
    @props(['data'])
    @if (isset($data) && count($data) > 0)

    {{$slot}}

    @else
    <div class="alert alert-danger">No Data Found !</div>
    @endif
</div>