var minDate, maxDate, editMode, j;
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
    Swal.fire({
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
    Swal.fire({
        title: copyText.value,
        text: "Copied to Clipboard!",
        icon: "success",
        timer: 2000
    });
}

function sweet(title, text, icon, btnName1, btnName2, url1, url2){
    if(!btnName2 && !url2){
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: btnName1,
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                window.location.href = url1;
            }
        });
    }
    else{
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: btnName1,
            denyButtonText: btnName2,
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                window.location.href = url1;
            }
            else if(result.isDenied){
                window.location.href = url2;
            }
        });
    }
}

function validate_fileupload(reference_upload){
    $('.upload_label').html('Upload PDF or Image less than 5MB each');
    var files_length = $("#reference_upload").get(0).files.length;
    var error_ext = 0;
    var error_mb = 0;
    for(var i = 0; i < files_length; ++i) {
        var file1=$("#reference_upload").get(0).files[i].name;
        var file_size = $("#reference_upload").get(0).files[i].size;
        var ext = file1.split('.').pop().toLowerCase();
        if($.inArray(ext,['pdf','png','jpg','jpeg'])===-1){
            error_ext++;
        }
        if(file_size > 5242880){
            error_mb++;
        }
    }
    if(error_ext > 0 && error_mb > 0){
        Swal.fire('INVALID file type AND EXCEEDED maximum file size (5MB)!', 'Please upload file/s with valid file type like the following: pdf, png, jpg or jpeg; AND with file size not greater than 5MB each.', 'error');
        $('#reference_upload').val('');
        $('#reference_upload').focus();
        $('.disupload').hide();
        $('.disupload1').hide();
        $('#xbtn_upload').css("width", "100%");
        $('#btnUploadForm').css("width", "280px");
        return false;
    }
    else if(error_ext > 0){
        Swal.fire('INVALID file type!', 'Please upload file/s with valid file type like the following: pdf, png, jpg or jpeg.', 'error');
        $('#reference_upload').val('');
        $('#reference_upload').focus();
        $('.disupload').hide();
        $('.disupload1').hide();
        $('#xbtn_upload').css("width", "100%");
        $('#btnUploadForm').css("width", "280px");
        return false;
    }
    else if(error_mb > 0){
        Swal.fire('EXCEEDED maximum file size (5MB)!', 'Please upload valid file/s with file size not greater than 5MB each.', 'error');
        $('#reference_upload').val('');
        $('#reference_upload').focus();
        $('.disupload').hide();
        $('.disupload1').hide();
        $('#xbtn_upload').css("width", "100%");
        $('#btnUploadForm').css("width", "280px");
        return false;
    }
    else{
        if(files_length == 1){
            $('.upload_label').html(reference_upload.value.split("\\").pop());
            $('#btnUploadForm').css("width", "250px");
            if($('#referenceModal').is(':hidden')){
                $('.disupload').show();
            }
            else{
                $('.disupload1').show();
                $('#xbtn_upload').css("width", "263px");
            }
        }
        else if(files_length > 1){
            $('.upload_label').html('UPLOADED ('+files_length+') FILES');
            $('#btnUploadForm').css("width", "250px");
            if($('#referenceModal').is(':hidden')){
                $('.disupload').show();
            }
            else{
                $('.disupload1').show();
                $('#xbtn_upload').css("width", "263px");
            }
        }
        else{
            $('.upload_label').html('Upload PDF or Image less than 5MB each');
            $('.disupload').hide();
            $('.disupload1').hide();
            $('#xbtn_upload').css("width", "100%");
            $('#btnUploadForm').css("width", "280px");
        }
        return true;
    }
}

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

setInterval(runFunction, 0);
function runFunction(){
    if($('#newStockRequest').is(':visible') && $("#current_role").val() == 'sales'){
        var check1, check2, check3;
        var needdate = $('#needdate').val();
        var request_type = $('#request_type').val();
        var client_name = $.trim($('#client_name').val());
        var location_name = $.trim($('#location').val());
        var contact = $.trim($('#contact').val());
        var remarks = $.trim($('#remarks').val());
        var reference = $.trim($('#reference').val());
        var reference_upload = $('#reference_upload').val();
        var asset_reqby_email = $.trim($('#asset_reqby_email').val()).toLowerCase();
        var asset_reqby_verify = $.trim($('#asset_reqby_verify').val()).toLowerCase();
        if($('.reference_field').is(':visible')){
            if(needdate && request_type && client_name && location_name && contact && reference && reference_upload && asset_reqby_email && asset_reqby_verify){
                $('.header_label').hide();
                check1 = true;
            }
            else{
                $('.header_label').show();
                check1 = false;
            }
        }
        else{
            if(needdate && request_type && client_name && location_name && contact && asset_reqby_email && asset_reqby_verify){
                $('.header_label').hide();
                check1 = true;
            }
            else{
                $('.header_label').show();
                check1 = false;
            }
        }
        if(asset_reqby_email && !validateEmail(asset_reqby_email)){
            $('#valid_text').html('Client Email Address has an invalid format!');
            $('.valid_label').show();
            check2 = false;
        }
        else{
            $('.valid_label').hide();
            check2 = true;
        }
        if(asset_reqby_email && asset_reqby_verify && asset_reqby_email != asset_reqby_verify){
            $('#verify_text').html('Client Email Address does not match the re-entered email confirmation text field!');
            $('.verify_label').show();
            check3 = false;
        }
        else{
            $('.verify_label').hide();
            check3 = true;
        }
        if(check1 == true && check2 == true && check3 == true && item_count == 0){
            $('#requestDetails').show();
        }
        else{
            $('#requestDetails').hide();
        }
        if(check1 == true && check2 == true && check3 == true && item_count != 0){
            $("#btnReissue").prop('disabled', false);
        }
        else{
            $("#btnReissue").prop('disabled', true);
        }
    }
    if($('#newStockRequest').is(':visible') && ($("#current_role").val() == 'admin' || $("#current_role").val() == 'encoder')){
        var check1, check2, check3;
        var needdate = $('#needdate').val();
        var asset_reqby = $.trim($('#asset_reqby').val());
        var asset_apvby = $.trim($('#asset_apvby').val());
        var asset_reqby_email = $.trim($('#asset_reqby_email').val()).toLowerCase();
        var asset_reqby_verify = $.trim($('#asset_reqby_verify').val()).toLowerCase();
        var asset_apvby_email = $.trim($('#asset_apvby_email').val()).toLowerCase();
        var asset_apvby_verify = $.trim($('#asset_apvby_verify').val()).toLowerCase();
        var reference_upload = $('#reference_upload').val();
        if(needdate && asset_reqby && asset_apvby && asset_reqby_email && asset_reqby_verify && asset_apvby_email && asset_apvby_verify && reference_upload){
            $('.header_label').hide();
            check1 = true;
        }
        else{
            $('.header_label').show();
            check1 = false;
        }
        if(asset_reqby_email && asset_apvby_email && !validateEmail(asset_reqby_email) && !validateEmail(asset_apvby_email)){
            $('#valid_text').html('Requester and Approver Email Addresses both have invalid formats!');
            $('.valid_label').show();
            check2 = false;
        }
        else if(asset_reqby_email && !validateEmail(asset_reqby_email)){
            $('#valid_text').html('Requester Email Address has an invalid format!');
            $('.valid_label').show();
            check2 = false;
        }
        else if(asset_apvby_email && !validateEmail(asset_apvby_email)){
            $('#valid_text').html('Approver Email Address has an invalid format!');
            $('.valid_label').show();
            check2 = false;
        }
        else{
            $('.valid_label').hide();
            check2 = true;
        }
        if(asset_reqby_email && asset_reqby_verify && asset_apvby_email && asset_apvby_verify && asset_reqby_email != asset_reqby_verify && asset_apvby_email != asset_apvby_verify){
            $('#verify_text').html('Requester and Approver Email Addresses do not match the re-entered email confirmation text fields!');
            $('.verify_label').show();
            check3 = false;
        }
        else if(asset_reqby_email && asset_reqby_verify && asset_reqby_email != asset_reqby_verify){
            $('#verify_text').html('Requester Email Address does not match the re-entered email confirmation text field!');
            $('.verify_label').show();
            check3 = false;
        }
        else if(asset_apvby_email && asset_apvby_verify && asset_apvby_email != asset_apvby_verify){
            $('#verify_text').html('Approver Email Address does not match the re-entered email confirmation text field!');
            $('.verify_label').show();
            check3 = false;
        }
        else{
            $('.verify_label').hide();
            check3 = true;
        }
        if(check1 == true && check2 == true && check3 == true){
            $('#requestDetails').show();
        }
        else{
            $('#requestDetails').hide();
        }
    }
    if($('#detailsStockRequest').is(':visible') && $("#current_role").val() == 'sales' && $("#status_details").val() == 'FOR APPROVAL' && editMode == true){
        var needdate = $('#needdate_details').val();
        var request_type = $('#request_type_details').val();
        var client_name = $.trim($('#client_name_details').val());
        var location_name = $.trim($('#location_details').val());
        var contact = $.trim($('#contact_details').val());
        var remarks = $.trim($('#remarks_details').val());
        var reference = $.trim($('#reference_details').val());
        var reference_upload = $('#reference_upload').val();
        if(request_type == 'SALES' && $('.reupload').is(':hidden')){
            if(needdate && client_name && location_name && contact && reference){
                $('#btnSaveChanges').prop('disabled', false);
                $('.header_label').hide();
            }
            else{
                $('#btnSaveChanges').prop('disabled', true);
                $('.header_label').show();
            }
        }
        else if(request_type == 'SALES' && $('.reupload').is(':visible')){
            if(needdate && client_name && location_name && contact && reference && reference_upload){
                $('#btnSaveChanges').prop('disabled', false);
                $('.header_label').hide();
            }
            else{
                $('#btnSaveChanges').prop('disabled', true);
                $('.header_label').show();
            }
        }
        else{
            if(needdate && client_name && location_name && contact){
                $('#btnSaveChanges').prop('disabled', false);
                $('.header_label').hide();
            }
            else{
                $('#btnSaveChanges').prop('disabled', true);
                $('.header_label').show();
            }
        }
    }
    if($('#referenceModal').is(':visible')){
        var reference = $.trim($('#x_reference').val());
        var reference_upload = $('#reference_upload').val();
        if(reference && reference_upload){
            $('#btnReference').show();
            $('#reference_note').hide();
        }
        else{
            $('#btnReference').hide();
            $('#reference_note').show();
        }
    }
    if($('#requestDetails').is(':visible')){
        var request_type = $('#request_type').val();
        if(request_type == '2' || request_type == '8'){
            $('.classWarranty').show();
        }
        else{
            $('.classWarranty').hide();
        }
    }
}

