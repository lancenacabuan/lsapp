var minDate, maxDate;
$(function(){
    var dtToday = new Date();
    
    var month = dtToday.getMonth() + 1;
    var day = dtToday.getDate();
    var year = dtToday.getFullYear();
    if(month < 10)
        month = '0' + month.toString();
    if(day < 10)
        day = '0' + day.toString();    
    minDate = year + '-' + month + '-' + day;

    $('#needdate').attr('min', minDate);
    $('#schedOn').attr('min', minDate);
});

const _MS_PER_DAY = 1000 * 60 * 60 * 24;
function dateDiffInDays(a, b){
    const utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
    const utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());
    
    return Math.floor((utc2 - utc1) / _MS_PER_DAY);
}

function copyReqNum(){
    var copyText = document.getElementById("request_num_details");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    swal({
        title: copyText.value,
        text: "Copied to Clipboard!",
        icon: "success",
        timer: 2000
    });
}

function generateReqNum(){
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

    for(var i = 0; i < 3; i++){
        result += characters.charAt(Math.floor(Math.random() * 6));
    }
    var request_number = date+result;

    $.ajax({
        type:'get',
        url:'/assembly/generateReqNum',
        async: false,
        data:{
            'request_number': request_number
        },
        success: function(data){
            if(data == 'unique'){
                document.getElementById("request_num").value = request_number;
            }
            else{
                generateReqNum();
            }
        },
        error: function(data){
            if(data.status == 401){
                location.reload();
            }
                alert(data.responseText);
        }
    });
}

$(".btnNewAssembly").on('click', function(){
    $('#newAssembly').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('.modal-body').html();
    $('#newAssembly').modal('show');
    generateReqNum();
});

setInterval(checkNewAssembly, 200);
function checkNewAssembly(){
    if($('#newAssembly').is(':visible')){
        if($('#needdate').val() && $('#assembly').val() && $('#qty').val() > 0 && $('#assemblypartsDetails').is(':hidden')){
            $('#btnAssemblyProceed').show();
        }
        else{
            $('#btnAssemblyProceed').hide();
        }
    }
}

$('.close').on('click', function(){
    location.reload();
});

$('#btnClose').on('click', function(){
    location.reload();
});

$('table.assemblyTable').dataTable().fnDestroy();
$('#loading').show(); Spinner(); Spinner.show();
$('table.assemblyTable').DataTable({ 
    columnDefs: [
        {
            "targets": [1],
            "render": $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'MMM. DD, YYYY')
        },
        {
            "targets": [7,8,9,10,11],
            "visible": false,
            "searchable": false
        }
    ],
    language: {
        processing: "Loading...",
        emptyTable: "No data available in table"
    },
    serverSide: true,
    ajax: {
        url: '/assembly/request_data',
    },
    columns: [
        {
            data: 'needdate',
            "render": function(data, type, row){
                if(row.status_id == '7' || row.status_id == '8' || row.status_id == '9' || row.status_id == '10' || row.status_id == '11'){
                    return "<span class='d-none'>"+row.needdate+"</span>"+moment(row.needdate).format('MMM. DD, YYYY');
                }
                else{
                    var a = new Date(minDate);
                    var b = new Date(row.needdate);
                    var difference = dateDiffInDays(a, b);
                    if(difference >= 0 && difference <= 3){
                        return "<span class='d-none'>"+row.needdate+"</span><span style='color: Blue; font-weight: bold;'>"+moment(row.needdate).format('MMM. DD, YYYY')+'&nbsp;&nbsp;&nbsp;'+"<i style='zoom: 150%; color: blue;' class='fa fa-exclamation-triangle'></i></span>";
                    }
                    else if(difference < 0){
                        return "<span class='d-none'>"+row.needdate+"</span><span style='color: Red; font-weight: bold;'>"+moment(row.needdate).format('MMM. DD, YYYY')+'&nbsp;&nbsp;&nbsp;'+"<i style='zoom: 150%; color: red;' class='fa fa-exclamation-circle'></i></span>";
                    }
                    else{
                        return "<span class='d-none'>"+row.needdate+"</span>"+moment(row.needdate).format('MMM. DD, YYYY');
                    }
                }
            }
        },
        { data: 'date' },
        { data: 'req_num' },
        { data: 'req_by' },
        { data: 'item_desc' },
        { data: 'qty' },
        {
            data: 'status',
            "render": function(data, type, row){
                if(row.status_id == '6'){
                    return "<span style='color: DarkSlateGray; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '1'){
                    return "<span style='color: Red; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '2' || row.status_id == '5'){
                    return "<span style='color: Indigo; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '3' || row.status_id == '4'){
                    return "<span style='color: Green; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '8' || row.status_id == '9'){
                    return "<span style='color: Blue; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '10'){
                    return "<span style='color: DarkBlue; font-weight: bold;'>"+row.status+"</span>";
                }
                else{
                    return "<span style='color: Gray; font-weight: bold;'>"+row.status+"</span>";
                }
            }
        },
        { data: 'item_id' },
        { data: 'status_id' },
        { data: 'prep_by' },
        { data: 'sched' },
        { data: 'user_id' },
    ],
    order:[],
    initComplete: function(){
        $('#loading').hide(); Spinner.hide();
    }
});

