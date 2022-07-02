$('table.defectiveTable').dataTable().fnDestroy();
var defectiveTable = $('table.defectiveTable').DataTable({
    aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
    language:{
        processing: "Loading...",
        emptyTable: "No data available in table"
    },
    serverSide: true,
    ajax:{
        url: '/defective/data',
    },
    columnDefs: [
        {
            "targets": [0],
            "visible": false,
            "searchable": true
        },
        {
            "targets": [1],
            "render": $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'MMM. DD, YYYY, h:mm A')
        }
    ],
    columns: [
        { data: 'defectiveDatetime' },
        { data: 'defectiveDate' },
        { data: 'name' },
        { data: 'return_number' },
        { data: 'category' },
        { data: 'item' },
        { data: 'serial' },
        {
            data: 'status',
            "render": function(data, type, row, meta){
                if($("#current_role").val() == 'viewer'){
                    if(row.status == 'defectives'){
                        return "<span style='color: Red; font-weight: bold;'>FOR RETURN</span>";
                    }
                    else{
                        return "<span style='color: Green; font-weight: bold;'>"+row.status+"</span>";
                    }
                }
                else{
                    if(row.status == 'defectives'){
                        return "<button class='btn btn-success bp btnReturnItem' id='"+ meta.row +"'>RETURN</button>";
                    }
                    else{
                        return "<span style='color: Green; font-weight: bold;'>"+row.status+"</span>";
                    }
                }
            }
        },
    ],
    order: [],
    initComplete: function(){
        return notifyDeadline();
    }
});

var row_count
setInterval(function(){
    $.ajax({
        url: "/defective/reload",
        success: function(data){
            if(data != row_count){
                row_count = data;
                defectiveTable.ajax.reload(null, false);
            }
        }
    });
}, 1000);

function generateReturnNum(){
    var today = new Date();
    var month = today.getMonth()+1;
    if(month <= 9){
        month = '0'+month;
    }
    var day = today.getDate();
    if(day <= 9){
        day = '0'+day;
    }
    var date = today.getFullYear()+'-'+month+day+'-';
    var result = '';
    var characters = '123456789';

    for(var i = 0; i < 4; i++){
        result += characters.charAt(Math.floor(Math.random() * 6));
    }
    var return_number = date+result;

    $.ajax({
        type:'get',
        url:'/generateReturnNum',
        async: false,
        data:{
            'return_number': return_number
        },
        success: function(data){
            if(data == 'unique'){
                document.getElementById("return_number").value = return_number;
            }
            else{
                generateReturnNum();
            }
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/defective';
            }
                alert(data.responseText);
        }
    });
}

$(document).on('click', '.btnReturnItem', function(){
    generateReturnNum();
    var id = $(this).attr("id");
    var data = $('table.defectiveTable').DataTable().row(id).data();

    swal({
        title: "RETURN DEFECTIVE ITEM w/ SERIAL: "+data.serial,
        text: "You are about to RETURN this DEFECTIVE ITEM. Continue?",
        icon: "warning",
        buttons: true,
        dangerMode: true
    })
    .then((willDelete) => {
        if(willDelete){
            $.ajax({
                url: '/defective/return',
                data:{
                    return_number: $("#return_number").val(),
                    stock_id: data.stock_id,
                    category_id: data.category_id,
                    item_id: data.item_id,
                    category: decodeHtml(data.category),
                    item: decodeHtml(data.item),
                    serial: data.serial
                },
                success: function(data){
                    if(data == 'false'){
                        swal({
                            title: "RETURN FAILED",
                            text: "Failed to return Defective Item!",
                            icon: "error",
                            timer: 2000
                        });
                        $('table.defectiveTable').DataTable().ajax.reload();
                    }
                    else{
                        swal({
                            title: "RETURN SUCCESS",
                            text: "Successfully returned Defective Item!",
                            icon: "success",
                            timer: 2000
                        });
                        $('table.defectiveTable').DataTable().ajax.reload();
                    }
                },
                error: function(data){
                    alert(data.responseText);
                }
            });
        }
    });
});