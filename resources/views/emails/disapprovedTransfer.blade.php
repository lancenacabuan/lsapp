<!DOCTYPE html>
<html>
<body>
    <p>Hello, {{$details['name']}}!<br><br>
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
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->prodcode}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">{{$x->item}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->quantity}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->uom}}</td>
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
        <br><br>
        Kindly login to your {{$details['role']}} account if you wish to view this request for deletion by clicking on the link below.<br>
        Thank you!
    </p>
    <a href="https://mainwh.apsoft.com.ph/stocktransfer?request_number={{$details['request_number']}}">https://mainwh.apsoft.com.ph/stocktransfer?request_number={{$details['request_number']}}</a>
    <br><br>
    This is a system-generated email. Please do not reply.
</body>
</html>