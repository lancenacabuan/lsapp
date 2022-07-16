@extends('layouts.app')
@section('content')
@role('merchant|accounting|assembler|approver - sales|approver - warehouse') {{---ROLES---}}
<script>
    window.location = '/';
</script>
@endrole
@role('sales') {{---ROLES---}}
@if(auth()->user()->id != $list->user_id)
<script>
    window.location = '/';
</script>
@endif
@endrole
@role('admin|encoder|viewer|sales') {{---ROLES---}}
<script>$('#loading').hide();</script>
@endrole
<input type="hidden" id="req_num" value="{{$list->req_num}}">
<input type="hidden" id="confirmed" value="{{$confirmed}}">
<div class="container-fluid">
    <button id="btnPrint" type="button" class="btn btn-primary bp" style="margin-right: 5px;">PRINT</button>
    <button id="btnSavePDF" type="button" class="btn btn-primary bp">SAVE AS PDF</button>
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
            @if($list->req_type_id == 2 || $list->req_type_id == 3)
            <tr height="20">
                <td colspan="1" style="font-weight: bold; width: 200px;">Delivery Receipt No.:</td>
                <td colspan="8">{{$list->req_num}}</td>
            </tr>
            <tr height="20">
                <td colspan="1" style="font-weight: bold;">Date Scheduled:</td>
                <td colspan="8" id="format_date1">{{$list->sched}}</td>
            </tr>
            <tr height="20">
                <td colspan="1" style="font-weight: bold;">Client Name:</td>
                <td colspan="8">{{$list->client_name}}</td>
            </tr>
            <tr height="20">
                <td colspan="1" style="font-weight: bold;">Address / Branch:</td>
                <td colspan="8">{{$list->location}}</td>
            </tr>
            <tr height="20">
                <td colspan="1" style="font-weight: bold;">Contact Person:</td>
                <td colspan="8">{{$list->contact}}</td>
            </tr>
            <tr height="20">
                <td colspan="1" style="font-weight: bold;">Remarks:</td>
                <td colspan="8">{{$list->remarks}}</td>
            </tr>
            <tr height="20">
                <td colspan="1" style="font-weight: bold;">Reference SO/PO No.:</td>
                <td colspan="8">{{$list->reference}}</td>
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
            @endif
            @if(count($listX) > 0)
            <tr height="20" class="tblPrepared">
                <td colspan="9">&nbsp;</td>
            </tr>
            <tr height="20" class="tblPrepared">
                <td colspan="9"><strong>FOR RECEIVING ITEMS</strong></td>
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
                <td colspan="9"><strong>RECEIVED ITEMS</strong></td>
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
                <td colspan="2" id="format_date5">{{$list->confirmdate ?? '______________________________'}}</td>
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
    var req_num = $('#req_num').val();
    var url = new URL(window.location.href);
    var token = url.searchParams.get("token");
    var demo = url.searchParams.get("demo");
    if($('#confirmed').val() == true){
        Swal.fire('THANK YOU', 'You already received these item/s! <br>No further actions needed.', 'info');
    }
    else{
        $('#loading').show();
        $.ajax({
            type: 'post',
            url: '/logConfirm',
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                'request_number': req_num,
                'token': token
            },
            success: function(data){
                if(data == 'true'){
                    $('#loading').hide();
                    Swal.fire('RECEIVE CONFIRMED', 'Stock Request '+req_num+' has been received successfully.', 'success');
                }
                else{
                    return false;
                }
            },
            error: function(data){
                if(data.status == 401){
                    window.location.reload;
                }
                alert(data.responseText);
            }
        });
    }
    for(var i = 1; i <= 5; i++){
        $('#format_date'+i).html(moment($('#format_date'+i).html()).format('dddd, MMMM DD, YYYY'));
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
    if($('.tblDefective tbody tr').length == 0){
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