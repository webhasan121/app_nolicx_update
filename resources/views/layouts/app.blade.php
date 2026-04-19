<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'nolicx') }}</title>

    {{-- site icon --}}
    <link rel="shortcut icon" href="{{asset('icon.png')}}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script> --}}

    {{--
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css"> --}}

    <style>
        /* td,
        th {
            white-space: nowrap
        } */

        tr:hover {
            background-color: #f3f4f6
        }

        tr:nth-child(even) {
            background-color: #f9fafb
        }

        .discount-badge {
            position: absolute;
            top: 0;
            left: 0;
            color: white;
            font-weight: bold;
            padding: 3px 8px;
            clip-path: polygon(0px 0px, 75px 0px, 0px 75px);
            width: 100px;
            height: 100px;
            text-align: center;
            display: flex;
            font-size: 18px;
        }
    </style>

    @stack('style')
</head>

<body class="h-screen overflow-x-hidden font-sans antialiased bg-gray-100">
    <div class="h-full overflow-y-auto">
        <x-client.support-button />
        @includeIf('layouts.user.dash.header')


        <!-- Page Heading -->
        <div class="flex sm:px-6 lg:px-8 ">
            <div class="hidden h-auto md:block" style="width:220px">
                <div class="w-full pt-2 pb-3">
                    {{-- <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                        wire:navigate>
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link> --}}

                    @php
                    $get = auth()->user()->active_nav;
                    @endphp

                    @includeif('layouts.responsive_navigation')

                    @if (auth()->user()->hasRole('vendor') && $get == 'vendor')
                    {{-- vendor primary nav --}}
                    @includeif('layouts.vendor.navigation.responsive')
                    @endif

                    @if (auth()->user()->hasRole('reseller') && $get == 'reseller')
                    {{-- reseller primary nav --}}
                    @includeif('layouts.reseller.navigation.responsive')
                    @endif

                    @if (auth()->user()->hasRole('rider') && $get == 'rider')
                    {{-- rider primary nav --}}
                    @includeIf('layouts.rider.navigation.responsive_navigation')
                    @endif
                </div>
            </div>
            <div class="w-full">

                @if (isset($header))
                <header class="">
                    <div class="w-full px-2 px-4 py-6 mx-auto sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
                @endif

                <!-- Page Content -->
                <main class="overflow-y-auto">
                    {{ $slot }}
                </main>

            </div>
        </div>
    </div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function () {
            if (window.Livewire && typeof window.Livewire.on === 'function') {
                Livewire.on('info', (data) => {
                    Swal.fire({
                        title: 'Attention',
                        text: data,
                        icon: 'Info',
                        confirmButtonText: 'OK'
                    })
                });
                Livewire.on('success', (data) => {
                    Swal.fire({
                        title: 'Done',
                        text: data,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    })
                });
                Livewire.on('warning', (data) => {
                    Swal.fire({
                        title: 'Alart !',
                        text: data,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    })
                });
                Livewire.on('error', (data) => {
                    Swal.fire({
                        title: 'Error !',
                        text: data,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    })
                });
            }

        });

</script>

@if($message = session('warning'))
<script>
    Swal.fire({
            title: 'Warning!',
            text: '{{$message}}',
            icon: 'warning',
            confirmButtonText: 'Understood'
        })
</script>
@endif

@if($message = session('success'))
<script>
    Swal.fire({
            title: 'Success',
            text: '{{$message}}',
            icon: 'success',
            confirmButtonText: 'Done'
        })
</script>
@endif

@if($message = session('error'))
<script>
    Swal.fire({
            title: 'Error !',
            text: '{{$message}}',
            icon: 'error',
            confirmButtonText: 'Close'
        })
</script>
@endif

@if($message = session('info'))
<script>
    Swal.fire({
            title: 'Info !',
            text: '{{$message}}',
            icon: 'info',
            confirmButtonText: 'Understood'
        })

</script>
@endif

@stack('script')

</html>
