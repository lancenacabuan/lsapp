<!DOCTYPE html>
<html>
<body>
    <p>Hello, Admin / Developer!<br><br>
    A new LOCATION REQUEST is waiting for your approval and implementation.<br></p>
    <strong>Location Name: {{$details['location']}}</strong><br>
    <p>
        Date Requested: {{$details['reqdate']}}<br>
        Requested By: {{$details['requested_by']}}<br>
        <br><br>
        Thank you!
    </p>
    <a href="{{ env('APP_URL_LIVE') }}maintenance?tbl=location">{{ env('APP_URL_LIVE') }}maintenance?tbl=location</a>
    <br><br>
    This is a system-generated email. Please do not reply.
</body>
</html>