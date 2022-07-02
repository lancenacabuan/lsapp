var targets = [0];
if($('#current_role').val() == 'sales' || $('#current_role').val() == 'merchant' || $('#current_role').val() == 'assembler') //---ROLES---//
{
    targets = [0,2,3];
}
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
            "targets": targets,
            "visible": false,
            "searchable": true
        },
        {
            "targets": [1],
            "render": $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'MMM. DD, YYYY, h:mm A')
        },
    ],
    columns: [
        { data: 'datetime' },
        { data: 'date' },
        { data: 'username' },
        { data: 'role' },
        { data: 'activity' }
    ],
    order: [],
    initComplete: function(){
        return notifyDeadline();
    }
});

$('.filter-input').on('keyup', function(){
    table.column($(this).data('column')).search($(this).val()).draw();
});

document.querySelectorAll('input[type=search]').forEach(function(input){
    input.addEventListener('mouseup', function(e){
        if(input.value.length > 0){
            setTimeout(function(){
                if(input.value.length === 0){
                    $('.filter-input').keyup();
                }
            }, 0);
        }
    });
});

var row_count;
setInterval(function(){
    $.ajax({
        url: "/index/reload",
        success: function(data){
            if(data != row_count){
                row_count = data;
                table.ajax.reload(null, false);
            }
        }
    });
}, 1000);

$(document).ready(function(){
    $('#hover1').hover(
        function(){
            $('.zoomout1').hide();
            $('.zoomin1').show();
            $('.box1').css({"zoom": "82%", "background-color": "#0d6efd"});
            $('.text1').css({"color": "#0d6efd"});
        },
        function(){
            $('.zoomout1').show();
            $('.zoomin1').hide();
            $('.box1').css({"zoom": "100%", "background-color": "#0d1a80"});
            $('.text1').css({"color": "#0d1a80"});
        }
    );
});

$(document).ready(function(){
    $('#hover2').hover(
        function(){
            $('.zoomout2').hide();
            $('.zoomin2').show();
            $('.box2').css({"zoom": "82%", "background-color": "#0d6efd"});
            $('.text2').css({"color": "#0d6efd"});
        },
        function(){
            $('.zoomout2').show();
            $('.zoomin2').hide();
            $('.box2').css({"zoom": "100%", "background-color": "#0d1a80"});
            $('.text2').css({"color": "#0d1a80"});
        }
    );
});

$(document).ready(function(){
    $('#hover3').hover(
        function(){
            $('.zoomout3').hide();
            $('.zoomin3').show();
            $('.box3').css({"zoom": "82%", "background-color": "#0d6efd"});
            $('.text3').css({"color": "#0d6efd"});
        },
        function(){
            $('.zoomout3').show();
            $('.zoomin3').hide();
            $('.box3').css({"zoom": "100%", "background-color": "#0d1a80"});
            $('.text3').css({"color": "#0d1a80"});
        }
    );
});

$(document).ready(function(){
    $('#hover4').hover(
        function(){
            $('.zoomout4').hide();
            $('.zoomin4').show();
            $('.box4').css({"zoom": "82%", "background-color": "#0d6efd"});
            $('.text4').css({"color": "#0d6efd"});
        },
        function(){
            $('.zoomout4').show();
            $('.zoomin4').hide();
            $('.box4').css({"zoom": "100%", "background-color": "#0d1a80"});
            $('.text4').css({"color": "#0d1a80"});
        }
    );
});

$(document).ready(function(){
    $('#hover5').hover(
        function(){
            $('.zoomout5').hide();
            $('.zoomin5').show();
            $('.box5').css({"zoom": "82%", "background-color": "#0d6efd"});
            $('.text5').css({"color": "#0d6efd"});
        },
        function(){
            $('.zoomout5').show();
            $('.zoomin5').hide();
            $('.box5').css({"zoom": "100%", "background-color": "#0d1a80"});
            $('.text5').css({"color": "#0d1a80"});
        }
    );
});