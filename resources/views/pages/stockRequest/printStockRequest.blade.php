@extends('layouts.app')
@section('content')
<input type="hidden" id="req_num" value="{{$list->req_num}}">
<div class="container-fluid">
    <button id="btnPrint" type="button" class="btn btn-primary bp" style="margin-right: 5px;">PRINT</button>
    <button id="btnSavePDF" type="button" class="btn btn-primary bp">SAVE AS PDF</button>
    <a href="/stockrequest?request_number={{$list->req_num}}" class="btn btn-primary float-right bp">BACK</a>
</div>
<br>
<div class="container-fluid">
    <div id="printPage" class="panel-body table-responsive" style="font-size: 12px; width: 100%;">
        <div style="height: 70px; line-height: 70px; font-weight: bold; color: #0d1a80; font-family: Arial; font-size: 22px;">
            <img src="{{asset('idsi.png')}}" style="height: 70px; width: auto; border-right: 1px solid #3333">
            MAIN WAREHOUSE STOCK MONITORING SYSTEM
        </div>
        <br>
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
                <td colspan="2" id="req_date">{{$list->req_date}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;">Stock Request No.:</td>
                <td colspan="2">{{$list->req_num}}</td>
            </tr>
            <tr height="20">
                <td colspan="2" height="20" style="font-weight: bold;">Date Needed:</td>
                <td colspan="2" id="need_date">{{$list->needdate}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;" class="tdHide">Reference SO/PO No.:</td>
                <td colspan="2">{{$list->reference}}</td>
            </tr>
            <tr height="20">
                <td colspan="2" height="20" style="font-weight: bold;">Requested By:</td>
                <td colspan="2">{{$list->req_by}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;">Date Scheduled:</td>
                <td colspan="2" id="sched">{{$list->sched}}</td>
            </tr>
            <tr height="20">
                <td colspan="2" height="20" style="font-weight: bold;">Date Prepared:</td>
                <td colspan="2" id="prep_date">{{$list2->prep_date}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;" class="tdHide">Client Name:</td>
                <td colspan="2">{{$list->client_name}}</td>
            </tr>
            <tr height="20">
                <td colspan="2" height="20" style="font-weight: bold;">Prepared By:</td>
                <td colspan="2">{{$list2->prep_by}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;" class="tdHide">Address / Branch:</td>
                <td colspan="2">{{$list->location}}</td>
            </tr>
            <tr height="20">
                <td colspan="2" height="20" style="font-weight: bold;">Request Type:</td>
                <td colspan="2" id="req_type">{{$list->req_type}}</td>
                <td colspan="5">&nbsp;</td>
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
                                <th>UOM</th>
                                <th>SERIAL</th>
                                <th>LOCATION</th>
                            </tr>
                            @foreach($list3 as $x)
                            <tr>
                                <td>{{$x->category}}</td>
                                <td>{{$x->item}}</td>
                                <td>{{$x->qty}}</td>
                                <td>{{$x->uom}}</td>
                                <td>{{$x->serial}}</td>
                                <td>{{$x->location}}</td>
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
                <td colspan="2">______________________________</td>
            </tr>
            <tr height="20">
                <td height="20"></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;">Date Received:</td>
                <td colspan="2">______________________________</td>
            </tr>
        </table>
    </div>
</div>
<script>
document.addEventListener("contextmenu", function(e){
    e.preventDefault();
}, false);

$(document).ready(function(){
    setTimeout(function(){$('#loading').hide(); Spinner.hide();}, 0);
    
    var req_date = $('#req_date').html();
    req_date = moment(req_date).format('dddd, MMMM DD, YYYY, h:mm A');
    $('#req_date').html(req_date);

    var need_date = $('#need_date').html();
    need_date = moment(need_date).format('dddd, MMMM DD, YYYY');
    $('#need_date').html(need_date);

    var prep_date = $('#prep_date').html();
    prep_date = moment(prep_date).format('dddd, MMMM DD, YYYY, h:mm A');
    $('#prep_date').html(prep_date);

    var sched = $('#sched').html();
    sched = moment(sched).format('dddd, MMMM DD, YYYY');
    $('#sched').html(sched);

    if($('#req_type').html() == 'SERVICE UNIT'){
        $('.tdHide').html('');
    }
});
</script>
@endsection