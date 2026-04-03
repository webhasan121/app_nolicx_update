@extends('layouts.user.dash.userDash')

@section('title')
    Task History | Gorom Bazar
@endsection

@section('content')
    <div class="container">
        <h4>Task History</h4>
        <hr>

        <div class="overflow-x-scroll">
            @if (count($tasks) > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>    
                            <th>Date</th>    
                            <th>Time</th>    
                            <th>Amount</th>    
                        </tr>    
                    </thead>           
                    @foreach ($tasks as $key => $com)
                        <tbody>
                            <tr>
                                <td>{{$loop->iteration }}</td>
                                <td>
                                    {{ date('d-m-Y', strtotime($com->created_at)) }}
                                </td>
                                <td>
                                    {{ date('h:i:s A', strtotime($com->created_at)) }}
                                </td>
                                <td>
                                    {{ $com->coin ?? "0" }} TK
                                </td>
                            </tr>
                        </tbody>
                    @endforeach         
                </table>

                {{-- {!! $tasks->link !!} --}}
            @else 
                <div class="alert alert-info">
                    No Data Available !
                </div>
            @endif
        </div>
        
    </div>
@endsection