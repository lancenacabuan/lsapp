var CategoryTable, ItemTable, ItemSerialTable, SerialTable, table, categoryID, categoryName;
function category(){
    $('table.CategoryTable').dataTable().fnDestroy();
    $('table.ItemTable').dataTable().fnDestroy();
    $('table.ItemSerialTable').dataTable().fnDestroy();
    $('table.SerialTable').dataTable().fnDestroy();
    $('#CategoryTableDiv').show();
    $('#ItemTableDiv').hide();
    $('#ItemSerialTableDiv').hide();
    $('#SerialTableDiv').hide();
    $('#stocksHeader').html('WAREHOUSE STOCKS');
    $('#btnBack').hide();
    $('#backBtn').hide();
    $('.br').hide();
    $('#loading').show(); Spinner(); Spinner.show();
    CategoryTable = 
        $('table.CategoryTable').DataTable({
            serverSide: true,
            ajax: 'category_data',
            columns: [
                { data: 'Category' },
                { data: 'Defective', width: '8%' },
                { data: 'Demo', width: '8%' },
                { data: 'Assembly', width: '8%' },
                { data: 'A1', width: '8%' },
                { data: 'A2', width: '8%' },
                { data: 'A3', width: '8%' },
                { data: 'A4', width: '8%' },
                { data: 'Balintawak', width: '8%' },
                { data: 'Malabon', width: '8%' },
                { data: 'Total_stocks', width: '8%' }
            ],
            order: [],
            initComplete: function(){
                return notifyDeadline();
            }
        });
}

$(document).ready(function(){   
    category();
    $('#prodcodediv').hide();
    $('#uomdiv').hide();
    $('#qtydiv').hide();
    $('#serialdiv').hide();
});

$(document).on('click', '#CategoryTable tbody tr', function(){
    var trdata = CategoryTable.row(this).data();
    categoryID = trdata.id;
    categoryName = decodeHtml(trdata.Category);
    $('table.CategoryTable').dataTable().fnDestroy();
    $('table.ItemTable').dataTable().fnDestroy();
    $('table.ItemSerialTable').dataTable().fnDestroy();
    $('table.SerialTable').dataTable().fnDestroy();
    $('#CategoryTableDiv').hide();
    $('#ItemTableDiv').show();
    $('#ItemSerialTableDiv').hide();
    $('#SerialTableDiv').hide();
    $('#stocksHeader').html(decodeHtml(trdata.Category));
    $('#btnBack').hide();
    $('#backBtn').show();
    $('.br').show();
    $('#loading').show(); Spinner(); Spinner.show();
    ItemTable = 
        $('table.ItemTable').DataTable({
            serverSide: true,
            ajax:{
                url: '/item_data',
                data:{
                    CategoryId: trdata.id
                }
            },
            columns: [
                { data: 'ProdCode', width: '15%' },
                { data: 'Item' },
                { data: 'Defective', width: '8%', width: '8%' },
                { data: 'Demo', width: '8%' },
                { data: 'Assembly', width: '8%' },
                { data: 'A1', width: '5%' },
                { data: 'A2', width: '5%' },
                { data: 'A3', width: '5%' },
                { data: 'A4', width: '5%' },
                { data: 'Balintawak', width: '8%' },
                { data: 'Malabon', width: '8%' },
                { data: 'Total_stocks', width: '8%' }
            ],
            order: [],
            initComplete: function(){
                return notifyDeadline();
            }
        });
});

$('#backBtn').on('click', function(){
    category();
});

