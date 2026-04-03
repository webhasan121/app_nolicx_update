<div>
    {{-- The Master doesn't talk, he acts. --}}
   <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">Your Tasks</x-slot>
                <x-slot name="content">
                    Your task and earnings
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <x-dashboard.table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Earning</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasks as $item)
                            <tr>
                                <td> {{$loop->iteration}} </td>
                                <td> {{$item->created_at?->toFormattedDateString()}} </td>
                                <td> {{$item->coin ?? 0}} </td>
                                <td>
                                    @php
                                        $m = round($item->time / 60, 0);
                                        $s = $item->time % 60;
                                    @endphp
                                    {{$m}} : {{$s}} min 
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>
   </x-dashboard.container>
</div>
