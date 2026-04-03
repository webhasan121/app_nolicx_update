<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}

    <x-dashboard.container>

        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    <b>
                        Notice:
                    </b>

                    You're order from Multiple Shops
                </x-slot>

                <x-slot name="content">
                    You have added product from more than one shop. Please nothe that, items from different shops are
                    shipped seperately, which will result in <strong>Multiple Shipping Charges.
                    </strong> <br>
                    To reduce delivery cost and ensure a smoother experience, we recommend placing orders from <strong>a
                        single shop at a time.</strong> Review the shop name to your cart before placing orders.
                </x-slot>
            </x-dashboard.section.header>
        </x-dashboard.section>

        <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    {{auth()->user()->myCarts()->count() ?? "0"}} items in cart
                </x-slot>
                <x-slot name="content">

                    <x-nav-link-btn @class(['hidden'=> request()->routeIs('user.carts.checkout')])
                        href="{{route('user.carts.checkout')}}">
                        checkout
                        {{-- <x-primary-button>
                        </x-primary-button> --}}
                    </x-nav-link-btn>
                </x-slot>
            </x-dashboard.section.header>
            <x-dashboard.section.inner>
                <x-dashboard.foreach :data="auth()->user()->myCarts">
                    <x-dashboard.table>
                        <thead>
                            <th></th>
                            <th></th>

                            <th>product</th>
                            <th>Shop</th>
                            <th>price</th>
                            <th>date</th>
                            <th>A/C</th>
                        </thead>

                        <tbody>
                            {{-- @php
                            $totalAmount = 0;
                            @endphp --}}
                            @foreach (auth()->user()->myCarts as $cart)
                            {{-- @php
                            $totalAmount =+ $cart->product?->price;
                            @endphp --}}
                            <tr>
                                <td></td>
                                <td>{{$loop->iteration}}</td>

                                <td>
                                    <x-nav-link class="text-xs"
                                        href="{{route('products.details', ['id' => $cart->product?->id ?? '',  'slug' => $cart->product?->slug ?? ''])}}">
                                        <img width="30px" height="30px"
                                            src="{{asset('storage/'. $cart->product?->thumbnail)}}" alt="">
                                        {{$cart->product?->name ?? "N/A" }}
                                    </x-nav-link>
                                </td>
                                <td class="text-xs">
                                    <x-nav-link>
                                        {{$cart->product?->owner?->resellerShop()->shop_name_en ?? "N/A"}}
                                    </x-nav-link>
                                </td>
                                <td>{{$cart?->price ?? "N/A" }}</td>
                                <td>{{$cart->created_at->diffForHumans() ?? "N/A" }}</td>
                                <td>
                                    <x-danger-button wire:click="remove({{$cart->id}})">remove</x-danger-button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>
                                    Total
                                </td>
                                <td></td>
                                <td class="bold">
                                    {{-- <strong> {{$totalAmount ?? "0"}} TK</strong> --}}
                                    <strong> {{auth()->user()->myCarts->sum('price') ?? "0"}} TK</strong>
                                </td>
                            </tr>

                        </tfoot>

                    </x-dashboard.table>
                </x-dashboard.foreach>
            </x-dashboard.section.inner>
        </x-dashboard.section>

        {{-- <x-dashboard.section>
            <x-dashboard.section.header>
                <x-slot name="title">
                    Cart Summery
                </x-slot>
                <x-slot name="content">
                    view your cart items and summery about different shops.
                </x-slot>
            </x-dashboard.section.header>

            <x-dashboard.section.inner>

            </x-dashboard.section.inner>
        </x-dashboard.section> --}}
    </x-dashboard.container>
</div>