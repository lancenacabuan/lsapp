var categoryID, categoryName, itemID, itemName, serialID, 
CategoryTable, ItemTable, ItemSerialTable, SerialTable, MinStocksTable, table;
function destroyTables(){
    $('table.CategoryTable').dataTable().fnDestroy();
    $('table.ItemTable').dataTable().fnDestroy();
    $('table.ItemSerialTable').dataTable().fnDestroy();
    $('table.SerialTable').dataTable().fnDestroy();
    $('table.MinStocksTable').dataTable().fnDestroy();
    $('svg').remove();
}

$(document).ready(function(){
    destroyTables();
    $('#prodcodediv').hide();
    $('#uomdiv').hide();
    $('#qtydiv').hide();
    $('#serialdiv').hide();
    $('#btnGenerate').show();
    $('#btnDownload').hide();
    $('#CategoryTableDiv').show();
    $('#ItemTableDiv').hide();
    $('#ItemSerialTableDiv').hide();
    $('#SerialTableDiv').hide();
    $('#MinStocksTableDiv').hide();
    $('#btnBack').hide();
    $('#backBtn').hide();
    $('#stocksHeader').html('WAREHOUSE STOCKS');
    $('#loading').show();
    CategoryTable = $('table.CategoryTable').DataTable({
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        iDisplayLength: -1,
        serverSide: true,
        ajax: 'category_data',
        columns: [
            { data: 'Category' },
            { data: 'Defective', width: '9%' },
            { data: 'Demo', width: '7%' },
            { data: 'Assembly', width: '9%' },
            { data: 'Asset', width: '10%' },
            { data: 'A1', width: '6%' },
            { data: 'A2', width: '6%' },
            { data: 'A3', width: '6%' },
            { data: 'A4', width: '6%' },
            { data: 'Balintawak', width: '10%' },
            { data: 'Malabon', width: '8%' },
            { data: 'Total_stocks', width: '10%' }
        ],
        fnRowCallback: function(nRow, aData) {
            if(aData.RowColor == 'RED') {
                $('td', nRow).css('color', 'red');
                $('td', nRow).css('font-weight', 'bold');
            }
        },
        order: [],
        initComplete: function(){
            return notifyDeadline();
        }
    });
});

$(document).on('click', '#CategoryTable tbody tr', function(){
    var trdata = CategoryTable.row(this).data();
    categoryID = trdata.id;
    categoryName = decodeHtml(trdata.Category);
    destroyTables();
    $('#btnGenerate').show();
    $('#btnDownload').hide();
    $('#CategoryTableDiv').hide();
    $('#ItemTableDiv').show();
    $('#ItemSerialTableDiv').hide();
    $('#SerialTableDiv').hide();
    $('#MinStocksTableDiv').hide();
    $('#btnBack').hide();
    $('#backBtn').show();
    $('#stocksHeader').html(categoryName);
    $('#loading').show();
    ItemTable = $('table.ItemTable').DataTable({
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        iDisplayLength: -1,
        serverSide: true,
        ajax:{
            url: '/item_data',
            data:{
                CategoryId: categoryID
            }
        },
        columns: [
            { data: 'ProdCode', width: '10%' },
            { data: 'Item' },
            { data: 'Defective', width: '8%' },
            { data: 'Demo', width: '6%' },
            { data: 'Assembly', width: '8%' },
            { data: 'Asset', width: '9%' },
            { data: 'A1', width: '5%' },
            { data: 'A2', width: '5%' },
            { data: 'A3', width: '5%' },
            { data: 'A4', width: '5%' },
            { data: 'Balintawak', width: '9%' },
            { data: 'Malabon', width: '7%' },
            { data: 'Total_stocks', width: '10%' }
        ],
        fnRowCallback: function(nRow, aData) {
            if(aData.RowColor == 'RED') {
                $('td', nRow).css('color', 'red');
                $('td', nRow).css('font-weight', 'bold');
            }
        },
        order: [],
        initComplete: function(){
            return notifyDeadline();
        }
    });
});

$('#backBtn').on('click', function(){
    window.location.href = '/stocks';
});

