<div>
    <!-- The best way to take care of the future is to take care of the present moment. - Thich Nhat Hanh -->
    @if ($errors->any())
        <ul>
            @foreach ($errors as $item)
                <li>
                    <x-input-error :messages="$item" />
                </li>
            @endforeach
        </ul>
    @endif
</div>