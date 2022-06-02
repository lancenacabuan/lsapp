<!DOCTYPE html>
<html>
<body>
    <p>Hello, {{$details['name']}}!<br><br>
    This is to inform you of a recent <span style="color: red;"><strong>ITEM SERIAL CHANGE</strong></span>.<br></p>
    <p>
        Date Edited: {{$details['editdate']}}<br>
        Edited By: {{$details['edited_by']}}<br><br>
        Category: {{$details['category']}}<br>
        Item Description: {{$details['item']}}<br><br>
        Edited Serial FROM: {{$details['serialfrom']}}<br>
        Edited Serial TO: {{$details['serialto']}}<br>
        <br><br>
        Thank you!
    </p>
    <a href="https://mainwh.apsoft.com.ph/">https://mainwh.apsoft.com.ph/</a>
    <br><br>
    This is a system-generated email. Please do not reply.
</body>
</html>