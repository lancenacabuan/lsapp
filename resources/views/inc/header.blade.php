<div class="d-flex">
    <a href="/">
        <img class="p-2 align-self-end" src="/storage/idsi.png" style="width: auto; height: 95px; line-height: 95px; border-right:1px solid rgba(0, 0, 0, 0.2)">
    </a>
    <a href="/" style="color: #0d1a80; font-family: Arial; font-weight: bold; font-size: 25px; line-height: 95px; margin-left: 10px; text-decoration: none;">
        MAIN WAREHOUSE STOCK MONITORING SYSTEM
    </a>
    <div class="p-2 ml-auto align-self-end d-flex" style="margin-bottom: 10px;">
        <div class="p-2 ml-auto" style="text-align: right; font-size: 12px;">
            {{Carbon\Carbon::now()->isoformat('dddd, MMMM D, YYYY')}}
            <span id="datetime"></span><br>
            <strong>{{auth()->user()->name}}</strong>&nbsp;
            [{{strtoupper(str_replace('"]','',(str_replace('["','',auth()->user()->getRoleNames()))))}}]<br>
            {{auth()->user()->email}}<br>
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