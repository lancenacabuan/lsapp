<!DOCTYPE html>
<html>
<body>
    <p>Hello, {{$details['name']}}!<br>
    This is to inform you that your {{$details['action']}} has been 
    <span style="color: red;"><strong>DISAPPROVED</strong></span> by {{$details['disapprovedby']}}.<br>
    Reason for Disapproval: {{$details['reason']}}<br>
    </p>
    <strong>Request Number: {{$details['request_number']}}</strong><br>
    <p>
        Date Requested: {{Carbon\Carbon::parse($details['reqdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Date Needed: {{Carbon\Carbon::parse($details['needdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Requested By: {{$details['requested_by']}}<br><br>
        FROM Location: {{$details['locfrom']}}<br>
        TO New Location: {{$details['locto']}}
        <br><br>
        <strong>REQUESTED ITEMS</strong>
        <br>
        <table style="border: 1px solid black; border-collapse: collapse; padding: 5px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">CATEGORY</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">ITEM DESCRIPTION</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">QTY</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">UOM</th>
                </tr>
            </thead>
            @foreach($details['items'] as $x)
            <tr>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->category}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">{{$x->item}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->quantity}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->uom}}</td>
            </tr>
            @endforeach 
        </table>
        <br><br>
        Kindly login to your {{$details['role']}} account if you wish to view this request for deletion by clicking on the link below.<br>
        Thank you!
    </p>
    <a href="{{ env('APP_URL') }}stocktransfer?request_number={{$details['request_number']}}">{{ env('APP_URL') }}stocktransfer?request_number={{$details['request_number']}}</a>
    <br><br>
    This is a system-generated email. Please do not reply.
</body>
</html>