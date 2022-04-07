var CategoryTable, ItemSerialTable, ItemTable, categoryID, categoryName;

function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if(charCode != 45  && charCode > 31 && (charCode < 48 || charCode > 57))
        return false;

    return true;
}

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
            columns: [
                { data: 'item' },
                { data: 'serial' },
                { data: 'location' },
                { data: 'rack' },
                { data: 'row' }
            ],
            initComplete: function(){
                $('#loading').hide(); Spinner.hide();
            }
        });
});
    
$('#butsave').on('click', function(){
    var AddStockForm = $('#AddStockForm');
    var category = $('#category').val();
    var item = $('#item').val();
    var location_id = $('#location').val();
    var rack = $('#rack').val();
    var row = $('#row').val();
    var uom = $('#uom').val();
    var qty = $('#qty').val();
    var serial = $('#serial').val();
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
        else{
            AddStockForm[0].reportValidity();
        }
    }
    else{
        if(qty && qty != 0){
            if(category && item && location_id){
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
            else{
                AddStockForm[0].reportValidity();
            }
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

$('.close').on('click', function(){
    location.reload();
});