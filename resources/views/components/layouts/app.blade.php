<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
      <meta name="keywords" content="" />
      <meta name="description" content="" />
      <meta name="author" content="" />
      <meta name="token" content="{{csrf_token()}}">

      {{-- <x-site_icon />  --}}

      <link rel="shortcut icon" href={{ asset("logo.png")}} type="">

      <x-site_title />

      <title>
        Client Print
      </title>

      {{-- google font  --}}
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
      
      
      {{-- <link rel="stylesheet" type="text/css" href="{{asset('assets/css/bootstrap.min.css')}}" /> --}}
      {{-- <link rel="stylesheet" type="text/css" href="{{asset('assets/user/css/bootstrap.css')}}" /> --}}
      <link href="{{asset('assets/user/css/font-awesome.min.css')}}" rel="stylesheet" />

      @vite(['resources/css/app.css', 'resources/js/app.js'])
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   </head>
   <body>


      <div class="">
         {{$slot}}
      </div>

   </body>
</script>
</html>