<!DOCTYPE html>
<html>
<body>
    <p>Hello, {{$details['name']}}!<br>
    This is to inform you that a {{$details['action']}} has been 
    <span style="color: blue;"><strong>{{$details['verb']}}</strong></span> by {{$details['receivedby']}}.<br>
    </p>
    <strong>Request Number: {{$details['request_number']}}</strong><br>
    <p>
        Date Requested: {{Carbon\Carbon::parse($details['reqdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Date Needed: {{Carbon\Carbon::parse($details['needdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Requested By: {{$details['requested_by']}}<br>
        Request Type: {{$details['reqtype']}}<br>
        Client Name: {{$details['client_name']}}<br>
        Address / Branch: {{$details['location']}}<br>
        Reference SO/PO No.: {{$details['reference']}}<br>
        Date Prepared: {{Carbon\Carbon::parse($details['prepdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Prepared By: {{$details['prepared_by']}}<br>
        Date Scheduled: {{Carbon\Carbon::parse($details['scheddate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Date Received: {{Carbon\Carbon::now()->isoformat('dddd, MMMM DD, YYYY')}}<br><br>
        <table style="border: 1px solid black; border-collapse: collapse; padding: 5px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">CATEGORY</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">ITEM DESCRIPTION</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">QTY</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">UOM</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">SERIAL</th>
                </tr>
            </thead>
            @foreach($details['items'] as $x)
            <tr>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->category}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->item}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->qty}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->uom}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->serial}}</td>
            </tr>
            @endforeach 
        </table>
        <br><br>
        Kindly login to your {{$details['role']}} account if you wish to view or download this request by clicking on the link below.<br>
        Thank you!
    </p>
    <a href="{{ env('APP_URL') }}printRequest?request_number={{$details['request_number']}}">{{ env('APP_URL') }}printRequest?request_number={{$details['request_number']}}</a>
    <br><br>
    This is a system-generated email. Please do not reply.
</body>
</html>