$('#btnBack').on('click', function(){
    destroyTables();
    $('#btnGenerate').show();
    $('#btnDownload').hide();
    $('#CategoryTableDiv').hide();
    $('#ItemTableDiv').show();
    $('#ItemSerialTableDiv').hide();
    $('#SerialTableDiv').hide();
    $('#MinStocksTableDiv').hide();
    $('#btnBack').hide();
    $('#backBtn').show();
    $('#stocksHeader').html(categoryName);
    $('#loading').show();
    ItemTable = $('table.ItemTable').DataTable({
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        iDisplayLength: -1,
        serverSide: true,
        ajax:{
            url: '/item_data',
            data:{
                CategoryId: categoryID
            }
        },
        columns: [
            { data: 'ProdCode', width: '10%' },
            { data: 'Item' },
            { data: 'Defective', width: '8%' },
            { data: 'Demo', width: '6%' },
            { data: 'Assembly', width: '8%' },
            { data: 'Asset', width: '9%' },
            { data: 'A1', width: '5%' },
            { data: 'A2', width: '5%' },
            { data: 'A3', width: '5%' },
            { data: 'A4', width: '5%' },
            { data: 'Balintawak', width: '9%' },
            { data: 'Malabon', width: '7%' },
            { data: 'Total_stocks', width: '10%' }
        ],
        fnRowCallback: function(nRow, aData) {
            if(aData.RowColor == 'RED') {
                $('td', nRow).css('color', 'red');
                $('td', nRow).css('font-weight', 'bold');
            }
        },
        order: [],
        initComplete: function(){
            return notifyDeadline();
        }
    });
});

