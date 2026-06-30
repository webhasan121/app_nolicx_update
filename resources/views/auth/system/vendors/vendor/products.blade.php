<x-app-layout>
    <x-dashboard.page-header>
        Vendor Products
        <br>
        @include('auth.system.vendors.navigations')
    </x-dashboard.page-header>
    
    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Products
                </x-slot>
                <x-slot name="content">
                    @php
                        $filter = request('filter') ?? 'Active';
                    @endphp
                    <x-nav-link :active="$filter == 'Active'">Active</x-nav-link>
                    <x-nav-link :activek="$filter == 'Disable'">Disable</x-nav-link>
                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>
                <div>
                    <x-dashboard.table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Price</th>
                                <th>Sell</th>
                                <th>Profit</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                
                        <tbody>
                            <tr>
                                <td>01</td>
                                <td></td>
                                <td>
                                    Product Title
                                </td>
                                <td>Active</td>
                                <td>200 TK</td>
                                <td>3</td>
                                <td>20 TK</td>
                                <td>20 July, 2024</td>
                                <td>
                                    <div class="flex">
                                        <x-nav-link href="/">
                                            Disable
                                        </x-nav-link>
                                        <x-nav-link href="/">
                                            View
                                        </x-nav-link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </x-dashboard.table>
                
                    <div class="alert alert-danger mt-2">
                        No Product found !
                    </div>
                </div>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>
    {{-- <x-dashboard.container>
        <x-dashboard.section>

        </x-dashboard.section>
    </x-dashboard.container> --}}


</x-app-layout>