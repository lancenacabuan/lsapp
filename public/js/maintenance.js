if(window.location.href == 'https://lance.idsi.com.ph/maintenance'){
    $('#nav1').addClass("active-link");
    $('.btnNewItem').show();
    $('#itemTable').show();
    var table;
    table = $('table.itemTable').DataTable({ 
        language: {
            processing: "Loading...",
            emptyTable: "No data found!"
        },
        processing: true,
        serverSide: false,
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
            { data: 'item_id'},
            { data: 'category'},
            { data: 'item_name'},
            { data: 'category_id'},
            { data: 'uom'}
        ],
        // order:[[1, 'asc'],[2, 'asc']],
        order:[[2, 'asc']],
    });

    $('.filter-input').keyup(function() {
        table.column( $(this).data('column'))
            .search( $(this).val())
            .draw();
    });
}
else if(window.location.href == 'https://lance.idsi.com.ph/maintenance?tbl=category'){
    $('#nav2').addClass("active-link");
    $('.btnNewCategory').show();
    $('#categoryTable').show();
    $('table.categoryTable').DataTable({ 
        language: {
            processing: "Loading...",
            emptyTable: "No data found!"
        },
        processing: true,
        serverSide: false,
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
        orderCellsTop: true,
        fixedHeader: true,            
    });
}
else{
    window.location.href = '/maintenance';
}

$(document).on('click', '.close', function(){
    location.reload();
});

