var minDate, maxDate;
$(function(){
    var dtToday = new Date();
    
    var month = dtToday.getMonth() + 1;
    var day = dtToday.getDate();
    var year = dtToday.getFullYear();
    if(month < 10)
        month = '0' + month.toString();
    if(day < 10)
        day = '0' + day.toString();    
    minDate = year + '-' + month + '-' + day;

    $('#needdate').attr('min', minDate);
    $('#schedOn').attr('min', minDate);
    $('#resched').attr('min', minDate);
});

const _MS_PER_DAY = 1000 * 60 * 60 * 24;
function dateDiffInDays(a, b){
    const utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
    const utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());
    
    return Math.floor((utc2 - utc1) / _MS_PER_DAY);
}

function copyReqNum(){
    var copyText = document.getElementById("request_num_details");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    swal({
        title: copyText.value,
        text: "Copied to Clipboard!",
        icon: "success",
        timer: 2000
    });
}

function decodeHtml(str){
    var map = {
        '&amp;': '&', 
        '&lt;': '<', 
        '&gt;': '>', 
        '&quot;': '"', 
        '&#039;': "'"
    };
    return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function(m){return map[m];});
}

function generateReqNum(){
    var today = new Date();
    var month = today.getMonth()+1;
    if(month <= 9){
        month = '0'+month;
    }
    var day = today.getDate();
    if(day <= 9){
        day = '0'+day;
    }
    var date = today.getFullYear()+'-'+month+day+'-';
    var result = '';
    var characters = '123456789';

    for(var i = 0; i < 3; i++){
        result += characters.charAt(Math.floor(Math.random() * 6));
    }
    var request_number = date+result;

    $.ajax({
        type:'get',
        url:'/generateReqNum',
        async: false,
        data:{
            'request_number': request_number
        },
        success: function(data){
            if(data == 'unique'){
                document.getElementById("reqnum").value = request_number;
            }
            else{
                generateReqNum();
            }
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/stocktransfer';
            }
                alert(data.responseText);
        }
    });
}

$(".btnNewStockTransfer").on('click', function(){
    $('#newStockTransfer').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('.modal-body').html();
    $('#newStockTransfer').modal('show');
    generateReqNum();
});

setInterval(runFunction, 0);
function runFunction(){
    if($('#newStockTransfer').is(':visible')){
        if($('#needdate').val() && $('#locfrom').val() && $('#locto').val()){
            $('#transrequestDetails').show();
            $('.header_label').hide();
        }
        else{
            $('#transrequestDetails').hide();
            $('.header_label').show();
        }
    }
}

$('#locfrom').on('change', function(){
    $('#item').find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
    $('#qty').val('');
    $('#qtystock').val('');
    $('#uom').val('');
    $('#prodcode').val('');
    var location_id = $(this).val();
    $.ajax({
        type:'get', 
        url:'/setcategory', 
        data:{
            'location_id': location_id
        }, 
        success: function(data){
            $('#category').find('option').remove().end()
            $('#category').append($('<option value="" selected disabled>Select Category</option>'));
            var list = $.map(data, function(value, index){ 
                return [value];
            });

            list.forEach(value => {
                $('#category').append($('<option>', {
                    value: value.category_id,
                    text: value.category
                }));
            });
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/stocktransfer';
            }
            alert(data.responseText);
        }
    });
});

$('#category').on('change', function(){
    $("#qty").prop('disabled', true);
    $("#qty").val('');
    $("#qtystock").val('');
    $("#uom").val('');
    $("#prodcode").val('');
    var category_id = $(this).val();
    $.ajax({
        type: 'get', 
        url: '/setitems', 
        data:{
            'category_id': category_id,
            'location_id': $('#locfrom').val()
        }, 
        success: function(data){
            $('#item').find('option').remove().end()
            $('#item').append($('<option value="" selected disabled>Select Item</option>'));
            var list = $.map(data, function(value, index){ 
                return [value];
            });

            list.forEach(value => {
                $('#item').append($('<option>', {
                    value: value.item_id,
                    text: value.item.toUpperCase()
                }));
            });
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/stocktransfer';
            }
            alert(data.responseText);
        }
    });
});

$('#item').on('change', function(){
    func_settransuom();
    func_qtystock();
});

function func_settransuom(){
    var item_id = $('#item').val();
    $.ajax({
        type: 'get', 
        url: '/settransuom', 
        data:{
            'item_id': item_id,
        }, 
        success: function(data){
            $('#prodcode').val(data[0].prodcode);
            $('#uom').val(data[0].uom);
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/stocktransfer';
            }
            alert(data.responseText);
        }
    });
}

function func_qtystock(){
    $('#qty').prop('disabled', false);
    $("#qty").val('1');
    var item_id = $('#item').val();
    $.ajax({
        type:'get', 
        url:'/qtystock', 
        data:{
            'item_id': item_id,
            'location_id': $('#locfrom').val()
        }, 
        success: function(data){
            var table = document.getElementById('tblNewStockTransfer');
            var qtyminus = 0;
            if(table.rows.length > 1){
                for(var r = 1, n = table.rows.length; r < n; r++){
                    for(var c = 0, m = table.rows[r].cells.length; c < m; c++){
                        if(table.rows[r].cells[2].innerHTML == $("#item option:selected").text()){
                            qtyminus = table.rows[r].cells[3].innerHTML;
                        }
                    }
                }
            }
            $('#qtystock').val(data - qtyminus);
            $('#qty').attr({
                "max" : data - qtyminus,
                "min" : 0
            });
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/stocktransfer';
            }
            alert(data.responseText);
        }
    });
}

$('.location').on('change', function(){
    $('.location option').show();
    $('.location').each(function(){
        var $this = $(this);
        $('.location').not($this).find('option').each(function(){
            if($(this).attr('value') == $this.val()){
                $(this).hide();
            }
        });
    });
    $('select option:contains("Select Location")').show();
});

$(".add-row").on('click', function(){
    var category = $("#category option:selected").text();
    var item = $("#item option:selected").text();
    var item_id = $("#item").val();
    var prodcode = $("#prodcode").val();
    var uom = $("#uom").val();
    var qty = parseInt($("#qty").val());
    var qtystock = parseInt($("#qtystock").val());
    var markup = "<tr><td class='d-none'>" + item_id + "</td><td>" + prodcode + "</td><td>" + item + "</td><td>" + qty + "</td><td>" + uom + "</td><td> <button type='button' style='zoom: 75%;' class='delete-row btn btn-danger bp'>REMOVE</button> </td></tr>";
    var ctr = 'false';
    if(category == "Select Category" || item == "Select Item" || qty == "" || qty == "0" || uom == ""){
        swal('REQUIRED','Please select an item!','error');
        return false;
    }
    else{
        if(qty > qtystock){
            swal('EXCEED LIMIT','Item quantity exceeds available stock!','error');
            return false;
        }
        else{
            $('#locfrom').prop('disabled', true);
            var table = document.getElementById('tblNewStockTransfer');
            var count = table.rows.length;
            for(i = 1; i < count; i++){
                var objCells = table.rows.item(i).cells;
                if(item==objCells.item(2).innerHTML){
                    objCells.item(3).innerHTML = parseInt(objCells.item(3).innerHTML) + parseInt(qty);
                    ctr = 'true';
                    category = $("#category").val('');
                    item = $("#item").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
                    prodcode = $("#prodcode").val('');
                    uom = $("#uom").val('');
                    qty = $("#qty").val('');
                    qtystock = $("#qtystock").val('');
                    $('#qty').prop('disabled', true);
                    return false;
                }
                else {
                    ctr = 'false';
                }
            }
            if(ctr == 'false')
            { $("#tblNewStockTransfer tbody").append(markup); }
            category = $("#category").val('');
            item = $("#item").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
            prodcode = $("#prodcode").val('');
            uom = $("#uom").val('');
            qty = $("#qty").val('');
            qtystock = $("#qtystock").val('');
            $('#qty').prop('disabled', true);
            $('#tblNewStockTransfer').show();
            $('#divNewStockTransfer').toggle();
            $('#btnClose').show();
            $('#btnSave').show();
        }
    }
    if($('#tblNewStockTransfer tbody').children().length==0){
        $('.submit_label').show();
    }
    else{
        $('.submit_label').hide();
    }
});

