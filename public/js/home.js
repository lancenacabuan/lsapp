$(document).ready(function(){
    $('#loading').show(); Spinner(); Spinner.show();
    $('table.user_logs').DataTable({ 
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
});