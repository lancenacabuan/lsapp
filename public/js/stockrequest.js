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

function copyAsmReqNum(){
    var copyText = document.getElementById("asm_request_num_details");
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

function copyRefNum(){
    if($("#reference_details").val() != ''){
        var copyText = document.getElementById("reference_details");
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
}

function sweet(title, text, icon, btnName, url){
    swal(title, text, icon, {
        buttons: {
            cancel: 'Cancel',
            catch: {
            text: btnName,
            value: 'button',
            }
        },
    })
    .then((value) => {
        if(value == 'button'){
            window.location.href = url;
        }
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

function validate_fileupload(reference_upload){
    $('#upload_label').html('Upload Image File (Less than 5MB)');
    if(!/(\.jpg|\.jpeg|\.png|\.gif)$/i.test(reference_upload.value)){
        swal('Invalid image file type or format!', 'Please upload an image file with a valid file type/format like the following: jpeg/jpg, png, or gif.', 'error');      
        $('#reference_upload').val('');
        $('#reference_upload').focus();
        return false;   
    }
    const oFile = document.getElementById("reference_upload").files[0];
    if(oFile.size > 5242880){
        swal('Exceeded maximum file size (5MB)!', 'Please upload a valid image file with a file size not greater than 5MB.', 'error');      
        $('#reference_upload').val('');
        $('#reference_upload').focus();
        return false; 
    }
    $('#upload_label').html(reference_upload.value.split("\\").pop());
    
    return true; 
}

$(document).ready(function(){
    if($(location).attr('pathname')+window.location.search == '/stockrequest?submit=success'){
        swal("SUBMIT SUCCESS", "STOCK REQUEST", "success");
        setTimeout(function(){location.href="/stockrequest"}, 2000);
    }
});

function generatedr(){
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
        url:'/generatedr',
        async: false,
        data:{
            'request_number': request_number
        },
        success: function(data){
            if(data == 'unique'){
                document.getElementById("request_num").value = request_number;
                document.getElementById("reqnum").value = request_number;
            }
            else{
                generatedr();
            }
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/stockrequest';
            }
                alert(data.responseText);
        }
    });
}

$(".btnNewStockRequest").on('click', function(){
    $('#newStockRequest').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('.modal-body').html();
    $('#newStockRequest').modal('show');
    generatedr();
});

setInterval(runFunction, 100);
function runFunction(){
    if($('#newStockRequest').is(':visible')){
        var needdate = $('#needdate').val();
        var request_type = $('#request_type').val();
        var warranty_type = $('#warranty_type').val();
        var client_name = $.trim($('#client_name').val());
        var location_name = $.trim($('#location').val());
        var reference = $.trim($('#reference').val());
        var reference_upload = $('#reference_upload').val();
        if($('.reference_field').is(':visible')){
            if(needdate && request_type && warranty_type && client_name && location_name && reference && reference_upload){
                $('#requestDetails').show();
                $('.header_label').hide();
            }
            else{
                $('#requestDetails').hide();
                $('.header_label').show();
            }
        }
        else{
            if(needdate && request_type && client_name && location_name){
                $('#requestDetails').show();
                $('.header_label').hide();
            }
            else{
                $('#requestDetails').hide();
                $('.header_label').show();
            }
        }
    }
}

setInterval(checkLocation, 100);
function checkLocation(){
    if($('#detailsStockRequest').is(':visible') && $('#status_id_details').val() == '13'){
        var warehouse = $('#warehouse_details').val();
        if(!warehouse){
            $('#warehouse_note').show();
            $('.btnReceiveAssembled').prop('disabled', true);
        }
        else{
            $('#warehouse_note').hide();
            $('.btnReceiveAssembled').prop('disabled', false);
        }
    }
}

$('#request_type').on('change', function(){
    var reqtype = $(this).val();
    $('#reference').val('');
    $('#reference_upload').val('');
    $('#warranty_type').val('');
    $('#upload_label').html('Upload Image File (Less than 5MB)');
    if(reqtype == '2'){
        $('.reference_field').show();
    }
    else{
        $('.reference_field').hide();
        $('.warranty_field').hide();
    }
});

$('#warranty_type').on('change', function(){
    $('.warranty_field').show();
    $('.listInclusive').hide();
    var id = $(this).val();
    $.ajax({
        type: 'get', 
        url: '/getInclusive', 
        data:{
            id: id
        }, 
        success: function(data){
            $('.duration').val(data[0].Duration+' MONTH/S');
            if(data[0].Inclusive != null){
                if(data[0].Inclusive.indexOf('Software') > -1){
                    $('.software').show();
                }
                if(data[0].Inclusive.indexOf('Onsite Visit') > -1){
                    $('.onsite').show();
                }
                if(data[0].Inclusive.indexOf('Phone Support') > -1){
                    $('.phone').show();
                }
                if(data[0].Inclusive.indexOf('Hardware') > -1){
                    $('.hardware').show();
                }
                if(data[0].Inclusive.indexOf('Parts Replacement') > -1){
                    $('.replacement').show();
                }
                if(data[0].Inclusive.indexOf('Service Unit') > -1){
                    $('.su').show();
                }
            }              
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/stockrequest';
            }
            alert(data.responseText);
        }
    });    
});

$('#warranty_type_details').on('change', function(){
    $('.warranty_field').show();
    $('.listInclusive').hide();
    var id = $(this).val();
    $.ajax({
        type: 'get', 
        url: '/getInclusive', 
        data:{
            id: id
        }, 
        success: function(data){
            $('.duration').val(data[0].Duration+' MONTH/S');
            if(data[0].Inclusive != null){
                if(data[0].Inclusive.indexOf('Software') > -1){
                    $('.software').show();
                }
                if(data[0].Inclusive.indexOf('Onsite Visit') > -1){
                    $('.onsite').show();
                }
                if(data[0].Inclusive.indexOf('Phone Support') > -1){
                    $('.phone').show();
                }
                if(data[0].Inclusive.indexOf('Hardware') > -1){
                    $('.hardware').show();
                }
                if(data[0].Inclusive.indexOf('Parts Replacement') > -1){
                    $('.replacement').show();
                }
                if(data[0].Inclusive.indexOf('Service Unit') > -1){
                    $('.su').show();
                }
            }              
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/stockrequest';
            }
            alert(data.responseText);
        }
    });    
});

$('#categoryReq').on('change', function(){
    var id = $('#categoryReq').val();
    var descOp = " ";
    $('#uom').val('');
    $.ajax({ 
        type:'get', 
        url:'/itemsreq', 
        data:{'category_id':id}, 
        success: function(data) 
            {
                var itemcode = $.map(data, function(value, index){ 
                    return [value];
                });
                descOp+='<option value="" selected disabled>Select Item</option>'; 
                itemcode.forEach(value => {
                    descOp+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>'; 
                });
                
                $("#itemReq").find('option').remove().end().append(descOp);                 
            },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/stockrequest';
            }
            alert(data.responseText);
        }
    });    
});

$('#itemReq').on('change', function(){
    var item_id = $(this).val();
    $.ajax({
        type:'get', 
        url:'/setuom', 
        data:{
            'item_id': item_id,
        }, 
        success: function(data){
            $('#uom').val(data);
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/stockrequest';
            }
            alert(data.responseText);
        }
    });
});

$(".add-row").on('click', function(){
    var category = $("#categoryReq option:selected").text();
    var item = $("#itemReq option:selected").text();
    var category_id = $("#categoryReq").val();
    var item_id = $("#itemReq").val();
    let qty = $("#qtyReq").val();
    var uom = $("#uom").val();
    var markup = "<tr><td style='display: none;'>" + category_id + "</td><td style='display: none;'>" + item_id + "</td><td>" + category + "</td><td>" + item + "</td><td>" + qty + "</td><td>" + uom + "</td><td> <button type='button' style='zoom: 75%;' class='delete-row btn btn-primary bp'>REMOVE</button> </td></tr>";
    var ctr = 'false';
    if(category == "Select Category" || item == "Select Item" || qty == "" || qty == "0" || uom == ""){
        swal('REQUIRED','Please select an item!','error');
        return false;
    }
    else{
        var table = document.getElementById('stockRequestTable');
        var count = table.rows.length;
        for(i = 1; i < count; i++){
            var objCells = table.rows.item(i).cells;
            if(item_id==objCells.item(1).innerHTML){
                objCells.item(4).innerHTML = parseInt(objCells.item(4).innerHTML) + parseInt(qty);
                ctr = 'true';
                category = $("#categoryReq").val('');
                item = $("#itemReq").find('option').remove().end().append('<option value="0">Select Item</option>').val()
                qty = $("#qtyReq").val('');
                uom = $('#uom').val('');
                return false;
            }
            else {
                ctr = 'false';
            }
        }
        if(ctr == 'false')
        { $("#stockRequestTable tbody").append(markup); }
        category = $("#categoryReq").val('');
        item = $("#itemReq").find('option').remove().end().append('<option value="0">Select Item</option>').val()
        qty = $("#qtyReq").val('');
        uom = $('#uom').val('');
        $('#stockRequestTable').show();
        $('#stockRequestDiv').toggle();
        $('#btnClose').show();
        $('#btnSave').show();
    }
    if($('#stockRequestTable tbody').children().length==0){
        $('.submit_label').show();
    }
    else{
        $('.submit_label').hide();
    }
});

$("#stockRequestTable").on('click', '.delete-row', function(){
    $(this).closest("tr").remove();
    if($('#stockRequestTable tbody').children().length==0){
        $('#stockRequestTable').hide();
        $('#stockRequestDiv').removeClass();
        $('#btnClose').hide();
        $('#btnSave').hide();
        $('.submit_label').show();
    }
});

