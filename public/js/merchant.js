var minDate, maxDate, editMode;
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

function validate_fileupload(reference_upload){
    $('.upload_label').html('Upload Image File/s less than 5MB each');
    var files_length = $("#reference_upload").get(0).files.length;
    var error_ext = 0;
    var error_mb = 0;
    for(var i = 0; i < files_length; ++i) {
        var file1=$("#reference_upload").get(0).files[i].name;
        var file_size = $("#reference_upload").get(0).files[i].size;
        var ext = file1.split('.').pop().toLowerCase();
        if($.inArray(ext,['jpg','jpeg','png','gif'])===-1){
            error_ext++;
        }
        if(file_size > 5242880){
            error_mb++;
        }
    }
    if(error_ext > 0 && error_mb > 0){
        swal('INVALID image file type AND EXCEEDED maximum file size (5MB)!', 'Please upload image file/s with valid file type like the following: jpeg/jpg, png, or gif; AND with file size not greater than 5MB each.', 'error');      
        $('#reference_upload').val('');
        $('#reference_upload').focus();
        $('.disupload').hide();
        $('#btnAttach').css("width", "280px");
        return false;
    }
    else if(error_ext > 0){
        swal('INVALID image file type!', 'Please upload image file/s with valid file type like the following: jpeg/jpg, png, or gif.', 'error');      
        $('#reference_upload').val('');
        $('#reference_upload').focus();
        $('.disupload').hide();
        $('#btnAttach').css("width", "280px");
        return false;
    }
    else if(error_mb > 0){
        swal('EXCEEDED maximum file size (5MB)!', 'Please upload valid image file/s with file size not greater than 5MB each.', 'error');      
        $('#reference_upload').val('');
        $('#reference_upload').focus();
        $('.disupload').hide();
        $('#btnAttach').css("width", "280px");
        return false;
    }
    else{
        if(files_length == 1){
            $('.upload_label').html(reference_upload.value.split("\\").pop());
            $('.disupload').show();
            $('#btnAttach').css("width", "250px");
        }
        else if(files_length > 1){
            $('.upload_label').html('UPLOADED ('+files_length+') IMAGE FILES');
            $('.disupload').show();
            $('#btnAttach').css("width", "250px");
        }
        else{
            $('.upload_label').html('Upload Image File/s less than 5MB each');
            $('.disupload').hide();
            $('#btnAttach').css("width", "280px");
        }
        return true;
    }
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
        type: 'get',
        url: '/generateReqNum',
        async: false,
        data:{
            'request_number': request_number
        },
        success: function(data){
            if(data == 'unique'){
                document.getElementById("request_num").value = request_number;
            }
            else{
                generateReqNum();
            }
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/merchant';
            }
            alert(data.responseText);
        }
    });
}

$(".btnNewMerchRequest").on('click', function(){
    $('#newMerchRequest').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('.modal-body').html();
    $('#newMerchRequest').modal('show');
    generateReqNum();
});

setInterval(runFunction, 0);
function runFunction(){
    if($('#newMerchRequest').is(':visible')){
        var needdate = $('#needdate').val();
        var orderID = $('#orderID').val();
        var reference_upload = $('#reference_upload').val();
        if(needdate && orderID && reference_upload){
            $('#requestDetails').show();
            $('.header_label').hide();
        }
        else{
            $('#requestDetails').hide();
            $('.header_label').show();
        }
    }
}

$('#categoryReq').on('change', function(){
    var id = $('#categoryReq').val();
    var descOp = " ";
    $('#prodcode').val('');
    $('#uom').val('');
    $("#qtyReq").val('');
    $.ajax({ 
        type: 'get', 
        url: '/merchant/items', 
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
                window.location.href = '/merchant';
            }
            alert(data.responseText);
        }
    });    
});

$('#itemReq').on('change', function(){
    var item_id = $(this).val();
    $.ajax({
        type: 'get', 
        url: '/merchant/uom', 
        data:{
            'item_id': item_id,
        }, 
        success: function(data){
            $('#prodcode').val(data[0].prodcode);
            $('#uom').val(data[0].uom);
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/merchant';
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
        url: '/merchant/warranty', 
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
                window.location.href = '/merchant';
            }
            alert(data.responseText);
        }
    });
});

