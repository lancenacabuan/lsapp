<!DOCTYPE html>
<html>
<body>
    <p>Hello, {{$details['name']}}!<br><br>
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
        @php
            $total = 0;
        @endphp
        <table style="border: 1px solid black; border-collapse: collapse; padding: 5px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">ITEM CODE</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">ITEM DESCRIPTION</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">QTY</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">UOM</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">SERIAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details['items'] as $x)
                @php
                    if($x['uom'] == 'Meter'){
                        $total+=1;
                    }
                    else{
                        $total+=$x['qty'];
                    }
                @endphp
                <tr>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->prodcode}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">{{$x->item}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->qty}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->uom}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{strtoupper($x->serial)}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold;">
                <tr>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; text-align: right;" colspan="2">TOTAL ITEM COUNT:</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$total}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;" colspan="2"></td>
                </tr>
            </tfoot>
        </table>
        <br><br>
        Kindly login to your {{$details['role']}} account if you wish to view or download this request by clicking on the link below.<br>
        Thank you!
    </p>
    <a href="{{ env('APP_URL_LIVE') }}printTransferRequest?request_number={{$details['request_number']}}">{{ env('APP_URL_LIVE') }}printTransferRequest?request_number={{$details['request_number']}}</a>
    <br><br>
    This is a system-generated email. Please do not reply.
</body>
</html>