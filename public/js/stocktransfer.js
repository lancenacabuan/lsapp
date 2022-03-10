var minDate;
var maxDate;
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
});

const _MS_PER_DAY = 1000 * 60 * 60 * 24;
function dateDiffInDays(a, b) {
    const utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
    const utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());
    
    return Math.floor((utc2 - utc1) / _MS_PER_DAY);
}

function copyReqNum() {
    var copyText = document.getElementById("reqnum_details");
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

function generateReqNum() {
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

    for ( var i = 0; i < 3; i++ ) {
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
        success: function (data) {
            if (data == 'unique') {
                document.getElementById("reqnum").value = request_number;
            }
            else{
                generateReqNum();
            }
        },
        error: function (data) {
            if(data.status == 401) {
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

$('#locfrom').on('change', function(){
    $("#item").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
    $('#qty').val('');
    $('#qtystock').val('');
    $('#uom').val('');
    $('#transrequestDetails').show();
    var location_id = $(this).val();
    $.ajax({
        type:'get', 
        url:'/setcategory', 
        data:{
            'location_id': location_id
        }, 
        success:function(data) {
            $('#category').find('option').remove().end()
            $('#category').append($('<option value="" selected disabled>Select Category</option>'));
            var list = $.map(data, function(value, index) { 
                return [value];
            });

            list.forEach(value => {             
                $('#category').append($('<option>', {
                    value: value.category_id,
                    text: value.category
                }));
            });
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/stocktransfer';
            }
            alert(data.responseText);
        }
    });
});

$('#category').on('change', function(){
    $('#qty').prop('disabled', true);
    $("#qty").val('');
    $("#qtystock").val('');
    $("#uom").val('');
    var category_id = $(this).val();
    $.ajax({
        type:'get', 
        url:'/setitems', 
        data:{
            'category_id': category_id,
            'location_id': $('#locfrom').val()
        }, 
        success:function(data) {
            $('#item').find('option').remove().end()
            $('#item').append($('<option value="" selected disabled>Select Item</option>'));
            var list = $.map(data, function(value, index) { 
                return [value];
            });

            list.forEach(value => {             
                $('#item').append($('<option>', {
                    value: value.item_id,
                    text: value.item
                }));
            });
        },
        error: function (data) {
            if(data.status == 401) {
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
        type:'get', 
        url:'/settransuom', 
        data:{
            'item_id': item_id,
        }, 
        success:function(data) {
            $('#uom').val(data);
        },
        error: function (data) {
            if(data.status == 401) {
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
        success:function(data) {
            var table = document.getElementById('tblNewStockTransfer');
            var qtyminus = 0;
            if(table.rows.length > 1){
                for (var r = 1, n = table.rows.length; r < n; r++) {
                    for (var c = 0, m = table.rows[r].cells.length; c < m; c++) {
                        if(table.rows[r].cells[1].innerHTML == $("#item option:selected").text()){
                            qtyminus = table.rows[r].cells[2].innerHTML;
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
        error: function (data) {
            if(data.status == 401) {
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
    var qty = parseInt($("#qty").val());
    var qtystock = parseInt($("#qtystock").val());
    var uom = $("#uom").val();
    var markup = "<tr><td>" + category + "</td><td>" + item + "</td><td>" + qty + "</td><td>" + uom + "</td><td> <button type='button' class='delete-row btn-primary btn-xs bp'>REMOVE</button> </td></tr>";
    var ctr='false';
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
            for (i = 1; i < count; i++) {

                var objCells = table.rows.item(i).cells;

                if(item==objCells.item(1).innerHTML){
                    objCells.item(2).innerHTML = parseInt(objCells.item(2).innerHTML) + parseInt(qty);
                    ctr='true';
                    category = $("#category").val('');
                    item = $("#item").find('option').remove().end().append('<option value="">Select Item</option>').val()
                    qty = $("#qty").val('');
                    qtystock = $("#qtystock").val('');
                    uom = $("#uom").val('');
                    $('#qty').prop('disabled', true);
                    return false;
                }
                else {
                    ctr='false';
                }
            }
            if(ctr=='false')
            { $("#tblNewStockTransfer tbody").append(markup); }
            category = $("#category").val('');
            item = $("#item").find('option').remove().end().append('<option value="">Select Item</option>').val()
            qty = $("#qty").val('');
            qtystock = $("#qtystock").val('');
            uom = $("#uom").val('');
            $('#qty').prop('disabled', true);
            $('#tblNewStockTransfer').show();
            $('#divNewStockTransfer').toggle();
            $('#btnClose').show();
            $('#btnSave').show();
        }
    } 
});

$("#tblNewStockTransfer").on('click', '.delete-row', function(){
    $("#category").val('');
    $("#item").find('option').remove().end().append('<option value="">Select Item</option>').val()
    $("#qty").val('');
    $("#qtystock").val('');
    $("#uom").val('');
    $('#qty').prop('disabled', true);
    $(this).closest("tr").remove();
    if ($('#tblNewStockTransfer tbody').children().length==0) {
        $('#tblNewStockTransfer').hide();
        $('#divNewStockTransfer').removeClass();
        $('#btnClose').hide();
        $('#btnSave').hide();
        $('#locfrom').prop('disabled', false);
    }
});

$(document).on('click', '#btnSave', function(){
    if($('#needdate').val() && $('#locfrom').val() && $('#locto').val())
    {
        if($("#needdate").val() < minDate){
            swal('Minimum Date is today!','Select within date range from today onwards.','error');
            return false;
        }
        else{
            swal({
                title: "SUBMIT STOCK TRANSFER REQUEST?",
                text: "You are about to SUBMIT this STOCK TRANSFER REQUEST!",
                icon: "warning",
                buttons: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        type:'post',
                        url:'/saveTransReqNum',
                        headers: {
                            'X-CSRF-TOKEN': $("#csrf").val(),
                        },
                        data:{
                            'request_number': $('#reqnum').val(),
                            'needdate': $('#needdate').val(),
                            'locfrom': $('#locfrom').val(),
                            'locto': $('#locto').val(),
                        },
                        success: function (data){
                            if(data == 'true'){
                                var myTable = $('#tblNewStockTransfer').DataTable();
                                var form_data  = myTable.rows().data();
                                $.each( form_data, function( key, value ) {
                                    $.ajax({
                                        type:'post',
                                        url:'/saveTransRequest',
                                        headers: {
                                            'X-CSRF-TOKEN': $("#csrf").val(),
                                        },
                                        data:{
                                            'request_number': $('#reqnum').val(),
                                            'category': value[0],
                                            'item': value[1],
                                            'quantity': value[2]
                                        },
                                        success: function (data){
                                            if(data == 'true'){
                                                return true;
                                            }
                                            else{
                                                return false;
                                            }
                                        },
                                        error: function (data) {
                                            if(data.status == 401) {
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
                                    headers: {
                                        'X-CSRF-TOKEN': $("#csrf").val()
                                    },
                                    data:{
                                        'request_number': $('#reqnum').val(),
                                    },
                                    success: function (data){
                                        if(data == 'true'){
                                            $('#loading').hide(); Spinner.hide();
                                            sweetAlert("SUBMIT SUCCESS", "STOCK TRANSFER REQUEST", "success");
                                            setTimeout(function(){location.href="/stocktransfer"}, 2000);
                                        }
                                        else{
                                            return false;
                                        }
                                    },
                                    error: function (data) {
                                        if(data.status == 401) {
                                            window.location.href = '/stocktransfer';
                                        }
                                        alert(data.responseText);
                                    }
                                });
                            }
                            else{
                                $('#newStockTransfer').hide();
                                sweetAlert("SUBMIT FAILED", "STOCK TRANSFER REQUEST", "error");
                                setTimeout(function(){location.href="/stocktransfer"}, 2000);
                            }
                        },
                        error: function (data){
                            if(data.status == 401) {
                                window.location.href = '/stocktransfer';
                            }
                            alert(data.responseText);
                        }
                    });
                }
            });
        }
    }
    else{
        var required_fields = [];
        var required_list;
        if(!$('#needdate').val()){
            required_fields.push('*Date Needed');
        }
        if(!$('#locfrom').val()){
            required_fields.push('*FROM Location');
        }
        if(!$('#locto').val()){
            required_fields.push('*TO New Location');
        }
        required_list = required_fields.join("\r\n");
        swal('Fill up all required fields!', required_list, 'error');
        return false;
    }   
});

$(document).on('click', '#close', function(){
    window.location.href = '/stocktransfer';
});

$(document).on('click', '#btnClose', function(){
    window.location.href = '/stocktransfer';
});

$(document).on('click', '#modalClose', function(){
    window.location.href = '/stocktransfer';
});

$('table.stocktransferTable').dataTable().fnDestroy();
$('#loading').show(); Spinner(); Spinner.show();
$('table.stocktransferTable').DataTable({ 
    columnDefs: [
        {
            "targets": [1],
            "render": $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'MMM. D, YYYY')
        },
        {
            "targets": [7,8,9,10,11,12,13],
            "visible": false,
            "searchable": false
        }
    ],
    language: {
        processing: "Loading...",
        emptyTable: "No data available in table"
    },
    serverSide: true,
    ajax: {
        url: '/transfer_data',
    },
    columns: [
        {
            data: 'needdate',
            "render": function(data, type, row){
                if(row.status_id == '7' || row.status_id == '8'){
                    return "<span class='d-none'>"+row.needdate+"</span>"+moment(row.needdate).format('MMM. D, YYYY');
                }
                else{
                    var a = new Date(minDate);
                    var b = new Date(row.needdate);
                    var difference = dateDiffInDays(a, b);
                    if(difference >= 0 && difference <= 3){
                        return "<span class='d-none'>"+row.needdate+"</span><span style='color: Blue; font-weight: bold;'>"+moment(row.needdate).format('MMM. D, YYYY')+'&nbsp;&nbsp;&nbsp;'+"<i style='zoom: 150%; color: blue;' class='fa fa-exclamation-triangle'></i></span>";
                    }
                    else if(difference < 0){
                        return "<span class='d-none'>"+row.needdate+"</span><span style='color: Red; font-weight: bold;'>"+moment(row.needdate).format('MMM. D, YYYY')+'&nbsp;&nbsp;&nbsp;'+"<i style='zoom: 150%; color: red;' class='fa fa-exclamation-circle'></i></span>";
                    }
                    else{
                        return "<span class='d-none'>"+row.needdate+"</span>"+moment(row.needdate).format('MMM. D, YYYY');
                    }
                }
            }
        },
        { data: 'date'},
        { data: 'req_num'},
        { data: 'req_by'},
        { data: 'location_from'},
        { data: 'location_to'},
        {
            data: 'status',
            "render": function(data, type, row){
                if(row.status_id == '6'){
                    return "<span style='color: DarkSlateGray; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '1'){
                    return "<span style='color: Red; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '2' || row.status_id == '5'){
                    return "<span style='color: Indigo; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '3' || row.status_id == '4'){
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
        { data: 'status_id'},
        { data: 'locfrom'},
        { data: 'locto'},
        { data: 'prep_by'},
        { data: 'sched'},
        { data: 'user_id'},
        { data: 'reason'}
    ],
    initComplete: function (){
        $('#loading').hide(); Spinner.hide();
    }
});

if(window.location.href != 'https://lance.idsi.com.ph/stocktransfer'){
    url = window.location.search;
    reqnum = url.replace('?request_number=', '');
    $.ajax({
        url: '/transModal',
        headers: {
            'X-CSRF-TOKEN': $("#csrf").val(),
        },
        dataType: 'json',
        type: 'get',
        data: {
            request_number: reqnum,
        },
        success: function(data) {
            $('#detailsStockTransfer').modal({
                backdrop: 'static',
                keyboard: false
            });
            var transitem = $.map(data.data, function(value, index) { 
                return [value];
            });
            transitem.forEach(value => {
                var requestStatus = value.status_id;
                var req_date = value.date;
                    req_date = moment(req_date).format('dddd, MMMM D, YYYY, h:mm A');
                    $('#reqdate_details').val(req_date);
                var need_date = value.needdate;
                    maxDate = need_date;
                    need_date = moment(need_date).format('dddd, MMMM D, YYYY');
                    $('#needdate_details').val(need_date);
                var req_num = value.req_num;
                    $('#reqnum_details').val(req_num);
                var req_by = value.req_by;
                    $('#reqby_details').val(req_by);
                var status = value.status;
                    $('#status_details').val(status);
                var prep_by = value.prep_by;
                    $('#prep_by').val(prep_by);
                    $('#prep_by1').val(prep_by);
                var sched = value.sched;
                    sched = moment(sched).format('dddd, MMMM D, YYYY');
                    $('#sched').val(sched);
                    $('#sched1').val(sched);
                var locfrom = value.locfrom;
                    $('#locfrom_details').val(locfrom);
                var locto = value.locto;
                    $('#locto_details').val(locto);
                var reason = value.reason;
                    $('#reason_details').val(reason);
                var btnDel = '';
                var hideCol = '';
            
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
                    $("#btnApprove").show();
                    $("#btnDisapprove").hide();
                    $("#reason_label").show();
                    $("#reason_details").show();
                }
                if(requestStatus == '1'|| requestStatus == '2'|| requestStatus == '3' || requestStatus == '4' || requestStatus == '5' || requestStatus == '8'){
                    $("#btnDelete").hide();
                    btnDel = 13;
                }
                if(requestStatus == '2' || requestStatus == '3' || requestStatus == '4' || requestStatus == '6' || requestStatus == '7' || requestStatus == '8'){
                    $("#btnProceed").hide();
                }
                if(requestStatus == '2' || requestStatus == '5'){
                    $("#schedItemsModal").show();
                }
                if(requestStatus == '3' || requestStatus == '4'){
                    $("#transitItemsModal").show();
                }
                if(requestStatus == '8'){
                    $("#transitItemsModal").show();
                    $("#btnReceive").hide();
                    document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
                }
            
                $('table.transferDetails').dataTable().fnDestroy();
                $('table.transferDetails').DataTable({ 
                    columnDefs: [
                        {
                            "targets": [5,6,7,8,9,10,hideCol,btnDel],
                            "visible": false,
                            "searchable": false
                        },
                        {   
                            "render": function (data, type, row, meta) {
                                    return '<button class="btn-primary bp btndelItem" id="'+ meta.row +'">REMOVE</button>';
                            },
                            "defaultContent": '',
                            "data": null,
                            "targets": [13]
                        }
                    ],
                    paging: false,
                    ordering: false,
                    info: false,
                    language: {
                        emptyTable: "No data available in table",
                        processing: "Loading...",
                    },
                    serverSide: true,
                    ajax: {
                        url: '/transferDetails',
                        data: {
                            reqnum: req_num,
                        },
                        dataType: 'json',
                        error: function(data) {
                            if(data.status == 401) {
                                window.location.href = '/stocktransfer';
                            }
                            alert(data.responseText);
                        },
                    },
                    columns: [
                        { data: 'category'},
                        { data: 'item'},
                        { data: 'uom'},
                        { data: 'quantity'},
                        { data: 'pending'},
                        { data: 'qtystock'},
                        { data: 'item_id'},
                        { data: 'qtya1'},
                        { data: 'qtya2'},
                        { data: 'qtya3'},
                        { data: 'qtya4'},
                        { data: 'qtybal'},
                        { data: 'qtymal'},
                        { data: 'item_id'}
            
                    ],
                    orderCellsTop: true,
                    fixedHeader: true,            
                });
                
                $('table.transItems').DataTable({
                    paging: false,
                    ordering: false,
                    info: false,
                    language: {
                        processing: "Loading...",
                        emptyTable: "No data available in table"
                    },
                    serverSide: true,
                    ajax: {
                        url: '/transItems',
                        data: {
                            request_number: $('#reqnum_details').val(),
                        }
                    },
                    columns: [
                        { data: 'category'},
                        { data: 'item'},
                        { data: 'qty'},
                        { data: 'uom'},
                        { data: 'serial'}
                    ]
                });
            });
        },
        error: function(data) {
            alert(data.responseText);
        }
    });
}

$('#stocktransferTable tbody').on('click', 'tr', function(){
    $('#detailsStockTransfer').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table =  $('table.stocktransferTable').DataTable(); 
    var data = table.row(this).data();
    var requestStatus = data.status_id;
    var req_date = data.date;
        req_date = moment(req_date).format('dddd, MMMM D, YYYY, h:mm A');
        $('#reqdate_details').val(req_date);
    var need_date = data.needdate;
        maxDate = need_date;
        need_date = moment(need_date).format('dddd, MMMM D, YYYY');
        $('#needdate_details').val(need_date);
    var req_num = data.req_num;
        $('#reqnum_details').val(req_num);
    var req_by = data.req_by;
        $('#reqby_details').val(req_by);
    var status = data.status;
        $('#status_details').val(status);
    var prep_by = data.prep_by;
        $('#prep_by').val(prep_by);
        $('#prep_by1').val(prep_by);
    var sched = data.sched;
        sched = moment(sched).format('dddd, MMMM D, YYYY');
        $('#sched').val(sched);
        $('#sched1').val(sched);
    var locfrom = data.locfrom;
        $('#locfrom_details').val(locfrom);
    var locto = data.locto;
        $('#locto_details').val(locto);
    var reason = data.reason;
        $('#reason_details').val(reason);
    var btnDel = '';
    var hideCol = '';

    $('.modal-body').html();
    $('#detailsStockTransfer').modal('show');
    if(locfrom == 5){
        hideCol = 12;
    }
    if(locfrom == 6){
        hideCol = 11;
    }
    if(data.user_id != $('#current_user').val()){
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
        $("#btnApprove").show();
        $("#btnDisapprove").hide();
        $("#reason_label").show();
        $("#reason_details").show();
    }
    if(requestStatus == '1'|| requestStatus == '2'|| requestStatus == '3' || requestStatus == '4' || requestStatus == '5' || requestStatus == '8'){
        $("#btnDelete").hide();
        btnDel = 13;
    }
    if(requestStatus == '2' || requestStatus == '3' || requestStatus == '4' || requestStatus == '6' || requestStatus == '7' || requestStatus == '8'){
        $("#btnProceed").hide();
    }
    if(requestStatus == '2' || requestStatus == '5'){
        $("#schedItemsModal").show();
    }
    if(requestStatus == '3' || requestStatus == '4'){
        $("#transitItemsModal").show();
    }
    if(requestStatus == '8'){
        $("#transitItemsModal").show();
        $("#btnReceive").hide();
        document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
    }

    $('table.transferDetails').dataTable().fnDestroy();
    $('table.transferDetails').DataTable({ 
        columnDefs: [
            {
                "targets": [5,6,7,8,9,10,hideCol,btnDel],
                "visible": false,
                "searchable": false
            },
            {   
                "render": function (data, type, row, meta) {
                        return '<button class="btn-primary bp btndelItem" id="'+ meta.row +'">REMOVE</button>';
                },
                "defaultContent": '',
                "data": null,
                "targets": [13]
            }
        ],
        paging: false,
        ordering: false,
        info: false,
        language: {
            emptyTable: "No data available in table",
            processing: "Loading...",
        },
        serverSide: true,
        ajax: {
            url: '/transferDetails',
            data: {
                reqnum: req_num,
            },
            dataType: 'json',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/stocktransfer';
                }
                alert(data.responseText);
            },
        },
        columns: [
            { data: 'category'},
            { data: 'item'},
            { data: 'uom'},
            { data: 'quantity'},
            { data: 'pending'},
            { data: 'qtystock'},
            { data: 'item_id'},
            { data: 'qtya1'},
            { data: 'qtya2'},
            { data: 'qtya3'},
            { data: 'qtya4'},
            { data: 'qtybal'},
            { data: 'qtymal'},
            { data: 'item_id'}

        ],
        orderCellsTop: true,
        fixedHeader: true,            
    });
    
    $('table.transItems').DataTable({
        paging: false,
        ordering: false,
        info: false,
        language: {
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax: {
            url: '/transItems',
            data: {
                request_number: $('#reqnum_details').val(),
            }
        },
        columns: [
            { data: 'category'},
            { data: 'item'},
            { data: 'qty'},
            { data: 'uom'},
            { data: 'serial'}
        ]
    });
});

$(document).on('click', '.btndelItem', function() {
    var id = $(this).attr("id");
    var data = $('table.transferDetails').DataTable().row(id).data();

    $.ajax({
        url: '/delTransItem',
        headers: {
            'X-CSRF-TOKEN': $("#csrf").val(),
        },
        dataType: 'json',
        type: 'DELETE',
        data: {
            req_num: $('#reqnum_details').val(),
            item_id: data.item_id
        },
        success: function(data) {
            if(data.result == 'false'){
                $('#detailsStockTransfer').hide();
                sweetAlert("DELETE FAILED", "STOCK TRANSFER REQUEST", "error");
                setTimeout(function(){window.location.reload()}, 2000);
            }
            else{
                if(data.count == 0){
                    $('#detailsStockTransfer').hide();
                    sweetAlert("DELETE SUCCESS", "STOCK TRANSFER REQUEST", "success");
                    setTimeout(function(){window.location.reload()}, 2000);
                }
                else{
                    $('table.transferDetails').DataTable().ajax.reload();
                }
            }
        },
        error: function(data) {
            alert(data.responseText);
        }
    });
});

$(document).on('click', '#btnDelete', function(){
    swal({
        title: "DELETE STOCK TRANSFER REQUEST?",
        text: "You are about to DELETE your STOCK TRANSFER REQUEST!\n This will be permanently deleted from the system.",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {     
            $.ajax({
                type:'get', 
                url:'/deleteTransfer', 
                data:{
                    'request_number': $('#reqnum_details').val()
                },
                success: function (data){
                    if(data == 'true'){
                        $('#detailsStockTransfer').hide();
                        sweetAlert("DELETE SUCCESS", "STOCK TRANSFER REQUEST", "success");
                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                    }
                    else{
                        $('#detailsStockTransfer').hide();
                        sweetAlert("DELETE FAILED", "STOCK TRANSFER REQUEST", "error");
                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                    }
                },
                error: function (data) {
                    if(data.status == 401) {
                        window.location.href = '/stocktransfer';
                    }
                    alert(data.responseText);
                }
            });
        }
    });   
});

$(document).on('click', '#btnApprove', function(){
    swal({
        title: "APPROVE STOCK TRANSFER REQUEST?",
        text: "You are about to APPROVE this STOCK TRANSFER REQUEST!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                type:'get',
                url:'/approveTransfer',
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                        },
                data:{
                    'request_number': $('#reqnum_details').val()
                },
                success: function (data){
                    if(data == 'true'){
                        $('#detailsStockTransfer').hide();
                        sweetAlert("APPROVE SUCCESS", "STOCK TRANSFER REQUEST", "success");
                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                    }
                    else{
                        $('#detailsStockTransfer').hide();
                        sweetAlert("APPROVE FAILED", "STOCK TRANSFER REQUEST", "error");
                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                    }
                },
                error: function (data) {
                    if(data.status == 401) {
                        window.location.href = '/stocktransfer';
                    }
                    alert(data.responseText);
                }
            });
        }
    }); 
});

$(document).on('click', '#btnDisapprove', function() {
    $('#reasonModal').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#reasonModal').modal('show');
});

$(document).on('click', '#btnReason', function(){
    if(!$('#reason').val()){
        sweetAlert("REASON REQUIRED!", "Please provide a reason for disapproving the request.", "error");
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
            if (willDelete) {
                $.ajax({
                    type:'get',
                    url:'/disapproveTransfer',
                    headers: {
                        'X-CSRF-TOKEN': $("#csrf").val(),
                            },
                    data:{
                        'request_number': $('#reqnum_details').val(),
                        'reason': $('#reason').val()
                    },
                    success: function (data){
                        if(data == 'true'){
                            scrollReset();
                            $('#reasonModal').hide();
                            $('#reasonModal').modal('dispose');
                            $('#detailsStockTransfer').hide();
                            $('#detailsStockTransfer').modal('dispose');
                            $('#loading').show(); Spinner(); Spinner.show();
                            $.ajax({
                                type:'get',
                                url:'/logTransDisapprove',
                                headers: {
                                    'X-CSRF-TOKEN': $("#csrf").val(),
                                        },
                                data:{
                                    'request_number': $('#reqnum_details').val(),
                                    'reason': $('#reason').val()
                                },
                                success: function (data){
                                    if(data == 'true'){
                                        $('#loading').hide(); Spinner.hide();
                                        sweetAlert("DISAPPROVE SUCCESS", "STOCK TRANSFER REQUEST", "success");
                                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                                    }
                                    else{
                                        return false;
                                    }
                                },
                                error: function (data) {
                                    if(data.status == 401) {
                                        window.location.href = '/stocktransfer';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else{
                            $('#reasonModal').hide();
                            $('#detailsStockTransfer').hide();
                            sweetAlert("DISAPPROVE FAILED", "STOCK TRANSFER REQUEST", "error");
                            setTimeout(function(){location.href="/stocktransfer"}, 2000);
                        }
                    },
                    error: function (data) {
                        if(data.status == 401) {
                            window.location.href = '/stocktransfer';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
});

$(document).on('click', '#btnTransit', function(){
    swal({
        title: "FOR RECEIVING?",
        text: "You are about to move these items FOR RECEIVING!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                type:'get',
                url:'/forReceiving',
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                        },
                data:{
                    'request_number': $('#reqnum_details').val()
                },
                success: function (data){
                    if(data == 'true'){
                        $('#detailsStockTransfer').hide();
                        sweetAlert("FOR RECEIVING SUCCESS", "STOCK TRANSFER REQUEST", "success");
                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                    }
                    else{
                        $('#detailsStockTransfer').hide();
                        sweetAlert("FOR RECEIVING FAILED", "STOCK TRANSFER REQUEST", "error");
                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                    }
                },
                error: function (data) {
                    if(data.status == 401) {
                        window.location.href = '/stocktransfer';
                    }
                    alert(data.responseText);
                }
            });
        }
    });    
});

$(document).on('click', '#btnReceive', function(){
    swal({
        title: "RECEIVE STOCK TRANSFER REQUEST?",
        text: "You are about to RECEIVE this Stock Transfer Request!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                type:'get',
                url:'/receiveTransfer',
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                        },
                data:{
                    'request_number': $('#reqnum_details').val()
                },
                success: function (data){
                    if(data == 'true'){
                        scrollReset();
                        $('#detailsStockTransfer').hide();
                        $('#detailsStockTransfer').modal('dispose');
                        $('#loading').show(); Spinner(); Spinner.show();
                        $.ajax({
                            type:'get',
                            url:'/logTransReceive',
                            headers: {
                                'X-CSRF-TOKEN': $("#csrf").val(),
                                    },
                            data:{
                                'request_number': $('#reqnum_details').val()
                            },
                            success: function (data){
                                if(data == 'true'){
                                    $('#loading').hide(); Spinner.hide();
                                    sweetAlert("RECEIVE SUCCESS", "STOCK TRANSFER REQUEST", "success");
                                    setTimeout(function(){location.href="/stocktransfer"}, 2000);
                                }
                                else{
                                    return false;
                                }
                            },
                            error: function (data) {
                                if(data.status == 401) {
                                    window.location.href = '/stocktransfer';
                                }
                                alert(data.responseText);
                            }
                        });
                    }
                    else{
                        $('#detailsStockTransfer').hide();
                        sweetAlert("RECEIVE FAILED", "STOCK TRANSFER REQUEST", "error");
                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                    }
                },
                error: function (data) {
                    if(data.status == 401) {
                        window.location.href = '/stocktransfer';
                    }
                    alert(data.responseText);
                }
            });
        }
    });    
});

var items = [];
$('table.transferDetails').DataTable().on('select', function(){});
$('.transferDetails tbody').on('click', 'tr', function(){
    var table =  $('table.transferDetails').DataTable();
    var data = table.row(this).data();
    var pend = data.pending;
    var item_id = data.item_id;
    var bal = data.qtybal;
    var mal = data.qtymal;

    if($('#locfrom_details').val() == 5){
        var stock = bal;
    }
    if($('#locfrom_details').val() == 6){
        var stock = mal;
    }

    if(pend == 0){
        swal('Item is fullfiled!','','success');
    }
    else if(stock == 0){
        swal('Item out of stock!','','error');
    }
    else{
        $(this).toggleClass('selected');
        if(items.includes(item_id) == true){
            items = items.filter(item => item !== item_id);
        }
        else {
            items.push(item_id);
        }
    }
    if(items.length == 0){
        $('#btnProceed').prop('disabled', true);
    }
    else{
        $('#btnProceed').prop('disabled', false);
    }
});

$("#btnProceed").unbind('click').click(function(){
    var reqnum = $('#reqnum_details').val();
    var j = 0;
    $("#transferDetails *").prop('disabled',true);
    $("#btnProceed").hide();
    $("#requestItems").slideDown();
    $('#schedOn').attr('max', maxDate);
    for(var i=0; i < items.length; i++){
        $.ajax({ 
            type:'get', 
            url:'/stocktrans', 
            data:{
                'reqnum': reqnum,
                'location': $('#locfrom_details').val(),
                'item_id': items[i]
            }, 
            success:function(data) {
                var transitem = $.map(data.data, function(value, index) { 
                    return [value];
                });

                transitem.forEach(value => {
                    if($('#locfrom_details').val() == 5){
                        var qtystock = value.qtybal;
                        var ser = value.serialbal;
                        var selOption = "<option value='5' selected>BALINTAWAK</option>";
                    }
                    if($('#locfrom_details').val() == 6){
                        var qtystock = value.qtymal;
                        var ser = value.serialmal;
                        var selOption = "<option value='6' selected>MALABON</option>";
                    }
                    if(qtystock <= value.pending){
                        var l = qtystock;
                    }
                    else{
                        var l = value.pending;
                    }
                    if(ser == '' || ser == ' ' || ser == null || ser == 'N\\\\A' || ser == 'N\\\/A' || ser == 'n\\\/a' || ser == 'NONE' || ser == 'None' || ser == 'none'){
                        $('#btnSubmit').prop('disabled', false);
                        var id = document.createElement("input");
                        id.setAttribute("id", "item_id"+j);
                        id.setAttribute("type", "hidden");
                        id.setAttribute("value", value.item_id);
                        var x = document.createElement("input");
                        x.setAttribute("id", "category"+j);
                        x.setAttribute("type", "text");
                        x.setAttribute("class", "form-control");
                        x.setAttribute("style", "width: 250px; font-size: 12px; margin-bottom: 10px;");
                        x.setAttribute("value", value.category);
                        var y = document.createElement("textarea");
                        y.setAttribute("id", "item"+j);
                        y.setAttribute("class", "form-control");
                        y.setAttribute("rows", "4");
                        y.setAttribute("style", "width: 250px; font-size: 12px; margin-left: 10px; margin-top: 52px; margin-bottom: 10px; resize: none;");
                        var z = document.createElement("select");
                        z.setAttribute("id", "location"+j);
                        z.setAttribute("class", "form-control");
                        z.setAttribute("style", "width: 150px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
                        var qty = document.createElement("input");
                        qty.setAttribute("id", "qty"+j);
                        qty.setAttribute("type", "number");
                        qty.setAttribute("class", "form-control qty");
                        qty.setAttribute("style", "width: 100px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
                        qty.setAttribute("max", l);
                        qty.setAttribute("min", '1');
                        qty.setAttribute("value", l);
                        var uom = document.createElement("input");
                        uom.setAttribute("id", "uom"+j);
                        uom.setAttribute("type", "text");
                        uom.setAttribute("class", "form-control");
                        uom.setAttribute("style", "width: 100px; font-size: 12px; margin-left: 10px; margin-bottom: 10px; margin-right: 300px;");
                        uom.setAttribute("value", value.uom);
                        document.getElementById("reqContents").appendChild(id);
                        document.getElementById("reqContents").appendChild(x);
                        document.getElementById("reqContents").appendChild(y);
                        document.getElementById("reqContents").appendChild(qty);
                        document.getElementById("reqContents").appendChild(uom);
                        // document.getElementById("reqContents").appendChild(z);
                        $("#item"+j).html(value.item); 
                        $("#category"+j).prop('readonly', true);
                        $("#item"+j).prop('readonly', true);
                        $("#uom"+j).prop('readonly', true);
                        $("#location"+j).prop('disabled', true);
                        $("#location"+j).append(selOption);
                        j++;
                    }
                    else{
                        for(var k=0; k < l; k++){
                            var id = document.createElement("input");
                            id.setAttribute("id", "item_id"+j);
                            id.setAttribute("type", "hidden");
                            id.setAttribute("value", value.item_id);
                            var x = document.createElement("input");
                            x.setAttribute("id", "category"+j);
                            x.setAttribute("type", "text");
                            x.setAttribute("class", "form-control");
                            x.setAttribute("style", "width: 250px; font-size: 12px; margin-bottom: 10px;");
                            x.setAttribute("value", value.category);
                            var y = document.createElement("textarea");
                            y.setAttribute("id", "item"+j);
                            y.setAttribute("class", "form-control");
                            y.setAttribute("rows", "4");
                            y.setAttribute("style", "width: 250px; font-size: 12px; margin-left: 10px; margin-top: 52px; margin-bottom: 10px; resize: none;");
                            var z = document.createElement("select");
                            z.setAttribute("id", "location"+j);
                            z.setAttribute("class", "form-control");
                            z.setAttribute("style", "width: 100px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
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
                            // document.getElementById("reqContents").appendChild(z);
                            $("#item"+j).html(value.item); 
                            $("#category"+j).prop('readonly', true);
                            $("#item"+j).prop('readonly', true);
                            $("#qty"+j).prop('readonly', true);
                            $("#uom"+j).prop('readonly', true);
                            $("#location"+j).prop('disabled', true);
                            $("#location"+j).append(selOption);
                            $("#serial"+j).append("<option value='' selected>Select Serial</option>");
                            let vid = "#serial"+j;
                            $.ajax({ 
                                type:'get', 
                                url:'/settransserials', 
                                data:{
                                    'item_id': value.item_id,
                                    'location': $('#locfrom_details').val()
                                }, 
                                success:function(d) {   
                                    var s = $.map(d, function(v) { 
                                        return [v];
                                    });
                
                                    s.forEach(v => {             
                                        $(vid).append($('<option>', {
                                            value: v.serial,
                                            text: v.serial
                                        }));
                                    });
                                    $(vid).chosen();
                                },
                                error: function (data) {
                                    if(data.status == 401) {
                                        window.location.href = '/stocktransfer';
                                    }
                                    alert(data.responseText);
                                }
                            });
                            j++;
                        }
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
                        if($('.serials').filter(function() { return !!this.value; }).length == 0) {
                            $('#btnSubmit').prop('disabled', true);
                        }
                        else{
                            $('#btnSubmit').prop('disabled', false);
                        }
                    });
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
                    else if($("#schedOn").val() > maxDate){
                        swal('Exceed Date Needed deadline!','Select within date range from today up to Date Needed.','error');
                        return false;
                    }
                    else{
                        swal({
                            title: "SCHEDULE STOCK TRANSFER REQUEST?",
                            text: "You are about to SCHEDULE this STOCK TRANSFER REQUEST!",
                            icon: "warning",
                            buttons: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {
                                for(var n=0; n < j; n++){
                                    if($('#serial'+n).val() != ''){
                                        $.ajax({
                                            type:'post',
                                            url:'/transferItems',
                                            headers: {
                                                'X-CSRF-TOKEN': $("#csrf").val()
                                            },
                                            data:{
                                                'request_number': reqnum,
                                                'item_id': $('#item_id'+n).val(),
                                                'category': $('#category'+n).val(),
                                                'item': $('#item'+n).val(),
                                                'qty': $('#qty'+n).val(),
                                                'serial': $('#serial'+n).find('option:selected').text(),
                                                'locfrom': $('#locfrom_details').val(),
                                                'locto': $('#locto_details').val(),
                                                'schedOn': $('#schedOn').val()
                                            },
                                            success: function (data){
                                                if(data == 'true'){
                                                    return true;
                                                }
                                                else{
                                                    return false;
                                                }
                                            },
                                            error: function (data) {
                                                if(data.status == 401) {
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
                                    headers: {
                                        'X-CSRF-TOKEN': $("#csrf").val()
                                    },
                                    data:{
                                        'request_number': reqnum,
                                        'schedOn': $('#schedOn').val()
                                    },
                                    success: function (){
                                        $('#stockRequestDetails').hide();
                                        sweetAlert("SCHEDULED SUCCESS", "STOCK TRANSFER REQUEST", "success");
                                        setTimeout(function(){location.href="/stocktransfer"}, 2000);
                                    },
                                    error: function (data) {
                                        if(data.status == 401) {
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
            error: function (data) {
                if(data.status == 401) {
                    window.location.href = '/stocktransfer';
                }
                alert(data.responseText);
            }
        }); 
    }
});

$("#btnBack").on('click', function(){
    $("#transferDetails *").prop('disabled', false);
    $('#btnSubmit').prop('disabled', true);
    $("#requestItems").hide();
    $("#btnProceed").show();
    $("#reqContents").empty();
});

$(document).on('click', '.btnPrint', function(){
    window.location.href = '/printTransferRequest?request_number='+$('#reqnum_details').val();
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
        if (willDelete) {
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