$('#btnBack').on('click', function(){
    $('table.CategoryTable').dataTable().fnDestroy();
    $('table.ItemTable').dataTable().fnDestroy();
    $('table.ItemSerialTable').dataTable().fnDestroy();
    $('table.SerialTable').dataTable().fnDestroy();
    $('#CategoryTableDiv').hide();
    $('#ItemTableDiv').show();
    $('#ItemSerialTableDiv').hide();
    $('#SerialTableDiv').hide();
    $('#stocksHeader').html(categoryName);
    $('#btnBack').hide();
    $('#backBtn').show();
    $('.br').show();
    $('#loading').show(); Spinner(); Spinner.show();
    ItemTable = 
        $('table.ItemTable').DataTable({
            serverSide: true,
            ajax:{
                url: '/item_data',
                data:{
                    CategoryId: categoryID
                }
            },
            columns: [
                { data: 'ProdCode', width: '15%' },
                { data: 'Item' },
                { data: 'Defective', width: '8%' },
                { data: 'Demo', width: '8%' },
                { data: 'Assembly', width: '8%' },
                { data: 'A1', width: '5%' },
                { data: 'A2', width: '5%' },
                { data: 'A3', width: '5%' },
                { data: 'A4', width: '5%' },
                { data: 'Balintawak', width: '8%' },
                { data: 'Malabon', width: '8%' },
                { data: 'Total_stocks', width: '8%' }
            ],
            order: [],
            initComplete: function(){
                return notifyDeadline();
            }
        });
});

$(document).on('click', '#ItemTable tbody tr', function(){
    var trdata = ItemTable.row(this).data();
    $('table.CategoryTable').dataTable().fnDestroy();
    $('table.ItemTable').dataTable().fnDestroy();
    $('table.ItemSerialTable').dataTable().fnDestroy();
    $('table.SerialTable').dataTable().fnDestroy();
    $('#CategoryTableDiv').hide();
    $('#ItemTableDiv').hide();
    $('#ItemSerialTableDiv').show();
    $('#SerialTableDiv').hide();
    $('#stocksHeader').html(decodeHtml(trdata.Item));
    $('#btnBack').show();
    $('#backBtn').hide();
    $('.br').show();
    $('#loading').show(); Spinner(); Spinner.show();
    ItemSerialTable = 
        $('table.ItemSerialTable').DataTable({
            serverSide: true,
            ajax:{
                url: '/itemserial_data',
                data:{
                    ItemId: trdata.id
                }
            },
            columnDefs: [
                {
                    "targets": [0,1],
                    "render": $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'MMM. DD, YYYY, h:mm A')
                },
            ],
            columns: [
                { data: 'addDate' },
                { data: 'modDate' },
                { data: 'name' },
                { data: 'qty' },
                { data: 'UOM' },
                { data: 'serial' },
                {
                    data: 'location',
                    "render": function(data, type, row){
                        if(row.status == 'defectives' || row.status == 'FOR RECEIVING'){
                            return 'DEFECTIVE';
                        }
                        else if(row.status == 'demo'){
                            return 'DEMO';
                        }
                        else if(row.status == 'assembly'){
                            return 'ASSEMBLY';
                        }
                        else{
                            return row.location
                        }
                    }
                },
                { data: 'rack' },
                { data: 'row' }
            ],
            order: [[1, 'desc']],
            initComplete: function(){
                return notifyDeadline();
            }
        });
});

$(document).on('click', '#ItemSerialTable tbody tr', function(){
    table = ItemSerialTable;
    if($("#current_role").val() == 'viewer'){
        return false;
    }
    var trdata = ItemSerialTable.row(this).data();
    if(trdata.status == 'defectives' || trdata.status == 'FOR RECEIVING'){
        return false;
    }
    if(trdata.UOM == 'Pc' || trdata.UOM == 'Meter'){
        return false;
    }
    $('#x_id').val(trdata.stock_id);
    $('#x_category').val(decodeHtml(trdata.category));
    $('#x_item').val(decodeHtml(trdata.item));
    $('#y_serial').val(trdata.serial);
    $('#x_serial').val(trdata.serial);

    $('#editSerialModal').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('.modal-body').html();
    $('#editSerialModal').modal('show');
});

