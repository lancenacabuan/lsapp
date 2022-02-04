var CategoryTable;

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
    $('#ItemTableDiv').hide();
    CategoryTable = 
        $('table.CategoryTable').DataTable({ 
            ajax: 'category_data',
            columns: [
                { data: 'Category'},
                { data: 'Defective'},
                { data: 'Demo'},
                { data: 'Assembly'},
                { data: 'A1'},
                { data: 'A2'},
                { data: 'A3'},
                { data: 'A4'},
                { data: 'Balintawak'},
                { data: 'Malabon'},
                { data: 'Total_stocks'}
            ]          
        });
}
$(document).ready(function () {   
    category();
});

$(document).on('click', '#CategoryTable tbody tr', function () {
    var trdata = CategoryTable.row(this).data();
    $('#CategoryTableDiv').hide();
    $('#ItemTableDiv').show();
    $('#itemCat').text(trdata.Category);
    $('#backBtn').show();
    $('table.ItemTable').dataTable().fnDestroy();
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
                { data: 'Defective'},
                { data: 'Demo'},
                { data: 'Assembly'},
                { data: 'A1'},
                { data: 'A2'},
                { data: 'A3'},
                { data: 'A4'},
                { data: 'Balintawak'},
                { data: 'Malabon'},
                { data: 'Total_stocks'}
            ]          
        });
});
    
$(document).on('click', '#butsave', function() {
    var AddStockForm = $('#AddStockForm');
    var category = $('#category').val();
    var item = $('#item').val();
    var location = $('#location').val();
    var qty = $('#quantity').val();
    if(category!="" && item!="" && location!="" && qty!=""){
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
                qty: qty
            },
            success: function(dataResult){                      
                $('#addStock').hide();
                sweetAlert("SAVED", "ITEM SUCCESFULLY", "success");
                setTimeout(function(){window.location.href="/stocks"} , 2000);                                   
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
});
$(document).on('click', '#backBtn', function(){
    category();
});

