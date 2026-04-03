@extends('layouts.user.app')
@section('title')
    About Us | Gorom Bazar
@endsection

@section('content')

<div class="container">

    {{-- <div class="  fw-bold py-3">
        <div class="container">
            <h1>
                About US
            </h1>
        </div>
    </div> --}}

    @component('components.home_pages_layout')
        @section('main')
            <h2 >About US</h2>
            <hr>
            <p>
                Welcome to GoromBazar – your one-stop destination for high-quality products at the best prices! We are a trusted online marketplace dedicated to bringing you the best shopping experience with a wide range of products, seamless ordering, and excellent customer service.
            </p>
            <hr>
            <h4 class="text_secondary bold">
                Who We Are
            </h4>
            <p>
                GoromBazar is more than just an eCommerce store; it’s a commitment to quality, affordability, and convenience. Our mission is to provide our customers with top-notch products while ensuring a hassle-free shopping experience. Whether you're looking for everyday essentials, trendy fashion, or the latest gadgets, we’ve got you covered!
            </p>
            <hr>
            <h4 class="text_secondary bold">
                Why Choose GoromBazar?
            </h4>
            <ul>
                <li>
                    ✅ High-Quality Products – We handpick every item to ensure superior quality.
                        
                </li>
                <li>
                    ✅ Best Prices Guaranteed – Get the most competitive prices without compromising on quality.
                </li>
                <li>
                    ✅ Fast & Secure Delivery – We ensure safe and timely delivery to your doorstep.
                </li>
                <li>
                    ✅ Easy Returns & Refunds – Hassle-free return policy for a smooth shopping experience.
                </li>
                <li>
                    ✅ Customer Support – Our dedicated support team is always here to help you.
                </li>
            </ul>
            <hr>
            <h4 class="text_secondary bold">
                Our Mission
            </h4>
            <p>
                To create a reliable and convenient online shopping experience with a strong focus on customer satisfaction.            </p>
            <hr>
            <h4 class="text_secondary bold">
                Our Vision
            </h4>
            <p>
                To become a leading eCommerce platform known for trust, quality, and affordability.
            </p>
            <hr>


        @endsection

        @section('right')
            <div style="display: grid; grid-template-columns:repeat(auto-fill, minmax(149px, 1fr));grid-gap: 10px;">
                @foreach($cat as $category)
                    <x-cat :cat="$category" :key="$category->id" />
                @endforeach    
            </div>
        @endsection
    @endcomponent

</div>
@endsection