$('#z_serial').on('keyup', function(){
    if(!$('#z_serial').val()){
        category();
    }
    else{
        $.ajax({
            url: '/serial_data',
            data:{
                serial: $('#z_serial').val()
            },
            success: function(data){
                $('table.CategoryTable').dataTable().fnDestroy();
                $('table.ItemTable').dataTable().fnDestroy();
                $('table.ItemSerialTable').dataTable().fnDestroy();
                $('table.SerialTable').dataTable().fnDestroy();
                $('#CategoryTableDiv').hide();
                $('#ItemTableDiv').hide();
                $('#ItemSerialTableDiv').hide();
                $('#SerialTableDiv').show();
                $('#stocksHeader').html($('#z_serial').val());
                $('#btnBack').hide();
                $('#backBtn').hide();
                $('.br').show();
                SerialTable = $('table.SerialTable').DataTable({
                    serverSide: true,
                    ajax:{
                        url: '/serial_data',
                        data:{
                            serial: $('#z_serial').val()
                        }
                    },
                    columnDefs: [
                        {
                            "targets": [0,1],
                            "render": $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'MMM. DD, YYYY, h:mm A')
                        },
                    ],
                    columns: [
                        { data: 'addDate' },
                        { data: 'modDate' },
                        { data: 'name' },
                        { data: 'item' },
                        { data: 'qty' },
                        { data: 'UOM' },
                        { data: 'serial' },
                        {
                            data: 'location',
                            "render": function(data, type, row){
                                if(row.status == 'defectives' || row.status == 'FOR RECEIVING'){
                                    return 'DEFECTIVE';
                                }
                                else if(row.status == 'demo'){
                                    return 'DEMO';
                                }
                                else if(row.status == 'assembly'){
                                    return 'ASSEMBLY';
                                }
                                else{
                                    return row.location
                                }
                            }
                        }
                    ],
                    order: [[1, 'desc']],
                    initComplete: function(){
                        return notifyDeadline();
                    }
                });
            }
        });
    }
});

$(document).on('click', '#SerialTable tbody tr', function(){
    table = SerialTable;
    if($("#current_role").val() == 'viewer'){
        return false;
    }
    var trdata = SerialTable.row(this).data();
    if(trdata.status == 'defectives' || trdata.status == 'FOR RECEIVING'){
        return false;
    }
    if(trdata.UOM == 'Pc' || trdata.UOM == 'Meter'){
        return false;
    }
    $('#x_id').val(trdata.stock_id);
    $('#x_category').val(decodeHtml(trdata.category));
    $('#x_item').val(decodeHtml(trdata.item));
    $('#y_serial').val(trdata.serial);
    $('#x_serial').val(trdata.serial);

    $('#editSerialModal').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('.modal-body').html();
    $('#editSerialModal').modal('show');
});

$('#serial').on('keyup', function(){
    var serial = $('#serial').val().toUpperCase();
    $('#serial').val(serial);
});

function specialChar(e){
    var k;
    document.all ? k = e.keyCode : k = e.which;
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || (k >= 48 && k <= 57));
}

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
            text: "Click 'OK' button to submit; otherwise, click 'Cancel' button to review details.",
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
                    url: '/editSerial',
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
                        if(data == 'true'){
                            $('#loading').hide(); Spinner.hide();
                            swal({
                                title: "EDIT SUCCESS",
                                text: "Item Serial edited successfully!",
                                icon: "success",
                                timer: 2000
                            });
                            table.ajax.reload();
                        }
                        else if(data == 'duplicate'){
                            $('#loading').hide(); Spinner.hide();
                            swal({
                                title: "DUPLICATE SERIAL",
                                text: "Serial already exists!",
                                icon: "error",
                                timer: 2000
                            });
                            table.ajax.reload();
                        }
                        else{
                            $('#loading').hide(); Spinner.hide();
                            swal({
                                title: "EDIT FAILED",
                                text: "Item Serial edit failed!",
                                icon: "error",
                                timer: 2000
                            });
                            table.ajax.reload();
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

$('#category').on('change', function(){
    var id = $('#category').val();
    var descOp = " ";
    $.ajax({
        type: 'get',
        url: '/getItems',
        data:{ 'category_id': id },            
        success: function(data){
            var itemcode = $.map(data, function(value, index){
                return [value];
            });
            descOp+='<option value="" selected disabled>Select Item (Required)</option>';
            itemcode.forEach(value => {
                descOp+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>';
            });
            $("#item").find('option').remove().end().append(descOp);               
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/stocks';
            }
            alert(data.responseText);
        }
    });
    $('#prodcodediv').hide();
    $('#uomdiv').hide();
    $('#qtydiv').hide();
    $('#serialdiv').hide();
});

