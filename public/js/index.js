$('#loading').show(); Spinner(); Spinner.show();
var table = $('table.user_logs').DataTable({
    language:{
        processing: "Loading...",
        emptyTable: "No data available in table"
    },
    serverSide: true,
    ajax:{
        url: '/index_data',
    },
    columnDefs: [
        {
            "targets": [0],
            "render": $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'MMM. DD, YYYY, h:mm A')
        },
    ],
    columns: [
        { data: 'date', width: '15%' },
        { data: 'username', width: '15%' },
        { data: 'role', width: '12%' },
        { data: 'activity' }
    ],
    order: [],
    initComplete: function(){
        return notifyDeadline();
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

$(document).ready(function(){
    $('#hover1').hover(
        function(){
            $('.zoom1').css({"zoom": "110%"});
            $('.box1').css({"zoom": "90%"});
        },
        function(){
            $('.zoom1').css({"zoom": "100%"});
            $('.box1').css({"zoom": "100%"});
        }
    );
});

$(document).ready(function(){
    $('#hover2').hover(
        function(){
            $('.zoom2').css({"zoom": "110%"});
            $('.box2').css({"zoom": "90%"});
        },
        function(){
            $('.zoom2').css({"zoom": "100%"});
            $('.box2').css({"zoom": "100%"});
        }
    );
});

$(document).ready(function(){
    $('#hover3').hover(
        function(){
            $('.zoom3').css({"zoom": "110%"});
            $('.box3').css({"zoom": "90%"});
        },
        function(){
            $('.zoom3').css({"zoom": "100%"});
            $('.box3').css({"zoom": "100%"});
        }
    );
});