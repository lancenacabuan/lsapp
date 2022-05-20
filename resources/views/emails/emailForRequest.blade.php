<!DOCTYPE html>
<html>
<body>
    <p>Hello, {{$details['name']}}!<br>
    A new {{$details['action']}} is waiting for your approval.<br></p>
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
        @if($details['reqtype'] == 'SALES')
        <br><br>Reference SO/PO No.: {{$details['reference']}}
        @endif
        <br><br>
        <strong>REQUESTED ITEMS</strong>
        <br>
        @if($details['reqtype'] == 'SALES')
        <table style="border: 1px solid black; border-collapse: collapse; padding: 5px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">CATEGORY</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">ITEM DESCRIPTION</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">QTY</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">UOM</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">WARRANTY TYPE</th>
                </tr>
            </thead>
            @foreach($details['items'] as $x)
            <tr>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['category']}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">{{$x['item']}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['quantity']}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['uom']}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{strtoupper($x['Warranty_Name'])}}</td>
            </tr>
            @endforeach 
        </table>
        @else
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
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['category']}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">{{$x['item']}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['quantity']}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['uom']}}</td>
            </tr>
            @endforeach 
        </table>
        @endif
        <br><br>
        Kindly login to your {{$details['role']}} account to process this request by clicking on the link below.<br>
        Thank you!
    </p>
    <a href="{{ env('APP_URL') }}stockrequest?request_number={{$details['request_number']}}">{{ env('APP_URL') }}stockrequest?request_number={{$details['request_number']}}</a>
    <br><br>
    This is a system-generated email. Please do not reply.
</body>
</html>