setInterval(checkLocation, 0);
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
    if(reqtype == '2' || reqtype == '8'){
        $('.reference_field').show();
        $('.divAdjust').hide();
    }
    else{
        $('.reference_field').hide();
        $('.divAdjust').show();
        $('#reference').val('');
        $('#reference_upload').val('');
        $('.upload_label').html('Upload PDF or Image less than 5MB each');
        $('.disupload').hide();
    }
    $("#categoryReq").val('');
    $("#itemReq").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
    $('#prodcode').val('');
    $("#uom").val('');
    $("#qtyReq").val('');
    $("#warrantyReq").val('');
    $('#warrantyDetails').hide();
    $("#stockRequestTable tbody tr").remove();
    if($('#stockRequestTable tbody').children().length==0){
        $('#stockRequestTable').hide();
        $('#stockRequestDiv').removeClass();
        $('.btnCloseCancel').hide();
        $('#btnSave').hide();
        $('.submit_label').show();
    }
});

$('#categoryReq').on('change', function(){
    var id = $('#categoryReq').val();
    var descOp = " ";
    $('#prodcode').val('');
    $('#uom').val('');
    $("#qtyReq").val('');
    $.ajax({ 
        type: 'get', 
        url: '/itemsreq', 
        data:{
            'category_id':id
        }, 
        success: function(data){
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
        type: 'get', 
        url: '/setuom', 
        data:{
            'item_id': item_id,
        }, 
        success: function(data){
            $('#prodcode').val(data[0].prodcode);
            $('#uom').val(data[0].uom);
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/stockrequest';
            }
            alert(data.responseText);
        }
    });
});

