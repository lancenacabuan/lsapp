var CategoryTable, ItemTable, ItemSerialTable, categoryID, categoryName;

function category(){
    $('table.CategoryTable').dataTable().fnDestroy();
    $('table.ItemTable').dataTable().fnDestroy();
    $('table.ItemSerialTable').dataTable().fnDestroy();
    $('#CategoryTableDiv').show();
    $('#ItemTableDiv').hide();
    $('#ItemSerialTableDiv').hide();
    $('#btnBack').hide();
    $('#backBtn').hide();
    $('#loading').show(); Spinner(); Spinner.show();
    CategoryTable = 
        $('table.CategoryTable').DataTable({
            serverSide: true,
            ajax: 'category_data',
            columns: [
                { data: 'Category' },
                { data: 'Defective' },
                { data: 'Demo' },
                { data: 'Assembly' },
                { data: 'A1' },
                { data: 'A2' },
                { data: 'A3' },
                { data: 'A4' },
                { data: 'Balintawak' },
                { data: 'Malabon' },
                { data: 'Total_stocks' }
            ],
            initComplete: function(){
                $('#loading').hide(); Spinner.hide();
            }
        });
}

$(document).ready(function(){   
    category();
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
    $('#CategoryTableDiv').hide();
    $('#ItemTableDiv').show();
    $('#ItemSerialTableDiv').hide();
    $('#itemCat').text(decodeHtml(trdata.Category));
    $('#btnBack').hide();
    $('#backBtn').show();
    $('#loading').show(); Spinner(); Spinner.show();
    ItemTable = 
        $('table.ItemTable').DataTable({
            serverSide: true,
            ajax: {
                url: 'item_data',
                data:{
                    CategoryId: trdata.id
                }
            },
            columns: [
                { data: 'Item' },
                { data: 'Defective' },
                { data: 'Demo' },
                { data: 'Assembly' },
                { data: 'A1' },
                { data: 'A2' },
                { data: 'A3' },
                { data: 'A4' },
                { data: 'Balintawak' },
                { data: 'Malabon' },
                { data: 'Total_stocks' }
            ],
            initComplete: function(){
                $('#loading').hide(); Spinner.hide();
            }
        });
});

$('#btnBack').on('click', function(){
    $('table.CategoryTable').dataTable().fnDestroy();
    $('table.ItemTable').dataTable().fnDestroy();
    $('table.ItemSerialTable').dataTable().fnDestroy();
    $('#CategoryTableDiv').hide();
    $('#ItemTableDiv').show();
    $('#ItemSerialTableDiv').hide();
    $('#itemCat').text(categoryName);
    $('#btnBack').hide();
    $('#backBtn').show();
    $('#loading').show(); Spinner(); Spinner.show();
    ItemTable = 
        $('table.ItemTable').DataTable({
            serverSide: true,
            ajax: {
                url: 'item_data',
                data:{
                    CategoryId: categoryID
                }
            },
            columns: [
                { data: 'Item' },
                { data: 'Defective' },
                { data: 'Demo' },
                { data: 'Assembly' },
                { data: 'A1' },
                { data: 'A2' },
                { data: 'A3' },
                { data: 'A4' },
                { data: 'Balintawak' },
                { data: 'Malabon' },
                { data: 'Total_stocks' }
            ],
            initComplete: function(){
                $('#loading').hide(); Spinner.hide();
            }
        });
});

