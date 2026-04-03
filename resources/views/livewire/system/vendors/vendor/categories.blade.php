<div>
    {{-- Stop trying to control. --}}
    <x-dashboard.page-header>
     
        @include('auth.system.vendors.navigations')
    </x-dashboard.page-header>

    <x-dashboard.container>
        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Categories
                </x-slot>
                <x-slot name="content">

                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>
                <x-dashboard.table>
                    
                    <tbody>
                        <tr>
                            <td>01</td>
                            <td></td>
                            <td>Untitled</td>
                            <td>20</td>
                            <td>Today</td>
                        </tr>
                    </tbody>
                </x-dashboard.table>
            </x-dashboard.section.inner>
        </x-dashboard.section>
    </x-dashboard.container>
    
</div>