$('#warrantyReq').on('change', function(){
    var id = $(this).val();
    if(id == 0 || id == ''){
        $('#warrantyDetails').hide();
        return false;
    }
    $('#warrantyDetails').show();
    $('.listInclusive').hide();
    $.ajax({
        type: 'get', 
        url: '/getInclusive', 
        data:{
            id: id
        }, 
        success: function(data){
            $('.warrantyName').val((data[0].Warranty_Name).toUpperCase());
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

$(".add-row").on('click', function(){
    var request_type = $('#request_type').val();
    var category = $("#categoryReq option:selected").text();
    var item = $("#itemReq option:selected").text();
    var warranty = $("#warrantyReq option:selected").text();
    var category_id = $("#categoryReq").val();
    var item_id = $("#itemReq").val();
    var warranty_id = $("#warrantyReq").val();
    var prodcode = $("#prodcode").val();
    var uom = $("#uom").val();
    let qty = $("#qtyReq").val();
    if(request_type == '2' || request_type == '8'){
        var markup = "<tr><td style='display: none;'>" + category_id + "</td><td style='display: none;'>" + item_id + "</td><td style='display: none;'>" + warranty_id + "</td><td style='display: none;'>" + category + "</td><td>" + prodcode + "</td><td>" + item + "</td><td>" + qty + "</td><td>" + uom + "</td><td>" + warranty + "</td><td><button type='button' style='zoom: 80%;' class='delete-row btn btn-danger bp'>REMOVE</button></td></tr>";
    }
    else{
        var markup = "<tr><td style='display: none;'>" + category_id + "</td><td style='display: none;'>" + item_id + "</td><td style='display: none;'></td><td style='display: none;'>" + category + "</td><td>" + prodcode + "</td><td>" + item + "</td><td>" + qty + "</td><td>" + uom + "</td><td style='display: none;'></td><td><button type='button' style='zoom: 80%;' class='delete-row btn btn-danger bp'>REMOVE</button></td></tr>";
    }
    var ctr = 'false';
    if((request_type == '2' || request_type == '8') && (category == "Select Category" || item == "Select Item" || qty == "" || qty == "0" || uom == "" || warranty == "Select Warranty Type")){
        Swal.fire('REQUIRED','Please fill up all required item details!','error');
        return false;
    }
    else if((request_type == '3' || request_type == '7') && (category == "Select Category" || item == "Select Item" || qty == "" || qty == "0" || uom == "")){
        Swal.fire('REQUIRED','Please fill up all required item details!','error');
        return false;
    }
    else{
        var table = document.getElementById('stockRequestTable');
        var count = table.rows.length;
        for(i = 1; i < count; i++){
            var objCells = table.rows.item(i).cells;
            if(item_id==objCells.item(1).innerHTML){
                objCells.item(6).innerHTML = parseInt(objCells.item(6).innerHTML) + parseInt(qty);
                objCells.item(8).innerHTML = warranty;
                objCells.item(2).innerHTML = warranty_id;
                ctr = 'true';
                category = $("#categoryReq").val('');
                item = $("#itemReq").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
                prodcode = $('#prodcode').val('');
                uom = $('#uom').val('');
                qty = $("#qtyReq").val('');
                warranty = $("#warrantyReq").val('');
                $('#warrantyDetails').hide();
                return false;
            }
            else {
                ctr = 'false';
            }
        }
        if(ctr == 'false')
        { $("#stockRequestTable tbody").append(markup); }
        category = $("#categoryReq").val('');
        item = $("#itemReq").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
        prodcode = $('#prodcode').val('');
        uom = $('#uom').val('');
        qty = $("#qtyReq").val('');
        warranty = $("#warrantyReq").val('');
        $('#warrantyDetails').hide();
        $('#stockRequestTable').show();
        $('#stockRequestDiv').toggle();
        $('.btnCloseCancel').show();
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
        $('.btnCloseCancel').hide();
        $('#btnSave').hide();
        $('.submit_label').show();
    }
});

$('#btnSave').on('click', function(){
    var warntext = '';
    var email1 = 'true';
    var email2 = 'true';
    var apiKey = $('#apiKey').val();
    if($("#current_role").val() == 'admin' || $("#current_role").val() == 'encoder'){
        var needdate = $('#needdate').val();
        var request_type = $('#request_type').val();
        var asset_reqby = $.trim($('#asset_reqby').val());
        var asset_apvby = $.trim($('#asset_apvby').val());
        var asset_reqby_email = $.trim($('#asset_reqby_email').val()).toLowerCase();
        var asset_reqby_verify = $.trim($('#asset_reqby_verify').val()).toLowerCase();
        var asset_apvby_email = $.trim($('#asset_apvby_email').val()).toLowerCase();
        var asset_apvby_verify = $.trim($('#asset_apvby_verify').val()).toLowerCase();
        var reference_upload = $('#reference_upload').val();
        $('#loading').show();
        setTimeout(function(){
            if(emailProvider(asset_reqby_email)){
                $.ajax({
                    headers:{
                        Authorization: "Bearer " + apiKey
                    },
                    async: false,
                    type: 'GET',
                    url: 'https://isitarealemail.com/api/email/validate?email='+asset_reqby_email,
                    success: function(data){
                        if(data.status == 'invalid'){
                            email1 = 'false';
                        }
                        else{
                            email1 = 'true';
                        }
                    }
                });
            }
            else{
                email1 = 'unknown';
            }
            if(emailProvider(asset_apvby_email)){
                $.ajax({
                    headers:{
                        Authorization: "Bearer " + apiKey
                    },
                    async: false,
                    type: 'GET',
                    url: 'https://isitarealemail.com/api/email/validate?email='+asset_apvby_email,
                    success: function(data){
                        if(data.status == 'invalid'){
                            email2 = 'false';
                        }
                        else{
                            email2 = 'true';
                        }
                    }
                });
            }
            else{
                email2 = 'unknown';
            }
            $('#loading').hide();
            if(email1 == 'false' && email2 == 'false'){
                Swal.fire('NON-EXISTENT EMAIL','Requester and Approver Email Addresses are both non-existent!','error');
                return false;
            }
            if(email1 == 'false'){
                Swal.fire('NON-EXISTENT EMAIL','Requester Email Address does not exist!','error');
                return false;
            }
            if(email2 == 'false'){
                Swal.fire('NON-EXISTENT EMAIL','Approver Email Address does not exist!','error');
                return false;
            }
            if(email1 == 'unknown' && email2 == 'unknown'){
                warntext = ' <br><strong style="color: red;">WARNING: Requester and Approver Email Addresses are both unverified! Continue?</strong>';
            }
            if(email1 == 'unknown' && email2 != 'unknown'){
                warntext = ' <br><strong style="color: red;">WARNING: Requester Email Address is not verified! Continue?</strong>';
            }
            if(email1 != 'unknown' && email2 == 'unknown'){
                warntext = ' <br><strong style="color: red;">WARNING: Approver Email Address is not verified! Continue?</strong>';
            }
            if(needdate < minDate){
                Swal.fire('Minimum Date is today!','Select within date range from today onwards.','error');
                return false;
            }
            Swal.fire({
                title: "SUBMIT STOCK REQUEST?",
                html: "Please review the details of your request. Click 'Confirm' button to submit; otherwise, click 'Cancel' button."+warntext,
                icon: "warning",
                showCancelButton: true,
                cancelButtonColor: '#3085d6',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Confirm',
                allowOutsideClick: false
            })
            .then((result) => {
                if(result.isConfirmed){
                    $.ajax({
                        type:'post',
                        url:'/saveReqNum',
                        async: false,
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{
                            'request_number': $('#request_num').val(),
                            'needdate': needdate,
                            'request_type': request_type,
                            'asset_reqby': asset_reqby,
                            'asset_apvby': asset_apvby,
                            'asset_reqby_email': asset_reqby_email,
                            'asset_apvby_email': asset_apvby_email
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
                                        headers:{
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        data:{
                                            'request_number': $('#request_num').val(),
                                            'category': value[0],
                                            'item': value[1],
                                            'warranty': value[2],
                                            'quantity': value[6]
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
                                if(!reference_upload){
                                    scrollReset();
                                    $('#newStockRequest').modal('hide');
                                    $('#loading').show();
                                    $.ajax({
                                        type:'post',
                                        url:'/asset/logSave',
                                        headers:{
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        data:{
                                            'request_number': $('#request_num').val()
                                        },
                                        success: function(data){
                                            if(data == 'true'){
                                                $('#loading').hide();
                                                Swal.fire("SUBMIT SUCCESS", "FIXED ASSET STOCK REQUEST", "success");
                                                setTimeout(function(){location.href="/stockrequest"}, 2000);
                                            }
                                            else{
                                                $('#loading').hide();
                                                Swal.fire("SUBMIT FAILED", "FIXED ASSET STOCK REQUEST", "error");
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
                                else{
                                    $('#newStockRequest').modal('hide');
                                    $('#loading').show();
                                    $('#btnUpload').click();
                                }
                            }
                            else if(data == 'xemail'){
                                Swal.fire("INVALID REQUESTER AND APPROVER EMAIL ADDRESSES", "Enter valid email addresses!", "error");
                                return false;
                            }
                            else if(data == 'xemail1'){
                                Swal.fire("INVALID REQUESTER EMAIL", "Enter a valid email address!", "error");
                                return false;
                            }
                            else if(data == 'xemail2'){
                                Swal.fire("INVALID APPROVER EMAIL", "Enter a valid email address!", "error");
                                return false;
                            }
                            else{
                                $('#newStockRequest').hide();
                                Swal.fire("SUBMIT FAILED", "FIXED ASSET STOCK REQUEST", "error");
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
        }, 500);
    }
    else{
        var needdate = $('#needdate').val();
        var request_type = $('#request_type').val();
        var client_name = $.trim($('#client_name').val());
        var location_name = $.trim($('#location').val());
        var contact = $.trim($('#contact').val());
        var remarks = $.trim($('#remarks').val());
        var asset_reqby_email = $.trim($('#asset_reqby_email').val()).toLowerCase();
        var asset_reqby_verify = $.trim($('#asset_reqby_verify').val()).toLowerCase();
        var reference = ($.trim($('#reference').val()).toUpperCase().split("\n")).join(', ');
        var reference_upload = $('#reference_upload').val();
        $('#loading').show();
        setTimeout(function(){
            if(emailProvider(asset_reqby_email)){
                $.ajax({
                    headers:{
                        Authorization: "Bearer " + apiKey
                    },
                    async: false,
                    type: 'GET',
                    url: 'https://isitarealemail.com/api/email/validate?email='+asset_reqby_email,
                    success: function(data){
                        if(data.status == 'invalid'){
                            email1 = 'false';
                        }
                        else{
                            email1 = 'true';
                        }
                    }
                });
            }
            else{
                email1 = 'unknown';
            }
            $('#loading').hide();
            if(email1 == 'false'){
                Swal.fire('NON-EXISTENT EMAIL','Client Email Address does not exist!','error');
                return false;
            }
            if(email1 == 'unknown'){
                warntext = ' <br><strong style="color: red;">WARNING: Client Email Address is not verified! Continue?</strong>';
            }
            if(needdate < minDate){
                Swal.fire('Minimum Date is today!','Select within date range from today onwards.','error');
                return false;
            }
            Swal.fire({
                title: "SUBMIT STOCK REQUEST?",
                html: "Please review the details of your request. Click 'Confirm' button to submit; otherwise, click 'Cancel' button."+warntext,
                icon: "warning",
                showCancelButton: true,
                cancelButtonColor: '#3085d6',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Confirm',
                allowOutsideClick: false
            })
            .then((result) => {
                if(result.isConfirmed){
                    $.ajax({
                        type:'post',
                        url:'/saveReqNum',
                        async: false,
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{
                            'request_number': $('#request_num').val(),
                            'needdate': needdate,
                            'request_type': request_type,
                            'client_name': client_name,
                            'location': location_name,
                            'contact': contact,
                            'remarks': remarks,
                            'asset_reqby_email': asset_reqby_email,
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
                                        headers:{
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        data:{
                                            'request_number': $('#request_num').val(),
                                            'category': value[0],
                                            'item': value[1],
                                            'warranty': value[2],
                                            'quantity': value[6]
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
                                if(!reference_upload){
                                    scrollReset();
                                    $('#newStockRequest').modal('hide');
                                    $('#loading').show();
                                    $.ajax({
                                        type:'post',
                                        url:'/logSave',
                                        headers:{
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        data:{
                                            'request_number': $('#request_num').val()
                                        },
                                        success: function(data){
                                            if(data == 'true'){
                                                $('#loading').hide();
                                                Swal.fire("SUBMIT SUCCESS", "STOCK REQUEST", "success");
                                                setTimeout(function(){location.href="/stockrequest"}, 2000);
                                            }
                                            else{
                                                $('#loading').hide();
                                                Swal.fire("SUBMIT FAILED", "STOCK REQUEST", "error");
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
                                else{
                                    $('#newStockRequest').modal('hide');
                                    $('#loading').show();
                                    $('#btnUpload').click();
                                }
                            }
                            else{
                                $('#newStockRequest').hide();
                                Swal.fire("SUBMIT FAILED", "STOCK REQUEST", "error");
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
        }, 500);
    }
});

$(document).on('click', '#btnSaveChanges', function(){
    var needdate = $('#needdate').val();
    var client_name = $('#client_name').val();
    var location_name = $('#location').val();
    var contact = $('#contact').val();
    var remarks = $('#remarks').val();
    var reference = $('#reference').val();
    var needdate_details = $('#needdate_details').val();
    var client_name_details = $.trim($('#client_name_details').val());
    var location_details = $.trim($('#location_details').val());
    var contact_details = $.trim($('#contact_details').val());
    var remarks_details = $.trim($('#remarks_details').val());
    var reference_details = ($.trim($('#reference_details').val()).toUpperCase().split("\n")).join(', ');
    var reference_upload = $('#reference_upload').val();
    if($('.reupload').is(':hidden')){
        if(needdate == needdate_details && client_name == client_name_details && location_name == location_details && contact == contact_details && remarks == remarks_details && reference == reference_details){
            Swal.fire("NO CHANGES FOUND", "Stock Request Details are still all the same!", "error");
            return false;
        }
    }
    Swal.fire({
        title: "EDIT STOCK REQUEST DETAILS?",
        text: "Please review the details of your request. Click 'Confirm' button to submit; otherwise, click 'Cancel' button.",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            scrollReset();
            $('#detailsStockRequest').modal('hide');
            $('#loading').show();
            $.ajax({
                type:'post',
                url:'/editRequest',
                async: false,
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    'request_number': $('#request_num_details').val(),
                    'needdate_orig': needdate,
                    'client_name_orig': client_name,
                    'location_orig': location_name,
                    'contact_orig': contact,
                    'remarks_orig': remarks,
                    'reference_orig': reference,
                    'needdate': needdate_details,
                    'client_name': client_name_details,
                    'location': location_details,
                    'contact': contact_details,
                    'remarks': remarks_details,
                    'reference': reference_details,
                    'reference_upload': reference_upload
                },
                success: function(data){
                    $('#loading').hide();
                    if(data == 'true'){
                        if(reference_upload){
                            $('#loading').show();
                            $('#btnUpload').click();
                        }
                        else{
                            Swal.fire("EDIT SUCCESS", "STOCK REQUEST", "success");
                            setTimeout(function(){location.href="/stockrequest"}, 2000);
                        }
                    }
                    else{
                        $('#newStockRequest').hide();
                        Swal.fire("EDIT FAILED", "STOCK REQUEST", "error");
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
    })
});

$(document).on('click', '.disupload', function(){
    $('#reference_upload').val('');
    $('.upload_label').html('Upload PDF or Image less than 5MB each');
    $('.disupload').hide();
    $('#btnUploadForm').css("width", "280px");
});

$(document).on('click', '.disupload1', function(){
    $('#reference_upload').val('');
    $('.upload_label').html('Upload PDF or Image less than 5MB each');
    $('.disupload1').hide();
    $('#xbtn_upload').css("width", "100%");
});

$(document).on('click', '#btnRemoveAttachment', function(){
    Swal.fire({
        title: "REMOVE ATTACHMENTS?",
        text: "You are about to REMOVE your ATTACHMENT SO/PO!\n This will be permanently deleted from the system.",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $("#attachmentModal").slideUp();
            $("#btnShowAttachment").hide();
            $("#btnHideAttachment").hide();
            $(".reupload").show();
        }
    });   
});

$('.btnClose').on('click', function(){
    window.location.href = '/stockrequest';
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
    if(origserial == newserial){
        Swal.fire("NO CHANGES FOUND", "Item Serial is still the same!", "error");
        return false;
    }
    if(!newserial || newserial == ''){
        Swal.fire("SERIAL REQUIRED", "Item Serial field cannot be left blank!", "error");
        return false;
    }
    if(['N/A', 'N /A', 'N/ A', 'N / A', 'NA', 'N A', 'NONE', 'N O N E'].includes(newserial) == true || newserial.length < 5){
        Swal.fire('INVALID ENTRY','Please enter only valid information!','error');
        return false;
    }
    if(!newserial.match(/\d+/g) && newserial){
        Swal.fire("INVALID ENTRY", "Item Serial should at least contain numeric characters!", "error");
        return false;
    }
    Swal.fire({
        title: "Confirm Serial: "+newserial+'?',
        text: "Click 'Confirm' button to submit; otherwise, click 'Cancel' button to review details.",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            scrollReset();
            $('#editSerialModal').modal('hide');
            $('#loading').show();
            $.ajax({
                type:'post',
                url: '/editSerial',
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    id: id,
                    category: category,
                    item: item,
                    origserial: origserial,
                    newserial: newserial
                },
                success: function(data){
                    if(data == 'true'){
                        $('#loading').hide();
                        Swal.fire({
                            title: "EDIT SUCCESS",
                            text: "Item Serial edited successfully!",
                            icon: "success",
                            timer: 2000
                        });
                        $(tblEdit).DataTable().ajax.reload();
                    }
                    else if(data == 'duplicate'){
                        $('#loading').hide();
                        Swal.fire({
                            title: "DUPLICATE SERIAL",
                            text: "Serial already exists!",
                            icon: "error",
                            timer: 2000
                        });
                        $(tblEdit).DataTable().ajax.reload();
                    }
                    else{
                        $('#loading').hide();
                        Swal.fire({
                            title: "EDIT FAILED",
                            text: "Item Serial edit failed!",
                            icon: "error",
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
});

$(document).on('click', '.btndelItem', function(){
    var id = $(this).attr("id");
    var data = $('table.stockDetails2').DataTable().row(id).data();
    $.ajax({
        type:'post',
        url: '/delReqItem',
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                $('#detailsStockRequest').hide();
                Swal.fire("DELETE FAILED", "STOCK REQUEST", "error");
                setTimeout(function(){window.location.reload()}, 2000);
            }
            else{
                if(data.count == 0){
                    $('#detailsStockRequest').hide();
                    Swal.fire("DELETE SUCCESS", "STOCK REQUEST", "success");
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
    Swal.fire({
        title: "DELETE STOCK REQUEST?",
        text: "You are about to DELETE your STOCK REQUEST!\n This will be permanently deleted from the system.",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $.ajax({
                type:'post', 
                url:'/deleteRequest',
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    'request_number': $('#request_num_details').val()
                },
                success: function(data){
                    if(data == 'true'){
                        $('#detailsStockRequest').hide();
                        Swal.fire("DELETE SUCCESS", "STOCK REQUEST", "success");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                    else{
                        $('#detailsStockRequest').hide();
                        Swal.fire("DELETE FAILED", "STOCK REQUEST", "error");
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
    Swal.fire({
        title: "APPROVE STOCK REQUEST?",
        text: "You are about to APPROVE this STOCK REQUEST!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $.ajax({
                type:'post',
                url:'/approveRequest',
                async: false,
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    'request_number': $('#request_num_details').val()
                },
                success: function(data){
                    if(data == 'true'){
                        scrollReset();
                        $('#detailsStockRequest').modal('hide');
                        $('#loading').show();
                        $.ajax({
                            type:'post',
                            url:'/logApprove',
                            headers:{
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:{
                                'request_number': $('#request_num_details').val()
                            },
                            success: function(data){
                                if(data == 'true'){
                                    $('#loading').hide();
                                    Swal.fire("APPROVE SUCCESS", "STOCK REQUEST", "success");
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
                        Swal.fire("APPROVE FAILED", "STOCK REQUEST", "error");
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
        Swal.fire("REASON REQUIRED", "Please provide a reason for disapproving the request.", "error");
        return false;
    }
    else{
        Swal.fire({
            title: "DISAPPROVE STOCK REQUEST?",
            text: "You are about to DISAPPROVE this STOCK REQUEST!",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    type:'post',
                    url:'/disapproveRequest',
                    async: false,
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        'request_number': $('#request_num_details').val(),
                        'reason': $('#reason').val()
                    },
                    success: function(data){
                        if(data == 'true'){
                            scrollReset();
                            $('#reasonModal').modal('hide');
                            $('#detailsStockRequest').modal('hide');
                            $('#loading').show();
                            $.ajax({
                                type:'post',
                                url:'/logDisapprove',
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    'request_number': $('#request_num_details').val(),
                                    'reason': $('#reason').val()
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide();
                                        Swal.fire("DISAPPROVE SUCCESS", "STOCK REQUEST", "success");
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
                            Swal.fire("DISAPPROVE FAILED", "STOCK REQUEST", "error");
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

$('.btnStaging').on('click', function(){
    Swal.fire({
        title: "FOR STAGING?",
        text: "You are about to move these items FOR STAGING!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $.ajax({
                type:'post',
                url:'/stageRequest',
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    'request_number': $('#request_num_details').val(),
                    'status': $('#status_id_details').val()
                },
                success: function(data){
                    if(data == 'true'){
                        $('#detailsStockRequest').hide();
                        Swal.fire("FOR STAGING SUCCESS", "STOCK REQUEST", "success");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                    else{
                        $('#detailsStockRequest').hide();
                        Swal.fire("FOR STAGING FAILED", "STOCK REQUEST", "error");
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

$('.btnTransit').on('click', function(){
    Swal.fire({
        title: "FOR RECEIVING?",
        text: "You are about to move these items FOR RECEIVING!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $.ajax({
                type:'post',
                url:'/inTransit',
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                        Swal.fire("FOR RECEIVING SUCCESS", "STOCK REQUEST", "success");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                    else{
                        $('#detailsStockRequest').hide();
                        Swal.fire("FOR RECEIVING FAILED", "STOCK REQUEST", "error");
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
        Swal.fire('Date Rescheduled is required!','Select within date range from today up to Date Needed.','error');
        return false;
    }
    else if($("#resched").val() < minDate){
        Swal.fire('Minimum Date is today!','Select within date range from today up to Date Needed.','error');
        return false;
    }
    // else if($("#resched").val() > maxDate){
    //     Swal.fire('Exceed Date Needed deadline!','Select within date range from today up to Date Needed.','error');
    //     return false;
    // }
    else{
        Swal.fire({
            title: "RESCHEDULE STOCK REQUEST?",
            text: "You are about to RESCHEDULE this STOCK REQUEST!",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    type:'post',
                    url:'/reschedRequest',
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        'request_number': $('#request_num_details').val(),
                        'request_type': $('#req_type_id_details').val(),
                        'resched': $("#resched").val()
                    },
                    success: function(data){
                        if(data == 'true'){
                            $('#detailsStockRequest').hide();
                            Swal.fire("RESCHEDULE SUCCESS", "STOCK REQUEST", "success");
                            setTimeout(function(){location.href="/stockrequest"}, 2000);
                        }
                        else{
                            $('#detailsStockRequest').hide();
                            Swal.fire("RESCHEDULE FAILED", "STOCK REQUEST", "error");
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
        serial.setAttribute("class", "form-control serialfunc");
        serial.setAttribute("placeholder", "Input Serial Number "+(parseInt(i)+1));
        serial.setAttribute("style", "width: 200px; font-size: 12px; margin: auto; margin-bottom: 10px;");
        document.getElementById("serialList").appendChild(serial);
    }
});

$(document).on('keyup', '.serialfunc', function(){
    var serial = $(this).val().toUpperCase();
    $(this).val(serial);
});

$(document).on('keypress', '.serialfunc', function(e){
    var k;
    document.all ? k = e.keyCode : k = e.which;
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || (k >= 48 && k <= 57));
});

setInterval(checkSerial, 0);
function checkSerial(){
    if($('#inputSerialModal').is(':visible')){
        if($('.serialfunc').filter(function(){ return !!this.value; }).length != var_qty){
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
    Swal.fire({
        title: "RECEIVE ASSEMBLED ITEM?",
        text: "You are about to RECEIVE this Assembled Item/s into warehouse stocks!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $.ajax({
                type:'post',
                url:'/assembly/receiveAssembled',
                async: false,
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                        Swal.fire("RECEIVE FAILED", "ASSEMBLED ITEM", "error");
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
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                        Swal.fire("RECEIVE SUCCESS", "ASSEMBLED ITEM", "success");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                    else{
                        $('#detailsStockRequest').hide();
                        Swal.fire("RECEIVE FAILED", "ASSEMBLED ITEM", "error");
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

$('.btnReissue').on('click', function(){
    Swal.fire({
        title: "RE-ISSUE ITEMS?",
        text: "You are about to RE-ISSUE these item/s for another request!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $('#detailsStockRequest').modal('hide');
            $('#txtNewStockRequest').html('RE-ISSUE STOCK REQUEST');
            $('.btnNewStockRequest').click();
            $('#request_type').val('2');
            $('#request_type').change();
            $('#request_type').prop('disabled', true);
            $('#reissueItemsModal').show();
            $('table.reissueItems').DataTable({
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
                    url: '/reissueItems',
                    data:{
                        items: items
                    }
                },
                order: [],
                columns: [
                    { data: 'prodcode' },
                    { data: 'item' },
                    { data: 'qty' },
                    { data: 'uom' },
                    { data: 'serial' }
                ],
                footerCallback: function(row,data,start,end,display){
                    var api = this.api(), data;
                    var intVal = function(i){
                        return typeof i === 'string'?
                            i.replace(/[\$,]/g,'')*1:
                            typeof i === 'number'?
                                i:0;
                    };
                    api.columns('.sum', {page:'all'}).every(function(){
                        var sum = this
                        .data()
                        .reduce(function(a,b){
                            return intVal(a) + intVal(b);
                        }, 0);
                        sum = sum.toString();
                        var pattern = /(-?\d+)(\d{3})/;
                        while(pattern.test(sum))
                        sum = sum.replace(pattern,"$1,$2");
                        this.footer().innerHTML = sum;
                    });
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

        if(req_type_id == '4' || req_type_id == '5' || req_type_id == '6' || req_type_id == '7'){
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

var exceed = [];
var items = [];
var item_count = 0;
$('table.stockDetails').DataTable().on('select', function(){});
$('.stockDetails tbody').on('click', 'tr', function(){
    if($("#current_role").val() == 'sales'){
        return false;
    }
    if($("#current_role").val() == 'viewer'){
        return false;
    }
    var req_type_id = $('#req_type_id_details').val();
    var requestStatus = $('#status_id_details').val();
    if((requestStatus == '2' || requestStatus == '3' || requestStatus == '4' || requestStatus == '6' || requestStatus == '7' || requestStatus > 7) && requestStatus != '24' && requestStatus != '31'){
        return false;
    }
    var table = $('table.stockDetails').DataTable();
    var data = table.row(this).data();
    item_count = table.data().count();
    var requested = data.quantity;
    var pend = data.pending;
    var stock = data.qtystock;
    var item_id = data.item_id;
    var bal = data.qtybal;
    var mal = data.qtymal;

    if(pend == 0){
        Swal.fire('Item is fullfiled!','','success');
    }
    else if(stock == 0){
        if(bal != 0 && mal != 0){
            sweet(
                'Item out of stock!',
                'Request Stock Transfer from Balintawak and/or Malabon? OR Add Stocks to Warehouse?',
                'warning',
                'Transfer Stocks',
                'Add Stocks',
                '/stocktransfer',
                '/stocks?item='+item_id
            );
        }
        else if(bal != 0 && mal == 0){
            sweet(
                'Item out of stock!',
                'Request Stock Transfer from Balintawak? OR Add Stocks to Warehouse?',
                'warning',
                'Transfer Stocks',
                'Add Stocks',
                '/stocktransfer',
                '/stocks?item='+item_id
            );
        }
        else if(bal == 0 && mal != 0){
            sweet(
                'Item out of stock!',
                'Request Stock Transfer from Malabon? OR Add Stocks to Warehouse?',
                'warning',
                'Transfer Stocks',
                'Add Stocks',
                '/stocktransfer',
                '/stocks?item='+item_id
            );
        }
        else{
            sweet(
                'Item out of stock!',
                'Add Stocks to Warehouse?',
                'warning',
                'Add Stocks',
                '',
                '/stocks?item='+item_id,
                ''
            );
        }
    }
    else if(stock < requested && items.includes(item_id) == false){
        if(bal >= requested && mal >= requested){
            sweet(
                'Insufficient stock!',
                'Request Stock Transfer from Balintawak and/or Malabon? OR Add Stocks to Warehouse?',
                'warning',
                'Transfer Stocks',
                'Add Stocks',
                '/stocktransfer',
                '/stocks?item='+item_id
            );
        }
        else if(bal >= requested){
            sweet(
                'Insufficient stock!',
                'Request Stock Transfer from Balintawak? OR Add Stocks to Warehouse?',
                'warning',
                'Transfer Stocks',
                'Add Stocks',
                '/stocktransfer',
                '/stocks?item='+item_id
            );
        }
        else if(mal >= requested){
            sweet(
                'Insufficient stock!',
                'Request Stock Transfer from Malabon? OR Add Stocks to Warehouse?',
                'warning',
                'Transfer Stocks',
                'Add Stocks',
                '/stocktransfer',
                '/stocks?item='+item_id
            );
        }
        else{
            sweet(
                'Insufficient stock!',
                'Add Stocks to Warehouse?',
                'warning',
                'Add Stocks',
                '',
                '/stocks?item='+item_id,
                ''
            );
        }
        if((req_type_id < 4 && req_type_id != 7) || req_type_id == 8){
            $(this).toggleClass('selected');
            if(items.includes(item_id) == true){
                items = items.filter(item => item !== item_id);
                exceed = exceed.filter(item => item !== item_id);
            }
            else {
                items.push(item_id);
                if(stock < pend){
                    exceed.push(item_id);
                }
            }
        }
    }
    else{
        if(req_type_id == '4' || req_type_id == '5' || req_type_id == '6' || req_type_id == '7'){
            Swal.fire('Sufficient stocks!','','success');
            return false;
        }
        $(this).toggleClass('selected');
        if(items.includes(item_id) == true){
            items = items.filter(item => item !== item_id);
            exceed = exceed.filter(item => item !== item_id);
        }
        else {
            items.push(item_id);
            if(stock < pend){
                exceed.push(item_id);
            }
        }
    }
    if(items.length == 0){
        $('#btnProceed').prop('disabled', true);
    }
    else{
        $('#btnProceed').prop('disabled', false);
    }
});

$('.table.schedItems1').DataTable().on('select', function(){});
$('.schedItems1 tbody').on('click', 'tr', function(){
    var req_by_id = $('#requested_by_id_details').val();
    var req_type_id = $('#req_type_id_details').val();
    var requestStatus = $('#status_id_details').val();
    if(($("#current_user").val() == req_by_id) && req_type_id == '8' && (requestStatus == '30' || requestStatus == '31')){
        var table = $('table.schedItems1').DataTable();
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
            $('.btnReissue').prop('disabled', true);
        }
        else{
            $('.btnReissue').prop('disabled', false);
        }
    }
});

$('.table.transItems').DataTable().on('select', function(){});
$('.transItems tbody').on('click', 'tr', function(){
    if($("#current_role").val() != 'sales'){
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

$('.table.transItems1').DataTable().on('select', function(){});
$('.transItems1 tbody').on('click', 'tr td:not(:nth-child(6))', function(){
    var req_type_id = $('#req_type_id_details').val();
    if(($("#current_role").val() == 'admin' || $("#current_role").val() == 'encoder') && req_type_id == '7'){
        var requestStatus = $('#status_id_details').val();
        if(requestStatus == '3' || requestStatus == '4'){
            var table = $('table.transItems1').DataTable();
            var data = table.row(this).data();
            item_count = table.data().count();
            
            if(items.includes(data.id) == true){
                items = items.filter(item => item !== data.id);
                $(this).parent().removeClass('selected');
            }
            else {
                items.push(data.id);
                $(this).parent().addClass('selected');
            }
            if(items.length == 0){
                $('.btnReceive').prop('disabled', true);
            }
            else{
                $('.btnReceive').prop('disabled', false);
            }
        }
    }
});

$('.table.incItems').DataTable().on('select', function(){});
$('.incItems tbody').on('click', 'tr', function(){
    var req_type_id = $('#req_type_id_details').val();
    var requestStatus = $('#status_id_details').val();
    if((requestStatus == '11' || requestStatus == '25' || requestStatus == '27' || requestStatus == '28' || requestStatus == '18' || requestStatus == '21') && ($("#current_role").val() == 'admin' || $("#current_role").val() == 'encoder')){
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
            $('.btnReceiveReturned').prop('disabled', true);
            $('#btnReceiveDfc').prop('disabled', true);
        }
        else{
            $('.btnReceiveReturned').prop('disabled', false);
            $('#btnReceiveDfc').prop('disabled', false);
        }
    }
    if(requestStatus == '17' && (($("#current_role").val() == 'sales') || (($("#current_role").val() == 'admin' || $("#current_role").val() == 'encoder') && req_type_id == '7'))){
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

var txtSchedule = 'SCHEDULE';
$("#btnProceed").unbind('click').click(function(){
    j = 0;
    var reqnum = $('#request_num_details').val();
    var req_type_id = $('#req_type_id_details').val();
    if((exceed.length != 0 || items.length < item_count) &&  req_type_id < 4){
        Swal.fire('NOTICE: PARTIAL', 'Stock Request will be prepared partially!', 'warning')
    }
    if(req_type_id == '4' || req_type_id == '5' || req_type_id == '6' || req_type_id == '7'){
        var form_data  = $('#stockDetailsrequest').DataTable().rows().data();
        form_data.each(function(value, index){
            items.push(value.item_id);
        });
    }
    if(req_type_id == '7'){
        $('#schedBy_label').html('Prepared By');
        $('#schedOn_label').html('Date Prepared');
        $('#schedOn').attr('type', 'hidden');
        $('#schedOn').val(minDate);
        $('#schedOn_text').css({"display": "block"});
        $('#schedOn_text').val(moment(minDate).format('dddd, MMMM DD, YYYY'));
        $("#schedOn_text").prop('readonly', true);
        $('#btnSubmit').val('PREPARE');
        txtSchedule = 'PREPARE';
    }
    $("#stockDetailsrequest *").prop('disabled', true);
    $("#proceed_label").hide();
    $("#btnProceed").hide();
    $("#reqContents").empty();
    $("#requestItems").slideDown();
    // $('#schedOn').attr('max', maxDate);
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
                        var warranty = document.createElement("input");
                        warranty.setAttribute("id", "warranty"+j);
                        warranty.setAttribute("type", "hidden");
                        warranty.setAttribute("value", value.warranty_id);
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
                        document.getElementById("reqContents").appendChild(warranty);
                        document.getElementById("reqContents").appendChild(x);
                        document.getElementById("reqContents").appendChild(y);
                        document.getElementById("reqContents").appendChild(qty);
                        document.getElementById("reqContents").appendChild(uom);
                        document.getElementById("reqContents").appendChild(serial);
                        document.getElementById("reqContents").appendChild(z);
                        $("#item"+j).html(value.item);
                        $("#prodcode"+j).prop('readonly', true);
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
                        Swal.fire('Date Scheduled is required!','Select within date range from today up to Date Needed.','error');
                        return false;
                    }
                    else if($("#schedOn").val() < minDate){
                        Swal.fire('Minimum Date is today!','Select within date range from today up to Date Needed.','error');
                        return false;
                    }
                    // else if($("#schedOn").val() > maxDate){
                    //     Swal.fire('Exceed Date Needed deadline!','Select within date range from today up to Date Needed.','error');
                    //     return false;
                    // }
                    else{
                        Swal.fire({
                            title: txtSchedule+" STOCK REQUEST?",
                            text: "You are about to "+txtSchedule+" this STOCK REQUEST!",
                            icon: "warning",
                            showCancelButton: true,
                            cancelButtonColor: '#3085d6',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'Confirm',
                            allowOutsideClick: false
                        })
                        .then((result) => {
                            if(result.isConfirmed){
                                for(var n=0; n < j; n++){
                                    if($('#serial'+n).val() != ''){
                                        $.ajax({
                                            type:'post',
                                            url:'/prepareItems',
                                            async: false,
                                            headers:{
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            },
                                            data:{
                                                'request_number': reqnum,
                                                'req_type_id': req_type_id,
                                                'stock_id': $('#serial'+n).val(),
                                                'item_id': $('#item_id'+n).val(),
                                                'warranty_id': $('#warranty'+n).val(),
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
                                    headers:{
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:{
                                        'request_number': reqnum,
                                        'req_type_id': req_type_id,
                                        'schedOn': $('#schedOn').val()
                                    },
                                    success: function(data){
                                        if(data == 'true'){
                                            $('#detailsStockRequest').hide();
                                            Swal.fire(txtSchedule+"D SUCCESS", "STOCK REQUEST", "success");
                                            setTimeout(function(){location.href="/stockrequest"}, 2000);
                                        }
                                        else{
                                            $('#detailsStockRequest').hide();
                                            Swal.fire(txtSchedule+"D FAILED", "STOCK REQUEST", "error");
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
    if(req_type_id == '4' || req_type_id == '5' || req_type_id == '6' || req_type_id == '7'){
        items = [];
    }
    $("#stockDetailsrequest *").prop('disabled', false);
    $("#proceed_label").show();
    $('#btnSubmit').prop('disabled', true);
    $("#requestItems").hide();
    $("#schedOn").val('');
    $("#btnProceed").show();
});

$("#btnSale").unbind('click').click(function(){
    j = 0;
    var reqnum = $('#request_num_details').val();
    $("#transItems *").prop('disabled', true);
    $("#demoreceive_label").hide();
    $(".soldhide").hide();
    $("#soldContents").empty();
    $("#soldItems").slideDown();
    for(var i=0; i < items.length; i++){
        $.ajax({ 
            type:'get', 
            url:'/soldreq', 
            data:{
                'item_id': items[i]
            }, 
            success: function(data){
                var reqitem = $.map(data.data, function(value, index){ 
                    return [value];
                });

                reqitem.forEach(value => {
                    var pid = document.createElement("input");
                    pid.setAttribute("id", "stock_idx"+j);
                    pid.setAttribute("type", "hidden");
                    pid.setAttribute("value", value.id);
                    var id = document.createElement("input");
                    id.setAttribute("id", "item_idx"+j);
                    id.setAttribute("type", "hidden");
                    id.setAttribute("value", value.item_id);
                    var x = document.createElement("input");
                    x.setAttribute("id", "prodcodex"+j);
                    x.setAttribute("type", "text");
                    x.setAttribute("class", "form-control");
                    x.setAttribute("style", "width: 250px; font-size: 12px; margin-bottom: 10px;");
                    x.setAttribute("value", value.prodcode);
                    var y = document.createElement("textarea");
                    y.setAttribute("id", "itemx"+j);
                    y.setAttribute("class", "form-control");
                    y.setAttribute("rows", "4");
                    y.setAttribute("style", "width: 250px; font-size: 12px; margin-left: 10px; margin-top: 52px; margin-bottom: 10px; resize: none;");
                    var qty = document.createElement("input");
                    qty.setAttribute("id", "qtyx"+j);
                    qty.setAttribute("type", "number");
                    qty.setAttribute("class", "form-control");
                    qty.setAttribute("style", "width: 100px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
                    qty.setAttribute("value", '1');
                    var uom = document.createElement("input");
                    uom.setAttribute("id", "uomx"+j);
                    uom.setAttribute("type", "text");
                    uom.setAttribute("class", "form-control");
                    uom.setAttribute("style", "width: 100px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
                    uom.setAttribute("value", value.uom);
                    var serial = document.createElement("input");
                    serial.setAttribute("id", "serialx"+j);
                    serial.setAttribute("class", "form-control");
                    serial.setAttribute("style", "width: 200px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
                    serial.setAttribute("value", value.serial);
                    var warranty = document.createElement("select");
                    warranty.setAttribute("id", "warrantyx"+j);
                    warranty.setAttribute("class", "form-control warrantyx");
                    warranty.setAttribute("style", "width: 210px; font-size: 12px; margin-left: -420px; margin-bottom: -70px;");
                    var required = document.createElement("span");
                    required.setAttribute("id", "requiredx"+j);
                    required.setAttribute("style", "font-size: 12px; color: Red; margin-left: -355px; margin-bottom: -120px; margin-right: 250px;");
                    required.textContent = "*Required Field";
                    var details = document.createElement("button");
                    details.setAttribute("id", "detailsx"+j);
                    details.setAttribute("type", "button");
                    details.setAttribute("class", "form-control btn-primary bp details");
                    details.setAttribute("style", "font-size: 12px; width: 145px; height: 30px; margin-left: 10px; margin-bottom: -70px; display: none;");
                    details.textContent = 'WARRANTY DETAILS';
                    var hideDetails = document.createElement("span");
                    hideDetails.setAttribute("id", "hideDetailsx"+j);
                    hideDetails.setAttribute("style", "font-size: 12px; width: 145px; height: 30px; margin-left: 10px; margin-bottom: -70px;");
                    hideDetails.textContent = " ";
                    document.getElementById("soldContents").appendChild(pid);
                    document.getElementById("soldContents").appendChild(id);
                    document.getElementById("soldContents").appendChild(x);
                    document.getElementById("soldContents").appendChild(y);
                    document.getElementById("soldContents").appendChild(qty);
                    document.getElementById("soldContents").appendChild(uom);
                    document.getElementById("soldContents").appendChild(serial);
                    document.getElementById("soldContents").appendChild(warranty);
                    document.getElementById("soldContents").appendChild(details);
                    document.getElementById("soldContents").appendChild(hideDetails);
                    document.getElementById("soldContents").appendChild(required);
                    $("#itemx"+j).html(value.item);
                    $("#prodcodex"+j).prop('readonly', true);
                    $("#itemx"+j).prop('readonly', true);
                    $("#qtyx"+j).prop('readonly', true);
                    $("#uomx"+j).prop('readonly', true);
                    $("#serialx"+j).prop('readonly', true);
                    $("#warrantyx"+j).append("<option value='' selected disabled>Select Warranty Type</option>");
                    $("#warrantyx"+j).append("<option value='0'>NO WARRANTY</option>");
                    let vidx = "#warrantyx"+j;
                    $.ajax({
                        type:'get',
                        url:'/setwarranty',
                        success: function(dx){
                            var sx = $.map(dx, function(vx){
                                return [vx];
                            });
        
                            sx.forEach(vx => {
                                $(vidx).append($('<option>', {
                                    value: vx.id,
                                    text: (vx.Warranty_Name).toUpperCase()
                                }));
                            });
                        },
                        error: function(data){
                            if(data.status == 401){
                                window.location.href = '/stockrequest';
                            }
                            alert(data.responseText);
                        }
                    });
                    j++;
                });
                for(var my=0; my < j; my++){
                    let idy = my;
                    $('#warrantyx'+my).on('change', function(){
                        if($("#warrantyx"+idy).val() == '' || $("#warrantyx"+idy).val() == '0'){
                            $('#detailsx'+idy).hide();
                            $('#hideDetailsx'+idy).show();
                            document.getElementById('requiredx'+idy).style.color = "Red";
                        }
                        else{
                            $('#detailsx'+idy).show();
                            $('#hideDetailsx'+idy).hide();
                            document.getElementById('requiredx'+idy).style.color = "White";
                        }
                        if($("#warrantyx"+idy).val() == '0'){
                            document.getElementById('requiredx'+idy).style.color = "White";
                        }
                    });
                }
                for(var mx=0; mx < j; mx++){
                    let idx = mx;
                    $('#detailsx'+mx).on('click', function(){
                        $('.warranty_title').html($("#warrantyx"+idx+" option:selected").text());
                        var id = $("#warrantyx"+idx).val();
                        $('.listInclusive').hide();
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
                }
                setInterval(checkWarranty, 0);
                function checkWarranty(){
                    if($('.warrantyx').filter(function(){ return !!this.value; }).length != j){
                        $('#btnConfirm').prop('disabled', true);
                        $('#soldwarning').show();
                    }
                    else{
                        $('#btnConfirm').prop('disabled', false);
                        $('#soldwarning').hide();
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
    }
});

$('#btnCancel').on('click', function(){
    $("#transItems *").prop('disabled', false);
    $("#demoreceive_label").show();
    $(".soldhide").show();
    $("#soldItems").hide();
});

$('.btnReceive').on('click', function(){
    var inc = 'false';
    var req_type_id = $('#req_type_id_details').val();
    if(items.length < item_count){
        inc = 'true';
    }
    if(req_type_id == 2 || req_type_id == 3 || req_type_id == 7 || req_type_id == 8){
        var title = 'SEND CONFIRMATION EMAIL?';
        var text = 'The Client/Requester will be sent a confirmation email for the received items!';
        var action = 'SEND';
    }
    else{
        var title = 'RECEIVE STOCK REQUEST?';
        var text = 'You are about to RECEIVE this Stock Request!';
        var action = 'RECEIVE';
    }
    Swal.fire({
        title: title,
        text: text,
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $.ajax({
                type: 'post',
                url: '/receiveRequest',
                async: false,
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    'request_number': $('#request_num_details').val(),
                    'request_type': req_type_id,
                    'inc': inc
                },
                success: function(data){
                    if(data == 'true'){
                        for(var i=0; i < items.length; i++){
                            $.ajax({
                                type: 'post',
                                url: '/receiveItems',
                                async: false,
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    'request_number': $('#request_num_details').val(),
                                    'request_type': req_type_id,
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
                        $('#detailsStockRequest').modal('hide');
                        $('#loading').show();
                        $.ajax({
                            type: 'post',
                            url: '/logReceive',
                            headers:{
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:{
                                'request_number': $('#request_num_details').val(),
                                'request_type': $('#req_type_id_details').val(),
                                'status': $('#status_id_details').val()
                            },
                            success: function(data){
                                if(data == 'true'){
                                    $('#loading').hide();
                                    Swal.fire(action+" SUCCESS", "STOCK REQUEST", "success");
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
                        Swal.fire(action+" FAILED", "STOCK REQUEST", "error");
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

$('.btnReceiveReturned').on('click', function(){
    var inc = 'false';
    var inctype = 'COMPLETE';
    if(items.length < item_count){
        inc = 'true';
        inctype = 'INCOMPLETE';
    }
    Swal.fire({
        title: "RECEIVE "+inctype+" RETURNED ITEMS?",
        text: "You are about to RECEIVE these RETURNED ITEMS!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $.ajax({
                type: 'post',
                url: '/receiveReturned',
                async: false,
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    'request_number': $('#request_num_details').val(),
                    'status_id': $('#status_id_details').val(),
                    'inc': inc
                },
                success: function(data){
                    if(data == 'true'){
                        for(var i=0; i < items.length; i++){
                            $.ajax({
                                type: 'post',
                                url: '/receiveRetItems',
                                async: false,
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                        $('#detailsStockRequest').modal('hide');
                        $('#loading').show();
                        $.ajax({
                            type: 'post',
                            url: '/logReceiveRet',
                            headers:{
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:{
                                'request_number': $('#request_num_details').val(),
                                'inc': inc
                            },
                            success: function(data){
                                if(data == 'true'){
                                    $('#loading').hide();
                                    Swal.fire("RECEIVED "+inctype, "RETURNED ITEMS", "success");
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
                        Swal.fire("RECEIVE FAILED", "RETURNED ITEMS", "error");
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
    Swal.fire({
        title: "RECEIVE "+inctype+" DEFECTIVE PARTS?",
        text: "You are about to RECEIVE these DEFECTIVE PARTS!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $.ajax({
                type: 'post',
                url: '/receiveDefective',
                async: false,
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                        $('#detailsStockRequest').modal('hide');
                        $('#loading').show();
                        $.ajax({
                            type: 'post',
                            url: '/logReceiveDfc',
                            headers:{
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:{
                                'request_number': $('#request_num_details').val(),
                                'inc': inc
                            },
                            success: function(data){
                                if(data == 'true'){
                                    $('#loading').hide();
                                    Swal.fire("RECEIVED "+inctype, "DEFECTIVE PARTS", "success");
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
                        Swal.fire("RECEIVE FAILED", "DEFECTIVE PARTS", "error");
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

$('#btnConfirm').on('click', function(){
    $('#referenceModal').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#referenceModal').modal('show');
});

$('#btnReference').on('click', function(){
    var reference = ($.trim($('#x_reference').val()).toUpperCase().split("\n")).join(', ');
    if(reference == ''){
        Swal.fire("SO/PO NUMBER REQUIRED", "Please fill up required field!", "error");
        return false;
    }
    else{
        Swal.fire({
            title: "FOR SALE STOCK REQUEST? CONFIRM REFERENCE PO/SO NO.: "+reference,
            text: "You are about to SELL the selected item/s of this STOCK REQUEST! Items that are not selected will be returned to warehouse stocks. CONTINUE?",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    type:'post',
                    url:'/saleRequest',
                    async: false,
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                                    headers:{
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:{
                                        'id': $('#stock_idx'+i).val(),
                                        'warranty_id': $('#warrantyx'+i).val()
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
                            $('#referenceModal').modal('hide');
                            $('#detailsStockRequest').modal('hide');
                            $('#loading').show();
                            $('#btnUpload').click();
                        }
                        else if(data == 'duplicate'){
                            Swal.fire("DUPLICATE SO/PO#", "Reference SO/PO Number already exists!", "error");
                            return false;
                        }
                        else{
                            $('#referenceModal').hide();
                            $('#detailsStockRequest').hide();
                            Swal.fire("SALE FAILED", "STOCK REQUEST", "error");
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
    Swal.fire({
        title: "RETURN STOCK REQUEST?",
        text: "You are about to RETURN the selected item/s of this STOCK REQUEST! Items that are not selected will remain listed for either sales or return. CONTINUE?",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $.ajax({
                type:'post',
                url:'/returnRequest',
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                        $('#detailsStockRequest').modal('hide');
                        $('#loading').show();
                        $.ajax({
                            type:'post',
                            url:'/logReturn',
                            headers:{
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:{
                                'request_number': $('#request_num_details').val()
                            },
                            success: function(data){
                                if(data == 'true'){
                                    $('#loading').hide();
                                    Swal.fire("RETURN SUCCESS", "STOCK REQUEST", "success");
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
                        Swal.fire("RETURN FAILED", "STOCK REQUEST", "error");
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

$(document).on('click', '.details', function(){
    $('.modal-body').html();
    $('#warrantyModal').modal('show');
});

$(document).on('click', '.detailsClose', function(){
    $('#warrantyModal').modal('hide');
});

$('.btnPrint').on('click', function(){
    window.location.href = '/printRequest?request_number='+$('#request_num_details').val();
});