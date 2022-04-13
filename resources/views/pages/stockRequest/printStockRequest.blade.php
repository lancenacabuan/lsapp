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
                <td colspan="2" class="tdHide">{{$list->reference}}</td>
                <td colspan="2" style="font-weight: bold; display: none;" class="tdShow">Assembly Request No.:</td>
                <td colspan="2" style="display: none;" class="tdShow">{{$list->assembly_reqnum}}</td>
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
                <td colspan="2" id="prep_date">{{$list->prepdate}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;" class="tdHide">Client Name:</td>
                <td colspan="2" class="tdHide">{{$list->client_name}}</td>
                <td colspan="2" style="font-weight: bold; display: none;" class="tdAssembly">Assembled Item Name:</td>
                <td colspan="2" style="display: none;" class="tdAssembly" id="ellipsis">{{$list->item_desc}}</td>
            </tr>
            <tr height="20">
                <td colspan="2" height="20" style="font-weight: bold;">Prepared By:</td>
                <td colspan="2">{{$list2->prepby}}</td>
                <td>&nbsp;</td>
                <td colspan="2" style="font-weight: bold;" class="tdHide">Address / Branch:</td>
                <td colspan="2" class="tdHide">{{$list->location}}</td>
                <td colspan="2" style="font-weight: bold; display: none;" class="tdAssembly">Quantity:</td>
                <td colspan="2" style="display: none;" class="tdAssembly">{{$list->qty}}-Unit/s</td>
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
    var req_date = $('#req_date').html();
    req_date = moment(req_date).format('dddd, MMMM DD, YYYY, h:mm A');
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

    if($('#req_type').html() == 'SERVICE UNIT' || $('#req_type').html() == 'ASSEMBLY' || $('#req_type').html() == 'REPLACEMENT'){
        $('.tdHide').html('');
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