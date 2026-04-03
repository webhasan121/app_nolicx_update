<div>
    
    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <div class="flex items-center justify-between">
                        <div>
                            Page Setup
                        </div>

                        <x-nav-link-btn href="{{route('system.pages.create')}}">
                            <i class="fas fa-plus pr-2"></i> Page
                        </x-nav-link-btn>
                    </div>
                </x-slot>

                <x-slot name="content">
                    Setup your necessary pages from here. add, edit and delete.
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <x-dashboard.table>
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Name </th>
                            <th>Content</th>
                            <th> Status </th>
                            <th> A/C </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($pages as $item)
                            <tr class="border-b hvoer:bg-gray-50">
                                <td> {{$item->id}} </td>
                                <td> 
                                    <x-nav-link href="{{route('system.pages.create', ['page' => $item->slug])}}">
                                        {{$item->name}}   
                                    </x-nav-link> 
                                    <br>
                                    <p class="text-xs">
                                        {{$item->title}}
                                    </p>
                                </td>
                                <td>
                                    {!!
                                        Str::limit($item->content, 100, '...')
                                    !!}
                                </td>
                                <td> {{$item->status}} </td>
                                <td> 
                                    <div class="flex">
                                        
                                        <x-danger-button wire:click="deletePage({{$item->id}})">
                                            <i class="fas fa-trash"></i>        
                                        </x-danger-button>   
                                    </div>    
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>


    </x-dashboard.container>
</div>
