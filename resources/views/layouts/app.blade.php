<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="//fonts.gstatic.com" rel="dns-prefetch">
    <link href="css/font-lato.css" rel='stylesheet' type='text/css'>
    <link href="{{asset('idsi.ico')}}" rel="icon" type="image/x-icon" />
    <link href="{{asset('idsi.ico')}}" rel="shortcut icon" type="image/x-icon" />
    <link href="css/jquery-ui.css" rel="stylesheet" type="text/css" />
    <link href="https://www.w3schools.com/w3css/4/w3.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="css/sweetalert.min.css" rel="stylesheet">
    <link href="css/chosen.css" rel="stylesheet" type="text/css" />

    <script src="js/inc/jquery.min.js"></script>
    <script src="js/inc/jquery-3.5.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="js/inc/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.8.1/html2pdf.bundle.min.js" integrity="sha512-vDKWohFHe2vkVWXHp3tKvIxxXg0pJxeid5eo+UjdjME3DBFBn2F8yWOE0XmiFcFbXxrEOR1JriWEno5Ckpn15A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="js/inc/sweetalert.min.js"></script>
    <script src="js/inc/moment.js"></script>
    <script src="js/inc/datetime.js"></script>
    <script src="js/inc/chosen.jquery.js"></script>
    <script src="js/inc/loading-spinner.js"></script>
    <script>
        function scrollReset(){
            $('html, body').animate({scrollTop:0}, 10);
        }
        $(document).on('keyup', '#x_serial', function(){
            var serial = $('#x_serial').val().toUpperCase();
            $('#x_serial').val(serial);
        });
        $(document).on('keypress', '#x_serial', function(e){
            var k;
            document.all ? k = e.keyCode : k = e.which;
            return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || (k >= 48 && k <= 57));
        });
        $(document).on('keyup', '.spChar', function(){
            var uppr = $(this).val().toUpperCase();
            $(this).val(uppr);
        });
        $(document).on('keypress', '.spChar', function(e){
            var k;
            document.all ? k = e.keyCode : k = e.which;
            return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8  || k == 13 || (k >= 48 && k <= 57));
        });
    </script>
    <style>
        #loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100000;
            width: 100vw;
            height: 100vh;
            background-color: rgba(192, 192, 192, 0.85);
            background-repeat: no-repeat;
            background-position: center;
            text-align: center;
            user-select: none;
            cursor: wait;
        }
        #btnReport {
            zoom: 90%;
            background: white;
            border-color: #0d1a80;
            color: #0d1a80;
            height: 30px !important;
            font-size: 12px !important;
            font-weight: bold;
            padding: 0px;
        }
        #btnReport:hover {
            background: #d9534f;
            border-color: #0d1a80;
            color: white;
        }
        #labelReport {
            zoom: 120%;
            margin-left: 2px;
            margin-right: 10px;
        }
        a, img, thead, .xD {
            -webkit-user-drag: none;
            -khtml-user-drag: none;
            -moz-user-drag: none;
            -o-user-drag: none;
            user-select: none;
            user-drag: none;
        }
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
            color:#0d6efd !important;
            border:0px;
        }
        .active {
            background-color: white;
            color: #0d1a80 !important;
        }
        .bp {
            font-weight: bold;
            background-color: #0d1a80;
            border-color: #0d1a80;
            color: white;
        }
        .close {
            zoom: 80%;
            color: white;
            opacity: 100%;
        }
        .swal-modal{
            zoom: 80% !important;
            width: 420px !important;
        }
        .swal-text{
            zoom: 120% !important;
        }
        .chosen-container-single .chosen-single {
            margin-top: -12px !important;
            margin-left: 10px !important;
            height: 30px !important;
            border-radius: 3px !important;
            border: 1px solid #CCCCCC !important;
        }
        .chosen-container-single .chosen-single span {
            padding-top: 2px !important;
        }
        .chosen-container-single .chosen-single div b {
            margin-top: 2px !important;
        }
        .chosen-container-active .chosen-single,
        .chosen-container-active.chosen-with-drop .chosen-single {
            border-color: #ccc !important;
            border-color: rgba(82, 168, 236, .8) !important;
            outline: 0 !important;
            outline: thin dotted \9 !important;
            -moz-box-shadow: 0 0 8px rgba(82, 168, 236, .6) !important;
            box-shadow: 0 0 8px rgba(82, 168, 236, .6) !important;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 110px;
            height: 34px;
            user-select: none;
        }
        .switch input {
            display: none;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ca2222;
            -webkit-transition: .4s;
            transition: .4s;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }
        input:checked + .slider {
            background-color: #2ab934;
        }
        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }
        input:checked + .slider:before {
            -webkit-transform: translateX(75px);
            -ms-transform: translateX(75px);
            transform: translateX(75px);
        }
        .on {
            display: none;
            color: white;
            position: absolute;
            transform: translate(-50%,-50%);
            top: 50%;
            left: 40%;
            font-size: 13px;
            font-weight: bold;
        }
        .off {
            color: white;
            position: absolute;
            transform: translate(-50%,-50%);
            top: 50%;
            left: 60%;
            font-size: 13px;
            font-weight: bold;
        }
        input:checked+ .slider .on {
            display: block;
        }
        input:checked + .slider .off {
            display: none;
        }
        .slider.round {
            border-radius: 34px;
        }
        .slider.round:before {
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div id="loading" style="color: #0d1a80; line-height: 60vh;">
        <strong style="font-size: 40px;">
            Please Wait...
        </strong>
        <br>
        <strong style="font-size: 22px;">
            Please DO NOT interrupt or cancel this process.
        </strong>
    </div>
    @if (!Auth::guest())
        @include('inc.header')
        @include('inc.navbar')
    @endif
    <div id="app" class="container-fluid">
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script>
        function decodeHtml(str){
            var map = {
                '&amp;': '&', 
                '&lt;': '<', 
                '&gt;': '>', 
                '&quot;': '"', 
                '&#039;': "'"
            };
            return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function(m){return map[m];});
        }
        function notifyDeadline(){
            $.ajax({
                type: 'get', 
                url: '/stockrequest/notify',
                success: function(){
                    $('#loading').hide(); Spinner.hide();
                }
            });
        }
        $(document).ready(function(){
            $('#btnReport').hover(
                function(){
                    $('#labelReport').css({"color": "red"});
                    $('#labelReport').removeClass('bg-danger');
                    $('#labelReport').addClass('bg-light');
                },
                function(){
                    $('#labelReport').css({"color": "white"});
                    $('#labelReport').removeClass('bg-light');
                    $('#labelReport').addClass('bg-danger');
                }
            );
        });
    </script>
    @if(Request::is('/'))
        <script src="{{ asset('js/index.js') }}"></script>
    @endif
    @if(Request::is('stocks'))
        <script src="{{ asset('js/stocks.js') }}"></script>
    @endif
    @if(Request::is('stockrequest'))
        <script src="{{ asset('js/stockrequest.js') }}"></script>
    @endif
    @if(Request::is('stocktransfer'))
        <script src="{{ asset('js/stocktransfer.js') }}"></script>
    @endif
    @if(Request::is('users'))
        <script src="{{ asset('js/users.js') }}"></script>
    @endif
    @if(Request::is('changepassword'))
        <script src="{{ asset('js/changepassword.js') }}"></script>
    @endif
    @if(Request::is('assembly'))
        <script src="{{ asset('js/assembly.js') }}"></script>
    @endif
    @if(Request::is('defective'))
        <script src="{{ asset('js/defective.js') }}"></script>
    @endif
    @if(Request::is('maintenance'))
        <script src="{{ asset('js/maintenance.js') }}"></script>
    @endif
</body>
</html>