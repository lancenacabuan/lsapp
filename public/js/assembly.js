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
    window.location.href = '/assembly';
});

$('#btnClose').on('click', function(){
    window.location.href = '/assembly';
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
                if(row.status_id >= 7){
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
                else if(row.status_id == '1' || row.status_id == '15'){
                    return "<span style='color: Red; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '2' || row.status_id == '5' || row.status_id == '16'){
                    return "<span style='color: Indigo; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '3' || row.status_id == '4' || row.status_id == '13'){
                    return "<span style='color: Green; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '8' || row.status_id == '9' || row.status_id == '12' || row.status_id == '14'){
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

if($(location).attr('pathname')+window.location.search != '/assembly'){
    url = window.location.search;
    reqnum = url.replace('?request_number=', '');
    $.ajax({
        url: '/reqModal',
        headers: {
            'X-CSRF-TOKEN': $("#csrf").val(),
        },
        dataType: 'json',
        type: 'get',
        data: {
            request_number: reqnum,
        },
        success: function(data){
            $('#detailsAssembly').modal({
                backdrop: 'static',
                keyboard: false
            });
            var reqitem = $.map(data.data, function(value, index){ 
                return [value];
            });
            reqitem.forEach(value => {
                var requestStatus = value.status_id;
                    $('#status_id_details').val(requestStatus);
                var req_type_id = value.req_type_id;
                    $('#req_type_id_details').val(req_type_id);
                var req_date = value.date;
                    req_date = moment(req_date).format('dddd, MMMM DD, YYYY, h:mm A');
                    $('#reqdate_details').val(req_date);
                var need_date = value.needdate;
                    maxDate = need_date;
                    need_date = moment(need_date).format('dddd, MMMM DD, YYYY');
                    $('#needdate_details').val(need_date);
                var req_num = value.req_num;
                    $('#request_num_details').val(req_num);
                var req_by = value.req_by;
                    $('#requested_by_details').val(req_by);
                var req_type = value.req_type;
                    $('#request_type_details').val(req_type);
                var item_id = value.item_id;
                    $('#item_id_details').val(item_id);
                var item_desc = value.item_desc;
                    $('#item_desc_details').val(item_desc);
                var qty = value.qty;
                    $('#qty_details').val(qty);
                var status = value.status;
                    $('#status_details').val(status);
                var prep_by = value.prep_by;
                    $('#prep_by').val(prep_by);
                    $('#prep_by1').val(prep_by);
                var sched = value.sched;
                    sched = moment(sched).format('dddd, MMMM DD, YYYY');
                    $('#sched').val(sched);
                    $('#sched1').val(sched);
            
                    $('.modal-body').html();
                    $('#detailsAssembly').modal('show');
                    if(requestStatus == '2'){
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'SCHEDULED ITEM DETAILS';
                    }
                    if(requestStatus == '3'){
                        $('#prepItemsModal').show();
                        $('#receive_label').show();
                        $('#btnReceive').show();
                        document.getElementById('modalheader').innerHTML = 'FOR RECEIVING ITEM DETAILS';
                    }
                    if(requestStatus == '12'){
                        $('#prepItemsModal').show();
                        $('#defective_label').show();
                        $('#btnAssemble').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                    }
                    if(requestStatus == '13'){
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'ASSEMBLED ITEM DETAILS';
                    }
                                
                $('table.stockDetails').dataTable().fnDestroy();    
                $('table.stockDetails').DataTable({
                    paging: false,
                    ordering: false,
                    info: false,
                    language: {
                        emptyTable: "No data available in table",
                        processing: "Loading...",
                    },
                    serverSide: true,
                    ajax: {
                        url: '/requestDetails',
                        data: {
                            reqnum: req_num,
                        }
                    },
                    order:[],
                    columns: [
                        { data: 'category' },
                        { data: 'item' },
                        { data: 'quantity' },
                        { data: 'uom' }
                    ],          
                });

                $('table.prepItems').dataTable().fnDestroy();
                $('table.prepItems').DataTable({
                    columnDefs: [
                        {
                            "targets": [6],
                            "visible": false,
                            "searchable": false
                        }
                    ],
                    paging: false,
                    ordering: false,
                    info: false,
                    language: {
                        processing: "Loading...",
                        emptyTable: "No data available in table"
                    },
                    serverSide: true,
                    ajax: {
                        url: '/schedItems',
                        data: {
                            request_number: req_num,
                        }
                    },
                    order:[],
                    columns: [
                        { data: 'category' },
                        { data: 'item' },
                        { data: 'qty' },
                        { data: 'uom' },
                        { data: 'serial' },
                        { data: 'location' },
                        { data: 'id' }
                    ]
                });
            });
        },
        error: function(data){
            alert(data.responseText);
        }
    });
}

$('#assemblyTable tbody').on('click', 'tr', function(){
    $('#detailsAssembly').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table = $('table.assemblyTable').DataTable(); 
    var value = table.row(this).data();
    var requestStatus = value.status_id;
        $('#status_id_details').val(requestStatus);
    var req_type_id = value.req_type_id;
        $('#req_type_id_details').val(req_type_id);
    var req_date = value.date;
        req_date = moment(req_date).format('dddd, MMMM DD, YYYY, h:mm A');
        $('#reqdate_details').val(req_date);
    var need_date = value.needdate;
        maxDate = need_date;
        need_date = moment(need_date).format('dddd, MMMM DD, YYYY');
        $('#needdate_details').val(need_date);
    var req_num = value.req_num;
        $('#request_num_details').val(req_num);
    var req_by = value.req_by;
        $('#requested_by_details').val(req_by);
    var item_id = value.item_id;
        $('#item_id_details').val(item_id);
    var item_desc = value.item_desc;
        $('#item_desc_details').val(item_desc);
    var qty = value.qty;
        $('#qty_details').val(qty);
    var status = value.status;
        $('#status_details').val(status);
    var prep_by = value.prep_by;
        $('#prep_by').val(prep_by);
        $('#prep_by1').val(prep_by);
    var sched = value.sched;
        sched = moment(sched).format('dddd, MMMM DD, YYYY');
        $('#sched').val(sched);
        $('#sched1').val(sched);

        $('.modal-body').html();
        $('#detailsAssembly').modal('show');
        if(requestStatus == '2'){
            $('#prepItemsModal').show();
            document.getElementById('modalheader').innerHTML = 'SCHEDULED ITEM DETAILS';
        }
        if(requestStatus == '3'){
            $('#prepItemsModal').show();
            $('#receive_label').show();
            $('#btnReceive').show();
            document.getElementById('modalheader').innerHTML = 'FOR RECEIVING ITEM DETAILS';
        }
        if(requestStatus == '12'){
            $('#prepItemsModal').show();
            $('#defective_label').show();
            $('#btnAssemble').show();
            document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
        }
        if(requestStatus == '13'){
            $('#prepItemsModal').show();
            document.getElementById('modalheader').innerHTML = 'ASSEMBLED ITEM DETAILS';
        }
                    
    $('table.stockDetails').dataTable().fnDestroy();    
    $('table.stockDetails').DataTable({ 
        paging: false,
        ordering: false,
        info: false,
        language: {
            emptyTable: "No data available in table",
            processing: "Loading...",
        },
        serverSide: true,
        ajax: {
            url: '/requestDetails',
            data: {
                reqnum: req_num,
            }
        },
        order:[],
        columns: [
            { data: 'category' },
            { data: 'item' },
            { data: 'quantity' },
            { data: 'uom' }
        ],
        orderCellsTop: true,
        fixedHeader: true,            
    });

    $('table.prepItems').dataTable().fnDestroy(); 
    $('table.prepItems').DataTable({
        columnDefs: [
            {
                "targets": [6],
                "visible": false,
                "searchable": false
            }
        ],
        paging: false,
        ordering: false,
        info: false,
        language: {
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax: {
            url: '/schedItems',
            data: {
                request_number: req_num,
            }
        },
        order:[],
        columns: [
            { data: 'category' },
            { data: 'item' },
            { data: 'qty' },
            { data: 'uom' },
            { data: 'serial' },
            { data: 'location' },
            { data: 'id' }
        ]
    });
});

var items = [];
var item_count = 0;
$('.table.prepItems').DataTable().on('select', function(){});
$('.prepItems tbody').on('click', 'tr', function(){
    var requestStatus = $('#status_id_details').val();
    if(requestStatus == '2'){
        return false;
    }
    if(requestStatus == '13'){
        return false;
    }
    var table = $('table.prepItems').DataTable();
    var data = table.row(this).data();
    item_count = table.data().count();

    $(this).toggleClass('selected');
    if(items.includes(data.id) == true){
        items = items.filter(item => item !== data.id);
    }
    else {
        items.push(data.id);
    }
    if(requestStatus == '3'){
        if(items.length == 0){
            $('#btnReceive').prop('disabled', true);
        }
        else{
            $('#btnReceive').prop('disabled', false);
        }
    }
    if(requestStatus == '12'){
        if(items.length == 0){
            $('#btnAssemble').show();
            $('#btnDefective').hide();
        }
        else{
            $('#btnAssemble').hide();
            $('#btnDefective').show();
        }
    }
});

$('#btnReceive').on('click', function(){
    var inc = 'false';
    var inctype = 'COMPLETE';
    if(items.length < item_count){
        inc = 'true';
        inctype = 'INCOMPLETE';
    }
    swal({
        title: "RECEIVE "+inctype+" ASSEMBLY PARTS?",
        text: "You are about to RECEIVE these ASSEMBLY PARTS!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if(willDelete){
            $.ajax({
                type:'post',
                url:'/assembly/receiveRequest',
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                },
                data:{
                    'request_number': $('#request_num_details').val(),
                    'inc': inc
                },
                success: function(data){
                    if(data == 'true'){
                        for(var i=0; i < items.length; i++){
                            $.ajax({
                                type:'post',
                                url:'/assembly/receiveItems',
                                headers: {
                                    'X-CSRF-TOKEN': $("#csrf").val(),
                                },
                                data:{
                                    'id': items[i]
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
                                        window.location.href = '/assembly';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        scrollReset();
                        $('#detailsAssembly').hide();
                        $('#detailsAssembly').modal('dispose');
                        $('#loading').show(); Spinner(); Spinner.show();
                        $.ajax({
                            type:'post',
                            url:'/assembly/logReceive',
                            headers: {
                                'X-CSRF-TOKEN': $("#csrf").val()
                            },
                            data:{
                                'request_number': $('#request_num_details').val(),
                                'inc': inc
                            },
                            success: function(data){
                                if(data == 'true'){
                                    $('#loading').hide(); Spinner.hide();
                                    swal("RECEIVED "+inctype, "ASSEMBLY REQUEST", "success");
                                    setTimeout(function(){location.href="/assembly"}, 2000);
                                }
                                else{
                                    return false;
                                }
                            },
                            error: function(data){
                                if(data.status == 401){
                                    window.location.href = '/assembly';
                                }
                                alert(data.responseText);
                            }
                        });
                    }
                    else{
                        $('#detailsAssembly').hide();
                        swal("RECEIVE FAILED", "ASSEMBLY REQUEST", "error");
                        setTimeout(function(){location.href="/assembly"}, 2000);
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/assembly';
                    }
                    alert(data.responseText);
                }
            });
        }
    });
});

$('#btnAssemble').on('click', function(){
    var item_desc_details = $('#item_desc_details').val();
    swal({
        title: "ASSEMBLE: "+item_desc_details+"?",
        text: "You are about to ASSEMBLE this Assembly Stock Request!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if(willDelete){
            $.ajax({
                type:'post',
                url:'/assembly/assembleRequest',
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                },
                data:{
                    'request_number': $('#request_num_details').val()
                },
                success: function(data){
                    if(data == 'true'){
                        $('#detailsAssembly').hide();
                        swal("ASSEMBLE SUCCESS", "ASSEMBLY REQUEST", "success");
                        setTimeout(function(){location.href="/assembly"}, 2000);
                    }
                    else{
                        $('#detailsAssembly').hide();
                        swal("ASSEMBLE FAILED", "ASSEMBLY REQUEST", "error");
                        setTimeout(function(){location.href="/assembly"}, 2000);
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/assembly';
                    }
                    alert(data.responseText);
                }
            });
        }
    });    
});