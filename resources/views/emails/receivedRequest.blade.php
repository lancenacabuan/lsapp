<!DOCTYPE html>
<html>
<body>
    <p>Hello, {{$details['name']}}!<br>
    This is to inform you that a {{$details['action']}} has been 
    <span style="color: blue;"><strong>{{$details['verb']}}</strong></span> by {{$details['receivedby']}}.<br>
    </p>
    <strong>Request Number: {{$details['request_number']}}</strong><br>
    <p>
        Request Type: {{$details['reqtype']}}<br>
        Date Requested: {{Carbon\Carbon::parse($details['reqdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Date Needed: {{Carbon\Carbon::parse($details['needdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Requested By: {{$details['requested_by']}}<br><br>
        Client Name: {{$details['client_name']}}<br>
        Address / Branch: {{$details['location']}}<br>
        Contact Person: {{$details['contact']}}<br>
        Remarks: {{$details['remarks']}}
        @if($details['req_type_id'] == 2 || ($details['req_type_id'] == 3 && $details['status_id'] == 10))
        <br><br>Reference SO/PO No.: {{$details['reference']}}
        @endif
        <br><br>
        Date Prepared: {{Carbon\Carbon::parse($details['prepdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Prepared By: {{$details['prepared_by']}}<br>
        Date Scheduled: {{Carbon\Carbon::parse($details['scheddate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Date Received: {{Carbon\Carbon::now()->isoformat('dddd, MMMM DD, YYYY')}}
        <br><br>
        <strong>{{$details['verb']}} ITEMS</strong>
        <br>
        @if($details['req_type_id'] == 2 || ($details['req_type_id'] == 3 && $details['status_id'] == 10))
        <table style="border: 1px solid black; border-collapse: collapse; padding: 5px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">ITEM DESCRIPTION</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">ITEM CODE</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">QTY</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">UOM</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">SERIAL</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">WARRANTY TYPE</th>
                </tr>
            </thead>
            @foreach($details['items'] as $x)
            <tr>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">{{$x['item']}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['prodcode']}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['qty']}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['uom']}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{strtoupper($x['serial'])}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{strtoupper($x['Warranty_Name'])}}</td>
            </tr>
            @endforeach 
        </table>
        @else
        <table style="border: 1px solid black; border-collapse: collapse; padding: 5px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">ITEM DESCRIPTION</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">ITEM CODE</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">QTY</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">UOM</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">SERIAL</th>
                </tr>
            </thead>
            @foreach($details['items'] as $x)
            <tr>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">{{$x['item']}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['prodcode']}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['qty']}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['uom']}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{strtoupper($x['serial'])}}</td>
            </tr>
            @endforeach 
        </table>
        @endif
        <br><br>
        @if($details['pendcount'] > 0)
        <strong>PENDING ITEMS</strong>
        <br><br>
        <table style="border: 1px solid black; border-collapse: collapse; padding: 5px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">ITEM DESCRIPTION</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">ITEM CODE</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">QTY</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">UOM</th>
                </tr>
            </thead>
            @foreach($details['penditems'] as $i)
            <tr>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">{{$i->item}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$i->prodcode}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$i->pending}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$i->uom}}</td>
            </tr>
            @endforeach 
        </table>
        <br><br>
        @endif
        Kindly login to your {{$details['role']}} account if you wish to view or download this request by clicking on the link below.<br>
        Thank you!
    </p>
    <a href="{{ env('APP_URL') }}printRequest?request_number={{$details['request_number']}}">{{ env('APP_URL') }}printRequest?request_number={{$details['request_number']}}</a>
    <br><br>
    This is a system-generated email. Please do not reply.
</body>
</html>