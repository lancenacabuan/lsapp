<!DOCTYPE html>
<html>
<body>
    <p>Hello, Admin / Developer!<br>
    A new LOCATION STATUS CHANGE REQUEST is waiting for your approval and implementation.<br></p>
    <strong>Location Name: {{$details['location']}}</strong><br>
    <strong>Status CHANGE FROM: {{$details['status_original']}}</strong><br>
    <strong>Status CHANGE INTO: {{$details['status']}}</strong><br>
    <p>
        Date Requested: {{$details['reqdate']}}<br>
        Requested By: {{$details['requested_by']}}<br>
        <br><br>
        Thank you!
    </p>
    <a href="https://lance.idsi.com.ph/maintenance?tbl=location">https://lance.idsi.com.ph/maintenance?tbl=location</a>
    <br><br>
    This is a system-generated email. Please do not reply.
</body>
</html>