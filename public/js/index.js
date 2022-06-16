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
        { data: 'username', width: '17%' },
        { data: 'role', width: '15%' },
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
            $('.zoom1').attr('src', 'index-stocks-hover.png').show();
            $('.zoom1').css({"zoom": "110%"});
            $('.box1').css({"zoom": "82%", "background-color": "#0d6efd"});
            $('.text1').css({"color": "#0d6efd"});
        },
        function(){
            $('.zoom1').attr('src', 'index-stocks.png').show();
            $('.zoom1').css({"zoom": "100%"});
            $('.box1').css({"zoom": "100%", "background-color": "#0d1a80"});
            $('.text1').css({"color": "#0d1a80"});
        }
    );
});

$(document).ready(function(){
    $('#hover2').hover(
        function(){
            $('.zoom2').attr('src', 'index-stockrequest-hover.png').show();
            $('.zoom2').css({"zoom": "110%"});
            $('.box2').css({"zoom": "82%", "background-color": "#0d6efd"});
            $('.text2').css({"color": "#0d6efd"});
        },
        function(){
            $('.zoom2').attr('src', 'index-stockrequest.png').show();
            $('.zoom2').css({"zoom": "100%"});
            $('.box2').css({"zoom": "100%", "background-color": "#0d1a80"});
            $('.text2').css({"color": "#0d1a80"});
        }
    );
});

$(document).ready(function(){
    $('#hover3').hover(
        function(){
            $('.zoom3').attr('src', 'index-stocktransfer-hover.png').show();
            $('.zoom3').css({"zoom": "110%"});
            $('.box3').css({"zoom": "82%", "background-color": "#0d6efd"});
            $('.text3').css({"color": "#0d6efd"});
        },
        function(){
            $('.zoom3').attr('src', 'index-stocktransfer.png').show();
            $('.zoom3').css({"zoom": "100%"});
            $('.box3').css({"zoom": "100%", "background-color": "#0d1a80"});
            $('.text3').css({"color": "#0d1a80"});
        }
    );
});

$(document).ready(function(){
    $('#hover4').hover(
        function(){
            $('.zoom4').attr('src', 'index-defective-hover.png').show();
            $('.zoom4').css({"zoom": "110%"});
            $('.box4').css({"zoom": "82%", "background-color": "#0d6efd"});
            $('.text4').css({"color": "#0d6efd"});
        },
        function(){
            $('.zoom4').attr('src', 'index-defective.png').show();
            $('.zoom4').css({"zoom": "100%"});
            $('.box4').css({"zoom": "100%", "background-color": "#0d1a80"});
            $('.text4').css({"color": "#0d1a80"});
        }
    );
});