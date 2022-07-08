<!DOCTYPE html>
<html>
<body>
    <p>Hello, {{$details['name']}}!<br><br>
    @if($details['reqtype'] == 'FIXED ASSET')
    A new <span style="color: red;"><strong>FIXED ASSET</strong></span> Stock Request has been submitted by Admin - {{$details['submitted_by']}} 
    for the requested items of {{$details['requested_by']}}.<br></p>
    @else
    {{$details['action']}} is waiting for your approval.<br></p>
    @endif
    <strong>Request Number: {{$details['request_number']}}</strong><br>
    <p>
        Request Type: {{$details['reqtype']}}<br>
        Date Requested: {{Carbon\Carbon::parse($details['reqdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Date Needed: {{Carbon\Carbon::parse($details['needdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        @if($details['reqtype'] == 'FIXED ASSET')
        <br>Requested By: {{$details['requested_by']}}<br>
        Approved By: {{$details['approved_by']}}<br>
        Submitted By: {{$details['submitted_by']}}
        @else
        Requested By: {{$details['requested_by']}}<br><br>
        Client Name: {{$details['client_name']}}<br>
        Address / Branch: {{$details['location']}}<br>
        Contact Person: {{$details['contact']}}<br>
        Remarks: {{$details['remarks']}}
        @endif
        @if($details['reqtype'] == 'SALES')
        <br><br>Reference SO/PO No.: {{$details['reference']}}
        @endif
        <br><br>
        <strong>REQUESTED ITEMS</strong>
        <br>
        @php
            $total = 0;
        @endphp
        @if($details['reqtype'] == 'SALES')
        <table style="border: 1px solid black; border-collapse: collapse; padding: 5px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">ITEM CODE</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">ITEM DESCRIPTION</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">QTY</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">UOM</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">WARRANTY TYPE</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details['items'] as $x)
                @php
                    if($x['uom'] == 'Meter'){
                        $total+=1;
                    }
                    else{
                        $total+=$x['quantity'];
                    }
                @endphp
                <tr>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['prodcode']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">{{$x['item']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['quantity']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['uom']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{strtoupper($x['Warranty_Name'])}}</td>
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
        @else
        <table style="border: 1px solid black; border-collapse: collapse; padding: 5px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">ITEM CODE</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">ITEM DESCRIPTION</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">QTY</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">UOM</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details['items'] as $x)
                @php
                    if($x['uom'] == 'Meter'){
                        $total+=1;
                    }
                    else{
                        $total+=$x['quantity'];
                    }
                @endphp
                <tr>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['prodcode']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">{{$x['item']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['quantity']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['uom']}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold;">
                <tr>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; text-align: right;" colspan="2">TOTAL ITEM COUNT:</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$total}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;" colspan="1"></td>
                </tr>
            </tfoot>
        </table>
        @endif
        @if($details['role'] != '')
        <br><br>
        Kindly login to your {{$details['role']}} account to process this request by clicking on the link below.<br>
        Thank you!
        @endif
    </p>
    @if($details['role'] != '')
    <a href="{{ env('APP_URL_LIVE') }}stockrequest?request_number={{$details['request_number']}}">{{ env('APP_URL_LIVE') }}stockrequest?request_number={{$details['request_number']}}</a>
    <br>
    @endif
    <br>
    This is a system-generated email. Please do not reply.
</body>
</html>