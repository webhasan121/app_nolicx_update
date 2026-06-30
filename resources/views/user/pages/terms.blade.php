@extends('layouts.user.app')
@section('title')
    Terms and Conditions | Gorom Bazar
@endsection

@section('content')
    <div class="container">
        @component('components.home_pages_layout')
            @section('main')
                <h2>
                    Terms and Conditions
                </h2>
                <hr>

                <div>
                    <p>
                        These Terms & Conditions govern the use of our eCommerce website. By accessing, placing an order, or using our services, you agree to comply with the following terms.
                    </p>
                </div>
                <hr>
                
                <div>
                    <h4 class="text_secondary bold">1. General Terms</h4>
                    <ul>
                        <li>
                            This website is owned and operated by GoromBazar.
                        </li>
                        <li>
                            We reserve the right to update or modify these terms at any time.
                        </li>
                        <li>
                            By using our website, you agree to follow the latest version of our Terms & Conditions.
                        </li>

                    </ul>
                </div>
                <hr>
                <div>
                    <h4 class="text_secondary bold">
                        2. Products & Services
                    </h4>
                    <ul>
                        <li>
                            We strive to provide accurate descriptions of all products listed on our website.
                        </li>
                        <li>
                            Product availability is subject to stock limitations, and we cannot guarantee that all items will always be available.
                        </li>
                        <li>
                            Prices and discounts may change at any time without prior notice.
                        </li>

                    </ul>
                </div>

                <hr>

                <div>
                    <h4 class="text_secondary bold" >
                        3. Orders & Payment
                    </h4>

                    <ul>
                        <li>
                            When you place an order, you will receive a confirmation email upon successful processing.
                        </li>
                        <li>
                            We accept payments through [list of accepted payment methods, e.g., bkash,nagad,rocket].
                        </li>
                        <li>
                            We reserve the right to cancel or refuse any order due to suspected fraud, pricing errors, or other issues.
                        </li>

                    </ul>
                </div>
                <hr>

                <div>
                    <h4 class="text_secondary bold">
                        4. Shipping & Delivery
                    </h4>
                    <ul>
                        <li>
                            Shipping times and costs are displayed during checkout and very based on location.
                        </li>
                        <li>
                            Delays may occur due to unforeseen circumstances, and we are not responsible for courier delays.
                        </li>
                        <li>
                            Customers must provide accurate shipping details; incorrect information may result in failed delivery.
                        </li>


                    </ul>
                </div>
                <hr>

                <div>
                    <h4 class="text_secondary bold">
                        5. Returns & Refunds
                    </h4>
                    <ul>
                        <li>
                            Our return and refund policy is outlined separately link to Returns & Refunds Policy.
                        </li>
                        <li>
                            Products must meet return eligibility conditions to qualify for a refund.
                        </li>
                        <li>
                            Refunds will be processed via the original payment method within 1 business days.
                        </li>

                    </ul>
                </div>
                <hr>

                <div>
                    <h4 class="text_secondary bold">
                        6. User Accounts & Responsibilities
                    </h4>    
                    <ul>
                        <li>
                            Users may be required to create an account for certain services.
                        </li>
                        <li>
                            You are responsible for maintaining the confidentiality of your account information.
                        </li>
                        <li>
                            Any fraudulent or unauthorized activity may result in account suspension.
                        </li>
                    </ul>
                </div>
                <hr>

                <div>
                    <h4 class="text_secondary bold">
                        7. Intellectual Property
                    </h4>
                    <ul>
                        <li>
                            All website content, including images, text, and logos, is the property of gorombazar and protected by copyright laws.
                        </li>
                        <li>
                            Unauthorized reproduction, distribution, or use of our content is strictly prohibited.
                        </li>
                    </ul>

                </div>
                <hr>

                <div>
                    <h4 class="text_secondary bold">
                        8. Limitation of Liability
                    </h4>
                    <ul>
                        <li>
                            We are not liable for any indirect, incidental, or consequential damages arising from the use of our website or services.
                        </li>
                        <li>
                            We do not guarantee uninterrupted or error-free operation of the website.                        
                        </li>
                    </ul>
                </div>

                <hr>
                <div>
                    <h4 class="text_secondary bold">
                        9. Governing Law
                    </h4>
                    <p>
                        These Terms & Conditions are governed by the laws of Bangladesh.
                    </p>
                </div>
            @endsection

            @section('right')
                
            @endsection
        @endcomponent
    </div>
@endsection