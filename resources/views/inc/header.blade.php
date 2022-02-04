<div class="d-flex">
    <a href="/">
    <img class="p-2 align-self-end" src="/storage/idsi.png" style="width: auto; height: 90px; border-right:1px solid #3333">
    </a>
    <a href="/" class="p-2 align-self-end" style="color: #0d1a80 !important; font-family: Arial !important; font-size:25px !important; margin-bottom:20px !important; text-decoration: none !important;">
    <h3 style="font-weight: bold !important;">MAIN WAREHOUSE STOCK MONITORING SYSTEM</h3>
    </a>
    <div class="p-2 ml-auto align-self-end d-flex">
        <div class="p-2 ml-auto" style="text-align: right; font-size:12px;">
            {{Carbon\Carbon::now()->isoformat('dddd, MMMM D, YYYY')}}
            <span id="datetime"></span></br>
            <strong>{{auth()->user()->name}}</strong>&nbsp;
            [{{strtoupper(str_replace('"]','',(str_replace('["','',auth()->user()->getRoleNames()))))}}]</br>
            {{auth()->user()->email}}</br>
            <a style="color: black !important;" href="{{ url('/changepassword') }}">
                Change Password
            </a>
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