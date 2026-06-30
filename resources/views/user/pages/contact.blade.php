@extends('layouts.user.app')
@section('title')
    Contact Us | Gorom Bazar
@endsection

@section('content')
    <div class="container">
        @component('components.home_pages_layout')
            @section('main')
            <h2 class="text-left">Contact Us</h2>
            <hr>
                <div>
                    <div class="row">
                        <div class="col-12 py-3">
                            <div class="rounded p-4">
                                

                                <div class="flex align-items-left text-dark mb-3">
                                    <i class=" p-2 rounded-circle bg_primary text-white fas fa-compass mr-3"></i>
                                    
                                        Uttara-10, Dhaka
                                    
                                </div>
                                
                                <div class="flex align-items-left text-dark mb-3">
                                    <i class=" p-2 rounded-circle bg_primary text-white fas fa-phone mr-3"></i>
                                    <a href="callto:+8801863767896">+8801863767896</a>
                                </div>

                                <div class="flex align-items-left text-dark mb-3">
                                    <i class=" p-2 rounded-circle bg_primary text-white fas fa-paper-plane mr-3"></i>
                                    <a href='mainto:gorombazar01@gmail.com'>gorombazar01@gmail.com</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endsection
        @endcomponent
    </div>
@endsection