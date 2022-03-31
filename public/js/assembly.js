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
        type:'get',
        url:'/assembly/generateReqNum',
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
                location.reload();
            }
                alert(data.responseText);
        }
    });
}

$(".btnNewAssembly").on('click', function(){
    $('#newAssembly').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('.modal-body').html();
    $('#newAssembly').modal('show');
    generateReqNum();
});

setInterval(checkNewAssembly, 200);
function checkNewAssembly(){
    if($('#newAssembly').is(':visible')){
        if($('#needdate').val() && $('#assembly').val() && $('#qty').val() > 0 && $('#assemblypartsDetails').is(':hidden')){
            $('#btnAssemblyProceed').show();
        }
        else{
            $('#btnAssemblyProceed').hide();
        }
    }
}

setInterval(checkCreateItem, 200);
function checkCreateItem(){
    var category = $('#aic_category').val();
    var item_description = $.trim($('#aic_item_description').val());
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
    var category = $('#aic_category').val();
    var item_description = $.trim($('#aic_item_description').val());
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

setInterval(runCompare, 200);
function runCompare(){
    if($('#detailsAssemblyItem').is(':visible')){
        var item_current = $.trim($('#aim_item_name_details').val());
        var item_original = $('#aim_item_name_details_original').val();
        var category_current = $('#aim_item_category_details').val();
        var category_original = $('#aim_item_category_details_original').val();
        if(!item_current || (item_current.toUpperCase() == item_original.toUpperCase() && category_current == category_original)){
            $('#btnUpdate').hide();
        }
        else{
            $('#btnUpdate').show();
        }
    }
}

$('#assemblyitemTable tbody').on('click', 'tr', function(){
    $('#detailsAssemblyItem').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table = $('table.assemblyitemTable').DataTable(); 
    var data = table.row(this).data();
    var item_id = data.id;
    $('#aim_item_id').val(item_id);
    var category_name = data.category;
    $('#aim_category_name_details_original').val(category_name);
    var item_category = data.category_id;
    $('#aim_item_category_details').val(item_category);
    $('#aim_item_category_details_original').val(item_category);
    var item_name = decodeHtml(data.item);
    $('#aim_item_name_details').val(item_name);
    $('#aim_item_name_details_original').val(item_name);
    
    $('.modal-body').html();
    $('#detailsAssemblyItem').modal('show');
    
    $('table.tblItemDetails').dataTable().fnDestroy();
    $('table.tblItemDetails').DataTable({
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        language: {
            emptyTable: "No data available in table",
            processing: "Loading...",
        },
        serverSide: true,
        ajax: {
            url: '/itemDetails',
            data: {
                item_id: item_id
            },
            dataType: 'json',
            error: function(data){
                if(data.status == 401){
                    location.reload();
                }
                alert(data.responseText);
            },
        },
        order:[[0, 'asc'],[1, 'asc']],
        columns: [
            { data: 'category' },
            { data: 'item' },
            { data: 'uom' },
            { data: 'quantity' }
        ],
        orderCellsTop: true,
        fixedHeader: true,            
    });
});

$('#btnUpdate').on('click', function(){
    var item_id = $('#aim_item_id').val();
    var category_name_original = $('#aim_category_name_details_original').val();
    var item_category_original = $('#aim_item_category_details_original').val();
    var item_name_original = $('#aim_item_name_details_original').val();
    var category_name = $('#aim_item_category_details').find('option:selected').text();
    var item_category = $('#aim_item_category_details').val();
    var item_name = $.trim($('#aim_item_name_details').val());
    swal({
        title: "UPDATE ASSEMBLY ITEM?",
        text: "You are about to UPDATE this Assembly Item!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if(willDelete){
            $.ajax({
                type:'post',
                url:'/changeItem',
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                },
                data: {
                    item_id: item_id,
                    category_name_original: category_name_original,
                    item_category_original: item_category_original,
                    item_name_original: item_name_original,
                    category_name: category_name,
                    item_category: item_category,
                    item_name: item_name
                },
                success: function(data){
                    if(data == 'true'){
                        $('#detailsAssemblyItem').hide();
                        swal("UPDATE SUCCESS", "ASSEMBLY ITEM", "success");
                        setTimeout(function(){location.reload();}, 2000);
                    }
                    else if(data == 'duplicate'){
                        swal("DUPLICATE ITEM", "Item Description already exists!", "error");
                        return false;
                    }
                    else{
                        $('#updateUser').hide();
                        swal("UPDATE FAILED", "ASSEMBLY ITEM", "error");
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

$('.close').on('click', function(){
    location.reload();
});

$('#btnClose').on('click', function(){
    location.reload();
});

$('#assemblyTable').DataTable();

$('#btnAssemblyProceed').on('click', function(){
    $('#btnAssemblyProceed').hide();
    $('#assemblypartsDetails').show();
    $("#assembly").prop('disabled', true);
    $("#qty").prop('disabled', true);
    $('table.tblPartsDetails').dataTable().fnDestroy();
    $('table.tblPartsDetails').DataTable({
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        language: {
            emptyTable: "No data available in table",
            processing: "Loading...",
        },
        serverSide: true,
        ajax: {
            url: '/partsDetails',
            data: {
                item_id: $("#assembly").val()
            },
            dataType: 'json',
            error: function(data){
                if(data.status == 401){
                    location.reload();
                }
                alert(data.responseText);
            },
        },
        order:[[0, 'asc'],[1, 'asc']],
        columns: [
            { data: 'category' },
            { data: 'item' },
            { data: 'uom' },
            { data: 'quantity' },
            { data: 'main' },
            { data: 'balintawak' },
            { data: 'malabon' }
        ],
        orderCellsTop: true,
        fixedHeader: true,            
    });
    setTimeout(setqty, 200);
});

function setqty(){
    var table = document.getElementById('tblPartsDetails');
    var count = table.rows.length;
    for(i = 1; i < count; i++){
        var objCells = table.rows.item(i).cells;
        objCells.item(3).innerHTML = parseInt(objCells.item(3).innerHTML) * parseInt($("#qty").val());    
    }
}

$('#btnAssemblyBack').on('click', function(){
    $('#btnAssemblyProceed').hide();
    $('table.tblPartsDetails').dataTable().fnDestroy();
    $("#assemblypartsDetails").hide();
    $("#assembly").prop('disabled', false);
    $("#qty").prop('disabled', false);
    $("#assembly").val('');
    $("#qty").val('');
});