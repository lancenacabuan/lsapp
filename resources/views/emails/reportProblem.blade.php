<!DOCTYPE html>
<html>
<body>
    <p>Hello, Admin / Developer!<br><br>
    A suggested feature, issue, error, or bug has been REPORTED to the technical support team and is currently waiting for your approval and implementation.<br></p>
    <strong>Tracking Ticket Number: {{$details['ticket_number']}}</strong><br>
    <p>
        Date Reported: {{$details['reportdate']}}<br>
        Reported By: {{$details['reported_by']}}<br>
        Contact Email: {{$details['email']}}<br><br>
        Report Category: {{$details['report_category']}}<br>
        Report Details: {{$details['details']}}
        <br><br>
        Thank you!
    </p>
    <a href="{{ env('APP_URL_LIVE') }}">{{ env('APP_URL_LIVE') }}</a>
    <br><br>
    This is a system-generated email. Please do not reply.
</body>
</html>