@extends('layouts.user.app')
@section('title')
    Returnd and Refund | Gorom Bazar
@endsection

@section('content')
<div class="container"> 
    @component('components.home_pages_layout')
        @section('main')
        <h2>
            Returns & Refunds
        </h2>
        <hr>

        <div>
            <h4 class="text_secondary bold">
                1. Eligibility for Returns
            </h4>
            <p>
                We want you to be completely satisfied with your purchase! If you are not happy with your order, you may return it under the following conditions:
            </p>
            <ul>
                <li>
                    The item must be returned within 7 days from the delivery date.
                </li>
                <li>
                    The item must be unused, unworn, and in its original packaging with tags intact.
                </li>
                <li>
                    Certain items like personalized, perishable, or intimate products (e.g., undergarments, food items, digital downloads) may not be eligible for return.
                </li>
            </ul>

            <hr>

            <div>

                <h4 class="text_secondary bold">
                    2. How to Initiate a Return
                </h4>
                <p>
                    To return an item, please follow these steps:
                </p>
                <ul>
                    <li>
                        Contact our support team at with your order details and reason for return.
                    </li>    
                    <li>
                        Our team will guide you through the return process and provide a return shipping address.
                    </li>
                    <li>
                        Pack the item securely and ship it back using a trackable courier service.
                    </li>
                </ul>
            </div>

            <hr>
            <div>
                <h4 class="text_secondary bold">
                    3. Refund Process
                </h4>

                <p>
                    Once we receive your return, we will:
                </p>

                <ul>
                    <li>
                        Inspect the item to ensure it meets return conditions.
                    </li>
                    <li>
                        Process your refund within 7 business days after approval.
                    </li>
                    <li>
                        Refunds will be credited to the original payment method (bkash,nagad,rocket etc.).
                    </li>
                </ul>
            </div>

            <hr>

            <div>   

                <h4 class="text_secondary bold">
                    4. Exchanges
                </h4>
                <p>
                    If you wish to exchange an item, please initiate a return first, and then place a new order.
                </p>
            </div>

            <hr>
            
            <div>
                <h4 class="text_secondary bold">
                    5. Return Shipping Costs
                </h4>
                <p>
                    If the return is due to our error (wrong item, defective product), we will cover the return shipping costs.
                </p>
                <p>
                    For other reasons (e.g., change of mind, wrong size ordered), customers are responsible for return shipping fees.
                </p>
            </div>

            <hr>
            
            <div>
                <h4 class="text_secondary bold">
                    6. Late or Missing Refunds
                </h4>
                <p>
                    If you haven’t received your refund yet:
                </p>

                <ul>
                    <li>
                        Check your bkash,nagad,rocket  payment method again.
                    </li>
                </ul>
            </div>
        </div>
        <hr>
        <div>
            <div>

                <h4 class="text_secondary bold">
                    ১. রিটার্নের যোগ্যতা
                </h4>
                <p>
                    আমরা চাই আপনি আপনার কেনাকাটা নিয়ে সম্পূর্ণ সন্তুষ্ট থাকুন! যদি আপনি আপনার অর্ডারে সন্তুষ্ট না হন, তাহলে নিম্নলিখিত শর্তে আপনি পণ্যটি ফেরত দিতে পারেন:
                </p>
                <ul>
                    <li>
                        পণ্যটি ডেলিভারির তারিখ থেকে 7 দিনের মধ্যে ফেরত দিতে হবে।
                    </li>
                    <li>
                        পণ্যটি অব্যবহৃত, অক্ষত এবং আসল প্যাকেজিং ও ট্যাগ সহ থাকতে হবে।
                    </li>
                    <li>
                        কিছু নির্দিষ্ট পণ্য (যেমন: কাস্টমাইজড, নষ্ট হয়ে যাওয়ার সম্ভাবনাযুক্ত, বা অন্তর্বাস, খাদ্যপণ্য, ডিজিটাল ডাউনলোড) ফেরতের জন্য যোগ্য নয়।
                    </li>
                </ul>

                <hr>
                <div>
                    <h4 class="text_secondary bold">
                        ২. রিটার্ন করার পদ্ধতি
                    </h4>
                    <p>
                        একটি পণ্য ফেরত দিতে নিম্নলিখিত ধাপগুলো অনুসরণ করুন:
                    </p>
                    <ul>
                        <li>
                            আমাদের সাপোর্ট টিমের সাথে যোগাযোগ করুন।
                        </li>
                        <li>
                            আপনার অর্ডারের বিবরণ ও ফেরতের কারণ জানান।
                        </li>
                        <li>
                            আমাদের টিম আপনাকে ফেরতের প্রক্রিয়া সম্পর্কে নির্দেশনা দেবে এবং ফেরত পাঠানোর ঠিকানা প্রদান করবে।
                        </li>
                        <li>
                            পণ্যটি নিরাপদে প্যাক করুন এবং ট্র্যাকযোগ্য কুরিয়ার পরিষেবা ব্যবহার করে ফেরত পাঠান।
                        </li>
                    </ul>
                </div>
                <hr>
                <div>
                    <h4 class="text_secondary bold">
                        ৩. রিফান্ড প্রক্রিয়া
                    </h4>
                    <p>
                        আমরা ফেরত পাওয়ার পর নিম্নলিখিত ধাপে রিফান্ড প্রক্রিয়া করবো:
                    </p>
                    <UL>
                        <li>
                            পণ্যটি পর্যালোচনা করা হবে এবং শর্ত পূরণ করলে অনুমোদন দেওয়া হবে।
                        </li>
                        <li>
                            অনুমোদনের পর  ৭ কর্মদিবসের মধ্যে রিফান্ড প্রক্রিয়া করা হবে।
                        </li>
                        <li>
                            রিফান্ডটি আপনার মূল পেমেন্ট পদ্ধতিতে () জমা হবে।
                        </li>

                    </UL>
                </div>
                <hr>

                <div>
                    <h4 class="text_secondary bold">
                        ৪. এক্সচেঞ্জ
                    </h4>
                    <p>
                        আপনি যদি পণ্য পরিবর্তন করতে চান, তাহলে প্রথমে পণ্য ফেরতের অনুরোধ করুন এবং নতুন অর্ডার দিন।
                    </p>
                </div>
                <hr>
                <div>
                    <h4 class="text_secondary bold">
                        ৫. রিটার্ন শিপিং খরচ
                    </h4>
                   <ul>
                        <li>
                            যদি আমাদের ভুলের কারণে (ভুল পণ্য, ত্রুটিপূর্ণ পণ্য) ফেরত দিতে হয়, তাহলে আমরা শিপিং খরচ বহন করবো।
                        </li>
                        <li>
                            অন্য কারণে (যেমন: মত পরিবর্তন, ভুল সাইজ) ফেরত পাঠালে শিপিং খরচ গ্রাহককে বহন করতে হবে।
                        </li>
                   </ul>
                </div>
                <hr>
                <div>
                    <h4 class="text_secondary bold">
                        ৬. দেরি বা অনুপস্থিত রিফান্ড
                    </h4>
                    <p>
                        যদি এখনও রিফান্ড না পান:
                    </p>
                    <ul>
                        <li>
                            আপনার পেমেন্ট মাধ্যম পুনরায় চেক করুন।
                        </li>
                        <li>
                            পেমেন্ট প্রদানকারীর সাথে যোগাযোগ করুন, কারণ প্রসেসিং সময় পরিবর্তিত হতে পারে।
                        </li>
                        <li>
                            সমস্যা থাকলে আমাদের সাথে যোগাযোগ করুন।
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @endsection

        @section('right')
            
        @endsection
    @endcomponent

</div>
@endsection