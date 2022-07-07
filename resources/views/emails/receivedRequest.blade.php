<!DOCTYPE html>
<html>
<body>
    @if($details['reqtype'] == 'FIXED ASSET')
    <p>Hello, {{$details['name']}}!<br><br>
    This is to inform you that a Fixed Asset Stock Request has been 
    <span style="color: blue;"><strong>RECEIVED</strong></span> by {{$details['requested_by']}} via Admin - {{$details['receivedby']}}.<br>
    </p>
    <strong>Request Number: {{$details['request_number']}}</strong><br>
    <p>
        Request Type: {{$details['reqtype']}}<br>
        Date Requested: {{Carbon\Carbon::parse($details['reqdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Date Needed: {{Carbon\Carbon::parse($details['needdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Date Prepared: {{Carbon\Carbon::parse($details['prepdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Date Scheduled: {{Carbon\Carbon::parse($details['scheddate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Date Received: {{Carbon\Carbon::now()->isoformat('dddd, MMMM DD, YYYY')}}<br><br>
        Requested By: {{$details['requested_by']}}<br>
        Approved By: {{$details['requested_by']}}<br>
        Submitted By: {{$details['requested_by']}}<br>
        Prepared By: {{$details['requested_by']}}<br>
        Received By: {{$details['requested_by']}}
        <br><br>
        <strong>RECEIVED ITEMS</strong>
        <br>
        @php
            $total = 0;
            $sum = 0;
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
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['prodcode']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">{{$x['item']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['qty']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['uom']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{strtoupper($x['serial'])}}</td>
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
        @if($details['role'] != '')
        Kindly login to your {{$details['role']}} account if you wish to view or download this request by clicking on the link below.<br>
        @endif
        Thank you!
    </p>
    @if($details['role'] != '')
    <a href="{{ env('APP_URL_LIVE') }}printRequest?request_number={{$details['request_number']}}">{{ env('APP_URL_LIVE') }}printRequest?request_number={{$details['request_number']}}</a>
    @endif
    <br><br>
    This is a system-generated email. Please do not reply.
    @else
    <p>Hello, {{$details['name']}}!<br><br>
    This is to inform you that a {{$details['action']}} has been 
    <span style="color: blue;"><strong>{{$details['verb']}}</strong></span> by {{$details['receivedby']}}.<br>
    </p>
    <strong>Request Number: {{$details['request_number']}}</strong><br>
    <p>
        Request Type: {{$details['reqtype']}}<br>
        Date Requested: {{Carbon\Carbon::parse($details['reqdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Date Needed: {{Carbon\Carbon::parse($details['needdate'])->isoformat('dddd, MMMM DD, YYYY')}}<br>
        Requested By: {{$details['requested_by']}}<br><br>
        @if($details['req_type_id'] == 6)
        Order ID: {{$details['orderID']}}
        @endif
        @if($details['req_type_id'] != 6)
        Client Name: {{$details['client_name']}}<br>
        Address / Branch: {{$details['location']}}<br>
        Contact Person: {{$details['contact']}}<br>
        Remarks: {{$details['remarks']}}
        @endif
        @if($details['req_type_id'] == 2 || ($details['req_type_id'] == 3 && ($details['status_id'] == 10 || $details['status_id'] >= 27)))
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
        @php
            $total = 0;
            $sum = 0;
            if($details['status_id'] == 9){
                $concat = '&demo=received';
            }
            else{
                $concat = NULL;
            }
        @endphp
        @if($details['req_type_id'] == 2 || $details['req_type_id'] == 6 || ($details['req_type_id'] == 3 && ($details['status_id'] == 10 || $details['status_id'] >= 27)))
        <table style="border: 1px solid black; border-collapse: collapse; padding: 5px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">ITEM CODE</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">ITEM DESCRIPTION</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">QTY</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">UOM</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">SERIAL</th>
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
                        $total+=$x['qty'];
                    }
                @endphp
                <tr>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['prodcode']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">{{$x['item']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['qty']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['uom']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{strtoupper($x['serial'])}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{strtoupper($x['Warranty_Name'])}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold;">
                <tr>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; text-align: right;" colspan="2">TOTAL ITEM COUNT:</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$total}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;" colspan="3"></td>
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
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['prodcode']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">{{$x['item']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['qty']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x['uom']}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{strtoupper($x['serial'])}}</td>
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
        @endif
        <br><br>
        @if($details['pendcount'] > 0)
        <strong>PENDING ITEMS</strong>
        <br><br>
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
                @foreach($details['penditems'] as $i)
                @php
                    if($i['uom'] == 'Meter'){
                        $sum+=1;
                    }
                    else{
                        $sum+=$i['pending'];
                    }
                @endphp
                <tr>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$i->prodcode}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; width: 300px;">{{$i->item}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$i->pending}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$i->uom}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-weight: bold;">
                <tr>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px; text-align: right;" colspan="2">TOTAL ITEM COUNT:</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$sum}}</td>
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;" colspan="1"></td>
                </tr>
            </tfoot>
        </table>
        <br><br>
        @endif
        Kindly login to your {{$details['role']}} account if you wish to view or download this request by clicking on the link below.<br>
        Thank you!
    </p>
    <a href="{{ env('APP_URL_LIVE') }}printRequest?request_number={{$details['request_number'].$concat}}">{{ env('APP_URL_LIVE') }}printRequest?request_number={{$details['request_number'].$concat}}</a>
    <br><br>
    This is a system-generated email. Please do not reply.
    @endif
</body>
</html>