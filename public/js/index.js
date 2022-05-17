var table;
$('#loading').show(); Spinner(); Spinner.show();
    table = $('table.user_logs').DataTable({
        language: {
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        scrollX: true,
        serverSide: true,
        ajax: {
            url: '/index_data',
        },
        columnDefs: [
            {
                "targets": [0],
                "render": $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'MMM. DD, YYYY, h:mm A')
            },
        ],
        columns: [
            { data: 'date' },
            { data: 'username' },
            { data: 'role' },
            { data: 'activity' }
        ],
        order:[],
        initComplete: function(){
            $('#loading').hide(); Spinner.hide();
        }
    });

$('.filter-input').on('keyup', function(){
    table.column($(this).data('column'))
        .search($(this).val())
        .draw();
});

setInterval(function(){
    $('table.user_logs').DataTable().ajax.reload(null, false);
}, 10000);