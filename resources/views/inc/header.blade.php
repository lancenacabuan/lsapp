<div class="d-flex xD" title="v1.0.5.5" style="height: 90px;">
    <a href="/">
        <img class="p-2 align-self-end" src="{{asset('/inc/idsi.png')}}" style="width: auto; height: 90px; line-height: 90px;">
    </a>
    <a href="/" style="color: #0d1a80; font-family: Arial; font-weight: bold; font-size: 25px; line-height: 90px; margin-left: 10px; text-decoration: none;">
        MAIN WAREHOUSE STOCK MONITORING SYSTEM
        @if(env('APP_URL') == 'https://mainwh.ideaservph.tech/')
        <sup><sup>BETA</sup></sup>
        <input type="hidden" id="current_url" value="beta" disabled>
        @else
        <input type="hidden" id="current_url" value="live" disabled>
        @endif
    </a>
    <div id="lblUser" class="p-2 ml-auto align-self-end d-flex" style="color: #0d1a80; display: none !important;">
        <div style="text-align: right; font-size: 12px;">
            {{ Carbon\Carbon::now()->isoformat('dddd, MMMM DD, YYYY') }}
            <span id="datetime"></span><br>
            <strong>{{ auth()->user()->name }}</strong>&nbsp;({{ auth()->user()->email }})<br>
            <span id="lblRole" style="font-weight: bold;">[{{ strtoupper(auth()->user()->getRoleNames()[0]) }}]</span>
            <span id="lblCompany">{{ auth()->user()->company }}</span><br>
            <span id="lblChangePassword" style="text-decoration: underline; cursor: pointer;">Change Password</span>
        </div>
        <i class="fa fa-user-circle fa-4x p-2" aria-hidden="true"></i>
    </div>
    <input type="hidden" id="current_user" value="{{auth()->user()->id}}" disabled>
    <input type="hidden" id="current_company" value="{{auth()->user()->company}}" disabled>
    <input type="hidden" id="current_role" value="{{auth()->user()->getRoleNames()[0]}}" disabled>
    <input type="hidden" id="apiKey" value="8d4493a1-ab93-47b7-a15e-49d9918b52a5" disabled>
</div>
<script>
    window.onload = displayClock();
    function displayClock(){
        var display = new Date().toLocaleTimeString();
        datetime.textContent = display;
        setTimeout(displayClock, 1000);
    }
    $(function(){
        var lblCompany = $('#lblCompany').text();
        if(lblCompany == 'Apsoft'){
            lblCompany = 'Apsoft, Inc.';
        }
        if(lblCompany == 'Ideaserv'){
            lblCompany = 'Ideaserv Systems, Inc.';
        }
        if(lblCompany == 'NuServ'){
            lblCompany = 'NuServ Solutions, Inc.';
        }
        if(lblCompany == 'Phillogix'){
            lblCompany = 'Phillogix Systems, Inc.';
        }
        return $('#lblCompany').text(lblCompany);
    });
    $(function(){
        $('#lblUser').show();
    });
</script>