$('#item').on('change', function(){
    var id = $('#item').val();
    $.ajax({
        type: 'get',
        url: '/getUOM',
        data:{
            'id': id
        },            
        success: function(data){
            if(data[0].UOM == "Unit"){
                $('#prodcodediv').show();
                $('#uomdiv').show();
                $('#qtydiv').show();
                if(data[0].serialize == 'YES'){
                    $('#serialdiv').show();
                    $('#serial').prop('required', true);
                }
                else{
                    $('#serialdiv').hide();
                    $('#serial').prop('required', false);
                }
                $('#prodcode').val(data[0].prodcode);
                $('#uom').val(data[0].UOM);
                $('#qty').val('1');
                $('#qty').prop('disabled', true);
            }
            else{
                $('#prodcodediv').show();
                $('#uomdiv').show();
                $('#qtydiv').show();
                $('#serialdiv').hide();
                $('#prodcode').val(data[0].prodcode);
                $('#uom').val(data[0].UOM);
                $('#qty').val('0');
                $('#qty').prop('disabled', false);
            }
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/stocks';
            }
            alert(data.responseText);
        }
    });
});

$('#btnSave').on('click', function(){
    var AddStockForm = $('#AddStockForm');
    var category = $('#category').val();
    var item = $('#item').val();
    var location_id = $('#location').val();
    var rack = $.trim($('#rack').val()).toUpperCase();
    var row = $.trim($('#row').val()).toUpperCase();
    var uom = $('#uom').val();
    var qty = $('#qty').val();
    var serial = $.trim($('#serial').val()).toUpperCase();
    var item_name = $("#item option:selected").text();
    var location_name = $("#location option:selected").text();
    if(!$('#rack').val()){
        rack = 'N/A';
    }
    if(!$('#row').val()){
        row = 'N/A';
    }
    if(uom == 'Unit'){
        if(($('#serial').is(':visible') && category && item && location_id && serial) || ($('#serial').is(':hidden') && category && item && location_id)){
            if(!$('#serial').val()){
                serial = 'N/A';
            }
            swal({
                title: "Are you really sure all details are entered correctly?",
                text: "Click 'OK' button to submit; otherwise, click 'Cancel' button to review details.",
                icon: "warning",
                buttons: true,
                dangerMode: true
            })
            .then((willDelete) => {
                if(willDelete){
                    $.ajax({
                        url: "/stocks/save",
                        type: "POST",
                        headers:{
                            'X-CSRF-TOKEN': $("#csrf").val()
                        },
                        data:{
                            _token: $("#csrf").val(),
                            category: category,
                            item: item,
                            location: location_id,
                            uom: uom,
                            serial: serial,
                            rack: rack,
                            row: row,
                            item_name: item_name,
                            location_name: location_name
                        },
                        success: function(data){
                            if(data == 'true'){
                                $('#addStock').hide();
                                swal("SAVE SUCCESS", "Stock added successfully!", "success");
                                setTimeout(function(){window.location.href="/stocks"}, 2000);
                            }
                            else if(data == 'duplicate'){
                                swal("DUPLICATE SERIAL", "Serial already exists!", "error");
                                return false;
                            }
                            else{
                                $('#addStock').hide();
                                swal("SAVE FAILED", "Failed to add stock!", "error");
                                setTimeout(function(){window.location.href="/stocks"}, 2000);
                            }
                        },
                        error: function(data){
                            if(data.status == 401){
                                window.location.href = '/stocks';
                            }
                            alert(data.responseText);
                        }
                    });
                }
            });
        }
        else{
            AddStockForm[0].reportValidity();
        }
    }
    else{
        if(qty > 0 && category && item && location_id){
            swal({
                title: "Are you really sure all details are entered correctly?",
                text: "Click 'OK' button to submit; otherwise, click 'Cancel' button to review details.",
                icon: "warning",
                buttons: true,
                dangerMode: true
            })
            .then((willDelete) => {
                if(willDelete){
                    $.ajax({
                        url: "/stocks/save",
                        type: "POST",
                        headers:{
                            'X-CSRF-TOKEN': $("#csrf").val()
                        },
                        data:{
                            _token: $("#csrf").val(),
                            category: category,
                            item: item,
                            location: location_id,
                            uom: uom,
                            qty: qty,
                            rack: rack,
                            row: row,
                            item_name: item_name,
                            location_name: location_name
                        },
                        success: function(data){
                            $('#addStock').hide();
                            if(data == 'true'){
                                swal("SAVE SUCCESS", "Stock added successfully!", "success");
                                setTimeout(function(){window.location.href="/stocks"}, 2000);
                            }
                            else{
                                swal("SAVE FAILED", "Failed to add stock!", "error");
                                setTimeout(function(){window.location.href="/stocks"}, 2000);
                            }
                        },
                        error: function(data){
                            if(data.status == 401){
                                window.location.href = '/stocks';
                            }
                            alert(data.responseText);
                        }
                    });
                }
            });
        }
        else{
            AddStockForm[0].reportValidity();
        }
    }
});

