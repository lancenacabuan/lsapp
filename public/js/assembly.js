$(document).ready(function(){
    $('#assemblyTable').DataTable();
});

$(document).on('click', '.close', function(){
    location.reload();
});

$(document).on('click', '#btnClose', function(){
    location.reload();
});

$(document).on('change', '#categoryItm', function(){ 
    var id=$('#categoryItm').val();
    var descOp = " ";
    $.ajax({ 
        type:'get', 
        url:'/itemsItm', 
        data:{'category_id':id}, 
        success: function(data) 
            {
                var itemcode = $.map(data, function(value, index) { 
                    return [value];
                });
                descOp+='<option selected disabled>Select Item</option>'; 
                itemcode.forEach(value => {
                    descOp+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>'; 
                });
                
                $("#itemItm").find('option').remove().end().append(descOp);                 
            },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/assembly';
            }
            alert(data.responseText);
        }
    });    
});

$(".add-row").on('click', function(){                   
    var category = $("#categoryItm option:selected").text();
    var item = $("#itemItm option:selected").text();
    let qty = $("#qtyItm").val();
    var markup = "<tr><td>" + category + "</td><td>" + item + "</td><td>" + qty + "</td><td> <button type='button' class='delete-row btn-primary btn-xs bp'>REMOVE</button> </td></tr>";
    var ctr='false';
    if(category == "Select Category" || item == "Select Item" || qty == "" || qty == "0"){
        swal('REQUIRED','Please select item!','error');
        return false;
    }
    else{
        var table = document.getElementById('tblCreateItem');
        var count = table.rows.length;
        for (i = 1; i < count; i++) {

            var objCells = table.rows.item(i).cells;

            if(item==objCells.item(1).innerHTML){
                objCells.item(2).innerHTML = parseInt(objCells.item(2).innerHTML) + parseInt(qty);
                ctr='true';
                category = $("#categoryItm").val('Select Category');
                item = $("#itemItm").find('option').remove().end().append('<option value="0">Select Item</option>').val()
                qty = $("#qtyItm").val('');
                return false;
            }
            else {
                ctr='false';
            }
        }
        if(ctr=='false')
        { $("#tblCreateItem tbody").append(markup); }
        category = $("#categoryItm").val('Select Category');
        item = $("#itemItm").find('option').remove().end().append('<option value="0">Select Item</option>').val()
        qty = $("#qtyItm").val('');
        $('#tblCreateItem').show();
        $('#divCreateItem').toggle();
        $('#btnClose').show();
        $('#btnSave').show();
    } 
});

$("#tblCreateItem").on('click', '.delete-row', function(){
    $(this).closest("tr").remove();
    if ($('#tblCreateItem tbody').children().length==0) {
        $('#tblCreateItem').hide();
        $('#divCreateItem').removeClass();
        $('#btnClose').hide();
        $('#btnSave').hide();
    }
});