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
        error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
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