$('#btnAssemblyProceed').on('click', function(){
    $('#btnAssemblyProceed').hide();
    $('#assemblypartsDetails').show();
    $("#assembly").prop('disabled', true);
    $("#qty").prop('disabled', true);
    $('table.tblPartsDetails').dataTable().fnDestroy();
    $('table.tblPartsDetails').DataTable({
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        columnDefs: [
            {
                "targets": [4,5],
                "visible": false,
                "searchable": false
            }
        ],
        language: {
            emptyTable: "No data available in table",
            processing: "Loading...",
        },
        serverSide: true,
        ajax: {
            url: '/partsDetails',
            data: {
                item_id: $("#assembly").val()
            },
            dataType: 'json',
            error: function(data){
                if(data.status == 401){
                    location.reload();
                }
                alert(data.responseText);
            },
        },
        order:[[0, 'asc'],[1, 'asc']],
        columns: [
            { data: 'category' },
            { data: 'item' },
            { data: 'quantity' },
            { data: 'uom' },
            { data: 'category_id' },
            { data: 'item_id' }
        ],
        orderCellsTop: true,
        fixedHeader: true,            
    });
    setTimeout(setqty, 1000);
});

function setqty(){
    var table = document.getElementById('tblPartsDetails');
    var count = table.rows.length;
    for(i = 1; i < count; i++){
        var objCells = table.rows.item(i).cells;
        objCells.item(2).innerHTML = parseInt(objCells.item(2).innerHTML) * parseInt($("#qty").val());    
    }
    $("#btnAssemblySave").prop('disabled', false);
}

$('#btnAssemblyBack').on('click', function(){
    $('#btnAssemblyProceed').hide();
    $('table.tblPartsDetails').dataTable().fnDestroy();
    $("#assemblypartsDetails").hide();
    $("#btnAssemblySave").prop('disabled', true);
    $("#assembly").prop('disabled', false);
    $("#qty").prop('disabled', false);
    $("#assembly").val('');
    $("#qty").val('');
});

$('#btnAssemblySave').on('click', function(){
    var needdate = $('#needdate').val();
    var request_type = '5';
    var item_id = $('#assembly').val();
    var item_desc = $("#assembly option:selected").text();
    var qty = $('#qty').val();
    if(needdate < minDate){
        swal('Minimum Date is today!','Select within date range from today onwards.','error');
        return false;
    }
    else{
        swal({
            title: "SUBMIT ASSEMBLY REQUEST?",
            text: "You are about to SUBMIT this ASSEMBLY REQUEST!",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                    type:'post',
                    url:'/assembly/saveReqNum',
                    headers: {
                        'X-CSRF-TOKEN': $("#csrf").val(),
                    },
                    data:{
                        'request_number': $('#request_num').val(),
                        'needdate': needdate,
                        'request_type': request_type,
                        'item_id': item_id,
                        'qty': qty
                    },
                    success: function(data){
                        if(data == 'true'){
                            var form_data  = $('#tblPartsDetails').DataTable().rows().data();
                            form_data.each(function(value, index){
                                $.ajax({
                                    type:'post',
                                    url:'/assembly/saveRequest',
                                    headers: {
                                        'X-CSRF-TOKEN': $("#csrf").val(),
                                    },
                                    data:{
                                        'request_number': $('#request_num').val(),
                                        'category': value.category_id,
                                        'item': value.item_id,
                                        'quantity': value.quantity,
                                        'qty': qty
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
                                        if(data.status == 401){
                                            location.reload();
                                        }
                                        alert(data.responseText);
                                    }
                                });
                            });
                            $.ajax({
                                type:'post',
                                url:'/assembly/logSave',
                                headers: {
                                    'X-CSRF-TOKEN': $("#csrf").val()
                                },
                                data:{
                                    'request_number': $('#request_num').val(),
                                    'item_desc': item_desc
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#newAssembly').hide();
                                        swal("SUBMIT SUCCESS", "ASSEMBLY REQUEST", "success");
                                        setTimeout(function(){location.reload()}, 2000);
                                    }
                                    else{
                                        return false;
                                    }
                                },
                                error: function(data){
                                    if(data.status == 401){
                                        location.reload();
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else{
                            $('#newAssembly').hide();
                            swal("SUBMIT FAILED", "ASSEMBLY REQUEST", "error");
                            setTimeout(function(){location.reload()}, 2000);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            location.reload();
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
});