$('#btnReset').on('click', function(){
    $('#AddStockForm').trigger('reset');
    $("#item").find('option').remove().end().append('<option value="" selected disabled>Select Item (Required)</option>');
    $('#prodcodediv').hide();
    $('#uomdiv').hide();
    $('#qtydiv').hide();
    $('#serialdiv').hide();
});

$('#btnImport').on('click', function(){
    $('#importStock').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('.modal-body').html();
    $('#importStock').modal('show');
});

$('#btnDetach').on('click', function(){
    $('#xlsx').val('');
});

function validate_xlsx(xlsx){
    var files_length = $("#xlsx").get(0).files.length;
    var error_ext = 0;
    var error_mb = 0;
    if(files_length > 1){
        swal('EXCEEDED allowed number of file upload!', 'Please upload only ONE (1) valid EXCEL file.', 'error');      
        $('#xlsx').val('');
        $('#xlsx').focus();
        return false;
    }
    for(var i = 0; i < files_length; ++i) {
        var file1=$("#xlsx").get(0).files[i].name;
        var file_size = $("#xlsx").get(0).files[i].size;
        var ext = file1.split('.').pop().toLowerCase();
        if($.inArray(ext,['xls','xlsx'])===-1){
            error_ext++;
        }
        if(file_size > (5242880 * 2)){
            error_mb++;
        }
    }
    if(error_ext > 0 && error_mb > 0){
        swal('INVALID file type AND EXCEEDED maximum file size (10MB)!', 'Please upload an EXCEL file with valid file type like the following: xls or xlsx; AND with file size not greater than 10MB.', 'error');      
        $('#xlsx').val('');
        $('#xlsx').focus();
        return false;
    }
    else if(error_ext > 0){
        swal('INVALID file type!', 'Please upload an EXCEL file with valid file type like the following: xls or xlsx.', 'error');      
        $('#xlsx').val('');
        $('#xlsx').focus();
        return false;
    }
    else if(error_mb > 0){
        swal('EXCEEDED maximum file size (10MB)!', 'Please upload a valid EXCEL file with file size not greater than 10MB.', 'error');      
        $('#xlsx').val('');
        $('#xlsx').focus();
        return false;
    }
    return true;
}

$('#btnUpload').on('click', function(){
    if($('#xlsx')[0].files.length === 0){
        $('#btnSubmit').click();
    }
    else{
        swal({
            title: "UPLOAD FILE IMPORT?",
            text: "Click 'OK' button to ADD STOCKS via uploading import file; otherwise, click 'Cancel' button to select a different file.",
            icon: "warning",
            buttons: true,
            dangerMode: true
        })
        .then((willDelete) => {
            if(willDelete){
                $('#btnSubmit').click();
            }
        });
    }
});

$(document).ready(function(){
    if($(location).attr('pathname')+window.location.search == '/stocks?import=success_without_errors'){
        $('#loading').hide(); Spinner.hide();
        swal("IMPORT SUCCESS", "ADD STOCKS via import file is successful without errors.", "success");
    }
    else if($(location).attr('pathname')+window.location.search == '/stocks?import=success_with_errors'){
        $('#loading').hide(); Spinner.hide();
        swal("IMPORT SUCCESS W/ ERRORS", "ADD STOCKS via import file is successful with some errors.", "warning");
    }
    else if($(location).attr('pathname')+window.location.search == '/stocks?import=failed'){
        $('#loading').hide(); Spinner.hide();
        swal("IMPORT FAILED", "ADD STOCKS via import file has failed.", "error");
    }
});