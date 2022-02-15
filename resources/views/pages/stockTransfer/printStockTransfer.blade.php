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
    <div>
    <img src="/storage/idsi.png" style="width: auto; height: 70px; border-right:1px solid #3333">
    <span style="vertical-align: center; font-weight: bold; color: #0d1a80; font-family: Arial; font-size:22px; text-decoration: none;">
    MAIN WAREHOUSE STOCK MONITORING SYSTEM</span>
    </div>
    <br/>
    <table cellspacing="0" cellpadding="0" style="width: 100%;">
        <col span="9" />
        <tr height="20">
            <td style="text-align: center; font-size: 18px;" colspan="9" height="20"><strong>STOCK TRANSFER REQUEST DELIVERY RECEIPT</strong></td>
        </tr>
        <tr height="20">
            <td colspan="9">&nbsp;</td>
        </tr>
        <tr height="20">
            <td colspan="2" height="20" style="font-weight: bold;">Date Requested:</td>
            <td colspan="2" id="req_date">{{$list->req_date}}</td>
            <td>&nbsp;</td>
            <td colspan="2" style="font-weight: bold;">Stock Transfer Request No.:</td>
            <td colspan="2">{{$list->req_num}}</td>
        </tr>
        <tr height="20">
            <td colspan="2" height="20" style="font-weight: bold;">Date Needed:</td>
            <td colspan="2" id="need_date">{{$list->needdate}}</td>
            <td>&nbsp;</td>
            <td colspan="2" style="font-weight: bold;">Date Scheduled:</td>
            <td colspan="2" id="sched">{{$list->sched}}</td>
        </tr>
        <tr height="20">
            <td colspan="2" height="20" style="font-weight: bold;">Requested By:</td>
            <td colspan="2">{{$list->req_by}}</td>
            <td>&nbsp;</td>
            <td colspan="2" style="font-weight: bold;">FROM Location</td>
            <td colspan="2" id="locfrom">{{$list->locfrom}}</td>
        </tr>
        <tr height="20">
            <td colspan="2" height="20" style="font-weight: bold;">Date Prepared:</td>
            <td colspan="2" id="prep_date">{{$list2->prep_date}}</td>
            <td>&nbsp;</td>
            <td colspan="2" style="font-weight: bold;">TO New Location</td>
            <td colspan="2" id="locto">{{$list->locto}}</td>
        </tr>
        <tr height="20">
            <td colspan="2" height="20" style="font-weight: bold;">Prepared By:</td>
            <td colspan="2">{{$list2->prep_by}}</td>
            <td>&nbsp;</td>
            <td colspan="2" style="font-weight: bold;"></td>
            <td colspan="2"></td>
        </tr>
        <tr height="20">
            <td colspan="9" height="20">&nbsp;</td>
        </tr>
        <tr height="20">
            <td colspan="9" height="20">
                <table id="stockTransTable" class="table stockTransTable display" style="margin-top: 10px;">
                    <thead>                            
                        <tr>
                            <th>CATEGORY</th>
                            <th>ITEM DESCRIPTION</th>
                            <th>QTY</th>
                            <th>UOM</th>
                            <th>SERIAL</th>
                        </tr>
                        @foreach($list3 as $x)
                        <tr>
                            <td>{{$x->category}}</td>
                            <td>{{$x->item}}</td>
                            <td>{{$x->qty}}</td>
                            <td>{{$x->uom}}</td>
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
<script>
document.addEventListener("contextmenu", function(e){
    e.preventDefault();
}, false);

$(document).ready(function(){
    var req_date = $('#req_date').html();
    req_date = moment(req_date).format('dddd, MMMM D, YYYY, h:mm A');
    $('#req_date').html(req_date);
    var need_date = $('#need_date').html();
    need_date = moment(need_date).format('dddd, MMMM D, YYYY');
    $('#need_date').html(need_date);
    var prep_date = $('#prep_date').html();
    prep_date = moment(prep_date).format('dddd, MMMM D, YYYY, h:mm A');
    $('#prep_date').html(prep_date);
    var sched = $('#sched').html();
    sched = moment(sched).format('dddd, MMMM D, YYYY');
    $('#sched').html(sched);

    if($('#locfrom').html() == '5'){
        $('#locfrom').html('BALINTAWAK');
    }
    if($('#locfrom').html() == '6'){
        $('#locfrom').html('MALABON');
    }
    if($('#locto').html() == '1'){
        $('#locto').html('A1');
    }
    if($('#locto').html() == '2'){
        $('#locto').html('A2');
    }
    if($('#locto').html() == '3'){
        $('#locto').html('A3');
    }
    if($('#locto').html() == '4'){
        $('#locto').html('A4');
    }
});
</script>
@endsection