$("#tblNewStockTransfer").on('click', '.delete-row', function(){
    $("#category").val('');
    $("#item").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
    $("#prodcode").val('');
    $("#uom").val('');
    $("#qty").val('');
    $("#qtystock").val('');
    $('#qty').prop('disabled', true);
    $(this).closest("tr").remove();
    if($('#tblNewStockTransfer tbody').children().length==0){
        $('#tblNewStockTransfer').hide();
        $('#divNewStockTransfer').removeClass();
        $('#btnClose').hide();
        $('#btnSave').hide();
        $('#locfrom').prop('disabled', false);
        $('.submit_label').show();
    }
});

$('#btnSave').on('click', function(){
    if($("#needdate").val() < minDate){
        swal('Minimum Date is today!','Select within date range from today onwards.','error');
        return false;
    }
    else{
        swal({
            title: "SUBMIT STOCK TRANSFER REQUEST?",
            text: "Please review the details of your request. Hit OK to confirm or CANCEL to edit it.",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                    type:'post',
                    url:'/saveTransReqNum',
                    async: false,
                    headers:{
                        'X-CSRF-TOKEN': $("#csrf").val()
                    },
                    data:{
                        'request_number': $('#reqnum').val(),
                        'needdate': $('#needdate').val(),
                        'locfrom': $('#locfrom').val(),
                        'locto': $('#locto').val(),
                    },
                    success: function(data){
                        if(data == 'true'){
                            var myTable = $('#tblNewStockTransfer').DataTable();
                            var form_data  = myTable.rows().data();
                            $.each(form_data, function(key, value){
                                $.ajax({
                                    type:'post',
                                    url:'/saveTransRequest',
                                    async: false,
                                    headers:{
                                        'X-CSRF-TOKEN': $("#csrf").val()
                                    },
                                    data:{
                                        'request_number': $('#reqnum').val(),
                                        'item': value[0],
                                        'quantity': value[3]
                                    },
                                    success: function(data){
                                        if(data == 'true'){
                                            return true;
                                        }
                                        else{
                                            return false;
                                        }
                                    },
                                    error: function(data){
                                        if(data.status == 401){
                                            window.location.href = '/stocktransfer';
                                        }
                                        alert(data.responseText);
                                    }
                                });
                            });
                            scrollReset();
                            $('#newStockTransfer').hide();
                            $('#newStockTransfer').modal('dispose');
                            $('#loading').show(); Spinner(); Spinner.show();
                            $.ajax({
                                type:'post',
                                url:'/logTransSave',
                                headers:{
                                    'X-CSRF-TOKEN': $("#csrf").val()
                                },
                                data:{
                                    'request_number': $('#reqnum').val(),
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide(); Spinner.hide();
                                        swal("SUBMIT SUCCESS", "STOCK TRANSFER REQUEST", "success");
                                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                                    }
                                    else{
                                        return false;
                                    }
                                },
                                error: function(data){
                                    if(data.status == 401){
                                        window.location.href = '/stocktransfer';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else{
                            $('#newStockTransfer').hide();
                            swal("SUBMIT FAILED", "STOCK TRANSFER REQUEST", "error");
                            setTimeout(function(){location.href="/stocktransfer"}, 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/stocktransfer';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
});

$('#close').on('click', function(){
    window.location.href = '/stocktransfer';
});

$('#btnClose').on('click', function(){
    window.location.href = '/stocktransfer';
});

$('#modalClose').on('click', function(){
    window.location.href = '/stocktransfer';
});

$('table.stocktransferTable').dataTable().fnDestroy();
$('#loading').show(); Spinner(); Spinner.show();
$('table.stocktransferTable').DataTable({ 
    columnDefs: [
        {
            "targets": [0],
            "render": $.fn.dataTable.render.moment('YYYY-MM-DD', 'MMM. DD, YYYY')
        },
        {
            "targets": [7,8,9,10,11,12,13],
            "visible": false,
            "searchable": false
        }
    ],
    language:{
        processing: "Loading...",
        emptyTable: "No data available in table"
    },
    serverSide: true,
    ajax:{
        url: '/transfer_data',
    },
    columns: [
        { data: 'reqdate' },
        {
            data: 'needdate',
            "render": function(data, type, row){
                if(row.status_id == '7' || row.status_id == '8'){
                    return "<span class='d-none'>"+row.needdate+"</span>"+moment(row.needdate).format('MMM. DD, YYYY');
                }
                else{
                    var a = new Date(minDate);
                    var b = new Date(row.needdate);
                    var difference = dateDiffInDays(a, b);
                    if(difference >= 0 && difference <= 3){
                        return "<span class='d-none'>"+row.needdate+"</span><span style='color: Blue; font-weight: bold;'>"+moment(row.needdate).format('MMM. DD, YYYY')+'&nbsp;&nbsp;&nbsp;'+"<i style='zoom: 150%; color: blue;' class='fa fa-exclamation-triangle'></i></span>";
                    }
                    else if(difference < 0){
                        return "<span class='d-none'>"+row.needdate+"</span><span style='color: Red; font-weight: bold;'>"+moment(row.needdate).format('MMM. DD, YYYY')+'&nbsp;&nbsp;&nbsp;'+"<i style='zoom: 150%; color: red;' class='fa fa-exclamation-circle'></i></span>";
                    }
                    else{
                        return "<span class='d-none'>"+row.needdate+"</span>"+moment(row.needdate).format('MMM. DD, YYYY');
                    }
                }
            }
        },
        { data: 'req_num' },
        { data: 'req_by' },
        { data: 'location_from' },
        { data: 'location_to' },
        {
            data: 'status',
            "render": function(data, type, row){
                if(row.status_id == '6'){
                    return "<span style='color: DarkSlateGray; font-weight: bold;'>"+row.status+'&nbsp;&nbsp;&nbsp;'+"<i style='zoom: 150%; color: DarkSlateGray;' class='fa fa-exclamation-triangle'></i></span>";
                }
                else if(row.status_id == '1' || row.status_id == '15'){
                    return "<span style='color: Red; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '2' || row.status_id == '5' || row.status_id == '16'){
                    return "<span style='color: Indigo; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '3' || row.status_id == '4' || row.status_id == '17'){
                    return "<span style='color: Green; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '8'){
                    return "<span style='color: Blue; font-weight: bold;'>"+row.status+"</span>";
                }
                else{
                    return "<span style='color: Gray; font-weight: bold;'>"+row.status+"</span>";
                }
            }
        },
        { data: 'status_id' },
        { data: 'locfrom' },
        { data: 'locto' },
        { data: 'prep_by' },
        { data: 'sched' },
        { data: 'user_id' },
        { data: 'reason' }
    ],
    order: [],
    initComplete: function(){
        return notifyDeadline();
    }
});

if($(location).attr('pathname')+window.location.search != '/stocktransfer'){
    url = window.location.search;
    reqnum = url.replace('?request_number=', '');
    $.ajax({
        url: '/transModal',
        headers:{
            'X-CSRF-TOKEN': $("#csrf").val()
        },
        dataType: 'json',
        type: 'get',
        data:{
            request_number: reqnum,
        },
        success: function(data){
            $('#detailsStockTransfer').modal({
                backdrop: 'static',
                keyboard: false
            });
            var transitem = $.map(data.data, function(value, index){ 
                return [value];
            });
            transitem.forEach(value => {
                var requestStatus = value.status_id;
                    $('#status_id_details').val(requestStatus);
                var req_type_id = value.req_type_id;
                    $('#req_type_id_details').val(req_type_id);
                var req_date = value.date;
                    req_date = moment(req_date).format('dddd, MMMM DD, YYYY, h:mm A');
                    $('#reqdate_details').val(req_date);
                var need_date = value.needdate;
                    maxDate = need_date;
                    need_date = moment(need_date).format('dddd, MMMM DD, YYYY');
                    $('#needdate_details').val(need_date);
                var req_num = value.req_num;
                    $('#request_num_details').val(req_num);
                var req_by = value.req_by;
                    $('#reqby_details').val(req_by);
                var status = value.status;
                    $('#status_details').val(status);
                var prep_by = value.prep_by;
                    $('#prep_by').val(prep_by);
                    $('#prep_by1').val(prep_by);
                    $('#reprep_by').val(prep_by);
                var sched = value.sched;
                    sched = moment(sched).format('dddd, MMMM DD, YYYY');
                    $('#sched').val(sched);
                    $('#sched1').val(sched);
                    $('#resched1').val(sched);
                var locfrom = value.locfrom;
                    $('#locfrom_details').val(locfrom);
                var locto = value.locto;
                    $('#locto_details').val(locto);
                var reason = value.reason;
                    $('#reason_details').val(reason);
                var btnDel = '';
                var hideCol = '';
                var hideEdit = '';
                var hideEdit1 = '';

                $('.modal-body').html();
                $('#detailsStockTransfer').modal('show');
                if(locfrom == 5){
                    hideCol = 12;
                }
                if(locfrom == 6){
                    hideCol = 11;
                }
                if(value.user_id != $('#current_user').val()){
                    $("#btnDelete").hide();
                    btnDel = 13;
                }
                else{
                    $("#btnDelete").show();
                }
                if(requestStatus != '6'){
                    $("#btnApprove").hide();
                    $("#btnDisapprove").hide();
                }
                if(requestStatus == '7'){
                    $("#btnApprove").hide();
                    $("#btnDisapprove").hide();
                    $("#reason_label").show();
                    $("#reason_details").show();
                }
                if(requestStatus == '1'|| requestStatus == '2'|| requestStatus == '3' || requestStatus == '4' || requestStatus == '5' || requestStatus >= 8){
                    $("#btnDelete").hide();
                    btnDel = 13;
                }
                if(requestStatus == '2' || requestStatus == '3' || requestStatus == '4' || requestStatus == '6' || requestStatus == '7' || requestStatus >= 8){
                    $("#proceed_label").hide();
                    $("#btnProceed").hide();
                }
                if(requestStatus == '2' || requestStatus == '5'){
                    $("#processModal").show();
                    $(".schedItemsModal").show();
                    if($("#current_role").val() == '["viewer"]'){
                        hideEdit = 5;
                    }
                }
                if(requestStatus == '3' || requestStatus == '4'){
                    $("#processModal").show();
                    document.getElementById('modalheader').innerHTML = 'FOR RECEIVING ITEM DETAILS';
                    $(".transitItemsModal").show();
                    hideEdit = 5;
                }
                if(requestStatus == '8'){
                    $("#processModal").show();
                    document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
                    $(".transitItemsModal").show();
                    $(".btnReceive").hide();
                    $(".receive_label").hide();
                    hideEdit = 5;
                }
                if(requestStatus == '15'){
                    $("#processModal").show();
                    document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
                    $(".btnReceive").hide();
                    hideEdit = 5;
                    $("#incItemsModal").show();
                    $('.divResched').show();
                    // $('#resched').attr('max', maxDate);
                    hideEdit1 = 5;
                }
                if(requestStatus == '16'){
                    $("#processModal").show();
                    document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
                    $(".btnReceive").hide();
                    hideEdit = 5;
                    $("#incItemsModal").show();
                    $('.divResched1').show();
                    hideEdit1 = 5;
                }
                if(requestStatus == '17'){
                    $("#processModal").show();
                    document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
                    $(".btnReceive").hide();
                    hideEdit = 5;
                    $("#incItemsModal").show();
                    $("#increceive_label").show();
                    $('.divResched1').show();
                    hideEdit1 = 5;
                    $(".btnTransit").hide();
                    $(".btnReceiveInc").show();
                }
                if(requestStatus == '1'|| requestStatus == '5' || requestStatus == '6'){
                    var transferDetails = [5,6,7,8,9,10,hideCol,btnDel];
                }
                else{
                    var transferDetails = [4,5,6,7,8,9,10,11,12,13];
                }

                $('table.transferDetails').dataTable().fnDestroy();
                $('table.transferDetails').DataTable({ 
                    columnDefs: [
                        {
                            "targets": transferDetails,
                            "visible": false,
                            "searchable": false
                        },
                        {   
                            "render": function(data, type, row, meta){
                                    return '<button style="zoom: 75%;" class="btn btn-danger bp btndelItem" id="'+ meta.row +'">REMOVE</button>';
                            },
                            "defaultContent": '',
                            "data": null,
                            "targets": [13]
                        }
                    ],
                    searching: false,
                    paging: false,
                    ordering: false,
                    info: false,
                    language:{
                        emptyTable: "No data available in table",
                        processing: "Loading...",
                    },
                    serverSide: true,
                    ajax:{
                        url: '/transferDetails',
                        data:{
                            reqnum: req_num,
                        },
                        dataType: 'json',
                        error: function(data){
                            if(data.status == 401){
                                window.location.href = '/stocktransfer';
                            }
                            alert(data.responseText);
                        },
                    },
                    order: [],
                    columns: [
                        { data: 'prodcode' },
                        { data: 'item' },
                        { data: 'uom' },
                        { data: 'quantity' },
                        { data: 'pending' },
                        { data: 'qtystock' },
                        { data: 'item_id' },
                        { data: 'qtya1' },
                        { data: 'qtya2' },
                        { data: 'qtya3' },
                        { data: 'qtya4' },
                        { data: 'qtybal' },
                        { data: 'qtymal' },
                        { data: 'item_id' }
                    ],
                    orderCellsTop: true,
                    fixedHeader: true,            
                });
                
                $('table.transItems').dataTable().fnDestroy();
                $('table.transItems').DataTable({
                    columnDefs: [
                        {
                            "targets": [hideEdit],
                            "visible": false,
                            "searchable": false
                        },
                        {   
                            "render": function(data, type, row, meta){
                                    return '<button style="zoom: 75%;" class="btn btn-primary bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                            },
                            "defaultContent": '',
                            "data": null,
                            "targets": [5]
                        }
                    ],
                    searching: false,
                    paging: false,
                    ordering: false,
                    info: false,
                    language:{
                        processing: "Loading...",
                        emptyTable: "No data available in table"
                    },
                    serverSide: true,
                    ajax:{
                        url: '/transItems',
                        data:{
                            request_number: $('#request_num_details').val(),
                        }
                    },
                    order: [],
                    columns: [
                        { data: 'prodcode' },
                        { data: 'item' },
                        { data: 'qty' },
                        { data: 'uom' },
                        { data: 'serial' },
                        { data: 'id' }
                    ]
                });

                $('table.incItems').dataTable().fnDestroy();
                $('table.incItems').DataTable({
                    columnDefs: [
                        {
                            "targets": [hideEdit1],
                            "visible": false,
                            "searchable": false
                        },
                        {
                            "render": function(data, type, row, meta){
                                    return '<button style="zoom: 75%;" class="btn btn-primary bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                            },
                            "defaultContent": '',
                            "data": null,
                            "targets": [5]
                        }
                    ],
                    searching: false,
                    paging: false,
                    ordering: false,
                    info: false,
                    language:{
                        processing: "Loading...",
                        emptyTable: "No data available in table"
                    },
                    serverSide: true,
                    ajax:{
                        url: '/incTransItems',
                        data:{
                            request_number: $('#request_num_details').val(),
                        }
                    },
                    order: [],
                    columns: [
                        { data: 'prodcode' },
                        { data: 'item' },
                        { data: 'qty' },
                        { data: 'uom' },
                        { data: 'serial' },
                        { data: 'id' }
                    ]
                });
            });
        },
        error: function(data){
            alert(data.responseText);
        }
    });
}

$('#stocktransferTable tbody').on('click', 'tr', function(){
    $('#detailsStockTransfer').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table = $('table.stocktransferTable').DataTable(); 
    var value = table.row(this).data();
    var requestStatus = value.status_id;
        $('#status_id_details').val(requestStatus);
    var req_type_id = value.req_type_id;
        $('#req_type_id_details').val(req_type_id);
    var req_date = value.date;
        req_date = moment(req_date).format('dddd, MMMM DD, YYYY, h:mm A');
        $('#reqdate_details').val(req_date);
    var need_date = value.needdate;
        maxDate = need_date;
        need_date = moment(need_date).format('dddd, MMMM DD, YYYY');
        $('#needdate_details').val(need_date);
    var req_num = value.req_num;
        $('#request_num_details').val(req_num);
    var req_by = value.req_by;
        $('#reqby_details').val(req_by);
    var status = value.status;
        $('#status_details').val(status);
    var prep_by = value.prep_by;
        $('#prep_by').val(prep_by);
        $('#prep_by1').val(prep_by);
        $('#reprep_by').val(prep_by);
    var sched = value.sched;
        sched = moment(sched).format('dddd, MMMM DD, YYYY');
        $('#sched').val(sched);
        $('#sched1').val(sched);
        $('#resched1').val(sched);
    var locfrom = value.locfrom;
        $('#locfrom_details').val(locfrom);
    var locto = value.locto;
        $('#locto_details').val(locto);
    var reason = value.reason;
        $('#reason_details').val(reason);
    var btnDel = '';
    var hideCol = '';
    var hideEdit = '';
    var hideEdit1 = '';

    $('.modal-body').html();
    $('#detailsStockTransfer').modal('show');
    if(locfrom == 5){
        hideCol = 12;
    }
    if(locfrom == 6){
        hideCol = 11;
    }
    if(value.user_id != $('#current_user').val()){
        $("#btnDelete").hide();
        btnDel = 13;
    }
    else{
        $("#btnDelete").show();
    }
    if(requestStatus != '6'){
        $("#btnApprove").hide();
        $("#btnDisapprove").hide();
    }
    if(requestStatus == '7'){
        $("#btnApprove").hide();
        $("#btnDisapprove").hide();
        $("#reason_label").show();
        $("#reason_details").show();
    }
    if(requestStatus == '1'|| requestStatus == '2'|| requestStatus == '3' || requestStatus == '4' || requestStatus == '5' || requestStatus >= 8){
        $("#btnDelete").hide();
        btnDel = 13;
    }
    if(requestStatus == '2' || requestStatus == '3' || requestStatus == '4' || requestStatus == '6' || requestStatus == '7' || requestStatus >= 8){
        $("#proceed_label").hide();
        $("#btnProceed").hide();
    }
    if(requestStatus == '2' || requestStatus == '5'){
        $("#processModal").show();
        $(".schedItemsModal").show();
        if($("#current_role").val() == '["viewer"]'){
            hideEdit = 5;
        }
    }
    if(requestStatus == '3' || requestStatus == '4'){
        $("#processModal").show();
        document.getElementById('modalheader').innerHTML = 'FOR RECEIVING ITEM DETAILS';
        $(".transitItemsModal").show();
        hideEdit = 5;
    }
    if(requestStatus == '8'){
        $("#processModal").show();
        document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
        $(".transitItemsModal").show();
        $(".btnReceive").hide();
        $(".receive_label").hide();
        hideEdit = 5;
    }
    if(requestStatus == '15'){
        $("#processModal").show();
        document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
        $(".btnReceive").hide();
        hideEdit = 5;
        $("#incItemsModal").show();
        $('.divResched').show();
        // $('#resched').attr('max', maxDate);
        hideEdit1 = 5;
    }
    if(requestStatus == '16'){
        $("#processModal").show();
        document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
        $(".btnReceive").hide();
        hideEdit = 5;
        $("#incItemsModal").show();
        $('.divResched1').show();
        hideEdit1 = 5;
    }
    if(requestStatus == '17'){
        $("#processModal").show();
        document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
        $(".btnReceive").hide();
        hideEdit = 5;
        $("#incItemsModal").show();
        $("#increceive_label").show();
        $('.divResched1').show();
        hideEdit1 = 5;
        $(".btnTransit").hide();
        $(".btnReceiveInc").show();
    }
    if(requestStatus == '1'|| requestStatus == '5' || requestStatus == '6'){
        var transferDetails = [5,6,7,8,9,10,hideCol,btnDel];
    }
    else{
        var transferDetails = [4,5,6,7,8,9,10,11,12,13];
    }

    $('table.transferDetails').dataTable().fnDestroy();
    $('table.transferDetails').DataTable({ 
        columnDefs: [
            {
                "targets": transferDetails,
                "visible": false,
                "searchable": false
            },
            {   
                "render": function(data, type, row, meta){
                        return '<button style="zoom: 75%;" class="btn btn-danger bp btndelItem" id="'+ meta.row +'">REMOVE</button>';
                },
                "defaultContent": '',
                "data": null,
                "targets": [13]
            }
        ],
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        language:{
            emptyTable: "No data available in table",
            processing: "Loading...",
        },
        serverSide: true,
        ajax:{
            url: '/transferDetails',
            data:{
                reqnum: req_num,
            },
            dataType: 'json',
            error: function(data){
                if(data.status == 401){
                    window.location.href = '/stocktransfer';
                }
                alert(data.responseText);
            },
        },
        order: [],
        columns: [
            { data: 'prodcode' },
            { data: 'item' },
            { data: 'uom' },
            { data: 'quantity' },
            { data: 'pending' },
            { data: 'qtystock' },
            { data: 'item_id' },
            { data: 'qtya1' },
            { data: 'qtya2' },
            { data: 'qtya3' },
            { data: 'qtya4' },
            { data: 'qtybal' },
            { data: 'qtymal' },
            { data: 'item_id' }
        ],
        orderCellsTop: true,
        fixedHeader: true,            
    });
    
    $('table.transItems').dataTable().fnDestroy();
    $('table.transItems').DataTable({
        columnDefs: [
            {
                "targets": [hideEdit],
                "visible": false,
                "searchable": false
            },
            {   
                "render": function(data, type, row, meta){
                        return '<button style="zoom: 75%;" class="btn btn-primary bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                },
                "defaultContent": '',
                "data": null,
                "targets": [5]
            }
        ],
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        language:{
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax:{
            url: '/transItems',
            data:{
                request_number: $('#request_num_details').val(),
            }
        },
        order: [],
        columns: [
            { data: 'prodcode' },
            { data: 'item' },
            { data: 'qty' },
            { data: 'uom' },
            { data: 'serial' },
            { data: 'id' }
        ]
    });

    $('table.incItems').dataTable().fnDestroy();
    $('table.incItems').DataTable({
        columnDefs: [
            {
                "targets": [hideEdit1],
                "visible": false,
                "searchable": false
            },
            {
                "render": function(data, type, row, meta){
                        return '<button style="zoom: 75%;" class="btn btn-primary bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                },
                "defaultContent": '',
                "data": null,
                "targets": [5]
            }
        ],
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        language:{
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax:{
            url: '/incTransItems',
            data:{
                request_number: $('#request_num_details').val(),
            }
        },
        order: [],
        columns: [
            { data: 'prodcode' },
            { data: 'item' },
            { data: 'qty' },
            { data: 'uom' },
            { data: 'serial' },
            { data: 'id' }
        ]
    });
});

var tblEdit;
$(document).on('click', '.btnEditSerial', function(){
    if($('#status_id_details').val() == '2' || $('#status_id_details').val() == '5'){
        tblEdit = 'table.transItems';
        var id = $(this).attr("id");
        var data = $(tblEdit).DataTable().row(id).data();
    }
    else if($('#status_id_details').val() == '3' || $('#status_id_details').val() == '4'){
        tblEdit = 'table.transItems';
        var id = $(this).attr("id");
        var data = $(tblEdit).DataTable().row(id).data();
    }
    else if($('#status_id_details').val() == '15' || $('#status_id_details').val() == '16' || $('#status_id_details').val() == '17'){
        tblEdit = 'table.incItems';
        var id = $(this).attr("id");
        var data = $(tblEdit).DataTable().row(id).data();
    }
    else{
        window.location.reload();
    }

    $('#x_id').val(data.id);
    $('#x_category').val(decodeHtml(data.category));
    $('#x_item').val(decodeHtml(data.item));
    $('#y_serial').val(data.serial);
    $('#x_serial').val(data.serial);

    $('#editSerialModal').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('.modal-body').html();
    $('#editSerialModal').modal('show');
});

$('#btnClear').on('click', function(){
    $('#x_serial').val('');
    $('#x_serial').focus();
});

$('#btnEdit').on('click', function(){
    var id = $('#x_id').val();
    var category = $('#x_category').val();
    var item = $('#x_item').val();
    var origserial = $('#y_serial').val().toUpperCase();
    var newserial = $.trim($('#x_serial').val()).toUpperCase();
    if(newserial == ''){
        newserial = 'N/A';
    }
    if(origserial == newserial){
        swal("NO CHANGES FOUND", "Item Serial is still the same!", "error");
        return false;
    }
    else{
        swal({
            title: "Confirm Serial: "+newserial+'?',
            text: "Click 'OK' button to submit; otherwise, click 'Cancel' button to recheck details.",
            icon: "warning",
            buttons: true,
            dangerMode: true
        })
        .then((willDelete) => {
            if(willDelete){
                scrollReset();
                $('#editSerialModal').hide();
                $('#editSerialModal').modal('dispose');
                $('#loading').show(); Spinner(); Spinner.show();
                $.ajax({
                    type:'post',
                    url: '/editTransSerial',
                    headers:{
                        'X-CSRF-TOKEN': $("#csrf").val()
                    },
                    data:{
                        id: id,
                        category: category,
                        item: item,
                        origserial: origserial,
                        newserial: newserial
                    },
                    success: function(data){
                        if(data == 'false'){
                            $('#loading').hide(); Spinner.hide();
                            swal({
                                title: "EDIT FAILED",
                                text: "ITEM SERIAL",
                                icon: "error",
                                timer: 2000
                            });
                            $(tblEdit).DataTable().ajax.reload();
                        }
                        else{
                            $('#loading').hide(); Spinner.hide();
                            swal({
                                title: "EDIT SUCCESS",
                                text: "ITEM SERIAL",
                                icon: "success",
                                timer: 2000
                            });
                            $(tblEdit).DataTable().ajax.reload();
                        }
                    },
                    error: function(data){
                        alert(data.responseText);
                    }
                });
            }
        });
    }
});

$(document).on('click', '.btndelItem', function(){
    var id = $(this).attr("id");
    var data = $('table.transferDetails').DataTable().row(id).data();
    $.ajax({
        type:'post',
        url: '/delTransItem',
        headers:{
            'X-CSRF-TOKEN': $("#csrf").val()
        },
        data:{
            req_num: $('#request_num_details').val(),
            item_id: data.item_id,
            item: decodeHtml(data.item),
            uom: data.uom,
            quantity: data.quantity
        },
        success: function(data){
            if(data.result == 'false'){
                $('#detailsStockTransfer').hide();
                swal("DELETE FAILED", "STOCK TRANSFER REQUEST", "error");
                setTimeout(function(){window.location.reload()}, 2000);
            }
            else{
                if(data.count == 0){
                    $('#detailsStockTransfer').hide();
                    swal("DELETE SUCCESS", "STOCK TRANSFER REQUEST", "success");
                    setTimeout(function(){window.location.reload()}, 2000);
                }
                else{
                    $('table.transferDetails').DataTable().ajax.reload();
                }
            }
        },
        error: function(data){
            alert(data.responseText);
        }
    });
});

$('#btnDelete').on('click', function(){
    swal({
        title: "DELETE STOCK TRANSFER REQUEST?",
        text: "You are about to DELETE your STOCK TRANSFER REQUEST!\n This will be permanently deleted from the system.",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if(willDelete){
            $.ajax({
                type:'post', 
                url:'/deleteTransfer',
                headers:{
                    'X-CSRF-TOKEN': $("#csrf").val()
                },
                data:{
                    'request_number': $('#request_num_details').val()
                },
                success: function(data){
                    if(data == 'true'){
                        $('#detailsStockTransfer').hide();
                        swal("DELETE SUCCESS", "STOCK TRANSFER REQUEST", "success");
                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                    }
                    else{
                        $('#detailsStockTransfer').hide();
                        swal("DELETE FAILED", "STOCK TRANSFER REQUEST", "error");
                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stocktransfer';
                    }
                    alert(data.responseText);
                }
            });
        }
    });   
});

$('#btnApprove').on('click', function(){
    swal({
        title: "APPROVE STOCK TRANSFER REQUEST?",
        text: "You are about to APPROVE this STOCK TRANSFER REQUEST!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if(willDelete){
            $.ajax({
                type:'post',
                url:'/approveTransfer',
                headers:{
                    'X-CSRF-TOKEN': $("#csrf").val()
                },
                data:{
                    'request_number': $('#request_num_details').val()
                },
                success: function(data){
                    if(data == 'true'){
                        $('#detailsStockTransfer').hide();
                        swal("APPROVE SUCCESS", "STOCK TRANSFER REQUEST", "success");
                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                    }
                    else{
                        $('#detailsStockTransfer').hide();
                        swal("APPROVE FAILED", "STOCK TRANSFER REQUEST", "error");
                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stocktransfer';
                    }
                    alert(data.responseText);
                }
            });
        }
    }); 
});

$('#btnDisapprove').on('click', function(){
    $('#reasonModal').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#reasonModal').modal('show');
});

$('#btnReason').on('click', function(){
    if(!$('#reason').val()){
        swal("REASON REQUIRED", "Please provide a reason for disapproving the request.", "error");
        return false;
    }
    else{
        swal({
            title: "DISAPPROVE STOCK TRANSFER REQUEST?",
            text: "You are about to DISAPPROVE this STOCK TRANSFER REQUEST!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                    type:'post',
                    url:'/disapproveTransfer',
                    async: false,
                    headers:{
                        'X-CSRF-TOKEN': $("#csrf").val()
                    },
                    data:{
                        'request_number': $('#request_num_details').val(),
                        'reason': $('#reason').val()
                    },
                    success: function(data){
                        if(data == 'true'){
                            scrollReset();
                            $('#reasonModal').hide();
                            $('#reasonModal').modal('dispose');
                            $('#detailsStockTransfer').hide();
                            $('#detailsStockTransfer').modal('dispose');
                            $('#loading').show(); Spinner(); Spinner.show();
                            $.ajax({
                                type:'post',
                                url:'/logTransDisapprove',
                                headers:{
                                    'X-CSRF-TOKEN': $("#csrf").val()
                                },
                                data:{
                                    'request_number': $('#request_num_details').val(),
                                    'reason': $('#reason').val()
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide(); Spinner.hide();
                                        swal("DISAPPROVE SUCCESS", "STOCK TRANSFER REQUEST", "success");
                                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                                    }
                                    else{
                                        return false;
                                    }
                                },
                                error: function(data){
                                    if(data.status == 401){
                                        window.location.href = '/stocktransfer';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else{
                            $('#reasonModal').hide();
                            $('#detailsStockTransfer').hide();
                            swal("DISAPPROVE FAILED", "STOCK TRANSFER REQUEST", "error");
                            setTimeout(function(){location.href="/stocktransfer"}, 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/stocktransfer';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
});

$('.btnTransit').on('click', function(){
    swal({
        title: "FOR RECEIVING?",
        text: "You are about to move these items FOR RECEIVING!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if(willDelete){
            $.ajax({
                type:'post',
                url:'/forReceiving',
                headers:{
                    'X-CSRF-TOKEN': $("#csrf").val()
                },
                data:{
                    'request_number': $('#request_num_details').val(),
                    'status': $('#status_id_details').val()
                },
                success: function(data){
                    if(data == 'true'){
                        $('#detailsStockTransfer').hide();
                        swal("FOR RECEIVING SUCCESS", "STOCK TRANSFER REQUEST", "success");
                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                    }
                    else{
                        $('#detailsStockTransfer').hide();
                        swal("FOR RECEIVING FAILED", "STOCK TRANSFER REQUEST", "error");
                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stocktransfer';
                    }
                    alert(data.responseText);
                }
            });
        }
    });    
});

$('#btnReschedule').on('click', function(){
    if(!$("#resched").val()){
        swal('Recheduled On is required!','Select within date range from today up to Date Needed.','error');
        return false;
    }
    else if($("#resched").val() < minDate){
        swal('Minimum Date is today!','Select within date range from today up to Date Needed.','error');
        return false;
    }
    // else if($("#resched").val() > maxDate){
    //     swal('Exceed Date Needed deadline!','Select within date range from today up to Date Needed.','error');
    //     return false;
    // }
    else{
        swal({
            title: "RESCHEDULE STOCK TRANSFER REQUEST?",
            text: "You are about to RESCHEDULE this STOCK TRANSFER REQUEST!",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                    type:'post',
                    url:'/reschedTransRequest',
                    headers:{
                        'X-CSRF-TOKEN': $("#csrf").val()
                    },
                    data:{
                        'request_number': $('#request_num_details').val(),
                        'resched': $("#resched").val()
                    },
                    success: function(data){
                        if(data == 'true'){
                            $('#detailsStockTransfer').hide();
                            swal("RESCHEDULE SUCCESS", "STOCK TRANSFER REQUEST", "success");
                            setTimeout(function(){location.href="/stocktransfer"}, 2000);
                        }
                        else{
                            $('#detailsStockTransfer').hide();
                            swal("RESCHEDULE FAILED", "STOCK TRANSFER REQUEST", "error");
                            setTimeout(function(){location.href="/stocktransfer"}, 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/stocktransfer';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
});

setInterval(checkReqType, 0);
function checkReqType(){
    if($('#detailsStockTransfer').is(':visible')){
        var status_id = $('#status_id_details').val();
        var locfrom = $('#locfrom_details').val();

        var table = $('#transferDetails').DataTable();
        var count = 0;

        if(status_id == '1'){
            $("#warning").show();
            $("#proceed_label").hide();
            $("#btnProceed").prop('disabled', false);
            $("#transferDetails *").prop('disabled', true);
            table.column(4).visible(false);
            var form_data  = $('#transferDetails').DataTable().rows().data();
            if(locfrom == '5'){
                $('#warning_span').html('BALINTAWAK');
                form_data.each(function(value, index){
                    if(parseInt(value.qtybal) < parseInt(value.quantity)){
                        $("#btnProceed").prop('disabled', true);
                        count++;
                        return false;
                    }
                });
            }
            if(locfrom == '6'){
                $('#warning_span').html('MALABON');
                form_data.each(function(value, index){
                    if(parseInt(value.qtymal) < parseInt(value.quantity)){
                        $("#btnProceed").prop('disabled', true);
                        count++;
                        return false;
                    }
                });
            }
            if(count == 0){
                $("#warning").hide();
            }
        }
    }
}

var items = [];
var item_count = 0;
// $('table.transferDetails').DataTable().on('select', function(){});
// $('.transferDetails tbody').on('click', 'tr', function(){
//     if($("#current_role").val() == '["viewer"]'){
//         return false;
//     }
//     var requestStatus = $('#status_id_details').val();
//     if(requestStatus == '2' || requestStatus == '3' || requestStatus == '4' || requestStatus == '6' || requestStatus == '7' || requestStatus >= 8){
//         return false;
//     }
//     var table = $('table.transferDetails').DataTable();
//     var data = table.row(this).data();
//     var pend = data.pending;
//     var item_id = data.item_id;
//     var bal = data.qtybal;
//     var mal = data.qtymal;

//     if($('#locfrom_details').val() == 5){
//         var stock = bal;
//     }
//     if($('#locfrom_details').val() == 6){
//         var stock = mal;
//     }

//     if(pend == 0){
//         swal('Item is fullfiled!','','success');
//     }
//     else if(stock == 0){
//         swal('Item out of stock!','','error');
//     }
//     else{
//         $(this).toggleClass('selected');
//         if(items.includes(item_id) == true){
//             items = items.filter(item => item !== item_id);
//         }
//         else {
//             items.push(item_id);
//         }
//     }
//     if(items.length == 0){
//         $('#btnProceed').prop('disabled', true);
//     }
//     else{
//         $('#btnProceed').prop('disabled', false);
//     }
// });

$('.table.transItems').DataTable().on('select', function(){});
$('.transItems tbody').on('click', 'tr', function(){
    if($("#current_role").val() == '["viewer"]'){
        return false;
    }
    var requestStatus = $('#status_id_details').val();
    if(requestStatus == '3' || requestStatus == '4'){
        var table = $('table.transItems').DataTable();
        var data = table.row(this).data();
        item_count = table.data().count();
    
        $(this).toggleClass('selected');
        if(items.includes(data.id) == true){
            items = items.filter(item => item !== data.id);
        }
        else {
            items.push(data.id);
        }
        if(items.length == 0){
            $('.btnReceive').prop('disabled', true);
        }
        else{
            $('.btnReceive').prop('disabled', false);
        }
    }
});

$('.table.incItems').DataTable().on('select', function(){});
$('.incItems tbody').on('click', 'tr', function(){
    if($("#current_role").val() == '["viewer"]'){
        return false;
    }
    var requestStatus = $('#status_id_details').val();
    if(requestStatus == '17'){
        var table = $('table.incItems').DataTable();
        var data = table.row(this).data();
        item_count = table.data().count();
    
        $(this).toggleClass('selected');
        if(items.includes(data.id) == true){
            items = items.filter(item => item !== data.id);
        }
        else {
            items.push(data.id);
        }
        if(items.length == 0){
            $('.btnReceive').prop('disabled', true);
        }
        else{
            $('.btnReceive').prop('disabled', false);
        }
    }
});

$("#btnProceed").unbind('click').click(function(){
    var j = 0;
    var reqnum = $('#request_num_details').val();
    var form_data  = $('#transferDetails').DataTable().rows().data();
    form_data.each(function(value, index){
        items.push(value.item_id);
    });
    $("#transferDetails *").prop('disabled',true);
    $("#proceed_label").hide();
    $("#btnProceed").hide();
    $("#reqContents").empty();
    $("#requestItems").slideDown();
    // $('#schedOn').attr('max', maxDate);
    for(var i=0; i < items.length; i++){
        $.ajax({ 
            type:'get', 
            url:'/stocktrans', 
            data:{
                'reqnum': reqnum,
                'location': $('#locfrom_details').val(),
                'item_id': items[i]
            }, 
            success: function(data){
                var transitem = $.map(data.data, function(value, index){ 
                    return [value];
                });

                transitem.forEach(value => {
                    if($('#locfrom_details').val() == 5){
                        var qtystock = value.qtybal;
                    }
                    if($('#locfrom_details').val() == 6){
                        var qtystock = value.qtymal;
                    }
                    if(qtystock <= value.pending){
                        var l = qtystock;
                    }
                    else{
                        var l = value.pending;
                    }
                    for(var k=0; k < l; k++){
                        var id = document.createElement("input");
                        id.setAttribute("id", "item_id"+j);
                        id.setAttribute("type", "hidden");
                        id.setAttribute("value", value.item_id);
                        var x = document.createElement("input");
                        x.setAttribute("id", "prodcode"+j);
                        x.setAttribute("type", "text");
                        x.setAttribute("class", "form-control");
                        x.setAttribute("style", "width: 250px; font-size: 12px; margin-bottom: 10px;");
                        x.setAttribute("value", value.prodcode);
                        var y = document.createElement("textarea");
                        y.setAttribute("id", "item"+j);
                        y.setAttribute("class", "form-control");
                        y.setAttribute("rows", "4");
                        y.setAttribute("style", "width: 250px; font-size: 12px; margin-left: 10px; margin-top: 52px; margin-bottom: 10px; resize: none;");
                        var qty = document.createElement("input");
                        qty.setAttribute("id", "qty"+j);
                        qty.setAttribute("type", "number");
                        qty.setAttribute("class", "form-control");
                        qty.setAttribute("style", "width: 100px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
                        qty.setAttribute("value", '1');
                        var uom = document.createElement("input");
                        uom.setAttribute("id", "uom"+j);
                        uom.setAttribute("type", "text");
                        uom.setAttribute("class", "form-control");
                        uom.setAttribute("style", "width: 100px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
                        uom.setAttribute("value", value.uom);
                        var serial = document.createElement("select");
                        serial.setAttribute("id", "serial"+j);
                        serial.setAttribute("class", "form-control serials");
                        serial.setAttribute("style", "width: 200px; font-size: 12px; margin-left: 10px; margin-bottom: 10px; margin-right: 400px;");
                        document.getElementById("reqContents").appendChild(id);
                        document.getElementById("reqContents").appendChild(x);
                        document.getElementById("reqContents").appendChild(y);
                        document.getElementById("reqContents").appendChild(qty);
                        document.getElementById("reqContents").appendChild(uom);
                        document.getElementById("reqContents").appendChild(serial);
                        $("#item"+j).html(value.item);
                        $("#prodcode"+j).prop('readonly', true);
                        $("#item"+j).prop('readonly', true);
                        $("#qty"+j).prop('readonly', true);
                        $("#uom"+j).prop('readonly', true);
                        $("#serial"+j).append("<option value='' selected>Select Serial</option>");
                        let vid = "#serial"+j;
                        $.ajax({
                            type:'get',
                            url:'/settransserials',
                            data:{
                                'item_id': value.item_id,
                                'location': $('#locfrom_details').val()
                            }, 
                            success: function(d){
                                var s = $.map(d, function(v){
                                    return [v];
                                });
            
                                s.forEach(v => {
                                    $(vid).append($('<option>', {
                                        value: v.id,
                                        text: v.serial
                                    }));
                                });
                                $(vid).chosen();
                            },
                            error: function(data){
                                if(data.status == 401){
                                    window.location.href = '/stocktransfer';
                                }
                                alert(data.responseText);
                            }
                        });
                        j++;
                    }
                });
                $('.serials').on('change', function(){
                    $('.serials option').show();
                    $('.serials').each(function(){
                        var $this = $(this);
                        $('.serials').not($this).find('option').each(function(){
                            if($(this).attr('value') == $this.val()){
                                $(this).hide();
                            }
                        });
                    });
                    $('select option:contains("Select Serial")').show();
                    $('.serials').trigger("chosen:updated");
                });
                for(var m=0; m < j; m++){
                    $('#serial'+m).on('change', function(){
                        if($('.serials').filter(function(){ return !!this.value; }).length == 0){
                            $('#btnSubmit').prop('disabled', true);
                        }
                        else{
                            $('#btnSubmit').prop('disabled', false);
                        }
                    });
                }
                setInterval(checkSerials, 0);
                function checkSerials(){
                    if($('.serials').filter(function(){ return !!this.value; }).length != j){
                        $('#btnSubmit').prop('disabled', true);
                        $('#schedwarning').show();
                    }
                    else{
                        $('#btnSubmit').prop('disabled', false);
                        $('#schedwarning').hide();
                    }
                }
                $("#btnSubmit").unbind('click').click(function(){
                    if(!$("#schedOn").val()){
                        swal('Scheduled On is required!','Select within date range from today up to Date Needed.','error');
                        return false;
                    }
                    else if($("#schedOn").val() < minDate){
                        swal('Minimum Date is today!','Select within date range from today up to Date Needed.','error');
                        return false;
                    }
                    // else if($("#schedOn").val() > maxDate){
                    //     swal('Exceed Date Needed deadline!','Select within date range from today up to Date Needed.','error');
                    //     return false;
                    // }
                    else{
                        swal({
                            title: "SCHEDULE STOCK TRANSFER REQUEST?",
                            text: "You are about to SCHEDULE this STOCK TRANSFER REQUEST!",
                            icon: "warning",
                            buttons: true,
                        })
                        .then((willDelete) => {
                            if(willDelete){
                                for(var n=0; n < j; n++){
                                    if($('#serial'+n).val() != ''){
                                        $.ajax({
                                            type:'post',
                                            url:'/transferItems',
                                            async: false,
                                            headers:{
                                                'X-CSRF-TOKEN': $("#csrf").val()
                                            },
                                            data:{
                                                'request_number': reqnum,
                                                'stock_id': $('#serial'+n).val(),
                                                'item_id': $('#item_id'+n).val(),
                                                'qty': $('#qty'+n).val(),
                                                'locto': $('#locto_details').val(),
                                            },
                                            success: function(data){
                                                if(data == 'true'){
                                                    return true;
                                                }
                                                else{
                                                    return false;
                                                }
                                            },
                                            error: function(data){
                                                if(data.status == 401){
                                                    window.location.href = '/stocktransfer';
                                                }
                                                alert(data.responseText);
                                            }
                                        });
                                    }
                                }
                                $.ajax({
                                    type:'post',
                                    url:'/logTransSched',
                                    headers:{
                                        'X-CSRF-TOKEN': $("#csrf").val()
                                    },
                                    data:{
                                        'request_number': reqnum,
                                        'schedOn': $('#schedOn').val()
                                    },
                                    success: function(data){
                                        if(data == 'true'){
                                            $('#detailsStockTransfer').hide();
                                            swal("SCHEDULED SUCCESS", "STOCK TRANSFER REQUEST", "success");
                                            setTimeout(function(){location.href="/stocktransfer"}, 2000);
                                        }
                                        else{
                                            $('#detailsStockTransfer').hide();
                                            swal("SCHEDULED FAILED", "STOCK TRANSFER REQUEST", "error");
                                            setTimeout(function(){location.href="/stocktransfer"}, 2000);
                                        }
                                    },
                                    error: function(data){
                                        if(data.status == 401){
                                            window.location.href = '/stocktransfer';
                                        }
                                        alert(data.responseText);
                                    }
                                });
                            }
                        }); 
                    }
                });
            },
            error: function(data){
                if(data.status == 401){
                    window.location.href = '/stocktransfer';
                }
                alert(data.responseText);
            }
        }); 
    }
});

$('#btnBack').on('click', function(){
    items = [];
    $("#transferDetails *").prop('disabled', false);
    $("#proceed_label").show();
    $('#btnSubmit').prop('disabled', true);
    $("#requestItems").hide();
    $("#schedOn").val('');
    $("#btnProceed").show();
});

$('.btnReceive').on('click', function(){
    var inc = 'false';
    var inctype = 'COMPLETE';
    if(items.length < item_count){
        inc = 'true';
        inctype = 'INCOMPLETE';
    }
    swal({
        title: "RECEIVE "+inctype+" STOCK TRANSFER REQUEST?",
        text: "You are about to RECEIVE this Stock Transfer Request!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if(willDelete){
            $.ajax({
                type: 'post',
                url: '/receiveTransfer',
                async: false,
                headers:{
                    'X-CSRF-TOKEN': $("#csrf").val()
                },
                data:{
                    'request_number': $('#request_num_details').val(),
                    'inc': inc
                },
                success: function(data){
                    if(data == 'true'){
                        for(var i=0; i < items.length; i++){
                            $.ajax({
                                type: 'post',
                                url: '/receiveTransItems',
                                async: false,
                                headers:{
                                    'X-CSRF-TOKEN': $("#csrf").val()
                                },
                                data:{
                                    'status': $('#status_id_details').val(),
                                    'id': items[i]
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        return true;
                                    }
                                    else{
                                        return false;
                                    }
                                },
                                error: function(data){
                                    if(data.status == 401){
                                        window.location.href = '/stocktransfer';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        scrollReset();
                        $('#detailsStockTransfer').hide();
                        $('#detailsStockTransfer').modal('dispose');
                        $('#loading').show(); Spinner(); Spinner.show();
                        $.ajax({
                            type: 'post',
                            url: '/logTransReceive',
                            headers:{
                                'X-CSRF-TOKEN': $("#csrf").val()
                            },
                            data:{
                                'request_number': $('#request_num_details').val(),
                                'status': $('#status_id_details').val(),
                                'inc': inc
                            },
                            success: function(data){
                                if(data == 'true'){
                                    $('#loading').hide(); Spinner.hide();
                                    swal("RECEIVED "+inctype, "STOCK TRANSFER REQUEST", "success");
                                    setTimeout(function(){location.href="/stocktransfer"}, 2000);
                                }
                                else{
                                    return false;
                                }
                            },
                            error: function(data){
                                if(data.status == 401){
                                    window.location.href = '/stocktransfer';
                                }
                                alert(data.responseText);
                            }
                        });
                    }
                    else{
                        $('#detailsStockTransfer').hide();
                        swal("RECEIVE FAILED", "STOCK TRANSFER REQUEST", "error");
                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stocktransfer';
                    }
                    alert(data.responseText);
                }
            });
        }
    });
});

$('.btnPrint').on('click', function(){
    window.location.href = '/printTransferRequest?request_number='+$('#request_num_details').val();
});