$(".add-row").on('click', function(){
    var category = $("#categoryReq option:selected").text();
    var item = $("#itemReq option:selected").text();
    var warranty = $("#warrantyReq option:selected").text();
    var category_id = $("#categoryReq").val();
    var item_id = $("#itemReq").val();
    var warranty_id = $("#warrantyReq").val();
    var prodcode = $("#prodcode").val();
    var uom = $("#uom").val();
    let qty = $("#qtyReq").val();
    var markup = "<tr><td style='display: none;'>" + category_id + "</td><td style='display: none;'>" + item_id + "</td><td style='display: none;'>" + warranty_id + "</td><td style='display: none;'>" + category + "</td><td>" + prodcode + "</td><td>" + item + "</td><td>" + qty + "</td><td>" + uom + "</td><td>" + warranty + "</td><td><button type='button' style='zoom: 80%;' class='delete-row btn btn-danger bp'>REMOVE</button></td></tr>";
    var ctr = 'false';
    if(category == "Select Category" || item == "Select Item" || qty == "" || qty == "0" || uom == "" || warranty == "Select Warranty Type"){
        swal('REQUIRED','Please fill up all required item details!','error');
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

$(document).on('click', '.disupload', function(){
    $('#reference_upload').val('');
    $('.upload_label').html('Upload Image File/s less than 5MB each');
    $('.disupload').hide();
    $('#btnAttach').css("width", "280px");
});

$('#btnSave').on('click', function(){
    var needdate = $('#needdate').val();
    var orderID = $('#orderID').val();
    var reference_upload = $('#reference_upload').val();
    if(needdate < minDate){
        swal('Minimum Date is today!','Select within date range from today onwards.','error');
        return false;
    }
    else{
        swal({
            title: "SUBMIT MERCHANT STOCK REQUEST?",
            text: "Please review the details of your request. Click 'OK' button to submit; otherwise, click 'Cancel' button.",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                    type:'post',
                    url:'/merchant/saveReqNum',
                    async: false,
                    headers:{
                        'X-CSRF-TOKEN': $("#csrf").val()
                    },
                    data:{
                        'request_number': $('#request_num').val(),
                        'needdate': needdate,
                        'orderID': orderID
                    },
                    success: function(data){
                        if(data == 'true'){
                            var myTable = $('#stockRequestTable').DataTable();
                            var form_data  = myTable.rows().data();
                            $.each(form_data, function(key, value){
                                $.ajax({
                                    type:'post',
                                    url:'/merchant/saveRequest',
                                    async: false,
                                    headers:{
                                        'X-CSRF-TOKEN': $("#csrf").val()
                                    },
                                    data:{
                                        'request_number': $('#request_num').val(),
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
                            scrollReset();
                            $('#newMerchRequest').modal('hide');
                            $('#loading').show(); Spinner(); Spinner.show();
                            $.ajax({
                                type:'post',
                                url:'/merchant/logSave',
                                headers:{
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
                                            swal("SUBMIT SUCCESS", "MERCHANT STOCK REQUEST", "success");
                                            setTimeout(function(){location.href="/stockrequest"}, 2000);
                                        }
                                    }
                                    else{
                                        return false;
                                    }
                                },
                                error: function(data){
                                    if(data.status == 401){
                                        window.location.href = '/merchant';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else if(data == 'duplicate'){
                            swal("INVALID ENTRY", "Order ID already exists! Please double check the Order ID and try again.", "error");
                            return false;
                        }
                        else{
                            $('#newMerchRequest').modal('hide');
                            swal("SUBMIT FAILED", "MERCHANT STOCK REQUEST", "error");
                            setTimeout(function(){location.href="/merchant"}, 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/merchant';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }  
});

$('.btnClose').on('click', function(){
    window.location.href = '/merchant';
});

$('table.merchantTable').dataTable().fnDestroy();
$('#loading').show(); Spinner(); Spinner.show();
$('table.merchantTable').DataTable({
    columnDefs: [
        {
            "targets": [0],
            "render": $.fn.dataTable.render.moment('YYYY-MM-DD', 'MMM. DD, YYYY')
        }
    ],
    language:{
        processing: "Loading...",
        emptyTable: "No data available in table"
    },
    serverSide: true,
    ajax:{
        url: '/merchant/data',
    },
    columns: [
        { data: 'reqdate' },
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
        { data: 'req_num' },
        { data: 'req_by' },
        { data: 'orderID' },
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
        }
    ],
    order: [],
    initComplete: function(){
        return notifyDeadline();
    }
});