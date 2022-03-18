$('#assemblyTable').DataTable();

$('.close').on('click', function(){
    location.reload();
});

$('#btnClose').on('click', function(){
    location.reload();
});

setInterval(runFunction, 200);
function runFunction(){
    var category = $('#category').val();
    var item_description = $.trim($('#item_description').val());
    if($('#createItem').is(':visible')){
        if(category && item_description){
            $('#partsDetails').show();
        }
        else{
            $('#partsDetails').hide();
        }
    }
}

$('#categoryAssembly').on('change', function(){
    var id = $('#categoryAssembly').val();
    var descOp = " ";
    $.ajax({
        type:'get',
        url:'/itemsAssembly',
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
                
                $("#itemAssembly").find('option').remove().end().append(descOp);                 
            },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/assembly';
            }
            alert(data.responseText);
        }
    });
});

$('#itemAssembly').on('change', function(){
    var item_id = $(this).val();
    $.ajax({
        type:'get',
        url:'/uomAssembly',
        data:{
            'item_id': item_id,
        }, 
        success: function(data){
            $('#uomAssembly').val(data);
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/assembly';
            }
            alert(data.responseText);
        }
    });
});

$(".add-row").on('click', function(){
    var category = $("#categoryAssembly option:selected").text();
    var item = $("#itemAssembly option:selected").text();
    let qty = $("#qtyAssembly").val();
    var uom = $("#uomAssembly").val();
    var markup = "<tr><td>" + category + "</td><td>" + item + "</td><td>" + qty + "</td><td>" + uom + "</td><td> <button type='button' style='zoom: 75%;' class='delete-row btn btn-primary bp'>REMOVE</button> </td></tr>";
    var ctr = 'false';
    if(category == "Select Category" || item == "Select Item" || qty == "" || qty == "0" || uom == ""){
        swal('REQUIRED','Please select an item!','error');
        return false;
    }
    else{
        var table = document.getElementById('tblCreateItem');
        var count = table.rows.length;
        for(i = 1; i < count; i++){
            var objCells = table.rows.item(i).cells;
            if(item==objCells.item(1).innerHTML){
                objCells.item(2).innerHTML = parseInt(objCells.item(2).innerHTML) + parseInt(qty);
                ctr = 'true';
                category = $("#categoryAssembly").val('');
                item = $("#itemAssembly").find('option').remove().end().append('<option value="0">Select Item</option>').val()
                qty = $("#qtyAssembly").val('');
                uom = $('#uomAssembly').val('');
                return false;
            }
            else {
                ctr = 'false';
            }
        }
        if(ctr == 'false')
        { $("#tblCreateItem tbody").append(markup); }
        category = $("#categoryAssembly").val('');
        item = $("#itemAssembly").find('option').remove().end().append('<option value="0">Select Item</option>').val()
        qty = $("#qtyAssembly").val('');
        uom = $('#uomAssembly').val('');
        $('#tblCreateItem').show();
        $('#divCreateItem').toggle();
        $('#btnClose').show();
        $('#btnSave').show();
    } 
});

$("#tblCreateItem").on('click', '.delete-row', function(){
    $(this).closest("tr").remove();
    if($('#tblCreateItem tbody').children().length==0){
        $('#tblCreateItem').hide();
        $('#divCreateItem').removeClass();
        $('#btnClose').hide();
        $('#btnSave').hide();
    }
});

$('#btnSave').on('click', function(){
    var category = $('#category').val();
    var item_description = $.trim($('#item_description').val());
    swal({
        title: "CREATE NEW ASSEMBLY ITEM?",
        text: "You are about to CREATE a new Assembly Item!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if(willDelete){
            $.ajax({
                type:'post',
                url:'/createItem',
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                },
                data:{
                    category_id: category,
                    item: item_description
                },
                success: function(data){
                    if(data.result == 'true'){
                        var myTable = $('#tblCreateItem').DataTable();
                        var form_data  = myTable.rows().data();
                        $.each(form_data, function(key, value){
                            $.ajax({
                                type:'post',
                                url:'/saveParts',
                                headers: {
                                    'X-CSRF-TOKEN': $("#csrf").val(),
                                },
                                data:{
                                    item_id: data.id,
                                    category: value[0],
                                    item: value[1],
                                    quantity: value[2]
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
                                        location.reload();
                                    }
                                    alert(data.responseText);
                                }
                            });
                        });
                        $.ajax({
                            type:'post',
                            url:'/logItem',
                            headers: {
                                'X-CSRF-TOKEN': $("#csrf").val()
                            },
                            data:{
                                item_id: data.id,
                                category: $("#category option:selected").text(),
                                item: item_description
                            },
                            success: function(data){
                                if(data == 'true'){
                                    $('#createItem').hide();
                                    swal("SUBMIT SUCCESS", "CREATE ITEM", "success");
                                    setTimeout(function(){location.reload();}, 2000);
                                }
                                else{
                                    return false;
                                }
                            },
                            error: function(data){
                                if(data.status == 401){
                                    location.reload();
                                }
                                alert(data.responseText);
                            }
                        });
                    }
                    else if(data.result == 'duplicate'){
                        swal("DUPLICATE ITEM", "Item Description already exists!", "error");
                        return false;
                    }
                    else{
                        $('#newStockRequest').hide();
                        swal("SUBMIT FAILED", "CREATE ITEM", "error");
                        setTimeout(function(){location.reload();}, 2000);
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        location.reload();
                    }
                    alert(data.responseText);
                }
            });
        }
    });
});