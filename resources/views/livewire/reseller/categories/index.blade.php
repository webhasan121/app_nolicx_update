<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <x-dashboard.page-header>
        Categories
    </x-dashboard.page-header>
    @livewire('vendor.categories.create',  key('cat_101'))

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Categories List
                </x-slot>
                <x-slot name="content">
                    View and Edit your listed categories
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <x-dashboard.table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Owner</th>
                            <th>Product</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $item)
                            <tr>
                                <td> {{$loop->iteration}} </td>
                                <td> {{$item->name ?? "N/A"}} </td>
                                <td> {{$item->user_id == auth()->user()->id ? "You" : "N/A"}} </td>
                                <td> {{$item->products?->count() ?? "0"}} </td>
                                <td> 

                                    {{$item->created_at->diffForHumans() ?? "N/A"}} 
                                    <br>
                                    <span class="text-xs">
                                        {{$item->created_at->toFormattedDateString()}}
                                    </span>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>
</div>