$('#btnSaveItem').on('click', function() {
    var category_name = $('#item_category').find('option:selected').text();
    var item_category = $('#item_category').val();
    var item_name = $('#item_name').val();
    var item_uom = $('#item_uom').val();
    if(item_name != "" && $('#item_category').find('option:selected').text() != 'Select Category' && $('#item_uom').find('option:selected').text() != 'Select UOM'){
        swal({
            title: "ADD NEW ITEM?",
            text: "You are about to ADD this new item!",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if (willDelete) {
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
                            sweetAlert("SAVE SUCCESS", "New Item has been saved.", "success");
                            setTimeout(function(){window.location.href="/maintenance"} , 2000);
                        }
                        else if(data.result == 'duplicate'){
                            sweetAlert("DUPLICATE ITEM", "Item Description already exists!", "error");
                            return false;
                        }
                        else{
                            $('#newItem').hide();
                            sweetAlert("SAVE FAILED", "MAINTENANCE", "error");
                            setTimeout(function(){window.location.href="/maintenance"} , 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401) {
                            window.location.href = '/maintenance';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
    else{
        swal('REQUIRED','Please fill all the fields!','error');
        return false;
    }
});

$('#itemTable tbody').on('click', 'tr', function () {
    $('#detailsItem').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table =  $('table.itemTable').DataTable(); 
    var data = table.row(this).data();
    var item_id = data.item_id;
        $('#item_id').val(item_id);
    var category_name = data.category;
        $('#category_name_details_original').val(category_name);
    var item_category = data.category_id;
        $('#item_category_details').val(item_category);
        $('#item_category_details_original').val(item_category);
    var item_name = decodeHtml(data.item_name);
        $('#item_name_details').val(item_name);
        $('#item_name_details_original').val(item_name);
    var item_uom = data.uom;
        $('#item_uom_details').val(item_uom);
        $('#item_uom_details_original').val(item_uom);
    
    $('.modal-body').html();
    $('#detailsItem').modal('show');
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

$('#btnUpdateItem').on('click', function() {
    var item_id = $('#item_id').val();
    var category_name_original = $('#category_name_details_original').val();
    var item_category_original = $('#item_category_details_original').val();
    var item_name_original = $('#item_name_details_original').val();
    var item_uom_original = $('#item_uom_details_original').val();
    var category_name = $('#item_category_details').find('option:selected').text();
    var item_category = $('#item_category_details').val();
    var item_name = $('#item_name_details').val();
    var item_uom = $('#item_uom_details').val();
    if(item_name != "" && $('#item_category_details').find('option:selected').text() != 'Select Category' && $('#item_uom_details').find('option:selected').text() != 'Select UOM'){
        swal({
            title: "UPDATE ITEM?",
            text: "You are about to UPDATE this item!",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if (willDelete) {
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
                            sweetAlert("UPDATE SUCCESS", "Item has been updated.", "success");
                            setTimeout(function(){window.location.href="/maintenance"} , 2000);
                        }
                        else if(data.result == 'no changes'){
                            sweetAlert("NO CHANGES FOUND", "Item Details are still all the same!", "error");
                            return false;
                        }
                        else if(data.result == 'duplicate'){
                            sweetAlert("DUPLICATE ITEM", "Item Description already exists!", "error");
                            return false;
                        }
                        else{
                            $('#detailsItem').hide();
                            sweetAlert("UPDATE FAILED", "MAINTENANCE", "error");
                            setTimeout(function(){window.location.href="/maintenance"} , 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401) {
                            window.location.href = '/maintenance';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
    else{
        swal('REQUIRED','Please fill all the fields!','error');
        return false;
    }
});

$('#btnSaveCategory').on('click', function() {
    var category = $('#category').val();
    if(category != ""){
        swal({
            title: "ADD NEW CATEGORY?",
            text: "You are about to ADD this new category!",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if (willDelete) {
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
                            sweetAlert("SAVE SUCCESS", "New Category has been saved.", "success");
                            setTimeout(function(){window.location.href="/maintenance?tbl=category"} , 2000);
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
                                    if(data.status == 401) {
                                        window.location.href = '/maintenance?tbl=category';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else if(data.result == 'duplicate'){
                            sweetAlert("DUPLICATE CATEGORY", "Category Name already exists!", "error");
                            return false;
                        }
                        else{
                            $('#newCategory').hide();
                            sweetAlert("SAVE FAILED", "MAINTENANCE", "error");
                            setTimeout(function(){window.location.href="/maintenance?tbl=category"} , 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401) {
                            window.location.href = '/maintenance?tbl=category';
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

$('#categoryTable tbody').on('click', 'tr', function () {
    $('#detailsCategory').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table =  $('table.categoryTable').DataTable(); 
    var data = table.row(this).data();
    var category_id = data.id;
        $('#category_id').val(category_id);
    var category = data.category;
        $('#category_details').val(category);
        $('#category_original').val(category);
    
    $('.modal-body').html();
    $('#detailsCategory').modal('show');
});

$('#btnUpdateCategory').on('click', function() {
    var category_id = $('#category_id').val();
    var category_details = $('#category_details').val();
    var category_original = $('#category_original').val();
    if(category_details != ""){
        swal({
            title: "UPDATE CATEGORY?",
            text: "You are about to UPDATE this category!",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: "/updateCategory",
                    type: "PUT",
                    headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                    },
                    data: {
                        _token: $("#csrf").val(),
                        category_id: category_id,
                        category_details: category_details,
                        category_original: category_original,
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('#detailsCategory').hide();
                            sweetAlert("UPDATE SUCCESS", "Category Name has been updated.", "success");
                            setTimeout(function(){window.location.href="/maintenance?tbl=category"} , 2000);
                            $.ajax({
                                url: "/logUpdateCategory",
                                type: "POST",
                                headers: {
                                'X-CSRF-TOKEN': $("#csrf").val(),
                                },
                                data: {
                                    category_id: data.category_id,
                                    category_details: data.category_details,
                                    category_original: data.category_original,
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
                                    if(data.status == 401) {
                                        window.location.href = '/maintenance?tbl=category';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else if(data.result == 'no changes'){
                            sweetAlert("NO CHANGES FOUND", "Category Name is still the same!", "error");
                            return false;
                        }
                        else if(data.result == 'duplicate'){
                            sweetAlert("DUPLICATE CATEGORY", "Category Name already exists!", "error");
                            return false;
                        }
                        else{
                            $('#detailsCategory').hide();
                            sweetAlert("UPDATE FAILED", "MAINTENANCE", "error");
                            setTimeout(function(){window.location.href="/maintenance?tbl=category"} , 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401) {
                            window.location.href = '/maintenance?tbl=category';
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