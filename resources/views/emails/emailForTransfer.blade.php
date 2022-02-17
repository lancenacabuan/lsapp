<!DOCTYPE html>
<html>
<body>
    <p>Hello, {{$details['name']}}!<br/>
    A new {{$details['action']}} is waiting for your approval.<br /></p>
    <strong>Request Number: {{$details['request_number']}}</strong><br />
    <p>
        Date Requested: {{Carbon\Carbon::parse($details['reqdate'])->isoformat('dddd, MMMM D, YYYY')}}<br />
        Requested By: {{$details['requested_by']}}<br />
        Date Needed: {{Carbon\Carbon::parse($details['needdate'])->isoformat('dddd, MMMM D, YYYY')}}<br />
        FROM Location: {{$details['locfrom']}}<br />
        TO New Location: {{$details['locto']}}<br /><br />
        <table style="border: 1px solid black; border-collapse: collapse; padding: 5px;">
            <thead>                            
                <tr>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">CATEGORY</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">ITEM DESCRIPTION</th>
                    <th style="border: 1px solid black; border-collapse: collapse; padding: 5px;">QTY</th>
                </tr>
            </thead>
            @foreach($details['items'] as $x)
            <tr>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->category}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->item}}</td>
                <td style="border: 1px solid black; border-collapse: collapse; padding: 5px;">{{$x->quantity}}</td>
            </tr>
            @endforeach 
        </table>
        <br /><br />
        Kindly login to your {{$details['role']}} account to process this request on the link below.<br />
        Thank you!
    </p>
    <a href="https://lance.idsi.com.ph/stocktransfer?request_number={{$details['request_number']}}">https://lance.idsi.com.ph/stocktransfer?request_number={{$details['request_number']}}</a>
    <br/><br/>
    This is a system-generated email. Please do not reply.
</body>
</html>