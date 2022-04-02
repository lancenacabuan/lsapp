if($(location).attr('pathname')+window.location.search == '/maintenance'){
    $('#nav1').addClass("active-link");
    $('.btnNewItem').show();
    $('#itemTable').show();
    $('#loading').show(); Spinner(); Spinner.show();
    $('table.itemTable').DataTable({
        language: {
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax: {
            url: '/fm_items',
        },
        columnDefs: [
        {
            "targets": [0,1,3,4],
            "visible": false,
            "searchable": false
        }],
        columns: [
            { data: 'id' },
            { data: 'category' },
            { data: 'item' },
            { data: 'category_id' },
            { data: 'UOM' }
        ],
        order:[[2, 'asc']],
        initComplete: function(){
            $('#loading').hide(); Spinner.hide();
        }
    });
}
else if($(location).attr('pathname')+window.location.search == '/maintenance?tbl=assembleditems'){
    $('#nav2').addClass("active-link");
    $('.btnCreateItem').show();
    $('#assemblyitemTable').show();
    $('#loading').show(); Spinner(); Spinner.show();
    $('table.assemblyitemTable').DataTable({
        language: {
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax: {
            url: '/asm_items',
        },
        columnDefs: [
        {
            "targets": [0,1,3,4],
            "visible": false,
            "searchable": false
        }],
        columns: [
            { data: 'id' },
            { data: 'category' },
            { data: 'item' },
            { data: 'category_id' },
            { data: 'UOM' }
        ],
        order:[[2, 'asc']],
        initComplete: function(){
            $('#loading').hide(); Spinner.hide();
        }
    });
}
else if($(location).attr('pathname')+window.location.search == '/maintenance?tbl=categories'){
    $('#nav3').addClass("active-link");
    $('.btnNewCategory').show();
    $('#categoryTable').show();
    $('#loading').show(); Spinner(); Spinner.show();
    $('table.categoryTable').DataTable({
        language: {
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax: {
            url: '/fm_categories',
        },
        columnDefs: [
        {
            "targets": [0],
            "visible": false,
            "searchable": false
        }],
        columns: [
            { data: 'id' },
            { data: 'category' }
        ],
        order:[[1, 'asc']],
        initComplete: function(){
            $('#loading').hide(); Spinner.hide();
        }
    });
}
else if($(location).attr('pathname')+window.location.search == '/maintenance?tbl=locations'){
    $('#nav4').addClass("active-link");
    $('.btnNewLocation').show();
    $('#locationTable').show();
    $('#loading').show(); Spinner(); Spinner.show();
    $('table.locationTable').DataTable({
        language: {
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax: {
            url: '/fm_locations',
        },
        columnDefs: [
        {
            "targets": [0],
            "visible": false,
            "searchable": false
        }],
        columns: [
            { data: 'location_id' },
            { data: 'location' },
            { data: 'status' }
        ],
        order:[[1, 'asc']],
        initComplete: function(){
            $('#loading').hide(); Spinner.hide();
        }
    });
}
else{
    window.location.href = '/maintenance';
}

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

$('.close').on('click', function(){
    location.reload();
});

$('#btnClose').on('click', function(){
    location.reload();
});

$('#btnSaveItem').on('click', function(){
    var category_name = $('#item_category').find('option:selected').text();
    var item_category = $('#item_category').val();
    var item_name = $.trim($('#item_name').val());
    var item_uom = $('#item_uom').val();
    if(item_name != "" && $('#item_category').find('option:selected').text() != 'Select Category' && $('#item_uom').find('option:selected').text() != 'Select UOM'){
        swal({
            title: "ADD NEW ITEM?",
            text: "You are about to ADD this new item!",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                    url: "/saveItem",
                    type: "POST",
                    headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                    },
                    data: {
                        _token: $("#csrf").val(),
                        category_name: category_name,
                        item_category: item_category,
                        item_name: item_name,
                        item_uom: item_uom
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('#newItem').hide();
                            swal("SAVE SUCCESS", "New Item has been saved.", "success");
                            setTimeout(function(){window.location.href="/maintenance"}, 2000);
                        }
                        else if(data.result == 'duplicate'){
                            swal("DUPLICATE ITEM", "Item Description already exists!", "error");
                            return false;
                        }
                        else{
                            $('#newItem').hide();
                            swal("SAVE FAILED", "MAINTENANCE - ITEM", "error");
                            setTimeout(function(){window.location.href="/maintenance"}, 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/maintenance';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
    else{
        swal('REQUIRED','Please fill up all required fields!','error');
        return false;
    }
});

$('#itemTable tbody').on('click', 'tr', function(){
    $('#detailsItem').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table = $('table.itemTable').DataTable(); 
    var data = table.row(this).data();
    var item_id = data.id;
        $('#item_id').val(item_id);
    var category_name = data.category;
        $('#category_name_details_original').val(category_name);
    var item_category = data.category_id;
        $('#item_category_details').val(item_category);
        $('#item_category_details_original').val(item_category);
    var item_name = decodeHtml(data.item);
        $('#item_name_details').val(item_name);
        $('#item_name_details_original').val(item_name);
    var item_uom = data.UOM;
        $('#item_uom_details').val(item_uom);
        $('#item_uom_details_original').val(item_uom);
    
    $('.modal-body').html();
    $('#detailsItem').modal('show');
});

$('#btnUpdateItem').on('click', function(){
    var item_id = $('#item_id').val();
    var category_name_original = $('#category_name_details_original').val();
    var item_category_original = $('#item_category_details_original').val();
    var item_name_original = $('#item_name_details_original').val();
    var item_uom_original = $('#item_uom_details_original').val();
    var category_name = $('#item_category_details').find('option:selected').text();
    var item_category = $('#item_category_details').val();
    var item_name = $.trim($('#item_name_details').val());
    var item_uom = $('#item_uom_details').val();
    
    if(item_name == "" || $('#item_category_details').find('option:selected').text() == 'Select Category' || $('#item_uom_details').find('option:selected').text() == 'Select UOM'){
        swal('REQUIRED','Please fill up all required fields!','error');
        return false;
    }
    else if(item_name_original.toUpperCase() == item_name.toUpperCase() && item_category_original == item_category && item_uom_original == item_uom){
        swal("NO CHANGES FOUND", "Item Details are still all the same!", "error");
        return false;
    }
    else{
        swal({
            title: "UPDATE ITEM?",
            text: "You are about to UPDATE this item!",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                    url: "/updateItem",
                    type: "PUT",
                    headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                    },
                    data: {
                        _token: $("#csrf").val(),
                        item_id: item_id,
                        category_name_original: category_name_original,
                        item_category_original: item_category_original,
                        item_name_original: item_name_original,
                        item_uom_original: item_uom_original,
                        category_name: category_name,
                        item_category: item_category,
                        item_name: item_name,
                        item_uom: item_uom
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('#detailsItem').hide();
                            swal("UPDATE SUCCESS", "Item has been updated.", "success");
                            setTimeout(function(){window.location.href="/maintenance"}, 2000);
                        }
                        else if(data.result == 'duplicate'){
                            swal("DUPLICATE ITEM", "Item Description already exists!", "error");
                            return false;
                        }
                        else{
                            $('#detailsItem').hide();
                            swal("UPDATE FAILED", "MAINTENANCE - ITEM", "error");
                            setTimeout(function(){window.location.href="/maintenance"}, 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/maintenance';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
});

$('#btnSaveCategory').on('click', function(){
    var category = $.trim($('#category').val());
    if(category != ""){
        swal({
            title: "ADD NEW CATEGORY?",
            text: "You are about to ADD this new category!",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                    url: "/saveCategory",
                    type: "POST",
                    headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                    },
                    data: {
                        _token: $("#csrf").val(),
                        category: category
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('#newCategory').hide();
                            swal("SAVE SUCCESS", "New Category has been saved.", "success");
                            setTimeout(function(){window.location.href="/maintenance?tbl=categories"}, 2000);
                            $.ajax({
                                url: "/logNewCategory",
                                type: "POST",
                                headers: {
                                'X-CSRF-TOKEN': $("#csrf").val(),
                                },
                                data: {
                                    id: data.id,
                                    category: data.category
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
                                        window.location.href = '/maintenance?tbl=categories';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else if(data.result == 'duplicate'){
                            swal("DUPLICATE CATEGORY", "Category Name already exists!", "error");
                            return false;
                        }
                        else{
                            $('#newCategory').hide();
                            swal("SAVE FAILED", "MAINTENANCE - CATEGORY", "error");
                            setTimeout(function(){window.location.href="/maintenance?tbl=categories"}, 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/maintenance?tbl=categories';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
    else{
        swal('REQUIRED','Category Name field is required!','error');
        return false;
    }
});

$('#categoryTable tbody').on('click', 'tr', function(){
    $('#detailsCategory').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table = $('table.categoryTable').DataTable(); 
    var data = table.row(this).data();
    var category_id = data.id;
        $('#category_id').val(category_id);
    var category = data.category;
        $('#category_details').val(category);
        $('#category_original').val(category);
    
    $('.modal-body').html();
    $('#detailsCategory').modal('show');
});

$('#btnUpdateCategory').on('click', function(){
    var category_id = $('#category_id').val();
    var category_original = $('#category_original').val();
    var category_details = $.trim($('#category_details').val().toUpperCase());
    
    if(category_details == ""){
        swal('REQUIRED','Category Name field is required!','error');
        return false;
    }
    else if(category_original == category_details){
        swal("NO CHANGES FOUND", "Category Name is still the same!", "error");
        return false;
    }
    else{
        swal({
            title: "UPDATE CATEGORY?",
            text: "You are about to UPDATE this category!",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                    url: "/updateCategory",
                    type: "PUT",
                    headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                    },
                    data: {
                        _token: $("#csrf").val(),
                        category_id: category_id,
                        category_original: category_original,
                        category_details: category_details
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('#detailsCategory').hide();
                            swal("UPDATE SUCCESS", "Category Name has been updated.", "success");
                            setTimeout(function(){window.location.href="/maintenance?tbl=categories"}, 2000);
                            $.ajax({
                                url: "/logUpdateCategory",
                                type: "POST",
                                headers: {
                                'X-CSRF-TOKEN': $("#csrf").val(),
                                },
                                data: {
                                    category_id: data.category_id,
                                    category_original: data.category_original,
                                    category_details: data.category_details
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
                                        window.location.href = '/maintenance?tbl=categories';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else if(data.result == 'duplicate'){
                            swal("DUPLICATE CATEGORY", "Category Name already exists!", "error");
                            return false;
                        }
                        else{
                            $('#detailsCategory').hide();
                            swal("UPDATE FAILED", "MAINTENANCE - CATEGORY", "error");
                            setTimeout(function(){window.location.href="/maintenance?tbl=categories"}, 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/maintenance?tbl=categories';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
});

$(".btnNewLocation").on('click', function(){
    $('#newLocation').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('.modal-body').html();
    $('#newLocation').modal('show');
});

$('#btnSaveLocation').on('click', function(){
    var location_name = $.trim($('#location').val());
    if(location_name != ""){
        swal({
            title: "REQUEST NEW LOCATION?",
            text: "You are about to REQUEST a new location!",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                    url: "/saveLocation",
                    type: "POST",
                    headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                    },
                    data: {
                        _token: $("#csrf").val(),
                        location: location_name
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            scrollReset();
                            $('#newLocation').hide();
                            $('#newLocation').modal('dispose');
                            $('#loading').show(); Spinner(); Spinner.show();
                            $.ajax({
                                url: "/logNewLocation",
                                type: "POST",
                                headers: {
                                'X-CSRF-TOKEN': $("#csrf").val(),
                                },
                                data: {
                                    id: data.id,
                                    location: data.location
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide(); Spinner.hide();
                                        swal("REQUEST SUCCESS", "New Location has been requested.", "success");
                                        setTimeout(function(){window.location.href="/maintenance?tbl=locations"}, 2000);
                                    }
                                    else{
                                        return false;
                                    }
                                },
                                error: function(data){
                                    if(data.status == 401){
                                        window.location.href = '/maintenance?tbl=locations';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else if(data.result == 'duplicate'){
                            swal("DUPLICATE LOCATION", "Location Name already exists!", "error");
                            return false;
                        }
                        else{
                            $('#newLocation').hide();
                            swal("REQUEST FAILED", "MAINTENANCE - LOCATION", "error");
                            setTimeout(function(){window.location.href="/maintenance?tbl=locations"}, 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/maintenance?tbl=locations';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
    else{
        swal('REQUIRED','Location Name field is required!','error');
        return false;
    }
});

$('#locationTable tbody').on('click', 'tr', function(){
    $('#detailsLocation').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table = $('table.locationTable').DataTable(); 
    var data = table.row(this).data();
    if(data.status.includes('CHANGE REQUESTED') || data.status.includes('PENDING')){
        return false;
    }
    else{
        var location_id = data.location_id;
            $('#location_id').val(location_id);
        var location_name = data.location;
            $('#location_details').val(location_name);
            $('#location_original').val(location_name);
        var status = data.status;
            $('#status_original').val(status);
            if(status == 'ACTIVE'){
                $('#status').prop('checked', true);
            }
            else{
                $('#status').prop('checked', false);
            }

        $('.modal-body').html();
        $('#detailsLocation').modal('show');
    }
});

$('#btnUpdateLocation').on('click', function(){
    if($('#status').is(":checked")){
        var status = 'ACTIVE';
    }
    else{
        var status = 'INACTIVE';
    }
    var location_id = $('#location_id').val();
    var location_original = $('#location_original').val();
    var location_details = $.trim($('#location_details').val().toUpperCase());
    var status_original = $('#status_original').val();

    if(location_details == ""){
        swal('REQUIRED','Location Name field is required!','error');
        return false;
    }
    if(location_original == location_details && status_original == status){
        swal("NO CHANGES FOUND", "Location Details are all still the same!", "error");
        return false;
    }
    if(location_original != location_details && status_original != status){
        swal("UPDATE FAILED", "STATUS CHANGE REQUEST is NOT allowed if the current Location Name has been changed!", "error");
        return false;
    }
    if(location_original == location_details && status != status_original){
        swal({
            title: "REQUEST STATUS CHANGE?",
            text: "You are about to request a STATUS CHANGE to this location!",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                    url: "/updateLocation",
                    type: "PUT",
                    headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                    },
                    data: {
                        _token: $("#csrf").val(),
                        location_id: location_id,
                        location_details: location_details,
                        status_original: status_original,
                        status: status
                    },
                    success: function(data){
                        if(data.result == 'request'){
                            scrollReset();
                            $('#detailsLocation').hide();
                            $('#detailsLocation').modal('dispose');
                            $('#loading').show(); Spinner(); Spinner.show();
                            $.ajax({
                                url: "/requestStatusChange",
                                type: "POST",
                                headers: {
                                'X-CSRF-TOKEN': $("#csrf").val(),
                                },
                                data: {
                                    id: data.id,
                                    location: data.location,
                                    status_original: data.status_original,
                                    status: data.status
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide(); Spinner.hide();
                                        swal("REQUEST SUCCESS", "Location Status Change has been requested.", "success");
                                        setTimeout(function(){window.location.href="/maintenance?tbl=locations"}, 2000);
                                    }
                                    else{
                                        return false;
                                    }
                                },
                                error: function(data){
                                    if(data.status == 401){
                                        window.location.href = '/maintenance?tbl=locations';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else{
                            $('#detailsLocation').hide();
                            swal("UPDATE FAILED", "MAINTENANCE - LOCATION", "error");
                            setTimeout(function(){window.location.href="/maintenance?tbl=locations"}, 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/maintenance?tbl=locations';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });

    }
    else{
        swal({
            title: "UPDATE LOCATION NAME?",
            text: "You are about to UPDATE this location!",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                    url: "/updateLocation",
                    type: "PUT",
                    headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                    },
                    data: {
                        _token: $("#csrf").val(),
                        location_id: location_id,
                        location_original: location_original,
                        location_details: location_details
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('#detailsLocation').hide();
                            swal("UPDATE SUCCESS", "Location Name has been updated.", "success");
                            setTimeout(function(){window.location.href="/maintenance?tbl=locations"}, 2000);
                        }
                        else if(data.result == 'duplicate'){
                            swal("DUPLICATE LOCATION", "Location Name already exists!", "error");
                            return false;
                        }
                        else{
                            $('#detailsLocation').hide();
                            swal("UPDATE FAILED", "MAINTENANCE - LOCATION", "error");
                            setTimeout(function(){window.location.href="/maintenance?tbl=locations"}, 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/maintenance?tbl=locations';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
});

setInterval(checkCreateItem, 200);
function checkCreateItem(){
    var item_description = $.trim($('#aic_item_description').val());
    if($('#createItem').is(':visible')){
        if(item_description){
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
    var item_description = $.trim($('#aic_item_description').val());
    swal({
        title: "CREATE NEW ASSEMBLED ITEM?",
        text: "You are about to CREATE a new Assembled Item!",
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
        if(!item_current || (item_current.toUpperCase() == item_original.toUpperCase())){
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
            { data: 'quantity' },
            { data: 'uom' }
        ],
        orderCellsTop: true,
        fixedHeader: true,            
    });
});

$('#btnUpdate').on('click', function(){
    var item_id = $('#aim_item_id').val();
    var item_name_original = $('#aim_item_name_details_original').val();
    var item_name = $.trim($('#aim_item_name_details').val());
    swal({
        title: "UPDATE ASSEMBLED ITEM?",
        text: "You are about to UPDATE this Assembled Item!",
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
                    item_name_original: item_name_original,
                    item_name: item_name
                },
                success: function(data){
                    if(data == 'true'){
                        $('#detailsAssemblyItem').hide();
                        swal("UPDATE SUCCESS", "ASSEMBLED ITEM", "success");
                        setTimeout(function(){location.reload();}, 2000);
                    }
                    else if(data == 'duplicate'){
                        swal("DUPLICATE ITEM", "Item Description already exists!", "error");
                        return false;
                    }
                    else{
                        $('#updateUser').hide();
                        swal("UPDATE FAILED", "ASSEMBLED ITEM", "error");
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