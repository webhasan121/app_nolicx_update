<div>
  {{-- Header --}}
  <x-dashboard.page-header >{{ __('Settings') }}</x-dashboard.page-header>

  {{-- Container --}}
  <x-dashboard.container >
    <x-dashboard.section >
      <x-dashboard.section.header >
        <x-slot name="title" >
          <div class="flex items-center justify-between">
            <div>{{ __('Branch Management') }}</div>
            <x-nav-link-btn href="{{route('system.branches.create')}}">
              <i class="fas fa-plus pr-2" ></i>
              <span>{{ __('Branch') }}</span>
            </x-nav-link-btn>
          </div>
        </x-slot>

        <x-slot name="content" >{{ __('Setup your necessary branches from here. add, edit and delete.') }}</x-slot>
      </x-dashboard.section.header>

      <x-dashboard.section.inner>
        <x-dashboard.table>
          <thead>
            <tr>
              <th>{{ __('#') }}</th>
              <th>{{ __('Name') }}</th>
              <th>{{ __('Email') }}</th>
              <th>{{ __('Type') }}</th>
              <th>{{ __('Created') }}</th>
              <th width="60" >{{ __('Action') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($branches as $branch)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $branch->name }}</td>
                <td>{{ $branch->email }}</td>
                <td>{{ $branch->type }}</td>
                <td>{{ $branch->created_at }}</td>
                <td>
                  <div class="flex items-center gap-2" >
                    <x-nav-link-btn href="{{route('system.branches.modify', $branch->id)}}" >
                      <i class="fas fa-edit" ></i>
                    </x-nav-link-btn>
                    <x-danger-button wire:click="delete({{$branch->id}})" >
                      <i class="fas fa-trash" ></i>        
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
