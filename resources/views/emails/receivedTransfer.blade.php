<!DOCTYPE html>
<html>
<body>
    <p>Hello, {{$details['name']}}!<br>
    This is to inform you that a {{$details['action']}} has been 
    <span style="color: blue;"><strong>RECEIVED</strong></span> by {{$details['receivedby']}}.<br>
    </p>
    <strong>Request Number: {{$details['request_number']}}</strong><br>
    <p>
        Date Requested: {{Carbon\Carbon::parse($details['reqdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Date Needed: {{Carbon\Carbon::parse($details['needdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Requested By: {{$details['requested_by']}}<br><br>
        FROM Location: {{$details['locfrom']}}<br>
        TO New Location: {{$details['locto']}}<br><br>
        Date Prepared: {{Carbon\Carbon::parse($details['prepdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Prepared By: {{$details['prepared_by']}}<br>
        Date Scheduled: {{Carbon\Carbon::parse($details['scheddate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Date Received: {{Carbon\Carbon::now()->isoformat('dddd, MMMM DD, YYYY')}}
        <br><br>
        <strong>TRANSFERRED ITEMS</strong>
        <br>
        <table style="border: 1px solid black; border-collapse: collapse; padding: 5px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">CATEGORY</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">ITEM DESCRIPTION</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">QTY</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">UOM</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">SERIAL</th>
                </tr>
            </thead>
            @foreach($details['items'] as $x)
            <tr>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->category}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">{{$x->item}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->qty}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->uom}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{strtoupper($x->serial)}}</td>
            </tr>
            @endforeach 
        </table>
        <br><br>
        Kindly login to your {{$details['role']}} account if you wish to view or download this request by clicking on the link below.<br>
        Thank you!
    </p>
    <a href="{{ env('APP_URL') }}printTransferRequest?request_number={{$details['request_number']}}">{{ env('APP_URL') }}printTransferRequest?request_number={{$details['request_number']}}</a>
    <br><br>
    This is a system-generated email. Please do not reply.
</body>
</html>