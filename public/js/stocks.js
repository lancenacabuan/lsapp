var CategoryTable, location, ItemSerialTable, ItemTable;

function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 45  && charCode > 31 && (charCode < 48 || charCode > 57))
        return false;

    return true;
}

function category() {
    $('table.CategoryTable').dataTable().fnDestroy();
    $('table.ItemTable').dataTable().fnDestroy();
    $('#CategoryTableDiv').show();
    $('#backBtn').hide();
    $('#ItemSerialTableDiv').hide();
    $('#ItemTableDiv').hide();
    $('#loading').show();
    CategoryTable = 
        $('table.CategoryTable').DataTable({ 
            ajax: 'category_data',
            columns: [
                { data: 'Category'},
                { data: 'A1'},
                { data: 'A2'},
                { data: 'A3'},
                { data: 'A4'},
                { data: 'Balintawak'},
                { data: 'Malabon'},
                { data: 'Total_stocks'}
            ],
            initComplete: function (){
                $('#loading').hide();
            }
        });
}
$(document).ready(function () {   
    category();
    $('#serialdiv').hide();
    $('#qtydiv').hide();
});

$(document).on('click', '#CategoryTable tbody tr', function () {
    var trdata = CategoryTable.row(this).data();
    $('#CategoryTableDiv').hide();
    $('#ItemSerialTableDiv').hide();
    $('#ItemTableDiv').show();
    $('#itemCat').text(decodeHtml(trdata.Category));
    $('#backBtn').show();
    $('table.ItemTable').dataTable().fnDestroy();
    $('#loading').show();
    ItemTable = 
        $('table.ItemTable').DataTable({ 
            ajax: {
                url: 'item_data',
                data:{
                    CategoryId: trdata.id
                }
            },
            columns: [
                { data: 'Item'},
                { data: 'A1'},
                { data: 'A2'},
                { data: 'A3'},
                { data: 'A4'},
                { data: 'Balintawak'},
                { data: 'Malabon'},
                { data: 'Total_stocks'}
            ],
            initComplete: function (){
                $('#loading').hide();
            }
        });
});

$(document).on('click', '#ItemTable tbody tr', function () {
    var trdata = ItemTable.row(this).data();
    $('#CategoryTableDiv').hide();
    $('#ItemSerialTableDiv').show();
    $('#ItemTableDiv').hide();
    $('#itemName').text(decodeHtml(trdata.Item));
    $('#backBtn').show();
    $('table.ItemSerialTable').dataTable().fnDestroy();
    $('#loading').show();
    ItemSerialTable = 
        $('table.ItemSerialTable').DataTable({ 
            ajax: {
                url: 'itemserial_data',
                data:{
                    ItemId: trdata.id
                }
            },
            columns: [
                { data: 'item'},
                { data: 'serial'},
                { data: 'location'},
                { data: 'rack'},
                { data: 'row'}
            ],
            initComplete: function (){
                $('#loading').hide();
            }
        });
});
    
$(document).on('click', '#butsave', function() {
    var AddStockForm = $('#AddStockForm');
    var category = $('#category').val();
    var item = $('#item').val();
    var location = $('#location').val();
    var rack = $('#rack').val();
    var row = $('#row').val();
    var serial = $('#serial').val();
    if (!$('#serial').val()) {
        serial = 'N/A';
    }
    var qty = $('#qty').val();
    if ($('#serial').is(':visible')) {
        if(category && item && location){
            $.ajax({
                url: "stocks/save",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                },
                data: {
                    _token: $("#csrf").val(),
                    category: category,
                    item: item,
                    location: location,
                    serial: serial,
                    rack: rack,
                    row: row
                },
                success: function(dataResult){                      
                    $('#addStock').hide();
                    sweetAlert("SAVED", "ITEM SUCCESSFULLY ADDED", "success").then(function() {
                        window.location.href = 'stocks';
                    });
                    setTimeout(function(){window.location.href = 'stocks';} , 2000);                                   
                },
                error: function (data) {
                    if(data.status == 401) {
                        window.location.href = '/login';
                    }
                    alert(data.responseText);
                }
            });
        }else{
            AddStockForm[0].reportValidity();
        }
    }
    else{
        if (qty && qty != 0) {
            if(category && item && location){
                $.ajax({
                    url: "stocks/save",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $("#csrf").val(),
                    },
                    data: {
                        _token: $("#csrf").val(),
                        category: category,
                        item: item,
                        location: location,
                        qty: qty,
                        rack: rack,
                        row: row
                    },
                    success: function(dataResult){                      
                        $('#addStock').hide();
                        sweetAlert("SAVED", "ITEM SUCCESSFULLY ADDED", "success").then(function() {
                            window.location.href = 'stocks';
                        });
                        setTimeout(function(){window.location.href = 'stocks';} , 2000);                                   
                    },
                    error: function (data) {
                        if(data.status == 401) {
                            window.location.href = '/login';
                        }
                        alert(data.responseText);
                    }
                });
            }else{
                AddStockForm[0].reportValidity();
            }
        }else{
            AddStockForm[0].reportValidity();
        }
    }
});

$(document).on('change', '#category', function(){
    var id=$('#category').val();
    var descOp = " ";
        $.ajax({
            type:'get',
            url:'/addStockitem',
            data:{'category_id':id},            
            success:function(data)
                {
                    var itemcode = $.map(data, function(value, index) {
                        return [value];
                    });
                    descOp+='<option value="" selected disabled>Select Item</option>';
                    itemcode.forEach(value => {
                        descOp+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>';
                    });
                    $("#item").find('option').remove().end().append(descOp);               
                },
            error: function (data) {
                if(data.status == 401) {
                    window.location.href = '/stocks';
                }
                alert(data.responseText);
            }
        });
    $('#serialdiv').hide();
    $('#qtydiv').hide();
});

$(document).on('change', '#item', function(){
    var id=$('#item').val();
    var descOp = " ";
        $.ajax({
            type:'get',
            url:'getUOM',
            data:{'id':id},            
            success:function(data)
                {
                    if (data.uom == "Unit") {
                        $('#serialdiv').show();
                        $('#qtydiv').hide();
                    }else{
                        $('#serialdiv').hide();
                        $('#qtydiv').show();
                    }
                },
            error: function (data) {
                if(data.status == 401) {
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
    return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function(m) {return map[m];});
}

$(document).on('click', '#backBtn', function(){
    category();
});

$(document).on('click', '.close', function(){
    location.reload();
});