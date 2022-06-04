$('#loading').show(); Spinner(); Spinner.show();
var table = $('table.user_logs').DataTable({
    language:{
        processing: "Loading...",
        emptyTable: "No data available in table"
    },
    serverSide: true,
    ajax:{
        url: '/index/data',
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

var logs;
setInterval(function(){
    $.ajax({
        url: "/index/reload",
        success: function(data){
            if(data != logs){
                logs = data;
                table.ajax.reload(null, false);
            }
        }
    });
}, 3000);

$(document).ready(function(){
    $('#hover1').hover(
        function(){
            $('.zoom1').css({"zoom": "110%"});
            $('.box1').css({"zoom": "90%"});
            $('.text1').css({"color": "#0d6efd"});
        },
        function(){
            $('.zoom1').css({"zoom": "100%"});
            $('.box1').css({"zoom": "100%"});
            $('.text1').css({"color": "#0d1a80"});
        }
    );
});

$(document).ready(function(){
    $('#hover2').hover(
        function(){
            $('.zoom2').css({"zoom": "110%"});
            $('.box2').css({"zoom": "90%"});
            $('.text2').css({"color": "#0d6efd"});
        },
        function(){
            $('.zoom2').css({"zoom": "100%"});
            $('.box2').css({"zoom": "100%"});
            $('.text2').css({"color": "#0d1a80"});
        }
    );
});

$(document).ready(function(){
    $('#hover3').hover(
        function(){
            $('.zoom3').css({"zoom": "110%"});
            $('.box3').css({"zoom": "90%"});
            $('.text3').css({"color": "#0d6efd"});
        },
        function(){
            $('.zoom3').css({"zoom": "100%"});
            $('.box3').css({"zoom": "100%"});
            $('.text3').css({"color": "#0d1a80"});
        }
    );
});