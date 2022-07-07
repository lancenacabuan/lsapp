<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{asset('idsi.ico')}}" rel="icon" type="image/x-icon"/>
    <link href="{{asset('idsi.ico')}}" rel="shortcut icon" type="image/x-icon"/>
    <link href="https://fonts.gstatic.com/" rel="preconnect" crossorigin>
    <link href="https://www.w3schools.com/w3css/4/w3.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css' rel='stylesheet'>
    <link href="css/chosen.css" rel="stylesheet" type="text/css"/>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="js/inc/jquery.min.js"></script>
    <script src="js/inc/jquery-3.5.1.js"></script>
    <script src="js/inc/loading-spinner.js"></script>
    <style>
        .f-outline{
            position: relative;
        }
        .fi{
            background-color: white !important;
        }
        .forminput{
            background-color: white;
            font-size: 13px !important;
            font-family: Arial, Helvetica, sans-serif !important;
        }
        .formlabel{
            font-size: 13px;
            font-family: Arial, Helvetica, sans-serif !important;
            position: absolute;
            left: 0.2rem;
            top: 0.5rem;
            padding: 0 0.5rem;
            color: black;
            cursor: text;
            transition: top 200ms ease-in,
                left 200ms ease-in,
                font-size 200ms ease-in;
        }
        .forminput:focus{
            background-color: white;
            border-color: #0d1a80;
            border-width: 2px;
            box-shadow: none !important;
        }
        .forminput:hover{
            background-color: white;
            border-color: #0d1a80;
            border-width: 2px;
            box-shadow: none !important;
        }
        .forminput:focus ~ .form-label,
        .forminput:not(:placeholder-shown).forminput:not(:focus)
        ~.formlabel{
            top: -0.8rem;
            font-size: 0.8rem;
            left: 0.4rem;
            background-color: white;
            color: #0d1a80;
        }
        input:-webkit-autofill,
        input:-webkit-autofill:focus{
            transition: background-color 600000s 0s, color 600000s 0s;
        }
        input[data-autocompleted]{
            background-color: transparent !important;
        }
        #loading{
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
        #btnReport{
            zoom: 90%;
            background: white;
            border-color: #0d1a80;
            color: #0d1a80;
            height: 30px !important;
            font-size: 12px !important;
            font-weight: bold;
            padding: 0px;
        }
        #btnReport:hover{
            background: #d9534f;
            border-color: #0d1a80;
            color: white;
        }
        a, a:hover, img, thead, .xD{
            -webkit-user-drag: none;
            -khtml-user-drag: none;
            -moz-user-drag: none;
            -o-user-drag: none;
            user-select: none;
            user-drag: none;
            text-decoration: none;
        }
        nav{
            background: #0d1a80;
        }
        .n{
            color: white !important;
            font-weight: bold;
            margin-left: 2px;
            margin-right: 2px;
        }
        .card{
            border-color: #0d1a80;
        }
        .card-header{
            background-color: #0d1a80;
            border-color: #0d1a80;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        #lblChangePassword:hover{
            color: #0d6efd !important;
        }
        .out:hover{
            background-color: #d9534f !important;
        }
        .nav-item>a:hover{
            background-color: #0d6efd;
            color:white !important;
            border:0px;
        }
        .active{
            background-color: white;
            color: #0d1a80 !important;
        }
        .bp{
            font-weight: bold;
            background-color: #0d1a80;
            border-color: #0d1a80;
            color: white;
        }
        .close{
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
        .chosen-container-single .chosen-single{
            margin-top: -12px !important;
            margin-left: 10px !important;
            height: 30px !important;
            border-radius: 3px !important;
            border: 1px solid #CCCCCC !important;
        }
        .chosen-container-single .chosen-single span{
            padding-top: 2px !important;
        }
        .chosen-container-single .chosen-single div b{
            margin-top: 2px !important;
        }
        .chosen-container-active .chosen-single,
        .chosen-container-active.chosen-with-drop .chosen-single{
            border-color: #ccc !important;
            border-color: rgba(82, 168, 236, .8) !important;
            outline: 0 !important;
            outline: thin dotted \9 !important;
            -moz-box-shadow: 0 0 8px rgba(82, 168, 236, .6) !important;
            box-shadow: 0 0 8px rgba(82, 168, 236, .6) !important;
        }
        .switch{
            position: relative;
            display: inline-block;
            width: 110px;
            height: 34px;
            user-select: none;
        }
        .switch input{
            display: none;
        }
        .slider{
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
        .slider:before{
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
        input:checked + .slider{
            background-color: #2ab934;
        }
        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }
        input:checked + .slider:before{
            -webkit-transform: translateX(75px);
            -ms-transform: translateX(75px);
            transform: translateX(75px);
        }
        .on{
            display: none;
            color: white;
            position: absolute;
            transform: translate(-50%,-50%);
            top: 50%;
            left: 40%;
            font-size: 13px;
            font-weight: bold;
        }
        .off{
            color: white;
            position: absolute;
            transform: translate(-50%,-50%);
            top: 50%;
            left: 60%;
            font-size: 13px;
            font-weight: bold;
        }
        input:checked + .slider .on{
            display: block;
        }
        input:checked + .slider .off{
            display: none;
        }
        .slider.round{
            border-radius: 34px;
        }
        .slider.round:before{
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div id="loading" style="color: #0d1a80; line-height: 60vh;">
        <strong style="font-size: 40px;">
            PLEASE WAIT...
        </strong>
        <br>
        <strong style="font-size: 22px;">
            Please DO NOT interrupt or cancel this process.
        </strong>
    </div>
    @if(!Auth::guest())
    @include('inc.header')
    @include('inc.navbar')
    @if(!Request::is('stocks'))
        <script>$('#loading').show(); Spinner(); Spinner.show();</script>
    @endif
    @else
    @include('inc.guest')
    @endif
    <div id="app" class="container-fluid">
        <main class="py-3">
            @yield('content')
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="js/inc/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.8.1/html2pdf.bundle.min.js" integrity="sha512-vDKWohFHe2vkVWXHp3tKvIxxXg0pJxeid5eo+UjdjME3DBFBn2F8yWOE0XmiFcFbXxrEOR1JriWEno5Ckpn15A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>
    <script src="js/inc/moment.js"></script>
    <script src="js/inc/datetime.js"></script>
    <script src="js/inc/chosen.jquery.js"></script>
    <script>
        setInterval(loadFunction, 0);
        function loadFunction(){
            if($('#loading').is(':visible')){
                $('html, body').css({
                    overflow: 'hidden',
                    height: '100%'
                });
                $('#current_user').focus();
            }
            else{
                $('html, body').css({
                    overflow: 'auto',
                    height: 'auto'
                });
            }
        }
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
            if($('#current_url').val() == 'live'){
                $.ajax({
                    type: 'get', 
                    url: '/stockrequest/notify',
                    success: function(){
                        $('#loading').hide(); Spinner.hide();
                    }
                });
            }
            else{
                $('#loading').hide(); Spinner.hide();
            }
        }
        function idleLogout(){
            var timer;
            window.onload = resetTimer;
            window.onmousemove = resetTimer;
            window.onmousedown = resetTimer;
            window.onclick = resetTimer;
            window.onkeydown = resetTimer;

            function autoLogout() {
                window.location.href = '/logout';
            }

            function resetTimer() {
                clearTimeout(timer);
                timer = setTimeout(autoLogout, 3600000);
            }
        }
        idleLogout();
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
    @if(Request::is('/') || Request::is('logs'))
        <script src="{{ asset('js/index.js') }}"></script>
    @endif
    @if(Request::is('stocks'))
        <script src="{{ asset('js/stocks.js') }}"></script>
    @endif
    @if(Request::is('stockrequest'))
        <script src="{{ asset('js/stockrequest_1.js') }}"></script>
        <script src="{{ asset('js/stockrequest_2.js') }}"></script>
    @endif
    @if(Request::is('stocktransfer'))
        <script src="{{ asset('js/stocktransfer.js') }}"></script>
    @endif
    @if(Request::is('assembly'))
    <script src="{{ asset('js/assembly.js') }}"></script>
    @endif
    @if(Request::is('merchant'))
    <script src="{{ asset('js/merchant.js') }}"></script>
    @endif
    @if(Request::is('defective'))
    <script src="{{ asset('js/defective.js') }}"></script>
    @endif
    @if(Request::is('maintenance'))
    <script src="{{ asset('js/maintenance.js') }}"></script>
    @endif
    @if(Request::is('users'))
        <script src="{{ asset('js/users.js') }}"></script>
    @endif
</body>
</html>