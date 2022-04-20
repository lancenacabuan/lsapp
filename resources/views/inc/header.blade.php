<div class="d-flex" title="v1.0.1.0">
    <a href="/">
        <img class="p-2 align-self-end" src="{{asset('idsi.png')}}" style="width: auto; height: 90px; line-height: 90px;">
    </a>
    <a href="/" style="color: #0d1a80; font-family: Arial; font-weight: bold; font-size: 25px; line-height: 90px; margin-left: 10px; text-decoration: none;">
        MAIN WAREHOUSE STOCK MONITORING SYSTEM
    </a>
    <div class="p-2 ml-auto align-self-end d-flex" style="margin-bottom: 10px;">
        <div style="text-align: right; font-size: 12px;">
            {{ Carbon\Carbon::now()->isoformat('dddd, MMMM DD, YYYY') }}
            <span id="datetime"></span><br>
            <strong>{{ auth()->user()->name }}</strong>&nbsp;
            {{ strtoupper(str_replace('"', '', auth()->user()->getRoleNames())) }}<br>
            {{ auth()->user()->email }}<br>
            <a style="color: black;" href="{{ url('/changepassword') }}">Change Password</a>
        </div>
        <i class="fa fa-user-circle fa-4x p-2" aria-hidden="true"></i>
    </div>
</div>
<script>
    window.onload = displayClock();
    function displayClock(){
        var display = new Date().toLocaleTimeString();
        datetime.textContent = display;
        setTimeout(displayClock, 1000);
    }
</script>