<div>
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
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $item)
                            <tr>
                                <td> {{$loop->iteration}} </td>
                                <td> 
                                    <div class="flex items-center">
                                        <img width="40px" heigh="40px" src="{{asset('storage/'. $item->image)}}" alt="">
                                        {{$item->name ?? "N/A"}} 
                                    </div>
                                   

                                </td>
                                <td> {{$item->user_id == auth()->user()->id ? "You" : "N/A"}} </td>
                                <td> {{$item->products?->count() ?? "0"}} </td>
                                <td> 

                                    {{$item->created_at->diffForHumans() ?? "N/A"}} 
                                    <br>
                                    <span class="text-xs">
                                        {{$item->created_at->toFormattedDateString()}}
                                    </span>

                                </td>
                                <td>
                                    <x-nav-link href="{{route('vendor.category.edit', ['cat' => $item->id])}}">
                                        <x-primary-button>
                                            edit
                                        </x-primary-button>
                                    </x-nav-link>
                                    <x-danger-button wire:click="destroy({{$item->id}})">
                                        delete
                                    </x-danger-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>
</div>
