@extends('layouts.app')
@section('content')
<input type="hidden" id="req_num" value="{{$list->req_num}}">
<div class="container">
    <a href="/stockrequest" class="btn btn-primary mr-auto float-right bp">BACK</a>
    <button id="btnPrint" type="button" class="btn btn-primary mr-auto bp">PRINT</button>
    <button id="btnSavePDF" type="button" class="btn btn-primary mr-auto bp" style="margin-right: 10px;">SAVE AS PDF</button>
</div>
<br/>
<div id="printPage" class="panel-body table-responsive" style="font-size: 12px; width: 100%;">
    <table cellspacing="0" cellpadding="0" style="width: 100%;">
        <col span="9" />
        <tr height="20">
            <td style="text-align: center; font-size: 18px;" colspan="9" height="20"><strong>STOCK REQUEST DELIVERY RECEIPT</strong></td>
        </tr>
        <tr height="20">
            <td colspan="9">&nbsp;</td>
        </tr>
        <tr height="20">
            <td colspan="2" height="20" style="font-weight: bold;">Date Requested:</td>
            <td colspan="2">{{$list->req_date}}</td>
            <td>&nbsp;</td>
            <td colspan="2" style="font-weight: bold;">Stock Request No.:</td>
            <td colspan="2">{{$list->req_num}}</td>
        </tr>
        <tr height="20">
            <td colspan="2" height="20" style="font-weight: bold;">Requested By:</td>
            <td colspan="2">{{$list->req_by}}</td>
            <td>&nbsp;</td>
            <td colspan="2" style="font-weight: bold;">Reference SO/PO No.:</td>
            <td colspan="2">{{$list->reference}}</td>
        </tr>
        <tr height="20">
            <td colspan="2" height="20" style="font-weight: bold;">Date Prepared:</td>
            <td colspan="2">{{$list2->prep_date}}</td>
            <td>&nbsp;</td>
            <td colspan="2" style="font-weight: bold;">Date Scheduled:</td>
            <td colspan="2">{{$list->sched}}</td>
        </tr>
        <tr height="20">
            <td colspan="2" height="20" style="font-weight: bold;">Prepared By:</td>
            <td colspan="2">{{$list2->prep_by}}</td>
            <td>&nbsp;</td>
            <td colspan="2" style="font-weight: bold;">Client Name:</td>
            <td colspan="2">{{$list->client_name}}</td>
        </tr>
        <tr height="20">
            <td colspan="2" height="20" style="font-weight: bold;">Request Type:</td>
            <td colspan="2">{{$list->req_type}}</td>
            <td>&nbsp;</td>
            <td colspan="2" style="font-weight: bold;">Address / Branch:</td>
            <td colspan="2">{{$list->location}}</td>
        </tr>
        <tr height="20">
            <td colspan="9" height="20">&nbsp;</td>
        </tr>
        <tr height="20">
            <td colspan="9" height="20">
                <table id="stockReqTable" class="table stockReqTable display" style="margin-top: 10px;">
                    <thead>                            
                        <tr>
                            <th>CATEGORY</th>
                            <th>ITEM DESCRIPTION</th>
                            <th>QTY</th>
                            <th>SERIAL</th>
                        </tr>
                        @foreach($list3 as $x)
                        <tr>
                            <td>{{$x->category}}</td>
                            <td>{{$x->item}}</td>
                            <td>{{$x->qty}}</td>
                            <td>{{$x->serial}}</td>
                        </tr>
                        @endforeach
                    </thead>    
                </table> 
            </td>
        </tr>
        <tr height="20">
            <td colspan="9" height="20">&nbsp;</td>
        </tr>
        <tr height="20">
            <td height="20">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td colspan="2" style="font-weight: bold;">Received By:</td>
            <td colspan="2">____________________</td>
        </tr>
        <tr height="20">
            <td height="20">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td colspan="2" style="font-weight: bold;">Date Received:</td>
            <td colspan="2">____________________</td>
        </tr>
    </table>
</div>
<script>
document.addEventListener("contextmenu", function(e){
    e.preventDefault();
}, false);
</script>
@endsection