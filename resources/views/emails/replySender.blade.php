<!DOCTYPE html>
<html>
<body>
    <p>Hello, {{$details['name']}}!<br><br>
    You have successfully REPORTED a suggested feature, issue, error, or bug to the technical support team and is currently waiting for their approval and implementation.<br></p>
    <strong>Tracking Ticket Number: {{$details['ticket_number']}}</strong><br>
    <p>
        Date Reported: {{$details['reportdate']}}<br>
        Reported By: {{$details['reported_by']}}<br><br>
        Report Category: {{$details['report_category']}}<br>
        Report Details: {{$details['details']}}
        <br><br>
        Should there be any concerns or follow up on this matter, <br>
        please let us know by sending an email to either the following: <br>
        <ul>
            <li>gerard.mallari@gmail.com</li>
            <li>jolopez@ideaserv.com.ph</li>
            <li>lancenacabuan@outlook.com</li>
        </ul>
        <br>
        Thank you!
    </p>
    <a href="https://mainwh.apsoft.com.ph/">https://mainwh.apsoft.com.ph/</a>
    <br><br>
    This is a system-generated email. Please do not reply.
</body>
</html>