if(window.location.href == 'https://lance.idsi.com.ph/filemaintenance'){
    $('#nav1').addClass("active-link");
    $('.btnNewItem').show();
    $('#itemTable').show();
    var table;
    table = $('table.itemTable').DataTable({ 
        dom: 'lrtip',
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
            "targets": [0],
            "visible": false,
            "searchable": false
        }],
        columns: [
            { data: 'item_id'},
            { data: 'category'},
            { data: 'item_name'}
        ],
        order:[[1, 'asc'],[2, 'asc']],
    });

    $('.filter-input').keyup(function() {
        table.column( $(this).data('column'))
            .search( $(this).val())
            .draw();
    });
}
else if(window.location.href == 'https://lance.idsi.com.ph/filemaintenance?tbl=category'){
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
    window.location.href = '/filemaintenance';
}

$(document).on('click', '#close', function(){
    window.location.href = '/filemaintenance?tbl=category';
});

$('#btnSaveCategory').on('click', function() {
    var category = $('#category').val();
    if(category!=""){
        $.ajax({
            url: "/saveCategory",
            type: "POST",
            headers: {
            'X-CSRF-TOKEN': $("#csrf").val(),
            },
            data: {
                _token: $("#csrf").val(),
                category: category,
            },
            success: function(data){
                if(data.result == 'true'){
                    $('#newCategory').hide();
                    sweetAlert("SAVE SUCCESS", "New Category has been saved.", "success");
                    setTimeout(function(){window.location.href="/filemaintenance?tbl=category"} , 2000);
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
                                window.location.href = '/filemaintenance?tbl=category';
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
                    sweetAlert("SAVE FAILED", "FILE MAINTENANCE", "error");
                    setTimeout(function(){window.location.href="/filemaintenance?tbl=category"} , 2000);
                }
            },
            error: function(data){
                if(data.status == 401) {
                    window.location.href = '/filemaintenance?tbl=category';
                }
                alert(data.responseText);
            }
        });
    }
    else{
        swal('REQUIRED','Category Name field is required!','error');
        return false;
    }
});