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
    Swal.fire({
        title: copyText.value,
        html: "Copied to Clipboard!",
        icon: "success",
        timer: 2000
    });
}

function copyAsmReqNum(){
    var copyText = document.getElementById("asm_request_num_details");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    Swal.fire({
        title: copyText.value,
        html: "Copied to Clipboard!",
        icon: "success",
        timer: 2000
    });
}

var generatedReqNum;
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
        type: 'get',
        url: '/generateReqNum',
        async: false,
        data:{
            'request_number': request_number
        },
        success: function(data){
            if(data == 'unique'){
                generatedReqNum = request_number;
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

setInterval(checkNewAssembly, 0);
function checkNewAssembly(){
    if($('#newAssembly').is(':visible')){
        if($('#needdate').val() && $('#assembly').val() && $('#qty').val() > 0 && $('#assemblypartsDetails').is(':hidden')){
            $('#btnAssemblyProceed').show();
            $('.header_label').hide();
        }
        else{
            $('#btnAssemblyProceed').hide();
            $('.header_label').show();
        }
    }
    if($('#assemblypartsDetails').is(':visible')){
        $('.header_label').hide();
    }
}

$('.close').on('click', function(){
    window.location.href = '/assembly';
});

$('#btnClose').on('click', function(){
    window.location.href = '/assembly';
});

$('table.assemblyTable').dataTable().fnDestroy();
var assemblyTable = $('table.assemblyTable').DataTable({
    aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
    language:{
        processing: "Loading...",
        emptyTable: "No data available in table"
    },
    serverSide: true,
    ajax:{
        url: '/assembly/request_data',
    },
    columnDefs: [
        {
            "targets": [0,1],
            "visible": false,
            "searchable": true
        },
        {
            "targets": [2],
            "render": $.fn.dataTable.render.moment('YYYY-MM-DD', 'MMM. DD, YYYY')
        }
    ],
    columns: [
        { data: 'reqdatetime' },
        { data: 'needdatetime' },
        { data: 'reqdate' },
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
        { data: 'req_num' },
        { data: 'req_type' },
        {
            data: 'item_desc',
            "render": function(data, type, row){
                if(row.item_id != '0'){
                    return row.item_desc;
                }
                else{
                    return '';
                }
            }
        },
        { data: 'qty' },
        {
            data: 'status',
            "render": function(data, type, row){
                if(row.status_id == '6'){
                    return "<span style='color: DarkSlateGray; font-weight: bold;'>"+row.status+'&nbsp;&nbsp;&nbsp;'+"<i style='zoom: 150%; color: DarkSlateGray;' class='fa fa-exclamation-triangle'></i></span>";
                }
                else if(row.status_id == '1' || row.status_id == '15' || row.status_id == '18' || row.status_id == '21' || row.status_id == '22' || row.status_id == '23' || row.status_id == '25'){
                    return "<span style='color: Red; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '2' || row.status_id == '5' || row.status_id == '16'){
                    return "<span style='color: Indigo; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '3' || row.status_id == '4' || row.status_id == '11' || row.status_id == '13' || row.status_id == '17'){
                    return "<span style='color: Green; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '8' || row.status_id == '12' || row.status_id == '19' || row.status_id == '20'){
                    return "<span style='color: Blue; font-weight: bold;'>"+row.status+"</span>";
                }
                else if(row.status_id == '14' || row.status_id == '26'){
                    return "<span style='color: DarkBlue; font-weight: bold;'>"+row.status+"</span>";
                }
                else{
                    return "<span style='color: Gray; font-weight: bold;'>"+row.status+"</span>";
                }
            }
        }
    ],
    order: [],
    initComplete: function(){
        return notifyDeadline();
    }
});

var data_update;
setInterval(function(){
    if($('#newAssembly').is(':hidden') && $('#detailsAssembly').is(':hidden') && $('#reportModal').is(':hidden') && $('#changePassword').is(':hidden') && $('#loading').is(':hidden')){
        $.ajax({
            url: "/assembly/reload",
            success: function(data){
                if(data != data_update){
                    data_update = data;
                    assemblyTable.ajax.reload(null, false);
                }
            }
        });
    }
}, 3000);

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
        language:{
            emptyTable: "No data available in table",
            processing: "Loading...",
        },
        serverSide: true,
        ajax:{
            url: '/partsDetails',
            data:{
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
        order: [],
        columns: [
            { data: 'prodcode' },
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
        Swal.fire('Minimum Date is today!','Select within date range from today onwards.','error');
        return false;
    }
    else{
        Swal.fire({
            title: "SUBMIT ASSEMBLY REQUEST?",
            html: "Please review the details of your request. Click 'Confirm' button to submit; otherwise, click 'Cancel' button.",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    type: 'post',
                    url: '/assembly/saveReqNum',
                    async: false,
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                                    type: 'post',
                                    url: '/assembly/saveRequest',
                                    async: false,
                                    headers:{
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:{
                                        'request_number': $('#request_num').val(),
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
                                type: 'post',
                                url: '/assembly/logSave',
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    'request_number': $('#request_num').val(),
                                    'item_desc': item_desc,
                                    'qty': qty
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#newAssembly').hide();
                                        Swal.fire("SUBMIT SUCCESS", "ASSEMBLY REQUEST", "success");
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
                            Swal.fire("SUBMIT FAILED", "ASSEMBLY REQUEST", "error");
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
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        type: 'get',
        data:{
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
                var asm_req_num = value.assembly_reqnum;
                    $('#asm_request_num_details').val(asm_req_num);
                var req_by = value.req_by;
                    $('#requested_by_details').val(req_by);
                var req_by_id = value.user_id;
                var req_type = value.req_type;
                    $('#request_type_details').val(req_type);
                var item_id = value.item_id;
                    $('#item_id_details').val(item_id);
                var item_desc = decodeHtml(value.item_desc);
                    $('#item_desc_details').val(item_desc);
                var qty = value.qty;
                    $('#qty_details').val(qty);
                var status = value.status;
                    $('#status_details').val(status);
                var prep_by = value.prep_by;
                    $('#prep_by').val(prep_by);
                    $('#prep_by1').val(prep_by);
                    $('#reprep_by').val(prep_by);
                    $('#reprep_by1').val(prep_by);
                var sched = value.sched;
                    sched = moment(sched).format('dddd, MMMM DD, YYYY');
                    $('#sched').val(sched);
                    $('#sched1').val(sched);
                    $('#resched').val(sched);
                    $('#resched1').val(sched);

                    if($("#current_role").val() == 'assembler' && [1, 2, 3, 6].includes(req_type_id) == true){
                        window.location.href = '/assembly';
                    }
                    if($("#current_role").val() == 'assembler' && $('#current_user').val() != value.user_id){
                        window.location.href = '/assembly';
                    }

                    var ajax_url = '/schedItems';
                    var rcv_url = '/schedItems';
                    var included = 'yes';

                    if($('#current_user').val() == req_by_id && requestStatus == 3){
                        $("#btnCancelRequest").show();
                    }

                    if(req_type_id == '4'){
                        $(".rephide").hide();
                        $(".repshow").show();
                    }

                    if(requestStatus == '1' && req_type_id != '4'){
                        $("#btnDelete").show();
                    }
                    if(requestStatus == '2'){
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'SCHEDULED ITEM DETAILS';
                    }
                    if(requestStatus == '3'){
                        $('#prepItemsModal').show();
                        $('#receive_label').show();
                        $('.btnReceive').show();
                        document.getElementById('modalheader').innerHTML = 'FOR RECEIVING ITEM DETAILS';
                    }
                    if(requestStatus == '11'){
                        var ajax_url = '/retItems';
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'RETURNED ITEM DETAILS';
                        $("#incFooter").hide();
                    }
                    if(requestStatus == '25'){
                        var ajax_url = '/retItems';
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE RETURNED ITEM DETAILS';
                        $("#incFooter").hide();
                    }
                    if(requestStatus == '12'){
                        $('#prepItemsModal').show();
                        $('#defective_label').show();
                        $('#btnAssemble').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                    }
                    if(requestStatus == '13'){
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'ASSEMBLED ITEM PARTS DETAILS';
                    }
                    if(requestStatus == '14'){
                        var rcv_url = '/receivedItems';
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'ASSEMBLED ITEM PARTS DETAILS';
                        $('#asmItemsModal').show();
                    }
                    if(requestStatus == '15'){
                        var ajax_url = '/incItems';
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $(".prephide").hide();
                        $("#incItemsModal").show();
                        $("#incFooter").hide();
                        $(".divPrint").show();
                    }
                    if(requestStatus == '16'){
                        var ajax_url = '/incItems';
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $(".prephide").hide();
                        $("#incItemsModal").show();
                        $(".divResched").show();
                        $("#incFooter").hide();
                        $(".divPrint").show();
                    }
                    if(requestStatus == '17'){
                        var ajax_url = '/incItems';
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $(".prephide").hide();
                        $("#incItemsModal").show();
                        $('#increceive_label').show();
                        $(".divResched").show();
                        $(".btnReceive").show();
                        $(".divPrint").show();
                    }
                    if(requestStatus == '18'){
                        var ajax_url = '/dfcItems';
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $(".prephide").hide();
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'DEFECTIVE ITEM DETAILS';
                        $("#incFooter").hide();
                        $(".divPrint").show();
                    }
                    if(requestStatus == '19'){
                        var ajax_url = '/schedItems';
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'REPLACEMENT ITEM DETAILS';
                    }
                    if(requestStatus == '20'){
                        $('#prepItemsModal').show();
                        $('#btnAssemble').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                    }
                    if(requestStatus == '21'){
                        var ajax_url = '/incdfcItems';
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $(".prephide").hide();
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE DEFECTIVE ITEM DETAILS';
                        $("#incFooter").hide();
                        $(".divPrint").show();
                    }
                    if(requestStatus == '22'){
                        var included = 'no';
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $(".prephide").hide();
                        $(".pendshow").show();
                    }
                    if(requestStatus == '23'){
                        var ajax_url = '/incItems';
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $(".prephide").hide();
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE REPLACEMENT ITEM DETAILS';
                        $("#incFooter").hide();
                        $(".divPrint").show();
                    }

                $('.modal-body').html();
                $('#detailsAssembly').modal('show');

                $('table.stockDetails').dataTable().fnDestroy();    
                $('table.stockDetails').DataTable({
                    searching: false,
                    paging: false,
                    ordering: false,
                    info: false,
                    language:{
                        emptyTable: "No data available in table",
                        processing: "Loading...",
                    },
                    serverSide: true,
                    ajax:{
                        url: '/requestDetails',
                        data:{
                            reqnum: req_num,
                        }
                    },
                    order: [],
                    columns: [
                        { data: 'prodcode' },
                        { data: 'item' },
                        { data: 'uom' },
                        { data: 'quantity' }
                    ],
                    footerCallback: function(row,data,start,end,display){
                        var api = this.api(), data;
                        var intVal = function(i){
                            return typeof i === 'string'?
                                i.replace(/[\$,]/g,'')*1:
                                typeof i === 'number'?
                                    i:0;
                        };
                        api.columns('.sum', {page:'all'}).every(function(){
                            var sum = this
                            .data()
                            .reduce(function(a,b){
                                return intVal(a) + intVal(b);
                            }, 0);
                            sum = sum.toString();
                            var pattern = /(-?\d+)(\d{3})/;
                            while(pattern.test(sum))
                            sum = sum.replace(pattern,"$1,$2");
                            this.footer().innerHTML = sum;
                        });
                    }
                });

                $('table.prepItems').dataTable().fnDestroy();
                $('table.prepItems').DataTable({
                    columnDefs: [
                        {
                            "targets": [5,6],
                            "visible": false,
                            "searchable": false
                        }
                    ],
                    searching: false,
                    paging: false,
                    ordering: false,
                    info: false,
                    language:{
                        processing: "Loading...",
                        emptyTable: "No data available in table"
                    },
                    serverSide: true,
                    ajax:{
                        url: rcv_url,
                        data:{
                            request_number: req_num,
                            included: included
                        }
                    },
                    order: [],
                    columns: [
                        { data: 'prodcode' },
                        { data: 'item' },
                        { data: 'qty' },
                        { data: 'uom' },
                        { data: 'serial' },
                        { data: 'id' },
                        { data: 'id' }
                    ],
                    footerCallback: function(row,data,start,end,display){
                        var api = this.api(), data;
                        var intVal = function(i){
                            return typeof i === 'string'?
                                i.replace(/[\$,]/g,'')*1:
                                typeof i === 'number'?
                                    i:0;
                        };
                        api.columns('.sum', {page:'all'}).every(function(){
                            var sum = this
                            .data()
                            .reduce(function(a,b){
                                return intVal(a) + intVal(b);
                            }, 0);
                            sum = sum.toString();
                            var pattern = /(-?\d+)(\d{3})/;
                            while(pattern.test(sum))
                            sum = sum.replace(pattern,"$1,$2");
                            this.footer().innerHTML = sum;
                        });
                    }
                });

                if(ajax_url != '/schedItems'){
                    $('table.incItems').dataTable().fnDestroy();
                    $('table.incItems').DataTable({
                        columnDefs: [
                            {
                                "targets": [5],
                                "visible": false,
                                "searchable": false
                            }
                        ],
                        searching: false,
                        paging: false,
                        ordering: false,
                        info: false,
                        language:{
                            processing: "Loading...",
                            emptyTable: "No data available in table"
                        },
                        serverSide: true,
                        ajax:{
                            url: ajax_url,
                            data:{
                                request_number: req_num,
                            }
                        },
                        order: [],
                        columns: [
                            { data: 'prodcode' },
                            { data: 'item' },
                            { data: 'qty' },
                            { data: 'uom' },
                            { data: 'serial' },
                            { data: 'id' }
                        ],
                        footerCallback: function(row,data,start,end,display){
                            var api = this.api(), data;
                            var intVal = function(i){
                                return typeof i === 'string'?
                                    i.replace(/[\$,]/g,'')*1:
                                    typeof i === 'number'?
                                        i:0;
                            };
                            api.columns('.sum', {page:'all'}).every(function(){
                                var sum = this
                                .data()
                                .reduce(function(a,b){
                                    return intVal(a) + intVal(b);
                                }, 0);
                                sum = sum.toString();
                                var pattern = /(-?\d+)(\d{3})/;
                                while(pattern.test(sum))
                                sum = sum.replace(pattern,"$1,$2");
                                this.footer().innerHTML = sum;
                            });
                        }
                    });
                }

                if(requestStatus == '14'){
                    $.ajax({
                        type: 'get', 
                        url: '/getReceive', 
                        data:{
                            'request_number': $('#request_num_details').val()
                        }, 
                        success: function(data){
                            document.getElementById("recby").value = data.recby;
                            document.getElementById("recsched").value = moment(data.recsched).format('dddd, MMMM DD, YYYY, h:mm A');
                        },
                        error: function(data){
                            if(data.status == 401){
                                window.location.href = '/assembly';
                            }
                            alert(data.responseText);
                        }
                    });

                    $('table.asmItems').dataTable().fnDestroy();
                    $('table.asmItems').DataTable({
                        searching: false,
                        paging: false,
                        ordering: false,
                        info: false,
                        language:{
                            processing: "Loading...",
                            emptyTable: "No data available in table"
                        },
                        serverSide: true,
                        ajax:{
                            url: '/asmItems',
                            data:{
                                request_number: req_num,
                            }
                        },
                        order: [],
                        columns: [
                            { data: 'prodcode' },
                            { data: 'item' },
                            { data: 'qty' },
                            { data: 'uom' },
                            { data: 'serial' },
                            { data: 'location' }
                        ],
                        footerCallback: function(row,data,start,end,display){
                            var api = this.api(), data;
                            var intVal = function(i){
                                return typeof i === 'string'?
                                    i.replace(/[\$,]/g,'')*1:
                                    typeof i === 'number'?
                                        i:0;
                            };
                            api.columns('.sum', {page:'all'}).every(function(){
                                var sum = this
                                .data()
                                .reduce(function(a,b){
                                    return intVal(a) + intVal(b);
                                }, 0);
                                sum = sum.toString();
                                var pattern = /(-?\d+)(\d{3})/;
                                while(pattern.test(sum))
                                sum = sum.replace(pattern,"$1,$2");
                                this.footer().innerHTML = sum;
                            });
                        }
                    });
                }
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
    var asm_req_num = value.assembly_reqnum;
        $('#asm_request_num_details').val(asm_req_num);
    var req_by = value.req_by;
        $('#requested_by_details').val(req_by);
    var req_by_id = value.user_id;
    var req_type = value.req_type;
        $('#request_type_details').val(req_type);
    var item_id = value.item_id;
        $('#item_id_details').val(item_id);
    var item_desc = decodeHtml(value.item_desc);
        $('#item_desc_details').val(item_desc);
    var qty = value.qty;
        $('#qty_details').val(qty);
    var status = value.status;
        $('#status_details').val(status);
    var prep_by = value.prep_by;
        $('#prep_by').val(prep_by);
        $('#prep_by1').val(prep_by);
        $('#reprep_by').val(prep_by);
        $('#reprep_by1').val(prep_by);
    var sched = value.sched;
        sched = moment(sched).format('dddd, MMMM DD, YYYY');
        $('#sched').val(sched);
        $('#sched1').val(sched);
        $('#resched').val(sched);
        $('#resched1').val(sched);

        if($("#current_role").val() == 'assembler' && [1, 2, 3, 6].includes(req_type_id) == true){
            window.location.href = '/assembly';
        }
        if($("#current_role").val() == 'assembler' && $('#current_user').val() != value.user_id){
            window.location.href = '/assembly';
        }

        var ajax_url = '/schedItems';
        var rcv_url = '/schedItems';
        var included = 'yes';

        if($('#current_user').val() == req_by_id && requestStatus == 3){
            $("#btnCancelRequest").show();
        }

        if(req_type_id == '4'){
            $(".rephide").hide();
            $(".repshow").show();
        }

        if(requestStatus == '1' && req_type_id != '4'){
            $("#btnDelete").show();
        }
        if(requestStatus == '2'){
            $('#prepItemsModal').show();
            document.getElementById('modalheader').innerHTML = 'SCHEDULED ITEM DETAILS';
        }
        if(requestStatus == '3'){
            $('#prepItemsModal').show();
            $('#receive_label').show();
            $('.btnReceive').show();
            document.getElementById('modalheader').innerHTML = 'FOR RECEIVING ITEM DETAILS';
        }
        if(requestStatus == '11'){
            var ajax_url = '/retItems';
            $("#incItemsModal").show();
            document.getElementById('incmodalheader').innerHTML = 'RETURNED ITEM DETAILS';
            $("#incFooter").hide();
        }
        if(requestStatus == '25'){
            var ajax_url = '/retItems';
            $("#incItemsModal").show();
            document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE RETURNED ITEM DETAILS';
            $("#incFooter").hide();
        }
        if(requestStatus == '12'){
            $('#prepItemsModal').show();
            $('#defective_label').show();
            $('#btnAssemble').show();
            document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
        }
        if(requestStatus == '13'){
            $('#prepItemsModal').show();
            document.getElementById('modalheader').innerHTML = 'ASSEMBLED ITEM PARTS DETAILS';
        }
        if(requestStatus == '14'){
            var rcv_url = '/receivedItems';
            $('#prepItemsModal').show();
            document.getElementById('modalheader').innerHTML = 'ASSEMBLED ITEM PARTS DETAILS';
            $('#asmItemsModal').show();
        }
        if(requestStatus == '15'){
            var ajax_url = '/incItems';
            $('#prepItemsModal').show();
            document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            $(".prephide").hide();
            $("#incItemsModal").show();
            $("#incFooter").hide();
            $(".divPrint").show();
        }
        if(requestStatus == '16'){
            var ajax_url = '/incItems';
            $('#prepItemsModal').show();
            document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            $(".prephide").hide();
            $("#incItemsModal").show();
            $(".divResched").show();
            $("#incFooter").hide();
            $(".divPrint").show();
        }
        if(requestStatus == '17'){
            var ajax_url = '/incItems';
            $('#prepItemsModal').show();
            document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            $(".prephide").hide();
            $("#incItemsModal").show();
            $('#increceive_label').show();
            $(".divResched").show();
            $(".btnReceive").show();
            $(".divPrint").show();
        }
        if(requestStatus == '18'){
            var ajax_url = '/dfcItems';
            $('#prepItemsModal').show();
            document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            $(".prephide").hide();
            $("#incItemsModal").show();
            document.getElementById('incmodalheader').innerHTML = 'DEFECTIVE ITEM DETAILS';
            $("#incFooter").hide();
            $(".divPrint").show();
        }
        if(requestStatus == '19'){
            var ajax_url = '/schedItems';
            $('#prepItemsModal').show();
            document.getElementById('modalheader').innerHTML = 'REPLACEMENT ITEM DETAILS';
        }
        if(requestStatus == '20'){
            $('#prepItemsModal').show();
            $('#btnAssemble').show();
            document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
        }
        if(requestStatus == '21'){
            var ajax_url = '/incdfcItems';
            $('#prepItemsModal').show();
            document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            $(".prephide").hide();
            $("#incItemsModal").show();
            document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE DEFECTIVE ITEM DETAILS';
            $("#incFooter").hide();
            $(".divPrint").show();
        }
        if(requestStatus == '22'){
            var included = 'no';
            $('#prepItemsModal').show();
            document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            $(".prephide").hide();
            $(".pendshow").show();
        }
        if(requestStatus == '23'){
            var ajax_url = '/incItems';
            $('#prepItemsModal').show();
            document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            $(".prephide").hide();
            $("#incItemsModal").show();
            document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE REPLACEMENT ITEM DETAILS';
            $("#incFooter").hide();
            $(".divPrint").show();
        }

    $('.modal-body').html();
    $('#detailsAssembly').modal('show');

    $('table.stockDetails').dataTable().fnDestroy();    
    $('table.stockDetails').DataTable({
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        language:{
            emptyTable: "No data available in table",
            processing: "Loading...",
        },
        serverSide: true,
        ajax:{
            url: '/requestDetails',
            data:{
                reqnum: req_num,
            }
        },
        order: [],
        columns: [
            { data: 'prodcode' },
            { data: 'item' },
            { data: 'uom' },
            { data: 'quantity' }
        ],
        footerCallback: function(row,data,start,end,display){
            var api = this.api(), data;
            var intVal = function(i){
                return typeof i === 'string'?
                    i.replace(/[\$,]/g,'')*1:
                    typeof i === 'number'?
                        i:0;
            };
            api.columns('.sum', {page:'all'}).every(function(){
                var sum = this
                .data()
                .reduce(function(a,b){
                    return intVal(a) + intVal(b);
                }, 0);
                sum = sum.toString();
                var pattern = /(-?\d+)(\d{3})/;
                while(pattern.test(sum))
                sum = sum.replace(pattern,"$1,$2");
                this.footer().innerHTML = sum;
            });
        }
    });

    $('table.prepItems').dataTable().fnDestroy();
    $('table.prepItems').DataTable({
        columnDefs: [
            {
                "targets": [5,6],
                "visible": false,
                "searchable": false
            }
        ],
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        language:{
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax:{
            url: rcv_url,
            data:{
                request_number: req_num,
                included: included
            }
        },
        order: [],
        columns: [
            { data: 'prodcode' },
            { data: 'item' },
            { data: 'qty' },
            { data: 'uom' },
            { data: 'serial' },
            { data: 'id' },
            { data: 'id' }
        ],
        footerCallback: function(row,data,start,end,display){
            var api = this.api(), data;
            var intVal = function(i){
                return typeof i === 'string'?
                    i.replace(/[\$,]/g,'')*1:
                    typeof i === 'number'?
                        i:0;
            };
            api.columns('.sum', {page:'all'}).every(function(){
                var sum = this
                .data()
                .reduce(function(a,b){
                    return intVal(a) + intVal(b);
                }, 0);
                sum = sum.toString();
                var pattern = /(-?\d+)(\d{3})/;
                while(pattern.test(sum))
                sum = sum.replace(pattern,"$1,$2");
                this.footer().innerHTML = sum;
            });
        }
    });

    if(ajax_url != '/schedItems'){
        $('table.incItems').dataTable().fnDestroy();
        $('table.incItems').DataTable({
            columnDefs: [
                {
                    "targets": [5],
                    "visible": false,
                    "searchable": false
                }
            ],
            searching: false,
            paging: false,
            ordering: false,
            info: false,
            language:{
                processing: "Loading...",
                emptyTable: "No data available in table"
            },
            serverSide: true,
            ajax:{
                url: ajax_url,
                data:{
                    request_number: req_num,
                }
            },
            order: [],
            columns: [
                { data: 'prodcode' },
                { data: 'item' },
                { data: 'qty' },
                { data: 'uom' },
                { data: 'serial' },
                { data: 'id' }
            ],
            footerCallback: function(row,data,start,end,display){
                var api = this.api(), data;
                var intVal = function(i){
                    return typeof i === 'string'?
                        i.replace(/[\$,]/g,'')*1:
                        typeof i === 'number'?
                            i:0;
                };
                api.columns('.sum', {page:'all'}).every(function(){
                    var sum = this
                    .data()
                    .reduce(function(a,b){
                        return intVal(a) + intVal(b);
                    }, 0);
                    sum = sum.toString();
                    var pattern = /(-?\d+)(\d{3})/;
                    while(pattern.test(sum))
                    sum = sum.replace(pattern,"$1,$2");
                    this.footer().innerHTML = sum;
                });
            }
        });
    }

    if(requestStatus == '14'){
        $.ajax({
            type: 'get', 
            url: '/getReceive', 
            data:{
                'request_number': $('#request_num_details').val()
            }, 
            success: function(data){
                document.getElementById("recby").value = data.recby;
                document.getElementById("recsched").value = moment(data.recsched).format('dddd, MMMM DD, YYYY, h:mm A');
            },
            error: function(data){
                if(data.status == 401){
                    window.location.href = '/assembly';
                }
                alert(data.responseText);
            }
        });

        $('table.asmItems').dataTable().fnDestroy();
        $('table.asmItems').DataTable({
            searching: false,
            paging: false,
            ordering: false,
            info: false,
            language:{
                processing: "Loading...",
                emptyTable: "No data available in table"
            },
            serverSide: true,
            ajax:{
                url: '/asmItems',
                data:{
                    request_number: req_num,
                }
            },
            order: [],
            columns: [
                { data: 'prodcode' },
                { data: 'item' },
                { data: 'qty' },
                { data: 'uom' },
                { data: 'serial' },
                { data: 'location' }
            ],
            footerCallback: function(row,data,start,end,display){
                var api = this.api(), data;
                var intVal = function(i){
                    return typeof i === 'string'?
                        i.replace(/[\$,]/g,'')*1:
                        typeof i === 'number'?
                            i:0;
                };
                api.columns('.sum', {page:'all'}).every(function(){
                    var sum = this
                    .data()
                    .reduce(function(a,b){
                        return intVal(a) + intVal(b);
                    }, 0);
                    sum = sum.toString();
                    var pattern = /(-?\d+)(\d{3})/;
                    while(pattern.test(sum))
                    sum = sum.replace(pattern,"$1,$2");
                    this.footer().innerHTML = sum;
                });
            }
        });
    }
});

$('#btnDelete').on('click', function(){
    Swal.fire({
        title: "DELETE ASSEMBLY STOCK REQUEST?",
        html: "You are about to DELETE your ASSEMBLY STOCK REQUEST! <br><strong style='color: red;'>This will be permanently deleted from the system! CONTINUE?</strong>",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $.ajax({
                type: 'post', 
                url: '/deleteRequest',
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    'request_number': $('#request_num_details').val()
                },
                success: function(data){
                    if(data == 'true'){
                        $('#detailsAssembly').hide();
                        Swal.fire("DELETE SUCCESS", "ASSEMBLY REQUEST", "success");
                        setTimeout(function(){location.href="/assembly"}, 2000);
                    }
                    else{
                        $('#detailsAssembly').hide();
                        Swal.fire("DELETE FAILED", "ASSEMBLY REQUEST", "error");
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
    if(requestStatus > 13){
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
            $('.btnReceive').prop('disabled', true);
        }
        else{
            $('.btnReceive').prop('disabled', false);
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

$('.table.incItems').DataTable().on('select', function(){});
$('.incItems tbody').on('click', 'tr', function(){
    var requestStatus = $('#status_id_details').val();
    if(requestStatus != '17'){
        return false;
    }
    var table = $('table.incItems').DataTable();
    var data = table.row(this).data();
    item_count = table.data().count();

    $(this).toggleClass('selected');
    if(items.includes(data.id) == true){
        items = items.filter(item => item !== data.id);
    }
    else {
        items.push(data.id);
    }
    if(items.length == 0){
        $('.btnReceive').prop('disabled', true);
    }
    else{
        $('.btnReceive').prop('disabled', false);
    }
});

$('.btnReceive').on('click', function(){
    var inc = 'false';
    var inctype = 'COMPLETE';
    if(items.length < item_count){
        inc = 'true';
        inctype = 'INCOMPLETE';
    }
    Swal.fire({
        title: "RECEIVE "+inctype+" ASSEMBLY PARTS?",
        html: "You are about to RECEIVE these ASSEMBLY PARTS!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $.ajax({
                type: 'post',
                url: '/assembly/receiveRequest',
                async: false,
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    'request_number': $('#request_num_details').val(),
                    'assembly_reqnum': $('#asm_request_num_details').val(),
                    'request_type': $('#req_type_id_details').val(),
                    'inc': inc
                },
                success: function(data){
                    if(data == 'true'){
                        for(var i=0; i < items.length; i++){
                            $.ajax({
                                type: 'post',
                                url: '/assembly/receiveItems',
                                async: false,
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    'request_number': $('#request_num_details').val(),
                                    'status': $('#status_id_details').val(),
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
                        $('#detailsAssembly').modal('hide');
                        $('#loading').show();
                        $.ajax({
                            type: 'post',
                            url: '/assembly/logReceive',
                            headers:{
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:{
                                'request_number': $('#request_num_details').val(),
                                'status': $('#status_id_details').val(),
                                'inc': inc
                            },
                            success: function(data){
                                if(data == 'true'){
                                    $('#loading').hide();
                                    Swal.fire("RECEIVED "+inctype, "ASSEMBLY REQUEST", "success");
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
                        Swal.fire("RECEIVE FAILED", "ASSEMBLY REQUEST", "error");
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

$('#btnDefective').on('click', function(){
    generateReqNum();
    Swal.fire({
        title: "REQUEST REPLACEMENTS?",
        html: "You are about to REQUEST REPLACEMENTS for these DEFECTIVE ASSEMBLY PARTS!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $.ajax({
                type: 'post',
                url: '/assembly/defectiveRequest',
                async: false,
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    'request_number': $('#request_num_details').val(),
                    'generatedReqNum': generatedReqNum
                },
                success: function(data){
                    if(data == 'true'){
                        for(var i=0; i < items.length; i++){
                            $.ajax({
                                type: 'post',
                                url: '/assembly/defectiveItems',
                                async: false,
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    'generatedReqNum': generatedReqNum,
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
                        $('#detailsAssembly').modal('hide');
                        $('#loading').show();
                        $.ajax({
                            type: 'post',
                            url: '/assembly/logDefective',
                            headers:{
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:{
                                'request_number': $('#request_num_details').val(),
                                'generatedReqNum': generatedReqNum
                            },
                            success: function(data){
                                if(data == 'true'){
                                    $('#loading').hide();
                                    Swal.fire("REQUEST SUCCESS", "REPLACEMENT REQUEST", "success");
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
                        Swal.fire("REQUEST FAILED", "REPLACEMENT REQUEST", "error");
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
    var item_desc_details = decodeHtml($('#item_desc_details').val());
    Swal.fire({
        title: "ASSEMBLE: "+item_desc_details+"?",
        html: "You are about to ASSEMBLE this Assembly Stock Request!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $.ajax({
                type: 'post',
                url: '/assembly/assembleRequest',
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    'request_number': $('#request_num_details').val()
                },
                success: function(data){
                    if(data == 'true'){
                        $('#detailsAssembly').hide();
                        Swal.fire("ASSEMBLE SUCCESS", "ASSEMBLY REQUEST", "success");
                        setTimeout(function(){location.href="/assembly"}, 2000);
                    }
                    else{
                        $('#detailsAssembly').hide();
                        Swal.fire("ASSEMBLE FAILED", "ASSEMBLY REQUEST", "error");
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

$('#btnPending').on('click', function(){
    $.ajax({
        type: 'get', 
        url: '/getLink', 
        data:{
            'request_number': $('#request_num_details').val()
        }, 
        success: function(data){
            window.location.href = '/assembly?request_number='+data;
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/assembly';
            }
            alert(data.responseText);
        }
    });
});

$('.btnPrint').on('click', function(){
    window.location.href = '/printRequest?request_number='+$('#request_num_details').val();
});