$(document).on('click', '#ItemTable tbody tr', function(){
    var trdata = ItemTable.row(this).data();
    itemID = trdata.id;
    itemName = decodeHtml(trdata.Item);
    destroyTables();
    $('#btnGenerate').show();
    $('#btnDownload').hide();
    $('#CategoryTableDiv').hide();
    $('#ItemTableDiv').hide();
    $('#ItemSerialTableDiv').show();
    $('#SerialTableDiv').hide();
    $('#MinStocksTableDiv').hide();
    $('#btnBack').show();
    $('#backBtn').hide();
    $('#stocksHeader').html(itemName);
    $('#loading').show();
    if(trdata.serialize == 'YES'){
        ItemSerialTable = $('table.ItemSerialTable').DataTable({
            aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
            serverSide: true,
            ajax:{
                url: '/itemserial_data',
                data:{
                    ItemId: itemID
                }
            },
            columnDefs: [
                {
                    "targets": [0,1],
                    "visible": false,
                    "searchable": true
                },
                {
                    "targets": [2,3],
                    "render": $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'MMM. DD, YYYY, h:mm A')
                },
            ],
            columns: [
                { data: 'addDatetime' },
                { data: 'modDatetime' },
                { data: 'addDate', width: '14%' },
                { data: 'modDate', width: '14%' },
                { data: 'name' },
                { data: 'qty', width: '5%' },
                { data: 'UOM', width: '7%' },
                { data: 'serial' },
                { data: 'location' },
                { data: 'rack', width: '8%' },
                { data: 'row', width: '8%' }
            ],
            order: [],
            initComplete: function(){
                return notifyDeadline();
            }
        });
    }
    else{
        ItemSerialTable = $('table.ItemSerialTable').DataTable({
            aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
            serverSide: true,
            ajax:{
                url: '/itemserial_data',
                data:{
                    ItemId: itemID
                }
            },
            columnDefs: [
                {
                    "targets": [0,1],
                    "visible": false,
                    "searchable": true
                },
                {
                    "targets": [2,3],
                    "render": $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'MMM. DD, YYYY, h:mm A')
                },
                {
                    "targets": [7],
                    "visible": false,
                    "searchable": false
                },
            ],
            columns: [
                { data: 'addDatetime' },
                { data: 'modDatetime' },
                { data: 'addDate', width: '14%' },
                { data: 'modDate', width: '14%' },
                { data: 'name' },
                { data: 'qty', width: '5%' },
                { data: 'UOM', width: '7%' },
                { data: 'serial' },
                { data: 'location' },
                { data: 'rack', width: '8%' },
                { data: 'row', width: '8%' }
            ],
            order: [],
            initComplete: function(){
                return notifyDeadline();
            }
        });
    }
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
    if(trdata.serialize == 'NO'){
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
    if($('#loading').is(":visible")){
        return false;
    }
    serialID = $('#z_serial').val().toUpperCase();
    $.ajax({
        url: '/serial_data',
        data:{
            serial: serialID
        },
        success: function(data){
            destroyTables();
            $('#btnGenerate').show();
            $('#btnDownload').hide();
            $('#CategoryTableDiv').hide();
            $('#ItemTableDiv').hide();
            $('#ItemSerialTableDiv').hide();
            $('#SerialTableDiv').show();
            $('#MinStocksTableDiv').hide();
            $('#btnBack').hide();
            $('#backBtn').show();
            $('#stocksHeader').html($('#z_serial').val());
            SerialTable = $('table.SerialTable').DataTable({
                aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
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
                        "visible": false,
                        "searchable": true
                    },
                    {
                        "targets": [2,3],
                        "render": $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'MMM. DD, YYYY, h:mm A')
                    },
                ],
                columns: [
                    { data: 'addDatetime' },
                    { data: 'modDatetime' },
                    { data: 'addDate', width: '14%' },
                    { data: 'modDate', width: '14%' },
                    { data: 'name', width: '14%' },
                    { data: 'item' },
                    { data: 'serial', width: '14%' },
                    { data: 'location', width: '14%' }
                ],
                order: [],
                initComplete: function(){
                    return notifyDeadline();
                }
            });
        }
    });
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
    if(trdata.serialize == 'NO'){
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

document.querySelectorAll('input[type=search]').forEach(function(input){
    input.addEventListener('mouseup', function(e){
        if(input.value.length > 0){
            setTimeout(function(){
                if(input.value.length === 0){
                    $('#stocksHeader').html('');
                    $('#z_serial').keyup();
                }
            }, 0);
        }
    });
});

$('#btnGenerate').on('click', function(){
    destroyTables();
    $('#btnGenerate').hide();
    $('#btnDownload').show();
    $('#CategoryTableDiv').hide();
    $('#ItemTableDiv').hide();
    $('#ItemSerialTableDiv').hide();
    $('#SerialTableDiv').hide();
    $('#MinStocksTableDiv').show();
    $('#btnBack').hide();
    $('#backBtn').show();
    $('#stocksHeader').html('BELOW MINIMUM STOCKS');
    $('#loading').show();
    MinStocksTable = $('table.MinStocksTable').DataTable({
        dom: 'Blftrip',
        buttons: ['excel'],
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        iDisplayLength: -1,
        serverSide: true,
        ajax:{
            url: '/minstocks_data',
        },
        columns: [
            { data: 'Category', width: '15%' },
            { data: 'ProdCode', width: '15%' },
            { data: 'Item' },
            {
                data: 'Current_stocks',
                "render": function(data, type, row){
                    return row.Current_stocks+'-'+row.uom+'/s'
                }, 
                width: '12%'
            },
            {
                data: 'Minimum_stocks',
                "render": function(data, type, row){
                    return row.Minimum_stocks+'-'+row.uom+'/s'
                }, 
                width: '12%'
            }
        ],
        order: [],
        initComplete: function(){
            return notifyDeadline();
        }
    });
});

var data_update;
setInterval(function(){
    if($('#addStock').is(':hidden') && $('#importStock').is(':hidden') && $('#reportModal').is(':hidden') && $('#changePassword').is(':hidden') && $('#loading').is(':hidden')){
        $.ajax({
            url: "/stocks/reload",
            success: function(data){
                if(data != data_update){
                    data_update = data;
                    if($('#CategoryTableDiv').is(':visible')){
                        CategoryTable.ajax.reload(null, false);
                    }
                    if($('#ItemTableDiv').is(':visible')){
                        ItemTable.ajax.reload(null, false);
                    }
                    if($('#ItemSerialTableDiv').is(':visible')){
                        ItemSerialTable.ajax.reload(null, false);
                    }
                    if($('#SerialTableDiv').is(':visible')){
                        SerialTable.ajax.reload(null, false);
                    }
                    if($('#MinStocksTableDiv').is(':visible')){
                        MinStocksTable.ajax.reload(null, false);
                    }
                }
            }
        });
    }
}, 3000);

$('#btnDownload').on('click', function(){
    $('.buttons-excel').click();
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
                        table.ajax.reload();
                    }
                    else if(data == 'duplicate'){
                        $('#loading').hide();
                        Swal.fire({
                            title: "DUPLICATE SERIAL",
                            text: "Serial already exists!",
                            icon: "error",
                            timer: 2000
                        });
                        table.ajax.reload();
                    }
                    else{
                        $('#loading').hide();
                        Swal.fire({
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
                    $('#qty').prop('disabled', true);
                }
                else{
                    $('#serialdiv').hide();
                    $('#serial').prop('required', false);
                    $('#qty').prop('disabled', false);
                }
                $('#prodcode').val(data[0].prodcode);
                $('#uom').val(data[0].UOM);
                $('#qty').val('1');
            }
            else{
                $('#prodcodediv').show();
                $('#uomdiv').show();
                $('#qtydiv').show();
                $('#serialdiv').hide();
                $('#prodcode').val(data[0].prodcode);
                $('#uom').val(data[0].UOM);
                $('#qty').val('1');
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
            if(['N/A', 'N /A', 'N/ A', 'N / A', 'NA', 'N A', 'NONE', 'N O N E'].includes(serial) == true || serial.length < 5){
                Swal.fire('INVALID ENTRY','Please enter only valid information!','error');
                return false;
            }
            if(!serial.match(/\d+/g) && serial){
                Swal.fire("INVALID ENTRY", "Item Serial should at least contain numeric characters!", "error");
                return false;
            }
            if(!serial){
                serial = 'N/A';
            }
            Swal.fire({
                title: "Are you really sure all details are entered correctly?",
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
                    $.ajax({
                        url: "/stocks/save",
                        type: "POST",
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{
                            _token: $("#csrf").val(),
                            category: category,
                            item: item,
                            location: location_id,
                            uom: uom,
                            qty: qty,
                            serial: serial,
                            rack: rack,
                            row: row,
                            item_name: item_name,
                            location_name: location_name
                        },
                        success: function(data){
                            if(data == 'true'){
                                $('#addStock').hide();
                                Swal.fire("SAVE SUCCESS", "Stock added successfully!", "success");
                                setTimeout(function(){window.location.href="/stocks"}, 2000);
                            }
                            else if(data == 'duplicate'){
                                Swal.fire("DUPLICATE SERIAL", "Serial already exists!", "error");
                                return false;
                            }
                            else{
                                $('#addStock').hide();
                                Swal.fire("SAVE FAILED", "Failed to add stock!", "error");
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
            Swal.fire({
                title: "Are you really sure all details are entered correctly?",
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
                    $.ajax({
                        url: "/stocks/save",
                        type: "POST",
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                                Swal.fire("SAVE SUCCESS", "Stock added successfully!", "success");
                                setTimeout(function(){window.location.href="/stocks"}, 2000);
                            }
                            else{
                                Swal.fire("SAVE FAILED", "Failed to add stock!", "error");
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
        Swal.fire('EXCEEDED allowed number of file upload!', 'Please upload only ONE (1) valid EXCEL file.', 'error');      
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
        Swal.fire('INVALID file type AND EXCEEDED maximum file size (10MB)!', 'Please upload an EXCEL file with valid file type like the following: xls or xlsx; AND with file size not greater than 10MB.', 'error');      
        $('#xlsx').val('');
        $('#xlsx').focus();
        return false;
    }
    else if(error_ext > 0){
        Swal.fire('INVALID file type!', 'Please upload an EXCEL file with valid file type like the following: xls or xlsx.', 'error');      
        $('#xlsx').val('');
        $('#xlsx').focus();
        return false;
    }
    else if(error_mb > 0){
        Swal.fire('EXCEEDED maximum file size (10MB)!', 'Please upload a valid EXCEL file with file size not greater than 10MB.', 'error');      
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
        Swal.fire({
            title: "UPLOAD FILE IMPORT?",
            text: "Click 'Confirm' button to ADD STOCKS via uploading import file; otherwise, click 'Cancel' button to select a different file.",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                $('#btnSubmit').click();
            }
        });
    }
});

$(document).ready(function(){
    if($(location).attr('pathname')+window.location.search == '/stocks?import=success_without_errors'){
        $('#loading').hide();
        Swal.fire("IMPORT SUCCESS", "ADD STOCKS via import file is successful without errors.", "success");
    }
    else if($(location).attr('pathname')+window.location.search == '/stocks?import=success_with_errors'){
        $('#loading').hide();
        Swal.fire("IMPORT SUCCESS W/ ERRORS", "ADD STOCKS via import file is successful with some errors.", "warning");
    }
    else if($(location).attr('pathname')+window.location.search == '/stocks?import=failed'){
        $('#loading').hide();
        Swal.fire("IMPORT FAILED", "ADD STOCKS via import file has failed.", "error");
    }
    else if($(location).attr('pathname')+window.location.search == '/stocks?min=below'){
        $('#btnGenerate').click();
    }
    else if(($(location).attr('pathname')+window.location.search).includes('item_id') == true && 
    ($(location).attr('pathname')+window.location.search).includes('location_id') == true){
        var url = new URL(window.location.href);
        var item_id = url.searchParams.get("item_id");
        var location_id = url.searchParams.get("location_id");
        $.ajax({
            url: '/stocks/add',
            data:{
                'item_id': item_id
            },
            success: function(data){
                if(data == 'false'){
                    window.location.href = "/stocks";
                }
                else{
                    $('#location').val(location_id);
                    $('#category').val(data);
                    $('#category').change();
                    setTimeout(function(){
                        $('#item').val(item_id);
                        $('#item').change();
                        $('#btnAddStock').click();
                    }, 500);
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
    else if(($(location).attr('pathname')+window.location.search).includes('item') == true){
        url = window.location.search;
        item_id = url.replace('?item=', '');
        $.ajax({
            url: '/stocks/add',
            data:{
                'item_id': item_id
            },
            success: function(data){
                if(data == 'false'){
                    window.location.href = "/stocks";
                }
                else{
                    $('#category').val(data);
                    $('#category').change();
                    setTimeout(function(){
                        $('#item').val(item_id);
                        $('#item').change();
                        $('#btnAddStock').click();
                    }, 500);
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