$(document).on('click', '#ItemTable tbody tr', function(){
    var trdata = ItemTable.row(this).data();
    $('table.CategoryTable').dataTable().fnDestroy();
    $('table.ItemTable').dataTable().fnDestroy();
    $('table.ItemSerialTable').dataTable().fnDestroy();
    $('#CategoryTableDiv').hide();
    $('#ItemTableDiv').hide();
    $('#ItemSerialTableDiv').show();
    $('#itemName').text(decodeHtml(trdata.Item));
    $('#btnBack').show();
    $('#backBtn').hide();
    $('#loading').show(); Spinner(); Spinner.show();
    ItemSerialTable = 
        $('table.ItemSerialTable').DataTable({
            serverSide: true,
            ajax: {
                url: 'itemserial_data',
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
            order:[[1, 'desc']],
            initComplete: function(){
                $('#loading').hide(); Spinner.hide();
            }
        });
});

$(document).on('click', '#ItemSerialTable tbody tr', function(){
    if($("#current_role").val() == '["viewer"]'){
        return false;
    }
    var trdata = ItemSerialTable.row(this).data();
    if(trdata.status == 'defectives' || trdata.status == 'FOR RECEIVING'){
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
                            $('#loading').hide(); Spinner.hide();
                            swal({
                                title: "EDIT FAILED",
                                text: "ITEM SERIAL",
                                icon: "error",
                                timer: 2000
                            });
                            $('table.ItemSerialTable').DataTable().ajax.reload();
                        }
                        else{
                            $('#loading').hide(); Spinner.hide();
                            swal({
                                title: "EDIT SUCCESS",
                                text: "ITEM SERIAL",
                                icon: "success",
                                timer: 2000
                            });
                            $('table.ItemSerialTable').DataTable().ajax.reload();
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

$('#butsave').on('click', function(){
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
    if(!$('#serial').val()){
        serial = 'N/A';
    }
    if($('#serial').is(':visible')){
        if(category && item && location_id){
            swal({
                title: "Are you really sure all details are entered correctly?",
                text: "Click 'OK' button to submit; otherwise, click 'Cancel' button to recheck details.",
                icon: "warning",
                buttons: true,
                dangerMode: true
            })
            .then((willDelete) => {
                if(willDelete){
                    $.ajax({
                        url: "stocks/save",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $("#csrf").val()
                        },
                        data: {
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
                        success: function(dataResult){
                            $('#addStock').hide();
                            swal("SAVED", "ITEM SUCCESSFULLY ADDED", "success").then(function(){
                                window.location.href = 'stocks';
                            });
                            setTimeout(function(){window.location.href = 'stocks';}, 2000);
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
                text: "Click 'OK' button to submit; otherwise, click 'Cancel' button to recheck details.",
                icon: "warning",
                buttons: true,
                dangerMode: true
            })
            .then((willDelete) => {
                if(willDelete){
                    $.ajax({
                        url: "stocks/save",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $("#csrf").val()
                        },
                        data: {
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
                        success: function(dataResult){
                            $('#addStock').hide();
                            swal("SAVED", "ITEM SUCCESSFULLY ADDED", "success").then(function(){
                                window.location.href = 'stocks';
                            });
                            setTimeout(function(){window.location.href = 'stocks';}, 2000);
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

$('#category').on('change', function(){
    var id = $('#category').val();
    var descOp = " ";
    $.ajax({
        type: 'get',
        url: '/addStockitem',
        data: { 'category_id': id },            
        success: function(data){
            var itemcode = $.map(data, function(value, index){
                return [value];
            });
            descOp+='<option value="" selected disabled>Select Item</option>';
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
    $('#uomdiv').hide();
    $('#qtydiv').hide();
    $('#serialdiv').hide();
});

$('#item').on('change', function(){
    var id = $('#item').val();
    $.ajax({
        type: 'get',
        url: 'getUOM',
        data: { 'id': id },            
        success: function(data){
            if(data.uom == "Unit"){
                $('#uomdiv').show();
                $('#qtydiv').show();
                $('#serialdiv').show();
                $('#uom').val(data.uom);
                $('#qty').val('1');
                $('#qty').prop('disabled', true);
            }
            else{
                $('#uomdiv').show();
                $('#qtydiv').show();
                $('#serialdiv').hide();
                $('#uom').val(data.uom);
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

$('#backBtn').on('click', function(){
    category();
});

$('#btnReset').on('click', function(){
    $('#AddStockForm').trigger('reset');
    $('#uomdiv').hide();
    $('#qtydiv').hide();
    $('#serialdiv').hide();
});