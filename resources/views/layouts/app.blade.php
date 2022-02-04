<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script> --}}
    <script src="js/inc/jquery-3.5.1.js"></script>
    <script src="js/inc/moment.js"></script>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    {{-- <link rel="stylesheet" href="css/font-awesome.min.css"> --}}
    {{-- <link href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" rel="Stylesheet" type="text/css" /> --}}
    <link href="css/jquery-ui.css" rel="Stylesheet" type="text/css" />
    <link rel="icon" href="{{asset('idsi.ico')}}" type="image/x-icon" />
    <link rel="shortcut icon" href="{{asset('idsi.ico')}}" type="image/x-icon" />
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'> --}}
    {{-- <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'> --}}
    <link href="css/font-lato.css" rel='stylesheet' type='text/css'>
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css"> --}}
    <link rel="stylesheet" href="css/sweetalert.min.css">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    {{-- <link href="css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous"> --}}
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
    {{-- <link href="css/jquery.dataTables.min.css" rel="stylesheet"> --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css"> --}}
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script> --}}
    <script src="js/inc/jquery.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script> --}}
    <script src="js/inc/sweetalert.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    {{-- <script src="js/inc/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    {{-- <script src="js/inc/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script> --}}
    {{-- <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script> --}}
    <script src="js/inc/jquery.dataTables.min.js"></script>
    {{-- <script src="https://cdn.datatables.net/plug-ins/1.11.3/dataRender/datetime.js"></script> --}}
    <script src="js/inc/datetime.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.8.1/html2pdf.bundle.min.js" integrity="sha512-vDKWohFHe2vkVWXHp3tKvIxxXg0pJxeid5eo+UjdjME3DBFBn2F8yWOE0XmiFcFbXxrEOR1JriWEno5Ckpn15A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        nav {
            background: #0d1a80;
        }
        .n {
            color: white !important;
            font-weight: bold;
            margin-left: 2px;
            margin-right: 2px;
        }
        .nav-item>a:hover {
            background-color: white;
            color:black !important;
            border:0px;
        }
        .active {
            background-color: white;
            color: #0d1a80 !important;
        }
        .bp {
            font-weight: bold;
            background-color: #0d1a80;
            color: white;
        }
        .close {
            color: white;
        }
    </style>
</head>
<?php
  session_start();
?>
<body>
    @if (Auth::guest())
    @else
        @include('inc.header')
        @include('inc.navbar')
    @endif
    <div id="app" class="container">
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    
    @if(Request::is('stocks'))
        <script src="{{ asset('js/stocks.js') }}"></script>   
        <script src="{{ asset('js/item.js') }}"></script>
    @endif
    @if(Request::is('stockrequest'))
        <script src="{{ asset('js/stockrequest.js') }}"></script>  
    @endif
    @if(Request::is('printRequest'))
        <script src="{{ asset('js/stockrequest.js') }}"></script>  
    @endif
    @if(Request::is('users'))
        <script src="{{ asset('js/users.js') }}"></script>   
    @endif
    @if(Request::is('changepassword'))
        <script src="{{ asset('js/changepassword.js') }}"></script>   
    @endif
    @if(Request::is('filemaintenance'))
        <script src="{{ asset('js/filemaintenance.js') }}"></script>   
    @endif
</body>
</html>
