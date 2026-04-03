<div>
    
    <x-dashboard.section>
        <x-dashboard.section.header>
            <x-slot name="title">
                Vendor Request
            </x-slot>
            <x-slot name="content">
                Edit and Upgrade Your Vendor Request Form <a href="{{route('upgrade.vendor.index')}}">Previous Request</a>
                
            </x-slot>
        </x-dashboard.section.header>
    
        <x-dashboard.section.inner>
            @php
                $nav = request('nav') ?? "basic";
            @endphp
            <div class="flex justify-between">
                <div>
    
                    <x-nav-link :active="$nav == 'basic'" href="{{url()->current()}}?nav=basic">
                        Basic
                    </x-nav-link>
                    <x-nav-link :active="$nav == 'document'" href="{{url()->current()}}?nav=document">
                        Document
                    </x-nav-link>
                </div>
                <div>
                    <a href="{{route('upgrade.vendor.create')}}">New Request</a>
                </div>
            </div>
        </x-dashboard.section.inner>
    </x-dashboard.section>
    
    {{-- @if ($nav == 'basic')    
        <form action="{{route('upgrade.vendor.update', ['id' => $data->id])}}" method="post"> 
            @csrf
            @includeIf('user.pages.profile-upgrade.vendor.partials.basic')
        </form>
    @endif
    @if($nav == 'document')
    
        @includeIf('user.pages.profile-upgrade.vendor.partials.document')
    
    @endif --}}

</div>
    