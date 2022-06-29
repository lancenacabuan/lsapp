@extends('layouts.app')
@section('content')
@role('sales|approver - sales|accounting|assembler|merchant') {{---ROLES---}}
<script>
    window.location = '/';
</script>
@endrole
<input type="hidden" id="req_num" value="{{$list->req_num}}">
<div class="container-fluid">
    <button id="btnPrint" type="button" class="btn btn-primary bp" style="margin-right: 5px;">PRINT</button>
    <button id="btnSavePDF" type="button" class="btn btn-primary bp">SAVE AS PDF</button>
    <a href="/stocktransfer?request_number={{$list->req_num}}" class="btn btn-primary float-right bp">BACK</a>
</div>
<br>
<center>
<div style="width: 850px;">
    <div id="printPage" class="panel-body table-responsive" style="font-size: 12px; width: 100%;">
        <div style="text-align: left; height: 70px; line-height: 70px; font-weight: bold; color: #0d1a80; font-family: Arial; font-size: 22px;">
            <img src="/idsi.png" class="mr-2" style="height: 70px; width: auto;">
            IDEASERV SYSTEMS INC.
        </div>
        <br>
        <table id="tblPrint" cellspacing="0" cellpadding="0" style="width: 100%;">
            <col span="9" />
            <tr height="20">
                <td style="text-align: center; font-size: 18px;" colspan="9" height="20"><strong>STOCK TRANSFER RECEIPT</strong></td>
            </tr>
            <tr height="20">
                <td colspan="9">&nbsp;</td>
            </tr>
            <tr height="20">
                <td colspan="2" height="20" style="font-weight: bold;">Date Requested:</td>
                <td colspan="2" id="req_date">{{$list->req_date}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;">Stock Transfer Receipt No.:</td>
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
                <td colspan="2" id="prep_date">{{$list->prepdate}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;">TO New Location</td>
                <td colspan="2" id="locto">{{$list->locto}}</td>
            </tr>
            <tr height="20">
                <td colspan="2" height="20" style="font-weight: bold;">Prepared By:</td>
                <td colspan="2">{{$list2->prepby}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;"></td>
                <td colspan="2"></td>
            </tr>
            <tr height="20">
                <td colspan="9" height="20">&nbsp;</td>
            </tr>
            <tr height="20">
                @php
                    $total = 0;
                @endphp
                <td colspan="9" height="20">
                    <table id="stockTransTable" class="table stockTransTable display" style="margin-top: 10px;">
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
</center>
<script>
document.addEventListener("contextmenu", function(e){
    e.preventDefault();
}, false);

$(document).ready(function(){
    $('#loading').hide(); Spinner.hide();
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
    var x = $('#tblPrint tr').length;
    if(x > 30){
        $('.extend').show();
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
    swal({
        title: "SAVE AS PDF?",
        text: "You are about to SAVE this Stock Request as PDF!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if(willDelete){
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