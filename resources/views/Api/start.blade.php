@extends('layouts.api')

@section('content')
    <div class="">
        <h1>Api Overview</h1>
        <p>
            This API allows <strong>USERS</strong> to interact with the <strong>ERUHI</strong> . It provides Valid JSON endpoints to manage and retrieve data efficiently and securely. Before start integrating api, we need some configuration.
        </p>
        <hr/>
        <div class="p-2 rounded border bg-white">
           
            Base URL : 
            <h5>
                <b class="fw-bold alert-success p-1 w-100">https://eruhi.gorombazar.com/api</b>
            </h5>
            <p>
                Kep this mind, Base URL is dynamic. so save it to a grobal variable. Now we are using a sub-domain, but soon we will shipped to own hosting. so base url need to updated.
            </p>

            <h6>
                We allow here only <strong class="p-1 alert-info">GET</strong> and <strong class="p-1 alert-info">POST</strong> request.
            </h6>
        </div>
        <hr />

        <h3>Request Headers Settings</h3>
        <div class="p-2 rounded border bg-white">
           <p>
            You need to send bellow mandetory header to every request.
            <ul>
                <li><strong>Accept</strong> with the value of <strong>Application/Json</strong> </li>
                <li>X-MASTER-KEY</li>
                <li>
                    and another <b>Authorization</b> header with <strong>Bearer</strong> token for authenticated user. You will get the token after successfully authenticated.
                </li>
            </ul>


          
           </p>
            Api Master Key : 
            <h5>
                <b class="fw-bold"> {{config('app.api_master_key')}} </b>
            </h5>
            <p>
                This also a global. This master key need to send to every request <strong>X-MASTER-KEY</strong> as a header.
            </p>
            <hr>


        </div>



        <hr>

        <h3>Endpoints</h3>

    </div>
@endsection
