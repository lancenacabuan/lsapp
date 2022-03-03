$(document).ready(function () {
    $('#loading').show();
    $('table.user_logs').DataTable({ 
        dom:  '<lf<t>ip>',
        language: {
            processing: "Loading...",
            emptyTable: "No data found!"
        },
        scrollX: true,
        processing: true,
        serverSide: true,
        order: [],
        ajax: {
            url: '/index_data',
        },
        columnDefs: [
            {
                "targets": [0],
                "render": $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'MMM. D, YYYY, h:mm A')
            },
        ],
        columns: [
            { data: 'date'},
            { data: 'username'},                
            { data: 'role'},
            { data: 'activity'}
        ],
        initComplete: function (){
            $('#loading').hide();
        }
    });
});