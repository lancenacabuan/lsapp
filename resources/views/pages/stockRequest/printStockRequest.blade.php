@extends('layouts.app')
@section('content')
@role('approver - warehouse') {{---ROLES---}}
<script>
    window.location = '/';
</script>
@endrole
@role('sales|merchant|assembler') {{---ROLES---}}
@if(auth()->user()->id != $list->user_id)
<script>
    window.location = '/';
</script>
@endif
@endrole
@role('sales|approver - sales|accounting|merchant') {{---ROLES---}}
@if($list->req_type_id == '1' || ($list->req_type_id == '4' && auth()->user()->id != $list->user_id) || $list->req_type_id == '5' || $list->req_type_id == '7')
<script>
    window.location = '/';
</script>
@endif
@endrole
<input type="hidden" id="req_num" value="{{$list->req_num}}">
<input type="hidden" id="status" value="{{$list->status}}">
<input type="hidden" id="verify" value="{{$list->verify}}">
<div class="container-fluid">
    <button id="btnPrint" type="button" class="btn btn-primary bp" style="margin-right: 5px;">PRINT</button>
    <button id="btnSavePDF" type="button" class="btn btn-primary bp">SAVE AS PDF</button>
    @role('admin|encoder|viewer|sales|approver - sales|accounting') {{---ROLES---}}
    <a href="/stockrequest?request_number={{$list->req_num}}" class="btn btn-primary float-right bp">BACK</a>
    <a href="/printRequest?request_number={{$list->req_num}}&demo=received" id="btnDemoReceived" class="btn btn-primary float-right bp mr-2" style="display: none;">DEMO RECEIVED RECEIPT</a>
    <a href="/printRequest?request_number={{$list->req_num}}" id="btnDemoSold" class="btn btn-primary float-right bp mr-2" style="display: none;">DEMO SOLD RECEIPT</a>
    @endrole
    @role('assembler') {{---ROLES---}}
    <a href="/assembly?request_number={{$list->req_num}}" class="btn btn-primary float-right bp">BACK</a>
    @endrole
    @role('merchant') {{---ROLES---}}
    <a href="/merchant?request_number={{$list->req_num}}" class="btn btn-primary float-right bp">BACK</a>
    @endrole