$('#btnSave').on('click', function(){
    var needdate = $('#needdate').val();
    var request_type = $('#request_type').val();
    var warranty_type = $('#warranty_type').val();
    var client_name = $.trim($('#client_name').val());
    var location_name = $.trim($('#location').val());
    var reference = $.trim($('#reference').val()).toUpperCase();
    var reference_upload = $('#reference_upload').val();
    if(needdate < minDate){
        swal('Minimum Date is today!','Select within date range from today onwards.','error');
        return false;
    }
    else if(['N/A', 'N /A', 'N/ A', 'N / A', 'NA', 'N A', 'NONE', 'N O N E'].includes(reference) == true){
        swal('Invalid Reference SO/PO No.!','Please input a valid Reference SO/PO Number.','error');
        return false;
    }
    else{
        swal({
            title: "SUBMIT STOCK REQUEST?",
            text: "Please review the details of your request. Hit OK to confirm or CANCEL to edit it.",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                    type:'post',
                    url:'/saveReqNum',
                    async: false,
                    headers: {
                        'X-CSRF-TOKEN': $("#csrf").val()
                    },
                    data:{
                        'request_number': $('#request_num').val(),
                        'needdate': needdate,
                        'request_type': request_type,
                        'warranty_type': warranty_type,
                        'client_name': client_name,
                        'location': location_name,
                        'reference': reference
                    },
                    success: function(data){
                        if(data == 'true'){
                            var myTable = $('#stockRequestTable').DataTable();
                            var form_data  = myTable.rows().data();
                            $.each(form_data, function(key, value){
                                $.ajax({
                                    type:'post',
                                    url:'/saveRequest',
                                    async: false,
                                    headers: {
                                        'X-CSRF-TOKEN': $("#csrf").val()
                                    },
                                    data:{
                                        'request_number': $('#request_num').val(),
                                        'category': value[0],
                                        'item': value[1],
                                        'quantity': value[4]
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
                                            window.location.href = '/stockrequest';
                                        }
                                        alert(data.responseText);
                                    }
                                });
                            });
                            scrollReset();
                            $('#newStockRequest').hide();
                            $('#newStockRequest').modal('dispose');
                            $('#loading').show(); Spinner(); Spinner.show();
                            $.ajax({
                                type:'post',
                                url:'/logSave',
                                async: false,
                                headers: {
                                    'X-CSRF-TOKEN': $("#csrf").val()
                                },
                                data:{
                                    'request_number': $('#request_num').val(),
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide(); Spinner.hide();
                                        if(reference_upload){
                                            $('#btnUpload').click();
                                        }
                                        else{
                                            swal("SUBMIT SUCCESS", "STOCK REQUEST", "success");
                                            setTimeout(function(){location.href="/stockrequest"}, 2000);
                                        }
                                    }
                                    else{
                                        return false;
                                    }
                                },
                                error: function(data){
                                    if(data.status == 401){
                                        window.location.href = '/stockrequest';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else if(data == 'duplicate'){
                            swal("INVALID ENTRY", "Reference SO/PO Number already exists! Please double check the SO/PO Number and try again.", "error");
                            return false;
                        }
                        else{
                            $('#newStockRequest').hide();
                            swal("SUBMIT FAILED", "STOCK REQUEST", "error");
                            setTimeout(function(){location.href="/stockrequest"}, 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/stockrequest';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }  
});

$('#close').on('click', function(){
    window.location.href = '/stockrequest';
});

$('#btnClose').on('click', function(){
    window.location.href = '/stockrequest';
});

$('#modalClose').on('click', function(){
    window.location.href = '/stockrequest';
});

$('table.stockrequestTable').dataTable().fnDestroy();
$('#loading').show(); Spinner(); Spinner.show();
$('table.stockrequestTable').DataTable({ 
    columnDefs: [
        {
            "targets": [1],
            "render": $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'MMM. DD, YYYY')
        },
        {
            "targets": [7,8,9,10,11,12,13,14,15,16,17,18],
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
        url: '/request_data',
    },
    columns: [
        {
            data: 'needdate',
            "render": function(data, type, row){
                if(row.status_id >= 7){
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
        { data: 'date' },
        { data: 'req_num' },
        { data: 'reference' },
        { data: 'req_by' },
        { data: 'req_type' },
        {
            data: 'status',
            "render": function(data, type, row){
                if(row.status_id == '6'){
                    return "<span style='color: DarkSlateGray; font-weight: bold;'>"+row.status+'&nbsp;&nbsp;&nbsp;'+"<i style='zoom: 150%; color: DarkSlateGray;' class='fa fa-exclamation-triangle'></i></span>";
                }
                else if(row.status_id == '1' || row.status_id == '15' || row.status_id == '18' || row.status_id == '21' || row.status_id == '22' || row.status_id == '23' || row.status_id == '24'){
                    return "<span style='color: Red; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '2' || row.status_id == '5' || row.status_id == '16'){
                    return "<span style='color: Indigo; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '3' || row.status_id == '4' || row.status_id == '13' || row.status_id == '17'){
                    return "<span style='color: Green; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '8' || row.status_id == '9' || row.status_id == '12' || row.status_id == '14' || row.status_id == '19' || row.status_id == '20'){
                    return "<span style='color: Blue; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '10'){
                    return "<span style='color: DarkBlue; font-weight: bold;'>"+row.status+"</span>";
                }
                else{
                    return "<span style='color: Gray; font-weight: bold;'>"+row.status+"</span>";
                }
            }
        },
        { data: 'item_id' },
        { data: 'item_desc' },
        { data: 'qty' },
        { data: 'req_type_id' },
        { data: 'status_id' },
        { data: 'prep_by' },
        { data: 'sched' },
        { data: 'user_id' },
        { data: 'client_name' },
        { data: 'location' },
        { data: 'reason' },
        { data: 'assembly_reqnum' },
    ],
    order:[],
    initComplete: function(){
        $('#loading').hide(); Spinner.hide();
    }
});

if($(location).attr('pathname')+window.location.search != '/stockrequest'){
    url = window.location.search;
    reqnum = url.replace('?request_number=', '');
    $.ajax({
        url: '/reqModal',
        headers: {
            'X-CSRF-TOKEN': $("#csrf").val()
        },
        dataType: 'json',
        type: 'get',
        data: {
            request_number: reqnum,
        },
        success: function(data){
            $('#detailsStockRequest').modal({
                backdrop: 'static',
                keyboard: false
            });
            var reqitem = $.map(data.data, function(value, index){ 
                return [value];
            });
            reqitem.forEach(value => {
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
                var asm_req_num = value.assembly_reqnum;
                    $('#asm_request_num_details').val(asm_req_num);
                var req_by = value.req_by;
                    $('#requested_by_details').val(req_by);
                var req_type = value.req_type;
                    $('#request_type_details').val(req_type);
                var warranty_type = value.warranty_type;
                    $('#warranty_type_details').val(warranty_type);
                    $("#warranty_type_details").trigger('change');
                var item_id = value.item_id;
                    $('#item_id_details').val(item_id);
                var item_desc = value.item_desc;
                    $('#item_desc_details').val(item_desc);
                var qty = value.qty;
                    $('#qty_details').val(qty);
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
                var client_name = value.client_name;
                    $('#client_name_details').val(client_name);
                var location_name = value.location;
                    $('#location_details').val(location_name);
                var reference = value.reference;
                    $('#reference_details').val(reference);
                var reason = value.reason;
                    $('#reason_details').val(reason);
                var reference_attachment = value.reference_upload;
                    $('#reference_attachment').attr('src', '/uploads/'+reference_attachment).show();
            
                    $('.modal-body').html();
                    $('#detailsStockRequest').modal('show');

                    var ajax_url = '/schedItems';
                    var rcv_url = '/schedItems';
                    var included = 'yes';

                    if(req_type_id != '2'){
                        $(".sales_details").hide();
                    }
                    if(req_type_id == '1' || req_type_id == '5'){
                        $("#client_name_label").hide();
                        $("#client_name_details").hide();
                        $("#location_label").hide();
                        $("#location_details").hide();
                        $("#reference_label").hide();
                        $("#reference_details").hide();
                    }
                    if(req_type_id == '5'){
                        $("#proceed_label").hide();
                        $("#item_desc_label").show();
                        $("#item_desc_details").show();
                        $("#qty_label").show();
                        $("#qty_details").show();
                    }
                    if(req_type_id == '4'){
                        $(".dfchide").hide();
                        $(".dfcshow").show();
                    }
                    if(req_type_id == '4' && requestStatus == '1'){
                        $.ajax({ 
                            type:'get', 
                            url:'/checkStatus', 
                            data:{
                                'assembly_reqnum': $('#asm_request_num_details').val()
                            }, 
                            success: function(data){
                                if(data == '18' || data == '21'){
                                    $("#warning").hide();
                                    $("#btnProceed").hide();
                                    $(".rcvDef").show();
                                }
                            }
                        });
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
                    if(requestStatus == '1'|| requestStatus == '2'|| requestStatus == '3' || requestStatus == '4' || requestStatus == '5' || requestStatus > 7){
                        $("#btnDelete").hide();
                    }
                    if(requestStatus == '2' || requestStatus == '3' || requestStatus == '4' || requestStatus == '6' || requestStatus == '7' || requestStatus > 7){
                        $("#proceed_label").hide();
                        $("#btnProceed").hide();
                    }
                    if(requestStatus == '2' || requestStatus == '5'){
                        $("#schedItemsModal").show();
                        $.ajax({
                            type:'get', 
                            url:'/checkProcessed', 
                            data:{
                                'request_number': $('#request_num_details').val()
                            }, 
                            success: function(data){
                                if(data != 0){
                                    $("#receivedItemsModal").show();
                                }
                            },
                            error: function(data){
                                if(data.status == 401){
                                    window.location.href = '/stockrequest';
                                }
                                alert(data.responseText);
                            }
                        });
                    }
                    if(requestStatus == '3' || requestStatus == '4'){
                        $("#transitItemsModal").show();
                        if(req_type_id == '3'){
                            $(".btnReceive").html('RECEIVE DEMO');
                        }
                        if($("#current_role").val() == '["sales"]'){
                            $('#receive_label').show();
                        }
                        $.ajax({
                            type:'get', 
                            url:'/checkProcessed', 
                            data:{
                                'request_number': $('#request_num_details').val()
                            }, 
                            success: function(data){
                                if(data != 0){
                                    $("#receivedItemsModal").show();
                                }
                            },
                            error: function(data){
                                if(data.status == 401){
                                    window.location.href = '/stockrequest';
                                }
                                alert(data.responseText);
                            }
                        });
                    }
                    if(requestStatus == '8' || requestStatus == '9'|| requestStatus == '12' || requestStatus == '13' || requestStatus == '14' || requestStatus == '19' || requestStatus == '20' || requestStatus == '24'){
                        var rcv_url = 'receivedItems';
                        $("#transitItemsModal").show();
                        $(".btnReceive").hide();
                        document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
                        if(req_type_id == '3' && requestStatus == '9'){
                            $('#demoreceive_label').show();
                            $("#btnSale").show();
                            $("#btnReturn").show();
                        }
                        if(requestStatus == '12' || requestStatus == '20'){
                            document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        }
                        if(requestStatus == '13'){
                            document.getElementById('modalheader').innerHTML = 'ASSEMBLED ITEM PARTS DETAILS';
                            $("#warehouse_label").show();
                            $("#warehouse_details").show();
                            $("#warehouse_note").show();
                            $(".btnReceiveAssembled").show();
                            $("#btnHideDetails").show();
                            $("#divAssembly").show();
                            $("#request_info").hide();
                            $("#transitItemsModal").hide();
                        }
                        if(requestStatus == '14'){
                            document.getElementById('modalheader').innerHTML = 'ASSEMBLED ITEM PARTS DETAILS';
                            $('#asmItemsModal').show();
                        }
                        if(requestStatus == '19'){
                            document.getElementById('modalheader').innerHTML = 'REPLACEMENT ITEM DETAILS';
                        }
                    }
                    if(requestStatus == '10'){
                        var rcv_url = 'receivedItems';
                        $("#transitItemsModal").show();
                        $(".soldShow").show();
                        $(".btnReceive").hide();
                        $("#btnSale").hide();
                        $("#btnReturn").hide();
                        document.getElementById('modalheader').innerHTML = 'SOLD ITEM DETAILS';
                    }
                    if(requestStatus == '15'){
                        var ajax_url = '/incItems';
                        $("#receivedItemsModal").show();
                        document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        if(req_type_id != '5'){
                            document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
                        }
                        $("#incItemsModal").show();
                        $(".divResched").show();
                        $('#resched').attr('max', maxDate);
                        $("#btnReschedule").show();
                        if($("#current_role").val() == '["sales"]'){
                            $(".divResched").hide();
                        }
                    }
                    if(requestStatus == '16'){
                        var ajax_url = '/incItems';
                        $("#receivedItemsModal").show();
                        document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        if(req_type_id != '5'){
                            document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
                        }
                        $("#incItemsModal").show();
                        $(".divResched1").show();
                        $(".btnTransit").show();
                    }
                    if(requestStatus == '17'){
                        var ajax_url = '/incItems';
                        $("#receivedItemsModal").show();
                        document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        if(req_type_id != '5'){
                            document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
                        }
                        $("#incItemsModal").show();
                        $(".divResched1").show();
                        $("#incFooter").hide();
                    }
                    if(requestStatus == '17' && $("#current_role").val() == '["sales"]'){
                        $('#increceive_label').show();
                        $("#inc2Footer").show();
                    }
                    if(requestStatus == '18'){
                        var ajax_url = '/dfcItems';
                        $("#receivedItemsModal").show();
                        document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'DEFECTIVE ITEM DETAILS';
                        $("#request_info").hide();
                        $("#receivedItemsModal").hide();
                        $(".rcvShow").show();
                        $("#showMore").show();
                        $("#showLess").hide();
                    }
                    if(requestStatus == '21'){
                        var ajax_url = '/incdfcItems';
                        $("#receivedItemsModal").show();
                        document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE DEFECTIVE ITEM DETAILS';
                        $("#request_info").hide();
                        $("#receivedItemsModal").hide();
                        $(".rcvShow").show();
                        $("#showMore").show();
                        $("#showLess").hide();
                    }
                    if(requestStatus == '22'){
                        var rcv_url = 'receivedItems';
                        var included = 'no';
                        $("#transitItemsModal").show();
                        $(".prephide").hide();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $(".pendshow").show();
                    }
                    if(requestStatus == '23'){
                        var ajax_url = '/incItems';
                        $("#receivedItemsModal").show();
                        document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE REPLACEMENT ITEM DETAILS';
                        $("#incFooter").hide();
                    }
                    if(requestStatus == '24'){
                        $("#proceed_label").show();
                        $("#btnProceed").show();
                    }
                    if(value.user_id != $('#current_user').val()){
                        $(".btnReceive").hide();
                        $("#btnSale").hide();
                        $("#btnReturn").hide();
                    }
                    if(value.user_id == $('#current_user').val() && $("#current_role").val() == '["sales"]'){
                        $("#sd2").show();
                        $("#sd1").hide();
                    }
                    else{
                        $("#btnDelete").hide();
                        $("#sd1").show();
                        $("#sd2").hide();
                    }
                    if(requestStatus == '1'|| requestStatus == '2'|| requestStatus == '3' || requestStatus == '4' || requestStatus == '5' || requestStatus > 7){
                        $("#sd1").show();
                        $("#sd2").hide();
                    }
                    if(requestStatus == '1'|| requestStatus == '5' || requestStatus == '6' || requestStatus == '24'){
                        var targetStockDetails = [6,7,8,9,10];
                        var targetStockDetails1 = [5];
                        var targetStockDetails2 = [6];
                    }
                    else{
                        var targetStockDetails = [4,5,6,7,8,9,10,11,12];
                        var targetStockDetails1 = [4,5];
                        var targetStockDetails2 = [4,5,6];
                    }
                    if($("#current_role").val() == '["sales"]'){
                        $("#proceed_label").hide();
                    }
                    
                $('table.stockDetails').dataTable().fnDestroy();    
                $('table.stockDetails').DataTable({ 
                    columnDefs: [
                        {
                            "targets": targetStockDetails,
                            "visible": false,
                            "searchable": false
                        },
                        {
                            render: function(data,type,full,meta){
                                return "<div style='color: red;'>"+data+"</div>";
                            },
                            targets:[11,12]
                        }
                    ],
                    searching: false,
                    paging: false,
                    ordering: false,
                    info: false,
                    language: {
                        emptyTable: "No data available in table",
                        processing: "Loading...",
                    },
                    serverSide: true,
                    ajax: {
                        url: '/requestDetails',
                        data: {
                            reqnum: req_num,
                        },
                        dataType: 'json',
                        error: function(data){
                            if(data.status == 401){
                                window.location.href = '/stockrequest';
                            }
                            alert(data.responseText);
                        },
                    },
                    order:[],
                    columns: [
                        { data: 'category' },
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
                        { data: 'qtymal' }
                    ],
                    orderCellsTop: true,
                    fixedHeader: true,            
                }); 
                
                $('table.stockDetails1').dataTable().fnDestroy();    
                $('table.stockDetails1').DataTable({ 
                    columnDefs: [
                        {
                            "targets": targetStockDetails1,
                            "visible": false,
                            "searchable": false
                        }
                    ],
                    searching: false,
                    paging: false,
                    ordering: false,
                    info: false,
                    language: {
                        emptyTable: "No data available in table",
                        processing: "Loading...",
                    },
                    serverSide: true,
                    ajax: {
                        url: '/requestDetails',
                        data: {
                            reqnum: req_num,
                        },
                        dataType: 'json',
                        error: function(data){
                            if(data.status == 401){
                                window.location.href = '/stockrequest';
                            }
                            alert(data.responseText);
                        },
                    },
                    order:[],
                    columns: [
                        { data: 'category' },
                        { data: 'item' },
                        { data: 'uom' },
                        { data: 'quantity' },
                        { data: 'pending' },
                        { data: 'item_id' }
                    ],
                    orderCellsTop: true,
                    fixedHeader: true,            
                });
                
                $('table.stockDetails2').dataTable().fnDestroy();    
                $('table.stockDetails2').DataTable({ 
                    columnDefs: [
                        {
                            "targets": targetStockDetails2,
                            "visible": false,
                            "searchable": false
                        },
                        {   
                            "render": function(data, type, row, meta){
                                    return '<button style="zoom: 75%;" class="btn btn-primary bp btndelItem" id="'+ meta.row +'">REMOVE</button>';
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
                    language: {
                        emptyTable: "No data available in table",
                        processing: "Loading...",
                    },
                    serverSide: true,
                    ajax: {
                        url: '/requestDetails',
                        data: {
                            reqnum: req_num,
                        },
                        dataType: 'json',
                        error: function(data){
                            if(data.status == 401){
                                window.location.href = '/stockrequest';
                            }
                            alert(data.responseText);
                        },
                    },
                    order:[],
                    columns: [
                        { data: 'category' },
                        { data: 'item' },
                        { data: 'uom' },
                        { data: 'quantity' },
                        { data: 'pending' },
                        { data: 'item_id' },
                        { data: 'item_id' }
                    ],
                    orderCellsTop: true,
                    fixedHeader: true,            
                });
            
                $('table.receivedItems').DataTable({
                    searching: false,
                    paging: false,
                    ordering: false,
                    info: false,
                    language: {
                        processing: "Loading...",
                        emptyTable: "No data available in table"
                    },
                    serverSide: true,
                    ajax: {
                        url: '/receivedItems',
                        data: {
                            request_number: req_num,
                        }
                    },
                    order:[],
                    columns: [
                        { data: 'category' },
                        { data: 'item' },
                        { data: 'qty' },
                        { data: 'uom' },
                        { data: 'serial' }
                    ]
                });
            
                $('table.schedItems').DataTable({
                    columnDefs: [
                        {
                            "targets": [7],
                            "visible": false,
                            "searchable": false
                        },
                        {   
                            "render": function(data, type, row, meta){
                                    return '<button style="zoom: 75%;" class="btn btn-primary bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                            },
                            "defaultContent": '',
                            "data": null,
                            "targets": [6]
                        }
                    ],
                    searching: false,
                    paging: false,
                    ordering: false,
                    info: false,
                    language: {
                        processing: "Loading...",
                        emptyTable: "No data available in table"
                    },
                    serverSide: true,
                    ajax: {
                        url: '/schedItems',
                        data: {
                            request_number: req_num,
                        }
                    },
                    order:[],
                    columns: [
                        { data: 'category' },
                        { data: 'item' },
                        { data: 'qty' },
                        { data: 'uom' },
                        { data: 'serial' },
                        { data: 'location' },
                        { data: 'id' },
                        { data: 'id' }
                    ]
                });
            
                $('table.schedItems1').DataTable({
                    searching: false,
                    paging: false,
                    ordering: false,
                    info: false,
                    language: {
                        processing: "Loading...",
                        emptyTable: "No data available in table"
                    },
                    serverSide: true,
                    ajax: {
                        url: '/schedItems',
                        data: {
                            request_number: req_num,
                        }
                    },
                    order:[],
                    columns: [
                        { data: 'category' },
                        { data: 'item' },
                        { data: 'qty' },
                        { data: 'uom' },
                        { data: 'serial' },
                        { data: 'location' }
                    ]
                });
            
                $('table.transItems').dataTable().fnDestroy();
                $('table.transItems').DataTable({
                    columnDefs: [
                        {
                            "targets": [5],
                            "visible": false,
                            "searchable": false
                        },
                    ],
                    searching: false,
                    paging: false,
                    ordering: false,
                    info: false,
                    language: {
                        processing: "Loading...",
                        emptyTable: "No data available in table"
                    },
                    serverSide: true,
                    ajax: {
                        url: rcv_url,
                        data: {
                            request_number: req_num,
                        }
                    },
                    order:[],
                    columns: [
                        { data: 'category' },
                        { data: 'item' },
                        { data: 'qty' },
                        { data: 'uom' },
                        { data: 'serial' },
                        { data: 'location' }
                    ]
                });
            
                if(requestStatus == '3' || requestStatus == '4'){
                    $('table.transItems1').DataTable({
                        columnDefs: [
                            {
                                "targets": [5,7],
                                "visible": false,
                                "searchable": false
                            },
                            {
                                "render": function(data, type, row, meta){
                                        return '<button style="zoom: 75%;" class="btn btn-primary bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                                },
                                "defaultContent": '',
                                "data": null,
                                "targets": [6]
                            }
                        ],
                        searching: false,
                        paging: false,
                        ordering: false,
                        info: false,
                        language: {
                            processing: "Loading...",
                            emptyTable: "No data available in table"
                        },
                        serverSide: true,
                        ajax: {
                            url: rcv_url,
                            data: {
                                request_number: req_num,
                            }
                        },
                        order:[],
                        columns: [
                            { data: 'category' },
                            { data: 'item' },
                            { data: 'qty' },
                            { data: 'uom' },
                            { data: 'serial' },
                            { data: 'location' },
                            { data: 'id' },
                            { data: 'id' }
                        ]
                    });
                }
                else{
                    $('table.transItems1').DataTable({
                        columnDefs: [
                            {
                                "targets": [5,6,7],
                                "visible": false,
                                "searchable": false
                            },
                            {
                                "render": function(data, type, row, meta){
                                        return '<button style="zoom: 75%;" class="btn btn-primary bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                                },
                                "defaultContent": '',
                                "data": null,
                                "targets": [6]
                            }
                        ],
                        searching: false,
                        paging: false,
                        ordering: false,
                        info: false,
                        language: {
                            processing: "Loading...",
                            emptyTable: "No data available in table"
                        },
                        serverSide: true,
                        ajax: {
                            url: rcv_url,
                            data: {
                                request_number: req_num,
                                included: included
                            }
                        },
                        order:[],
                        columns: [
                            { data: 'category' },
                            { data: 'item' },
                            { data: 'qty' },
                            { data: 'uom' },
                            { data: 'serial' },
                            { data: 'location' },
                            { data: 'id' },
                            { data: 'id' }
                        ]
                    });
                }
            
                if(ajax_url != '/schedItems'){
                    if(($("#current_role").val() == '["admin"]' || $("#current_role").val() == '["encoder"]') && (requestStatus == '15' || requestStatus == '16' || requestStatus == '17')){
                        $('table.incItems').dataTable().fnDestroy();
                        $('table.incItems').DataTable({
                            columnDefs: [
                                {
                                    "targets": [5,7],
                                    "visible": false,
                                    "searchable": false
                                },
                                {
                                    "render": function(data, type, row, meta){
                                            return '<button style="zoom: 75%;" class="btn btn-primary bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                                    },
                                    "defaultContent": '',
                                    "data": null,
                                    "targets": [6]
                                }
                            ],
                            searching: false,
                            paging: false,
                            ordering: false,
                            info: false,
                            language: {
                                processing: "Loading...",
                                emptyTable: "No data available in table"
                            },
                            serverSide: true,
                            ajax: {
                                url: ajax_url,
                                data: {
                                    request_number: req_num,
                                }
                            },
                            order:[],
                            columns: [
                                { data: 'category' },
                                { data: 'item' },
                                { data: 'qty' },
                                { data: 'uom' },
                                { data: 'serial' },
                                { data: 'location' },
                                { data: 'id' },
                                { data: 'id' }
                            ]
                        });
                    }
                    else{
                        $('table.incItems').dataTable().fnDestroy();
                        $('table.incItems').DataTable({
                            columnDefs: [
                                {
                                    "targets": [5,6,7],
                                    "visible": false,
                                    "searchable": false
                                },
                                {
                                    "render": function(data, type, row, meta){
                                            return '<button style="zoom: 75%;" class="btn btn-primary bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                                    },
                                    "defaultContent": '',
                                    "data": null,
                                    "targets": [6]
                                }
                            ],
                            searching: false,
                            paging: false,
                            ordering: false,
                            info: false,
                            language: {
                                processing: "Loading...",
                                emptyTable: "No data available in table"
                            },
                            serverSide: true,
                            ajax: {
                                url: ajax_url,
                                data: {
                                    request_number: req_num,
                                }
                            },
                            order:[],
                            columns: [
                                { data: 'category' },
                                { data: 'item' },
                                { data: 'qty' },
                                { data: 'uom' },
                                { data: 'serial' },
                                { data: 'location' },
                                { data: 'id' },
                                { data: 'id' }
                            ]
                        });
                    }
                }

                if(requestStatus == '14'){
                    $.ajax({
                        type:'get', 
                        url:'/getReceive', 
                        data:{
                            'request_number': $('#request_num_details').val()
                        }, 
                        success: function(data){
                            document.getElementById("recby").value = data.recby;
                            document.getElementById("recsched").value = moment(data.recsched).format('dddd, MMMM DD, YYYY, h:mm A');
                        },
                        error: function(data){
                            if(data.status == 401){
                                window.location.href = '/stockrequest';
                            }
                            alert(data.responseText);
                        }
                    });

                    $('table.asmItems').dataTable().fnDestroy();
                    $('table.asmItems').DataTable({
                        searching: false,
                        paging: false,
                        ordering: false,
                        info: false,
                        language: {
                            processing: "Loading...",
                            emptyTable: "No data available in table"
                        },
                        serverSide: true,
                        ajax: {
                            url: '/asmItems',
                            data: {
                                request_number: req_num,
                            }
                        },
                        order:[],
                        columns: [
                            { data: 'category' },
                            { data: 'item' },
                            { data: 'qty' },
                            { data: 'uom' },
                            { data: 'serial' },
                            { data: 'location' }
                        ]
                    });
                }
            });
        },
        error: function(data){
            alert(data.responseText);
        }
    });
}

$('#stockrequestTable tbody').on('click', 'tr', function(){
    $('#detailsStockRequest').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table = $('table.stockrequestTable').DataTable(); 
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
    var asm_req_num = value.assembly_reqnum;
        $('#asm_request_num_details').val(asm_req_num);
    var req_by = value.req_by;
        $('#requested_by_details').val(req_by);
    var req_type = value.req_type;
        $('#request_type_details').val(req_type);
    var warranty_type = value.warranty_type;
        $('#warranty_type_details').val(warranty_type);
        $("#warranty_type_details").trigger('change');
    var item_id = value.item_id;
        $('#item_id_details').val(item_id);
    var item_desc = value.item_desc;
        $('#item_desc_details').val(item_desc);
    var qty = value.qty;
        $('#qty_details').val(qty);
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
    var client_name = value.client_name;
        $('#client_name_details').val(client_name);
    var location_name = value.location;
        $('#location_details').val(location_name);
    var reference = value.reference;
        $('#reference_details').val(reference);
    var reason = value.reason;
        $('#reason_details').val(reason);
    var reference_attachment = value.reference_upload;
        $('#reference_attachment').attr('src', '/uploads/'+reference_attachment).show();

        $('.modal-body').html();
        $('#detailsStockRequest').modal('show');

        var ajax_url = '/schedItems';
        var rcv_url = '/schedItems';
        var included = 'yes';

        if(req_type_id != '2'){
            $(".sales_details").hide();
        }
        if(req_type_id == '1' || req_type_id == '5'){
            $("#client_name_label").hide();
            $("#client_name_details").hide();
            $("#location_label").hide();
            $("#location_details").hide();
            $("#reference_label").hide();
            $("#reference_details").hide();
        }
        if(req_type_id == '5'){
            $("#proceed_label").hide();
            $("#item_desc_label").show();
            $("#item_desc_details").show();
            $("#qty_label").show();
            $("#qty_details").show();
        }
        if(req_type_id == '4'){
            $(".dfchide").hide();
            $(".dfcshow").show();
        }
        if(req_type_id == '4' && requestStatus == '1'){
            $.ajax({ 
                type:'get', 
                url:'/checkStatus', 
                data:{
                    'assembly_reqnum': $('#asm_request_num_details').val()
                }, 
                success: function(data){
                    if(data == '18' || data == '21'){
                        $("#warning").hide();
                        $("#btnProceed").hide();
                        $(".rcvDef").show();
                    }
                }
            });
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
        if(requestStatus == '1'|| requestStatus == '2'|| requestStatus == '3' || requestStatus == '4' || requestStatus == '5' || requestStatus > 7){
            $("#btnDelete").hide();
        }
        if(requestStatus == '2' || requestStatus == '3' || requestStatus == '4' || requestStatus == '6' || requestStatus == '7' || requestStatus > 7){
            $("#proceed_label").hide();
            $("#btnProceed").hide();
        }
        if(requestStatus == '2' || requestStatus == '5'){
            $("#schedItemsModal").show();
            $.ajax({
                type:'get', 
                url:'/checkProcessed', 
                data:{
                    'request_number': $('#request_num_details').val()
                }, 
                success: function(data){
                    if(data != 0){
                        $("#receivedItemsModal").show();
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stockrequest';
                    }
                    alert(data.responseText);
                }
            });
        }
        if(requestStatus == '3' || requestStatus == '4'){
            $("#transitItemsModal").show();
            if(req_type_id == '3'){
                $(".btnReceive").html('RECEIVE DEMO');
            }
            if($("#current_role").val() == '["sales"]'){
                $('#receive_label').show();
            }
            $.ajax({
                type:'get', 
                url:'/checkProcessed', 
                data:{
                    'request_number': $('#request_num_details').val()
                }, 
                success: function(data){
                    if(data != 0){
                        $("#receivedItemsModal").show();
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stockrequest';
                    }
                    alert(data.responseText);
                }
            });
        }
        if(requestStatus == '8' || requestStatus == '9'|| requestStatus == '12' || requestStatus == '13' || requestStatus == '14' || requestStatus == '19' || requestStatus == '20' || requestStatus == '24'){
            var rcv_url = 'receivedItems';
            $("#transitItemsModal").show();
            $(".btnReceive").hide();
            document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
            if(req_type_id == '3' && requestStatus == '9'){
                $('#demoreceive_label').show();
                $("#btnSale").show();
                $("#btnReturn").show();
            }
            if(requestStatus == '12' || requestStatus == '20'){
                document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            }
            if(requestStatus == '13'){
                document.getElementById('modalheader').innerHTML = 'ASSEMBLED ITEM PARTS DETAILS';
                $("#warehouse_label").show();
                $("#warehouse_details").show();
                $("#warehouse_note").show();
                $(".btnReceiveAssembled").show();
                $("#btnHideDetails").show();
                $("#divAssembly").show();
                $("#request_info").hide();
                $("#transitItemsModal").hide();
            }
            if(requestStatus == '14'){
                document.getElementById('modalheader').innerHTML = 'ASSEMBLED ITEM PARTS DETAILS';
                $('#asmItemsModal').show();
            }
            if(requestStatus == '19'){
                document.getElementById('modalheader').innerHTML = 'REPLACEMENT ITEM DETAILS';
            }
        }
        if(requestStatus == '10'){
            var rcv_url = 'receivedItems';
            $("#transitItemsModal").show();
            $(".soldShow").show();
            $(".btnReceive").hide();
            $("#btnSale").hide();
            $("#btnReturn").hide();
            document.getElementById('modalheader').innerHTML = 'SOLD ITEM DETAILS';
        }
        if(requestStatus == '15'){
            var ajax_url = '/incItems';
            $("#receivedItemsModal").show();
            document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            if(req_type_id != '5'){
                document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
            }
            $("#incItemsModal").show();
            $(".divResched").show();
            $('#resched').attr('max', maxDate);
            $("#btnReschedule").show();
            if($("#current_role").val() == '["sales"]'){
                $(".divResched").hide();
            }
        }
        if(requestStatus == '16'){
            var ajax_url = '/incItems';
            $("#receivedItemsModal").show();
            document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            if(req_type_id != '5'){
                document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
            }
            $("#incItemsModal").show();
            $(".divResched1").show();
            $(".btnTransit").show();
        }
        if(requestStatus == '17'){
            var ajax_url = '/incItems';
            $("#receivedItemsModal").show();
            document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            if(req_type_id != '5'){
                document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
            }
            $("#incItemsModal").show();
            $(".divResched1").show();
            $("#incFooter").hide();
        }
        if(requestStatus == '17' && $("#current_role").val() == '["sales"]'){
            $('#increceive_label').show();
            $("#inc2Footer").show();
        }
        if(requestStatus == '18'){
            var ajax_url = '/dfcItems';
            $("#receivedItemsModal").show();
            document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            $("#incItemsModal").show();
            document.getElementById('incmodalheader').innerHTML = 'DEFECTIVE ITEM DETAILS';
            $("#request_info").hide();
            $("#receivedItemsModal").hide();
            $(".rcvShow").show();
            $("#showMore").show();
            $("#showLess").hide();
        }
        if(requestStatus == '21'){
            var ajax_url = '/incdfcItems';
            $("#receivedItemsModal").show();
            document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            $("#incItemsModal").show();
            document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE DEFECTIVE ITEM DETAILS';
            $("#request_info").hide();
            $("#receivedItemsModal").hide();
            $(".rcvShow").show();
            $("#showMore").show();
            $("#showLess").hide();
        }
        if(requestStatus == '22'){
            var rcv_url = 'receivedItems';
            var included = 'no';
            $("#transitItemsModal").show();
            $(".prephide").hide();
            document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            $(".pendshow").show();
        }
        if(requestStatus == '23'){
            var ajax_url = '/incItems';
            $("#receivedItemsModal").show();
            document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            $("#incItemsModal").show();
            document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE REPLACEMENT ITEM DETAILS';
            $("#incFooter").hide();
        }
        if(requestStatus == '24'){
            $("#proceed_label").show();
            $("#btnProceed").show();
        }
        if(value.user_id != $('#current_user').val()){
            $(".btnReceive").hide();
            $("#btnSale").hide();
            $("#btnReturn").hide();
        }
        if(value.user_id == $('#current_user').val() && $("#current_role").val() == '["sales"]'){
            $("#sd2").show();
            $("#sd1").hide();
        }
        else{
            $("#btnDelete").hide();
            $("#sd1").show();
            $("#sd2").hide();
        }
        if(requestStatus == '1'|| requestStatus == '2'|| requestStatus == '3' || requestStatus == '4' || requestStatus == '5' || requestStatus > 7){
            $("#sd1").show();
            $("#sd2").hide();
        }
        if(requestStatus == '1'|| requestStatus == '5' || requestStatus == '6' || requestStatus == '24'){
            var targetStockDetails = [6,7,8,9,10];
            var targetStockDetails1 = [5];
            var targetStockDetails2 = [6];
        }
        else{
            var targetStockDetails = [4,5,6,7,8,9,10,11,12];
            var targetStockDetails1 = [4,5];
            var targetStockDetails2 = [4,5,6];
        }
        if($("#current_role").val() == '["sales"]'){
            $("#proceed_label").hide();
        }
        
    $('table.stockDetails').dataTable().fnDestroy();    
    $('table.stockDetails').DataTable({ 
        columnDefs: [
            {
                "targets": targetStockDetails,
                "visible": false,
                "searchable": false
            },
            {
                render: function(data,type,full,meta){
                    return "<div style='color: red;'>"+data+"</div>";
                },
                targets:[11,12]
            }
        ],
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        language: {
            emptyTable: "No data available in table",
            processing: "Loading...",
        },
        serverSide: true,
        ajax: {
            url: '/requestDetails',
            data: {
                reqnum: req_num,
            },
            dataType: 'json',
            error: function(data){
                if(data.status == 401){
                    window.location.href = '/stockrequest';
                }
                alert(data.responseText);
            },
        },
        order:[],
        columns: [
            { data: 'category' },
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
            { data: 'qtymal' }
        ],
        orderCellsTop: true,
        fixedHeader: true,            
    }); 
    
    $('table.stockDetails1').dataTable().fnDestroy();    
    $('table.stockDetails1').DataTable({ 
        columnDefs: [
            {
                "targets": targetStockDetails1,
                "visible": false,
                "searchable": false
            }
        ],
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        language: {
            emptyTable: "No data available in table",
            processing: "Loading...",
        },
        serverSide: true,
        ajax: {
            url: '/requestDetails',
            data: {
                reqnum: req_num,
            },
            dataType: 'json',
            error: function(data){
                if(data.status == 401){
                    window.location.href = '/stockrequest';
                }
                alert(data.responseText);
            },
        },
        order:[],
        columns: [
            { data: 'category' },
            { data: 'item' },
            { data: 'uom' },
            { data: 'quantity' },
            { data: 'pending' },
            { data: 'item_id' }
        ],
        orderCellsTop: true,
        fixedHeader: true,            
    });
    
    $('table.stockDetails2').dataTable().fnDestroy();    
    $('table.stockDetails2').DataTable({ 
        columnDefs: [
            {
                "targets": targetStockDetails2,
                "visible": false,
                "searchable": false
            },
            {   
                "render": function(data, type, row, meta){
                        return '<button style="zoom: 75%;" class="btn btn-primary bp btndelItem" id="'+ meta.row +'">REMOVE</button>';
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
        language: {
            emptyTable: "No data available in table",
            processing: "Loading...",
        },
        serverSide: true,
        ajax: {
            url: '/requestDetails',
            data: {
                reqnum: req_num,
            },
            dataType: 'json',
            error: function(data){
                if(data.status == 401){
                    window.location.href = '/stockrequest';
                }
                alert(data.responseText);
            },
        },
        order:[],
        columns: [
            { data: 'category' },
            { data: 'item' },
            { data: 'uom' },
            { data: 'quantity' },
            { data: 'pending' },
            { data: 'item_id' },
            { data: 'item_id' }
        ],
        orderCellsTop: true,
        fixedHeader: true,            
    });

    $('table.receivedItems').DataTable({
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        language: {
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax: {
            url: '/receivedItems',
            data: {
                request_number: req_num,
            }
        },
        order:[],
        columns: [
            { data: 'category' },
            { data: 'item' },
            { data: 'qty' },
            { data: 'uom' },
            { data: 'serial' }
        ]
    });

    $('table.schedItems').DataTable({
        columnDefs: [
            {
                "targets": [7],
                "visible": false,
                "searchable": false
            },
            {   
                "render": function(data, type, row, meta){
                        return '<button style="zoom: 75%;" class="btn btn-primary bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                },
                "defaultContent": '',
                "data": null,
                "targets": [6]
            }
        ],
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        language: {
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax: {
            url: '/schedItems',
            data: {
                request_number: req_num,
            }
        },
        order:[],
        columns: [
            { data: 'category' },
            { data: 'item' },
            { data: 'qty' },
            { data: 'uom' },
            { data: 'serial' },
            { data: 'location' },
            { data: 'id' },
            { data: 'id' }
        ]
    });

    $('table.schedItems1').DataTable({
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        language: {
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax: {
            url: '/schedItems',
            data: {
                request_number: req_num,
            }
        },
        order:[],
        columns: [
            { data: 'category' },
            { data: 'item' },
            { data: 'qty' },
            { data: 'uom' },
            { data: 'serial' },
            { data: 'location' }
        ]
    });

    $('table.transItems').dataTable().fnDestroy();
    $('table.transItems').DataTable({
        columnDefs: [
            {
                "targets": [5],
                "visible": false,
                "searchable": false
            },
        ],
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        language: {
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax: {
            url: rcv_url,
            data: {
                request_number: req_num,
            }
        },
        order:[],
        columns: [
            { data: 'category' },
            { data: 'item' },
            { data: 'qty' },
            { data: 'uom' },
            { data: 'serial' },
            { data: 'location' }
        ]
    });

    if(requestStatus == '3' || requestStatus == '4'){
        $('table.transItems1').DataTable({
            columnDefs: [
                {
                    "targets": [5,7],
                    "visible": false,
                    "searchable": false
                },
                {
                    "render": function(data, type, row, meta){
                            return '<button style="zoom: 75%;" class="btn btn-primary bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                    },
                    "defaultContent": '',
                    "data": null,
                    "targets": [6]
                }
            ],
            searching: false,
            paging: false,
            ordering: false,
            info: false,
            language: {
                processing: "Loading...",
                emptyTable: "No data available in table"
            },
            serverSide: true,
            ajax: {
                url: rcv_url,
                data: {
                    request_number: req_num,
                }
            },
            order:[],
            columns: [
                { data: 'category' },
                { data: 'item' },
                { data: 'qty' },
                { data: 'uom' },
                { data: 'serial' },
                { data: 'location' },
                { data: 'id' },
                { data: 'id' }
            ]
        });
    }
    else{
        $('table.transItems1').DataTable({
            columnDefs: [
                {
                    "targets": [5,6,7],
                    "visible": false,
                    "searchable": false
                },
                {
                    "render": function(data, type, row, meta){
                            return '<button style="zoom: 75%;" class="btn btn-primary bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                    },
                    "defaultContent": '',
                    "data": null,
                    "targets": [6]
                }
            ],
            searching: false,
            paging: false,
            ordering: false,
            info: false,
            language: {
                processing: "Loading...",
                emptyTable: "No data available in table"
            },
            serverSide: true,
            ajax: {
                url: rcv_url,
                data: {
                    request_number: req_num,
                    included: included
                }
            },
            order:[],
            columns: [
                { data: 'category' },
                { data: 'item' },
                { data: 'qty' },
                { data: 'uom' },
                { data: 'serial' },
                { data: 'location' },
                { data: 'id' },
                { data: 'id' }
            ]
        });
    }

    if(ajax_url != '/schedItems'){
        if(($("#current_role").val() == '["admin"]' || $("#current_role").val() == '["encoder"]') && (requestStatus == '15' || requestStatus == '16' || requestStatus == '17')){
            $('table.incItems').dataTable().fnDestroy();
            $('table.incItems').DataTable({
                columnDefs: [
                    {
                        "targets": [5,7],
                        "visible": false,
                        "searchable": false
                    },
                    {
                        "render": function(data, type, row, meta){
                                return '<button style="zoom: 75%;" class="btn btn-primary bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                        },
                        "defaultContent": '',
                        "data": null,
                        "targets": [6]
                    }
                ],
                searching: false,
                paging: false,
                ordering: false,
                info: false,
                language: {
                    processing: "Loading...",
                    emptyTable: "No data available in table"
                },
                serverSide: true,
                ajax: {
                    url: ajax_url,
                    data: {
                        request_number: req_num,
                    }
                },
                order:[],
                columns: [
                    { data: 'category' },
                    { data: 'item' },
                    { data: 'qty' },
                    { data: 'uom' },
                    { data: 'serial' },
                    { data: 'location' },
                    { data: 'id' },
                    { data: 'id' }
                ]
            });
        }
        else{
            $('table.incItems').dataTable().fnDestroy();
            $('table.incItems').DataTable({
                columnDefs: [
                    {
                        "targets": [5,6,7],
                        "visible": false,
                        "searchable": false
                    },
                    {
                        "render": function(data, type, row, meta){
                                return '<button style="zoom: 75%;" class="btn btn-primary bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                        },
                        "defaultContent": '',
                        "data": null,
                        "targets": [6]
                    }
                ],
                searching: false,
                paging: false,
                ordering: false,
                info: false,
                language: {
                    processing: "Loading...",
                    emptyTable: "No data available in table"
                },
                serverSide: true,
                ajax: {
                    url: ajax_url,
                    data: {
                        request_number: req_num,
                    }
                },
                order:[],
                columns: [
                    { data: 'category' },
                    { data: 'item' },
                    { data: 'qty' },
                    { data: 'uom' },
                    { data: 'serial' },
                    { data: 'location' },
                    { data: 'id' },
                    { data: 'id' }
                ]
            });
        }
    }

    if(requestStatus == '14'){
        $.ajax({
            type:'get', 
            url:'/getReceive', 
            data:{
                'request_number': $('#request_num_details').val()
            }, 
            success: function(data){
                document.getElementById("recby").value = data.recby;
                document.getElementById("recsched").value = moment(data.recsched).format('dddd, MMMM DD, YYYY, h:mm A');
            },
            error: function(data){
                if(data.status == 401){
                    window.location.href = '/stockrequest';
                }
                alert(data.responseText);
            }
        });

        $('table.asmItems').dataTable().fnDestroy();
        $('table.asmItems').DataTable({
            searching: false,
            paging: false,
            ordering: false,
            info: false,
            language: {
                processing: "Loading...",
                emptyTable: "No data available in table"
            },
            serverSide: true,
            ajax: {
                url: '/asmItems',
                data: {
                    request_number: req_num,
                }
            },
            order:[],
            columns: [
                { data: 'category' },
                { data: 'item' },
                { data: 'qty' },
                { data: 'uom' },
                { data: 'serial' },
                { data: 'location' }
            ]
        });
    }
});

var tblEdit;
$(document).on('click', '.btnEditSerial', function(){
    if($('#status_id_details').val() == '2' || $('#status_id_details').val() == '5'){
        tblEdit = 'table.schedItems';
        var id = $(this).attr("id");
        var data = $(tblEdit).DataTable().row(id).data();
    }
    else if($('#status_id_details').val() == '3' || $('#status_id_details').val() == '4'){
        tblEdit = 'table.transItems1';
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
                $.ajax({
                    type:'post',
                    url: '/editSerial',
                    headers: {
                        'X-CSRF-TOKEN': $("#csrf").val()
                    },
                    data: {
                        id: id,
                        category: category,
                        item: item,
                        origserial: origserial,
                        newserial: newserial
                    },
                    success: function(data){
                        if(data == 'false'){
                            $('#editSerialModal').hide();
                            $('#editSerialModal').modal('dispose');
                            swal({
                                title: "EDIT FAILED",
                                text: "ITEM SERIAL",
                                icon: "error",
                                timer: 2000
                            });
                            $(tblEdit).DataTable().ajax.reload();
                        }
                        else{
                            $('#editSerialModal').hide();
                            $('#editSerialModal').modal('dispose');
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
    var data = $('table.stockDetails2').DataTable().row(id).data();
    $.ajax({
        type:'post',
        url: '/delReqItem',
        headers: {
            'X-CSRF-TOKEN': $("#csrf").val()
        },
        data: {
            req_num: $('#request_num_details').val(),
            item_id: data.item_id,
            item: data.item,
            uom: data.uom,
            quantity: data.quantity
        },
        success: function(data){
            if(data.result == 'false'){
                $('#detailsStockRequest').hide();
                swal("DELETE FAILED", "STOCK REQUEST", "error");
                setTimeout(function(){window.location.reload()}, 2000);
            }
            else{
                if(data.count == 0){
                    $('#detailsStockRequest').hide();
                    swal("DELETE SUCCESS", "STOCK REQUEST", "success");
                    setTimeout(function(){window.location.reload()}, 2000);
                }
                else{
                    $('table.stockDetails2').DataTable().ajax.reload();
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
        title: "DELETE STOCK REQUEST?",
        text: "You are about to DELETE your STOCK REQUEST!\n This will be permanently deleted from the system.",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if(willDelete){
            $.ajax({
                type:'post', 
                url:'/deleteRequest',
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val()
                },
                data:{
                    'request_number': $('#request_num_details').val()
                },
                success: function(data){
                    if(data == 'true'){
                        $('#detailsStockRequest').hide();
                        swal("DELETE SUCCESS", "STOCK REQUEST", "success");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                    else{
                        $('#detailsStockRequest').hide();
                        swal("DELETE FAILED", "STOCK REQUEST", "error");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stockrequest';
                    }
                    alert(data.responseText);
                }
            });
        }
    });   
});

$('#btnApprove').on('click', function(){
    swal({
        title: "APPROVE STOCK REQUEST?",
        text: "You are about to APPROVE this STOCK REQUEST!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if(willDelete){
            $.ajax({
                type:'post',
                url:'/approveRequest',
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val()
                },
                data:{
                    'request_number': $('#request_num_details').val()
                },
                success: function(data){
                    if(data == 'true'){
                        $('#detailsStockRequest').hide();
                        swal("APPROVE SUCCESS", "STOCK REQUEST", "success");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                    else{
                        $('#detailsStockRequest').hide();
                        swal("APPROVE FAILED", "STOCK REQUEST", "error");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stockrequest';
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
            title: "DISAPPROVE STOCK REQUEST?",
            text: "You are about to DISAPPROVE this STOCK REQUEST!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                    type:'post',
                    url:'/disapproveRequest',
                    async: false,
                    headers: {
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
                            $('#detailsStockRequest').hide();
                            $('#detailsStockRequest').modal('dispose');
                            $('#loading').show(); Spinner(); Spinner.show();
                            $.ajax({
                                type:'post',
                                url:'/logDisapprove',
                                async: false,
                                headers: {
                                    'X-CSRF-TOKEN': $("#csrf").val()
                                },
                                data:{
                                    'request_number': $('#request_num_details').val(),
                                    'reason': $('#reason').val()
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide(); Spinner.hide();
                                        swal("DISAPPROVE SUCCESS", "STOCK REQUEST", "success");
                                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                                    }
                                    else{
                                        return false;
                                    }
                                },
                                error: function(data){
                                    if(data.status == 401){
                                        window.location.href = '/stockrequest';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else{
                            $('#reasonModal').hide();
                            $('#detailsStockRequest').hide();
                            swal("DISAPPROVE FAILED", "STOCK REQUEST", "error");
                            setTimeout(function(){location.href="/stockrequest"}, 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/stockrequest';
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
                url:'/inTransit',
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val()
                },
                data:{
                    'request_number': $('#request_num_details').val(),
                    'assembly_reqnum': $('#asm_request_num_details').val(),
                    'request_type': $('#req_type_id_details').val(),
                    'status': $('#status_id_details').val()
                },
                success: function(data){
                    if(data == 'true'){
                        $('#detailsStockRequest').hide();
                        swal("FOR RECEIVING SUCCESS", "STOCK REQUEST", "success");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                    else{
                        $('#detailsStockRequest').hide();
                        swal("FOR RECEIVING FAILED", "STOCK REQUEST", "error");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stockrequest';
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
    else if($("#resched").val() > maxDate){
        swal('Exceed Date Needed deadline!','Select within date range from today up to Date Needed.','error');
        return false;
    }
    else{
        swal({
            title: "RESCHEDULE STOCK REQUEST?",
            text: "You are about to RESCHEDULE this STOCK REQUEST!",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                    type:'post',
                    url:'/reschedRequest',
                    headers: {
                        'X-CSRF-TOKEN': $("#csrf").val()
                    },
                    data:{
                        'request_number': $('#request_num_details').val(),
                        'request_type': $('#req_type_id_details').val(),
                        'resched': $("#resched").val()
                    },
                    success: function(data){
                        if(data == 'true'){
                            $('#detailsStockRequest').hide();
                            swal("RESCHEDULE SUCCESS", "STOCK REQUEST", "success");
                            setTimeout(function(){location.href="/stockrequest"}, 2000);
                        }
                        else{
                            $('#detailsStockRequest').hide();
                            swal("RESCHEDULE FAILED", "STOCK REQUEST", "error");
                            setTimeout(function(){location.href="/stockrequest"}, 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/stockrequest';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
});

var var_qty = 0;
$('.btnReceiveAssembled').on('click', function(){
    $('#inputSerialModal').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#inputSerialModal').modal('show');
    $("#serialList").empty();

    var_qty = $("#qty_details").val();
    for(var i=0; i < var_qty; i++){
        var serial = document.createElement("input");
        serial.setAttribute("id", "serials"+i);
        serial.setAttribute("class", "form-control serial");
        serial.setAttribute("placeholder", "Input Serial Number "+(parseInt(i)+1));
        serial.setAttribute("style", "width: 200px; font-size: 12px; margin: auto; margin-bottom: 10px;");
        document.getElementById("serialList").appendChild(serial);
    }
});

setInterval(checkSerial, 0);
function checkSerial(){
    if($('#inputSerialModal').is(':visible')){
        if($('.serial').filter(function(){ return !!this.value; }).length != var_qty){
            $('#btnReceiveAssembled').prop('disabled', true);
            $('#serial_note').show();
        }
        else{
            $('#btnReceiveAssembled').prop('disabled', false);
            $('#serial_note').hide();
        }
    }
}

$('#btnReceiveAssembled').on('click', function(){
    swal({
        title: "RECEIVE ASSEMBLED ITEM?",
        text: "You are about to RECEIVE this Assembled Item/s into warehouse stocks!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if(willDelete){
            $.ajax({
                type:'post',
                url:'/assembly/receiveAssembled',
                async: false,
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val()
                },
                data:{
                    'request_number': $('#request_num_details').val()
                },
                success: function(data){
                    if(data == 'true'){
                        for(var q=0; q < var_qty; q++){
                            $.ajax({
                                type:'post',
                                url:'/assembly/addAssembled',
                                async: false,
                                headers: {
                                    'X-CSRF-TOKEN': $("#csrf").val()
                                },
                                data:{
                                    'request_number': $('#request_num_details').val(),
                                    'item_id': $('#item_id_details').val(),
                                    'location_id': $('#warehouse_details').val(),
                                    'serial': $('#serials'+q).val()
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
                                        window.location.href = '/stockrequest';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                    }
                    else{
                        $('#detailsStockRequest').hide();
                        swal("RECEIVE FAILED", "ASSEMBLED ITEM", "error");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stockrequest';
                    }
                    alert(data.responseText);
                }
            });
            $.ajax({
                type:'post',
                url:'/assembly/logAssembled',
                async: false,
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val()
                },
                data:{
                    'request_number': $('#request_num_details').val(),
                    'qty': $("#qty_details").val(),
                    'item_name': $("#item_desc_details").val(),
                    'location_name': $("#warehouse_details option:selected").text()
                },
                success: function(data){
                    if(data == 'true'){
                        $('#detailsStockRequest').hide();
                        swal("RECEIVE SUCCESS", "ASSEMBLED ITEM", "success");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                    else{
                        $('#detailsStockRequest').hide();
                        swal("RECEIVE FAILED", "ASSEMBLED ITEM", "error");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stockrequest';
                    }
                    alert(data.responseText);
                }
            });
        }
    }); 
});

setInterval(checkReqType, 0);
function checkReqType(){
    if($('#detailsStockRequest').is(':visible')){
        var req_type_id = $('#req_type_id_details').val();
        var status_id = $('#status_id_details').val();

        if(req_type_id == '4' || req_type_id == '5'){
            var table = $('#stockDetailsrequest').DataTable();
            var count = 0;

            if(status_id == '1'){
                $("#warning").show();
            }
            if($('#warningdfc').is(':visible')){
                $("#warning").hide();
            }
            $("#proceed_label").hide();
            $("#btnProceed").prop('disabled', false);
            $("#stockDetailsrequest *").prop('disabled', true);
            table.column(4).visible(false);
            var form_data  = $('#stockDetailsrequest').DataTable().rows().data();
            form_data.each(function(value, index){
                if(parseInt(value.qtystock) < parseInt(value.quantity)){
                    $("#btnProceed").prop('disabled', true);
                    count++;
                    return false;
                }
            });
            if(count == 0){
                $("#warning").hide();
            }
        }
    }
}

var items = [];
var item_count = 0;
$('table.stockDetails').DataTable().on('select', function(){});
$('.stockDetails tbody').on('click', 'tr', function(){
    if($("#current_role").val() == '["sales"]'){
        return false;
    }
    if($("#current_role").val() == '["viewer"]'){
        return false;
    }
    var requestStatus = $('#status_id_details').val();
    if((requestStatus == '2' || requestStatus == '3' || requestStatus == '4' || requestStatus == '6' || requestStatus == '7' || requestStatus > 7) && requestStatus != '24'){
        return false;
    }
    var table = $('table.stockDetails').DataTable();
    var data = table.row(this).data();
    var pend = data.pending;
    var stock = data.qtystock;
    var item_id = data.item_id;
    var bal = data.qtybal;
    var mal = data.qtymal;

    if(pend == 0){
        swal('Item is fullfiled!','','success');
    }
    else if(stock == 0){
        if(bal != 0 && mal != 0){
            sweet(
                'Item out of stock!',
                'Request Stock Transfer from Balintawak and/or Malabon.',
                'warning',
                'Go to Stock Transfer',
                '/stocktransfer'
            );
        }
        else if(bal != 0 && mal == 0){
            sweet(
                'Item out of stock!',
                'Request Stock Transfer from Balintawak.',
                'warning',
                'Go to Stock Transfer',
                '/stocktransfer'
            );
        }
        else if(bal == 0 && mal != 0){
            sweet(
                'Item out of stock!',
                'Request Stock Transfer from Malabon.',
                'warning',
                'Go to Stock Transfer',
                '/stocktransfer'
            );
        }
        else{
            swal('Item out of stock!','','error');
        }
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

$('.table.transItems').DataTable().on('select', function(){});
$('.transItems tbody').on('click', 'tr', function(){
    if($("#current_role").val() != '["sales"]'){
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
    if(requestStatus == '9'){
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
            $('#btnSale').prop('disabled', true);
            $('#btnReturn').prop('disabled', true);
        }
        else{
            $('#btnSale').prop('disabled', false);
            $('#btnReturn').prop('disabled', false);
        }
    }
});

$('.table.incItems').DataTable().on('select', function(){});
$('.incItems tbody').on('click', 'tr', function(){
    var requestStatus = $('#status_id_details').val();
    if((requestStatus == '18' || requestStatus == '21') && ($("#current_role").val() == '["admin"]' || $("#current_role").val() == '["encoder"]')){
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
            $('#btnReceiveDfc').prop('disabled', true);
        }
        else{
            $('#btnReceiveDfc').prop('disabled', false);
        }
    }
    if(requestStatus == '17' && $("#current_role").val() == '["sales"]'){
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
    var req_type_id = $('#req_type_id_details').val();
    if(req_type_id == '4' || req_type_id == '5'){
        var form_data  = $('#stockDetailsrequest').DataTable().rows().data();
        form_data.each(function(value, index){
            items.push(value.item_id);
        });
    }
    $("#stockDetailsrequest *").prop('disabled', true);
    $("#proceed_label").hide();
    $("#btnProceed").hide();
    $("#reqContents").empty();
    $("#requestItems").slideDown();
    $('#schedOn').attr('max', maxDate);
    for(var i=0; i < items.length; i++){
        $.ajax({ 
            type:'get', 
            url:'/stockreq', 
            data:{
                'reqnum': reqnum,
                'item_id': items[i]
            }, 
            success: function(data){
                var reqitem = $.map(data.data, function(value, index){ 
                    return [value];
                });

                reqitem.forEach(value => {
                    if(value.qtystock <= value.pending){
                        var l = value.qtystock;
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
                        serial.setAttribute("style", "width: 200px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
                        document.getElementById("reqContents").appendChild(id);
                        document.getElementById("reqContents").appendChild(x);
                        document.getElementById("reqContents").appendChild(y);
                        document.getElementById("reqContents").appendChild(qty);
                        document.getElementById("reqContents").appendChild(uom);
                        document.getElementById("reqContents").appendChild(serial);
                        document.getElementById("reqContents").appendChild(z);
                        $("#item"+j).html(value.item);
                        $("#category"+j).prop('readonly', true);
                        $("#item"+j).prop('readonly', true);
                        $("#qty"+j).prop('readonly', true);
                        $("#uom"+j).prop('readonly', true);
                        $("#location"+j).prop('disabled', true);
                        $("#serial"+j).append("<option value='' selected>Select Serial</option>");
                        let vid = "#serial"+j;
                        $.ajax({
                            type:'get',
                            url:'/setserials',
                            data:{
                                'item_id': value.item_id
                            }, 
                            success: function(d){
                                var s = $.map(d, function(v){
                                    return [v];
                                });
            
                                s.forEach(v => {
                                    if(v.serial == 'N/A'){
                                        $(vid).append($('<option>', {
                                            value: v.id,
                                            text: v.serial+' - '+v.location
                                        }));
                                    }
                                    else{
                                        $(vid).append($('<option>', {
                                            value: v.id,
                                            text: v.serial
                                        }));
                                    }
                                });
                                $(vid).chosen();
                            },
                            error: function(data){
                                if(data.status == 401){
                                    window.location.href = '/stockrequest';
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
                    let id = '#location'+m;
                    $('#serial'+m).on('change', function(){
                        if($('.serials').filter(function(){ return !!this.value; }).length == 0){
                            $('#btnSubmit').prop('disabled', true);
                        }
                        else{
                            $('#btnSubmit').prop('disabled', false);
                        }
                        var serial_id = $(this).val();
                        $.ajax({
                            type:'get', 
                            url:'/setlocation', 
                            data:{
                                'serial_id': serial_id
                            }, 
                            success: function(data){
                                $(id).find('option').remove().end()
                                $(id).append($('<option>', {
                                    value: data.location_id,
                                    text: data.location
                                }));
                            },
                            error: function(data){
                                if(data.status == 401){
                                    window.location.href = '/stockrequest';
                                }
                                alert(data.responseText);
                            }
                        });
                    });
                }
                if(req_type_id == '4' || req_type_id == '5'){
                    setInterval(checkSerials, 0);
                    function checkSerials(){
                        if($('.serials').filter(function(){ return !!this.value; }).length != j){
                            $('#btnSubmit').prop('disabled', true);
                        }
                        else{
                            $('#btnSubmit').prop('disabled', false);
                        }
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
                    else if($("#schedOn").val() > maxDate){
                        swal('Exceed Date Needed deadline!','Select within date range from today up to Date Needed.','error');
                        return false;
                    }
                    else{
                        swal({
                            title: "SCHEDULE STOCK REQUEST?",
                            text: "You are about to SCHEDULE this STOCK REQUEST!",
                            icon: "warning",
                            buttons: true,
                        })
                        .then((willDelete) => {
                            if(willDelete){
                                for(var n=0; n < j; n++){
                                    if($('#serial'+n).val() != ''){
                                        $.ajax({
                                            type:'post',
                                            url:'/prepareItems',
                                            async: false,
                                            headers: {
                                                'X-CSRF-TOKEN': $("#csrf").val()
                                            },
                                            data:{
                                                'request_number': reqnum,
                                                'req_type_id': req_type_id,
                                                'stock_id': $('#serial'+n).val(),
                                                'item_id': $('#item_id'+n).val(),
                                                'qty': $('#qty'+n).val()
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
                                                    window.location.href = '/stockrequest';
                                                }
                                                alert(data.responseText);
                                            }
                                        });
                                    }
                                }
                                $.ajax({
                                    type:'post',
                                    url:'/logSched',
                                    async: false,
                                    headers: {
                                        'X-CSRF-TOKEN': $("#csrf").val()
                                    },
                                    data:{
                                        'request_number': reqnum,
                                        'req_type_id': req_type_id,
                                        'schedOn': $('#schedOn').val()
                                    },
                                    success: function(data){
                                        if(data == 'true'){
                                            $('#detailsStockRequest').hide();
                                            swal("SCHEDULED SUCCESS", "STOCK REQUEST", "success");
                                            setTimeout(function(){location.href="/stockrequest"}, 2000);
                                        }
                                        else{
                                            $('#detailsStockRequest').hide();
                                            swal("SCHEDULED FAILED", "STOCK REQUEST", "error");
                                            setTimeout(function(){location.href="/stockrequest"}, 2000);
                                        }
                                    },
                                    error: function(data){
                                        if(data.status == 401){
                                            window.location.href = '/stockrequest';
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
                    window.location.href = '/stockrequest';
                }
                alert(data.responseText);
            }
        }); 
    }
});

$('#btnBack').on('click', function(){
    var req_type_id = $('#req_type_id_details').val();
    if(req_type_id == '4' || req_type_id == '5'){
        items = [];
    }
    $("#stockDetailsrequest *").prop('disabled', false);
    $("#proceed_label").show();
    $('#btnSubmit').prop('disabled', true);
    $("#requestItems").hide();
    $("#schedOn").val('');
    $("#btnProceed").show();
});

$('.btnReceive').on('click', function(){
    var inc = 'false';
    if(items.length < item_count){
        inc = 'true';
    }
    swal({
        title: "RECEIVE STOCK REQUEST?",
        text: "You are about to RECEIVE this Stock Request!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if(willDelete){
            $.ajax({
                type: 'post',
                url: '/receiveRequest',
                async: false,
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val()
                },
                data:{
                    'request_number': $('#request_num_details').val(),
                    'request_type': $('#req_type_id_details').val(),
                    'inc': inc
                },
                success: function(data){
                    if(data == 'true'){
                        for(var i=0; i < items.length; i++){
                            $.ajax({
                                type: 'post',
                                url: '/receiveItems',
                                async: false,
                                headers: {
                                    'X-CSRF-TOKEN': $("#csrf").val()
                                },
                                data:{
                                    'request_type': $('#req_type_id_details').val(),
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
                                        window.location.href = '/stockrequest';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        scrollReset();
                        $('#detailsStockRequest').hide();
                        $('#detailsStockRequest').modal('dispose');
                        $('#loading').show(); Spinner(); Spinner.show();
                        $.ajax({
                            type: 'post',
                            url: '/logReceive',
                            async: false,
                            headers: {
                                'X-CSRF-TOKEN': $("#csrf").val()
                            },
                            data:{
                                'request_number': $('#request_num_details').val(),
                                'request_type': $('#req_type_id_details').val(),
                                'status': $('#status_id_details').val(),
                                'inc': inc
                            },
                            success: function(data){
                                if(data == 'true'){
                                    $('#loading').hide(); Spinner.hide();
                                    swal("RECEIVE SUCCESS", "STOCK REQUEST", "success");
                                    setTimeout(function(){location.href="/stockrequest"}, 2000);
                                }
                                else{
                                    return false;
                                }
                            },
                            error: function(data){
                                if(data.status == 401){
                                    window.location.href = '/stockrequest';
                                }
                                alert(data.responseText);
                            }
                        });
                    }
                    else{
                        $('#detailsStockRequest').hide();
                        swal("RECEIVE FAILED", "STOCK REQUEST", "error");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stockrequest';
                    }
                    alert(data.responseText);
                }
            });
        }
    });
});

$('#btnReceiveDfc').on('click', function(){
    var inc = 'false';
    var inctype = 'COMPLETE';
    if(items.length < item_count){
        inc = 'true';
        inctype = 'INCOMPLETE';
    }
    swal({
        title: "RECEIVE "+inctype+" DEFECTIVE PARTS?",
        text: "You are about to RECEIVE these DEFECTIVE PARTS!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if(willDelete){
            $.ajax({
                type: 'post',
                url: '/receiveDefective',
                async: false,
                headers: {
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
                                url: '/receiveDfcItems',
                                async: false,
                                headers: {
                                    'X-CSRF-TOKEN': $("#csrf").val()
                                },
                                data:{
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
                                        window.location.href = '/stockrequest';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        scrollReset();
                        $('#detailsStockRequest').hide();
                        $('#detailsStockRequest').modal('dispose');
                        $('#loading').show(); Spinner(); Spinner.show();
                        $.ajax({
                            type: 'post',
                            url: '/logReceiveDfc',
                            async: false,
                            headers: {
                                'X-CSRF-TOKEN': $("#csrf").val()
                            },
                            data:{
                                'request_number': $('#request_num_details').val(),
                                'inc': inc
                            },
                            success: function(data){
                                if(data == 'true'){
                                    $('#loading').hide(); Spinner.hide();
                                    swal("RECEIVED "+inctype, "DEFECTIVE PARTS", "success");
                                    setTimeout(function(){location.href="/stockrequest"}, 2000);
                                }
                                else{
                                    return false;
                                }
                            },
                            error: function(data){
                                if(data.status == 401){
                                    window.location.href = '/stockrequest';
                                }
                                alert(data.responseText);
                            }
                        });
                    }
                    else{
                        $('#detailsStockRequest').hide();
                        swal("RECEIVE FAILED", "DEFECTIVE PARTS", "error");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stockrequest';
                    }
                    alert(data.responseText);
                }
            });
        }
    });
});

$('#btnSale').on('click', function(){
    $('#referenceModal').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#referenceModal').modal('show');
});

$('#btnReference').on('click', function(){
    var reference = $.trim($('#x_reference').val()).toUpperCase();
    if(reference == ''){
        swal("SO/PO NUMBER REQUIRED", "Please fill up required field!", "error");
        return false;
    }
    else{
        swal({
            title: "FOR SALE STOCK REQUEST? CONFIRM REFERENCE PO/SO NO.: "+reference,
            text: "You are about to SELL the selected item/s of this STOCK REQUEST! Items that are not selected will be returned to warehouse stocks. CONTINUE?",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                    type:'post',
                    url:'/saleRequest',
                    async: false,
                    headers: {
                        'X-CSRF-TOKEN': $("#csrf").val()
                    },
                    data:{
                        'request_number': $('#request_num_details').val(),
                        'reference': reference,
                        'check': 'false'
                    },
                    success: function(data){
                        if(data == 'true'){
                            for(var i=0; i < items.length; i++){
                                $.ajax({
                                    type: 'post',
                                    url: '/sellItems',
                                    async: false,
                                    headers: {
                                        'X-CSRF-TOKEN': $("#csrf").val()
                                    },
                                    data:{
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
                                            window.location.href = '/stockrequest';
                                        }
                                        alert(data.responseText);
                                    }
                                });
                            }
                            scrollReset();
                            $('#referenceModal').hide();
                            $('#referenceModal').modal('dispose');
                            $('#detailsStockRequest').hide();
                            $('#detailsStockRequest').modal('dispose');
                            $('#loading').show(); Spinner(); Spinner.show();
                            $.ajax({
                                type:'post',
                                url:'/logSold',
                                async: false,
                                headers: {
                                    'X-CSRF-TOKEN': $("#csrf").val()
                                },
                                data:{
                                    'request_number': $('#request_num_details').val()
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide(); Spinner.hide();
                                        swal("SALE SUCCESS", "STOCK REQUEST", "success");
                                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                                    }
                                    else{
                                        return false;
                                    }
                                },
                                error: function(data){
                                    if(data.status == 401){
                                        window.location.href = '/stockrequest';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else if(data == 'duplicate'){
                            swal("INVALID ENTRY", "Reference SO/PO Number already exists! Please double check the SO/PO Number and try again.", "error");
                            return false;
                        }
                        else{
                            $('#referenceModal').hide();
                            $('#detailsStockRequest').hide();
                            swal("SALE FAILED", "STOCK REQUEST", "error");
                            setTimeout(function(){location.href="/stockrequest"}, 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/stockrequest';
                        }
                        alert(data.responseText);
                    }
                });
            }
        }); 
    }
});

$('#btnReturn').on('click', function(){
    var all = 'false';
    if(items.length == item_count){
        all = 'true';
    }
    swal({
        title: "RETURN STOCK REQUEST?",
        text: "You are about to RETURN the selected item/s of this STOCK REQUEST! Items that are not selected will remain listed for either sales or return. CONTINUE?",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if(willDelete){
            $.ajax({
                type:'post',
                url:'/returnRequest',
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val()
                },
                data:{
                    'request_number': $('#request_num_details').val(),
                    'all': all
                },
                success: function(data){
                    if(data == 'true'){
                        for(var i=0; i < items.length; i++){
                            $.ajax({
                                type: 'post',
                                url: '/returnItems',
                                async: false,
                                headers: {
                                    'X-CSRF-TOKEN': $("#csrf").val()
                                },
                                data:{
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
                                        window.location.href = '/stockrequest';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        scrollReset();
                        $('#detailsStockRequest').hide();
                        $('#detailsStockRequest').modal('dispose');
                        $('#loading').show(); Spinner(); Spinner.show();
                        $.ajax({
                            type:'post',
                            url:'/logReturn',
                            async: false,
                            headers: {
                                'X-CSRF-TOKEN': $("#csrf").val()
                            },
                            data:{
                                'request_number': $('#request_num_details').val()
                            },
                            success: function(data){
                                if(data == 'true'){
                                    $('#loading').hide(); Spinner.hide();
                                    swal("RETURN SUCCESS", "STOCK REQUEST", "success");
                                    setTimeout(function(){location.href="/stockrequest"}, 2000);
                                }
                                else{
                                    return false;
                                }
                            },
                            error: function(data){
                                if(data.status == 401){
                                    window.location.href = '/stockrequest';
                                }
                                alert(data.responseText);
                            }
                        });
                    }
                    else{
                        $('#detailsStockRequest').hide();
                        swal("RETURN FAILED", "STOCK REQUEST", "error");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stockrequest';
                    }
                    alert(data.responseText);
                }
            });
        }
    }); 
});

$("#btnShowAttachment").on('click', function(){
    $("#btnShowAttachment").hide();
    $("#btnHideAttachment").show();
    $("#attachmentModal").slideDown();
});

$("#btnHideAttachment").on('click', function(){
    $("#btnShowAttachment").show();
    $("#btnHideAttachment").hide();
    $("#attachmentModal").slideUp();
});

$("#btnShowDetails").on('click', function(){
    $("#divAssembly").hide();
    $("#request_info").show();
    $("#transitItemsModal").show();
});

$("#btnHideDetails").on('click', function(){
    $("#divAssembly").show();
    $("#request_info").hide();
    $("#transitItemsModal").hide();
});

$("#showMore").on('click', function(){
    $("#request_info").show();
    $("#receivedItemsModal").show();
    $("#showMore").hide();
    $("#showLess").show();
});

$("#showLess").on('click', function(){
    $("#request_info").hide();
    $("#receivedItemsModal").hide();
    $("#showMore").show();
    $("#showLess").hide();
});

$('#btnDefDetails').on('click', function(){
    window.location.href = '/stockrequest?request_number='+$('#asm_request_num_details').val();
});

$('#btnPending').on('click', function(){
    $.ajax({
        type:'get', 
        url:'/getLink', 
        data:{
            'request_number': $('#request_num_details').val()
        }, 
        success: function(data){
            window.location.href = '/stockrequest?request_number='+data;
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/stockrequest';
            }
            alert(data.responseText);
        }
    });
});

$('.btnPrint').on('click', function(){
    window.location.href = '/printRequest?request_number='+$('#request_num_details').val();
});