@extends('layouts.user.dash.userDash')
@section('content')
<div class="container">

    <h3>
        Purchase Statistics
    </h3>
    {{-- <p>
        Users can earn commissions by <strong>purchasing products</strong>. The commission will be credited to their wallet. Additionally, users can earn commissions when <strong>referred users</strong> make a purchase.                    
    </p> --}}
    <hr>
    @php
        $current = url()->current();
    @endphp
    <form action="{{$current}}">

        <select name="filter" id="filter" class="py-1 px-2">
            <option @selected(request('filter') == 'weak') value="weak">This Weak</option>
            <option @selected(request('filter') == 'today') value="today">Today</option>
            <option @selected(request('filter') == 'month') value="month">This Month</option>
            <option @selected(request('filter') == '*') value="*">All Time</option>
        </select>
        <button class="btn btn-sm btn-success">Check</button>
    </form>
    <table class="table mt-3">
        <tr>
            <td colspan="3">total</td>
            <th> 
                {{$comissions->sum('comission') ?? "0"}} TK    
            </th>
        </tr>
    </table>

    <div class="overflow-x-scroll">
       
        @if (count($comissions) > 0)     
            <table class="table ">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Time</th>
                        <th>Product</th>
                        <th>Amount</th>
                        <th></th>
                    </tr>
                </thead>
            
                <tbody>
                @foreach ($comissions as $com)
                    <tr>
                        <td>{{ $loop->iteration }} </td>
                        <td>{{ $com->created_at->toFormattedDateString() ?? "Not Found!" }} </td>
                        <td>
                            <img style="height: 20px" src="{{asset('product-images/'.$com->product?->image)}}" alt="">
                        </td>
                        <td>
                            {{$com->comission?? "0"}} TK
                        </td>
                        <td>
                            {{$com->info}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            
            </table>
        @else
            <div class="alert alert-info">
                No Date Found !
            </div>
        @endif
    </div>
</div>
{{-- <canvas id="ComissionChart"></canvas> --}}

@push('script')
    
<script>

$(document).ready(function(){
    // let chartLebels = [];
    // let date = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31];
    // let dataForChart = [];

    // axios.get("{{route('user.coin.comission.api')}}", {
    // '_token':"{{csrf_token()}}",
    // })
    // .then(function (response) {
    //     // dataForChart = response.data[1];
    //     const ctx = document.getElementById('ComissionChart');
        
    //     response.data[2].forEach((element, index) => {
    //         // dataForChart.push(element.comission);
    //         // chartLebels.push(new Date(element).getDate())
    //         let date = new Date(element).getDate();
    //         console.log(date);
            
    //     });
    //     new Chart(ctx, {
    //     type: 'line',
    //     data: {
    //         //label 1 - 31
    
    //         // labels: date,
    //         labels:chartLebels,
    //         datasets: [{
    //             label: 'comission',
    //             // data: [12, 19, 3, 5, 2, 3],
    //             data:response.data[1],
    //             borderWidth: 1
    //         }]
    //     },
    //     options: {
    //         scales: {
    //         y: {
    //             beginAtZero: true
    //         }
    //         }
    //     }
    //     });
    //     // console.log(chartLebels);

    //     // new Date('2025-02-04T09:25:02.000000Z').getDay() 
    //     // let getTimestamp = response.data[2];
    //     //get the 1-31 days from here
    //     // let d = new Date();
        

    // })
    // .catch(function (error) {
    //     console.log(error);
    // });

    // console.log(dataForChart);



});
</script>
@endpush
@endsection 