</div>
<br>
<center>
<div style="width: 850px;">
    <div id="printPage" class="panel-body table-responsive" style="font-size: 12px; width: 100%;">
        <div style="text-align: left; height: 70px; line-height: 70px; font-weight: bold; color: #0d1a80; font-family: Arial; font-size: 22px;">
            <img src="/inc/idsi.png" class="mr-2" style="height: 70px; width: auto;">
            IDEASERV SYSTEMS INC.
        </div>
        <br>
        <table id="tblPrint" cellspacing="0" cellpadding="0" style="width: 100%;">
            <col span="9" />
            <tr height="20">
                @if($list->req_type_id == 7)
                <td style="text-align: center; font-size: 18px;" colspan="9" height="20"><strong>TRANSMITTAL RECEIPT</strong></td>
                @else
                <td style="text-align: center; font-size: 18px;" colspan="9" height="20"><strong>DELIVERY RECEIPT</strong></td>
                @endif
            </tr>
            <tr height="20">
                <td colspan="9">&nbsp;</td>
            </tr>
            @if($list->req_type_id == 2 || $list->req_type_id == 8 || ($list->req_type_id == 3 && ($list->status_id == 10 || $list->status_id >= 27)))
            <tr height="20">
                <td colspan="1" style="font-weight: bold; width: 200px;">Delivery Receipt No.:</td>
                <td colspan="8">{{$list->req_num}}</td>
            </tr>
            <tr height="20">
                <td colspan="1" style="font-weight: bold;">Date Scheduled:</td>
                <td colspan="8" id="sched">{{$list->sched}}</td>
            </tr>
            <tr height="20">
                <td colspan="1" style="font-weight: bold;" class="tdHide">Client Name:</td>
                <td colspan="8" class="tdHide">{{$list->client_name}}</td>
            </tr>
            <tr height="20">
                <td colspan="1" style="font-weight: bold;" class="tdHide">Address / Branch:</td>
                <td colspan="8" class="tdHide">{{$list->location}}</td>
            </tr>
            <tr height="20">
                <td colspan="1" style="font-weight: bold;" class="tdHide">Contact Person:</td>
                <td colspan="8" class="tdHide">{{$list->contact}}</td>
            </tr>
            <tr height="20">
                <td colspan="1" style="font-weight: bold;" class="tdHide">Remarks:</td>
                <td colspan="8" class="tdHide">{{$list->remarks}}</td>
            </tr>
            <tr height="20">
                <td colspan="1" style="font-weight: bold;" class="tdHide">Reference SO/PO No.:</td>
                <td colspan="8" class="tdHide">{{$list->reference}}</td>
            </tr>
            @elseif($list->req_type_id == 7)
            <tr height="20">
                <td colspan="2" style="font-weight: bold;">Request Type:</td>
                <td colspan="2">{{$list->req_type}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;">Transmittal Receipt No.:</td>
                <td colspan="2">{{$list->req_num}}</td>
            </tr>
            <tr height="20">
                <td colspan="2" style="font-weight: bold;">Requested By:</td>
                <td colspan="2">{{$list->asset_reqby}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;">Date Requested:</td>
                <td colspan="2" id="format_date1">{{$list->req_date}}</td>
            </tr>
            <tr height="20">
                <td colspan="2" style="font-weight: bold;">Approved By:</td>
                <td colspan="2">{{$list->asset_apvby}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;">Date Needed:</td>
                <td colspan="2" id="format_date2">{{$list->needdate}}</td>
            </tr>
            <tr height="20">
                <td colspan="2" style="font-weight: bold;">Submitted By:</td>
                <td colspan="2">{{$list->req_by}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;">Date Prepared:</td>
                <td colspan="2" id="format_date3">{{$list->prepdate}}</td>
            </tr>
            <tr height="20">
                <td colspan="2" style="font-weight: bold;">Prepared By:</td>
                <td colspan="2">{{$list2->prepby}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;">Date Scheduled:</td>
                <td colspan="2" id="format_date4">{{$list->sched}}</td>
            </tr>
            @else
            <tr height="20">
                <td colspan="2" style="font-weight: bold;">Request Type:</td>
                <td colspan="2" id="req_type">{{$list->req_type}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;">Stock Request No.:</td>
                <td colspan="2">{{$list->req_num}}</td>
            </tr>
            <tr height="20">
                <td colspan="2" style="font-weight: bold;">Date Requested:</td>
                <td colspan="2" id="req_date">{{$list->req_date}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;">Date Scheduled:</td>
                <td colspan="2" id="sched">{{$list->sched}}</td>
            </tr>
            <tr height="20">
                <td colspan="2" style="font-weight: bold;">Date Needed:</td>
                <td colspan="2" id="need_date">{{$list->needdate}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;" class="tdHide">Client Name:</td>
                <td colspan="2" class="tdHide">{{$list->client_name}}</td>
                <td colspan="2" style="font-weight: bold; display: none;" class="tdMerchant">Order ID:</td>
                <td colspan="2" style="display: none;" class="tdMerchant">{{$list->orderID}}</td>
                <td colspan="2" style="font-weight: bold; display: none;" class="tdAssembly">Assembled Item Code:</td>
                <td colspan="2" style="display: none;" class="tdAssembly">{{$list1[0]->item_code ?? 'N/A'}}</td>
            </tr>
            <tr height="20">
                <td colspan="2" style="font-weight: bold;">Requested By:</td>
                <td colspan="2">{{$list->req_by}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;" class="tdHide">Address / Branch:</td>
                <td colspan="2" class="tdHide">{{$list->location}}</td>
                <td colspan="2" style="font-weight: bold; display: none;" class="tdShow">Original Request No.:</td>
                <td colspan="2" style="display: none;" class="tdShow">{{$list->assembly_reqnum}}</td>
                <td colspan="2" style="font-weight: bold; display: none;" class="tdAssembly">Assembled Item Name:</td>
                <td colspan="2" style="display: none;" class="tdAssembly" id="ellipsis">{{$list1[0]->item_desc ?? 'N/A'}}</td>
            </tr>
            <tr height="20">
                <td colspan="2" style="font-weight: bold;">Date Prepared:</td>
                <td colspan="2" id="prep_date">{{$list->prepdate}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;" class="tdHide">Contact Person:</td>
                <td colspan="2" class="tdHide">{{$list->contact}}</td>
                <td colspan="2" style="font-weight: bold; display: none;" class="tdAssembly">Quantity:</td>
                <td colspan="2" style="display: none;" class="tdAssembly">{{$list->qty}}-Unit/s</td>
            </tr>
            <tr height="20">
                <td colspan="2" style="font-weight: bold;">Prepared By:</td>
                <td colspan="2">{{$list2->prepby}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;" class="tdHide">Remarks:</td>
                <td colspan="2" class="tdHide">{{$list->remarks}}</td>
            </tr>
            <tr height="20">
                @if($list->req_type_id == 2 || $list->req_type_id == 8 || ($list->req_type_id == 3 && ($list->status_id == 10 || $list->status_id >= 27)))
                <td colspan="2" style="font-weight: bold;" class="tdHide">Reference SO/PO No.:</td>
                <td colspan="7" class="tdHide">{{$list->reference}}</td>
                @else
                <td colspan="9">&nbsp;</td>
                @endif
            </tr>
            @endif
            @if(count($listX) > 0)
            <tr height="20" class="tblPrepared">
                <td colspan="9">&nbsp;</td>
            </tr>
            <tr height="20" class="tblPrepared">
                <td colspan="9"><strong>{{$list->status}} ITEMS</strong></td>
            </tr>
            <tr height="20" class="tblPrepared">
                @php
                    $total = 0;
                @endphp
                <td colspan="9" height="20">
                    <table class="table tblPrepared display" style="margin-top: 10px;">
                        <thead>
                            <tr>
                                <th>ITEM CODE</th>
                                <th>ITEM DESCRIPTION</th>
                                <th>QTY</th>
                                <th>UOM</th>
                                <th>SERIAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listX as $x)
                            @php
                                if($x['uom'] == 'Meter'){
                                    $total+=1;
                                }
                                else{
                                    $total+=$x['qty'];
                                }
                            @endphp
                            <tr>
                                <td>{{$x['prodcode']}}</td>
                                <td>{{$x['item']}}</td>
                                <td>{{$x['qty']}}</td>
                                <td>{{$x['uom']}}</td>
                                <td>{{strtoupper($x['serial'])}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" style="text-align: right;">TOTAL ITEM COUNT:</th>
                                <th>{{$total}}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
            @endif
            <tr height="20" class="tblHide tblReceived">
                <td colspan="9">&nbsp;</td>
            </tr>
            <tr height="20" class="tblHide tblReceived">
                <td colspan="9"><strong id="tblReceived">RECEIVED ITEMS</strong></td>
            </tr>
            <tr height="20" class="tblHide tblReceived">
                @php
                    $total = 0;
                @endphp
                <td colspan="9" height="20">
                    <table class="table tblReceived display" style="margin-top: 10px;">
                        <thead>
                            <tr>
                                <th>ITEM CODE</th>
                                <th>ITEM DESCRIPTION</th>
                                <th>QTY</th>
                                <th>UOM</th>
                                <th>SERIAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($list3 as $x)
                            @php
                                if($x['uom'] == 'Meter'){
                                    $total+=1;
                                }
                                else{
                                    $total+=$x['qty'];
                                }
                            @endphp
                            <tr>
                                <td>{{$x['prodcode']}}</td>
                                <td>{{$x['item']}}</td>
                                <td>{{$x['qty']}}</td>
                                <td>{{$x['uom']}}</td>
                                <td>{{strtoupper($x['serial'])}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" style="text-align: right;">TOTAL ITEM COUNT:</th>
                                <th>{{$total}}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
            @if(count($list5) > 0)
            <tr height="20" class="tblHide tblPrevReceived">
                <td colspan="9">&nbsp;</td>
            </tr>
            <tr height="20" class="tblHide tblPrevReceived">
                <td colspan="9"><strong>PREVIOUSLY RECEIVED ITEMS</strong></td>
            </tr>
            <tr height="20" class="tblHide tblPrevReceived">
                @php
                    $total = 0;
                @endphp
                <td colspan="9" height="20">
                    <table class="table tblPrevReceived display" style="margin-top: 10px;">
                        <thead>
                            <tr>
                                <th>ITEM CODE</th>
                                <th>ITEM DESCRIPTION</th>
                                <th>QTY</th>
                                <th>UOM</th>
                                <th>SERIAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($list5 as $x)
                            @php
                                if($x['uom'] == 'Meter'){
                                    $total+=1;
                                }
                                else{
                                    $total+=$x['qty'];
                                }
                            @endphp
                            <tr>
                                <td>{{$x['prodcode']}}</td>
                                <td>{{$x['item']}}</td>
                                <td>{{$x['qty']}}</td>
                                <td>{{$x['uom']}}</td>
                                <td>{{strtoupper($x['serial'])}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" style="text-align: right;">TOTAL ITEM COUNT:</th>
                                <th>{{$total}}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
            @endif
            @if($getList3 == true)
            <script>$('.tblHide').hide();</script>
            <tr height="20" class="tblUpdReceived">
                <td colspan="9">&nbsp;</td>
            </tr>
            <tr height="20" class="tblUpdReceived">
                <td colspan="9"><strong>RECEIVED ITEMS</strong></td>
            </tr>
            <tr height="20" class="tblUpdReceived">
                @php
                    $total = 0;
                @endphp
                <td colspan="9" height="20">
                    <table class="table tblUpdReceived display" style="margin-top: 10px;">
                        <thead>
                            <tr>
                                <th>ITEM CODE</th>
                                <th>ITEM DESCRIPTION</th>
                                <th>QTY</th>
                                <th>UOM</th>
                                <th>SERIAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($list5 as $x)
                            @php
                                if($x['uom'] == 'Meter'){
                                    $total+=1;
                                }
                                else{
                                    $total+=$x['qty'];
                                }
                            @endphp
                            <tr>
                                <td>{{$x['prodcode']}}</td>
                                <td>{{$x['item']}}</td>
                                <td>{{$x['qty']}}</td>
                                <td>{{$x['uom']}}</td>
                                <td>{{strtoupper($x['serial'])}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" style="text-align: right;">TOTAL ITEM COUNT:</th>
                                <th>{{$total}}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
            @endif
            @if(count($list4) > 0)
            <tr height="20" class="tblIncomplete">
                <td colspan="9">&nbsp;</td>
            </tr>
            <tr height="20" class="tblIncomplete">
                <td colspan="9"><strong>INCOMPLETE ITEMS</strong></td>
            </tr>
            <tr height="20" class="tblIncomplete">
                @php
                    $total = 0;
                @endphp
                <td colspan="9" height="20">
                    <table class="table tblIncomplete display" style="margin-top: 10px;">
                        <thead>
                            <tr>
                                <th>ITEM CODE</th>
                                <th>ITEM DESCRIPTION</th>
                                <th>QTY</th>
                                <th>UOM</th>
                                <th>SERIAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($list4 as $x)
                            @php
                                if($x['uom'] == 'Meter'){
                                    $total+=1;
                                }
                                else{
                                    $total+=$x['qty'];
                                }
                            @endphp
                            <tr>
                                <td>{{$x['prodcode']}}</td>
                                <td>{{$x['item']}}</td>
                                <td>{{$x['qty']}}</td>
                                <td>{{$x['uom']}}</td>
                                <td>{{strtoupper($x['serial'])}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" style="text-align: right;">TOTAL ITEM COUNT:</th>
                                <th>{{$total}}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
            @endif
            @if(count($list6) > 0)
            <tr height="20" class="tblDefective">
                <td colspan="9">&nbsp;</td>
            </tr>
            <tr height="20" class="tblDefective">
                <td colspan="9"><strong>DEFECTIVE ITEMS</strong></td>
            </tr>
            <tr height="20" class="tblDefective">
                @php
                    $total = 0;
                @endphp
                <td colspan="9" height="20">
                    <table class="table tblDefective display" style="margin-top: 10px;">
                        <thead>
                            <tr>
                                <th>ITEM CODE</th>
                                <th>ITEM DESCRIPTION</th>
                                <th>QTY</th>
                                <th>UOM</th>
                                <th>SERIAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($list6 as $x)
                            @php
                                if($x['uom'] == 'Meter'){
                                    $total+=1;
                                }
                                else{
                                    $total+=$x['qty'];
                                }
                            @endphp
                            <tr>
                                <td>{{$x['prodcode']}}</td>
                                <td>{{$x['item']}}</td>
                                <td>{{$x['qty']}}</td>
                                <td>{{$x['uom']}}</td>
                                <td>{{strtoupper($x['serial'])}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" style="text-align: right;">TOTAL ITEM COUNT:</th>
                                <th>{{$total}}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
            @endif
            @if(count($list0) > 0)
            <tr height="20" class="tblPending">
                <td colspan="9">&nbsp;</td>
            </tr>
            <tr height="20" class="tblPending">
                <td colspan="9"><strong>PENDING ITEMS</strong></td>
            </tr>
            <tr height="20" class="tblPending">
                @php
                    $total = 0;
                @endphp
                <td colspan="9" height="20">
                    <table class="table tblPending display" style="margin-top: 10px;">
                        <thead>
                            <tr>
                                <th>ITEM CODE</th>
                                <th>ITEM DESCRIPTION</th>
                                <th>QTY</th>
                                <th>UOM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($list0 as $x)
                            @php
                                if($x['uom'] == 'Meter'){
                                    $total+=1;
                                }
                                else{
                                    $total+=$x['pending'];
                                }
                            @endphp
                            <tr>
                                <td>{{$x['prodcode']}}</td>
                                <td>{{$x['item']}}</td>
                                <td>{{$x['pending']}}</td>
                                <td>{{$x['uom']}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" style="text-align: right;">TOTAL ITEM COUNT:</th>
                                <th>{{$total}}</th>
                                <th colspan="1"></th>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
            @endif
            <tr height="20" class="extend" style="display: none;">
                <td colspan="9">&nbsp;</td>
            </tr>
            <tr height="20" class="extend" style="display: none;">
                <td colspan="9">&nbsp;</td>
            </tr>
            <tr height="20" class="extend" style="display: none;">
                <td colspan="9">&nbsp;</td>
            </tr>
            <tr height="20" class="extend" style="display: none;">
                <td colspan="9">&nbsp;</td>
            </tr>
            <tr height="20">
                <td height="20">&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;">Received By:</td>
                <td colspan="2">{{$list->asset_reqby ?? $list->client_name ?? '______________________________'}}</td>
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
</center>
<script>
document.addEventListener("contextmenu", function(e){
    e.preventDefault();
}, false);

$(document).ready(function(){
    $('#loading').hide();
    var url = new URL(window.location.href);
    var demo = url.searchParams.get("demo");

    if($('#status').val().includes('SOLD')){
        $('#tblReceived').html('SOLD ITEMS');
        $('#btnDemoReceived').show();
    }
    if($('#status').val().includes('SOLD') && demo == 'received'){
        $('#tblReceived').html('RECEIVED ITEMS');
        $('#btnDemoSold').show();
        $('#btnDemoReceived').hide();
    }

    for(var i = 1; i <= 4; i++){
        $('#format_date'+i).html(moment($('#format_date'+i).html()).format('dddd, MMMM DD, YYYY'));
    }

    var req_date = $('#req_date').html();
    req_date = moment(req_date).format('dddd, MMMM DD, YYYY');
    $('#req_date').html(req_date);

    var need_date = $('#need_date').html();
    need_date = moment(need_date).format('dddd, MMMM DD, YYYY');
    $('#need_date').html(need_date);

    var prep_date = $('#prep_date').html();
    prep_date = moment(prep_date).format('dddd, MMMM DD, YYYY');
    $('#prep_date').html(prep_date);

    var sched = $('#sched').html();
    sched = moment(sched).format('dddd, MMMM DD, YYYY');
    $('#sched').html(sched);

    if($('#req_type').html() == 'SERVICE UNIT' || $('#req_type').html() == 'ASSEMBLY' || $('#req_type').html() == 'REPLACEMENT' || $('#req_type').html() == 'MERCHANT'){
        $('.tdHide').html('');
    }
    if($('#req_type').html() == 'MERCHANT'){
        $('.tdHide').hide();
        $('.tdMerchant').show();
    }
    if($('#req_type').html() == 'REPLACEMENT'){
        $('.tdHide').hide();
        $('.tdShow').show();
    }
    if($('#req_type').html() == 'ASSEMBLY'){
        $('.tdHide').hide();
        $('.tdAssembly').show();
        
        var ellipsis = $('#ellipsis').html();
        if(ellipsis.length > 40){
            ellipsis = ellipsis.substring(0, 40) + '...';
            $('#ellipsis').html(ellipsis);
        }
    }
    var x = $('#tblPrint tr:visible').length;
    if(x > 30){
        $('.extend').show();
    }
    if($('.tblPrepared tbody tr').length == 0){
        $('.tblPrepared').hide();
    }
    if($('.tblReceived tbody tr').length == 0){
        $('.tblReceived').hide();
    }
    if($('.tblPrevReceived tbody tr').length == 0 || demo == 'received'){
        $('.tblPrevReceived').hide();
    }
    if($('.tblUpdReceived tbody tr').length == 0){
        $('.tblUpdReceived').hide();
    }
    if($('.tblIncomplete tbody tr').length == 0){
        $('.tblIncomplete').hide();
    }
    if($('.tblDefective tbody tr').length == 0 || $('#verify').val() == 'Confirmed'){
        $('.tblDefective').hide();
    }
    if($('.tblPending tbody tr').length == 0){
        $('.tblPending').hide();
    }
});

$(document).on('click', '#btnPrint', function(){
    var printContents=document.getElementById('printPage').innerHTML;
    var originalContents=document.body.innerHTML;
    document.body.innerHTML=printContents;
    window.print();
    document.body.innerHTML=originalContents;
});

$(document).on('click', '#btnSavePDF', function(){
    Swal.fire({
        title: "SAVE AS PDF?",
        text: "You are about to SAVE this Stock Request as PDF!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            var content = document.getElementById('printPage');
            var options = {
                margin:       0.5,
                filename:     $('#req_num').val()+'.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf(content, options);
        }
    });  
});
</script>
@endsection