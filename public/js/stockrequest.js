var minDate;
var maxDate;
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
function dateDiffInDays(a, b) {
    const utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
    const utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());
    
    return Math.floor((utc2 - utc1) / _MS_PER_DAY);
}

function copyReqNum() {
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

function copyRefNum() {
    if($("#reference_details").val() != ''){
        var copyText = document.getElementById("reference_details");
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
}

function generatedr() {
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

    for ( var i = 0; i < 3; i++ ) {
        result += characters.charAt(Math.floor(Math.random() * 6));
    }
    var request_number = date+result;

    $.ajax({
        type:'get',
        url:'/generatedr',
        async: false,
        data:{
            'request_number': request_number
        },
        success: function (data) {
            if (data == 'unique') {
                document.getElementById("request_num").value = request_number;
            }
            else{
                generatedr();
            }
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/stockrequest';
            }
                alert(data.responseText);
        }
    });
}

$(".newstockreq").on('click', function(){
    $('#newStockRequest').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('.modal-body').html();
    $('#newStockRequest').modal('show');
    generatedr();
});

$('table.stockDetails').DataTable().on('select', function(){});
var items = [];
$('.stockDetails tbody').on('click', 'tr', function () {
    var pend = $('#stockDetailsrequest').DataTable().cell(this,4).data();
    var stock = $('#stockDetailsrequest').DataTable().cell(this,5).data();
    var item_id = $('#stockDetailsrequest').DataTable().cell(this,6).data();
    var bal = $('#stockDetailsrequest').DataTable().cell(this,11).data();
    var mal = $('#stockDetailsrequest').DataTable().cell(this,12).data();

    if(pend == 0){
        swal('Item is fullfiled!','','success');
    }
    else if(stock == 0){
        if(bal != 0 && mal != 0){
            swal('Item out of stock!','Request Stock Transfer from Balintawak and/or Malabon.','warning');
        }
        else if(bal != 0 && mal == 0){
            swal('Item out of stock!','Request Stock Transfer from Balintawak.','warning');
        }
        else if(bal == 0 && mal != 0){
            swal('Item out of stock!','Request Stock Transfer from Malabon.','warning');
        }
        else{
            swal('Item out of stock!','','error');
        }
    }
    else{
        $(this).toggleClass('selected');
        if(items.includes(item_id) == true){
            items = items.filter(item => item !== item_id);
        }
        else {
            items.push(item_id);
        }
    }
    if(items.length == 0){
        $('#btnProceed').prop('disabled', true);
    }
    else{
        $('#btnProceed').prop('disabled', false);
    }
});

$("#btnProceed").unbind('click').click(function(){
    var reqnum = $('#request_num_details').val();
    var j = 0;
    $("#stockDetailsrequest *").prop('disabled',true);
    $("#btnProceed").hide();
    $("#requestItems").slideDown();
    $('#schedOn').attr('max', maxDate);
    for(var i=0; i < items.length; i++){
        $.ajax({ 
            type:'get', 
            url:'/stockreq', 
            data:{
                'reqnum': reqnum,
                'item_id': items[i]
            }, 
            success:function(data) {
                var reqitem = $.map(data.data, function(value, index) { 
                    return [value];
                });

                reqitem.forEach(value => {
                    if(value.qtystock <= value.pending){
                        var l = value.qtystock;
                    }
                    else{
                        var l = value.pending;
                    }
                    if(value.uom != 'Unit'){
                        $('#btnSubmit').prop('disabled', false);
                        var id = document.createElement("input");
                        id.setAttribute("id", "item_id"+j);
                        id.setAttribute("type", "hidden");
                        id.setAttribute("value", value.item_id);
                        var x = document.createElement("input");
                        x.setAttribute("id", "category"+j);
                        x.setAttribute("type", "text");
                        x.setAttribute("class", "form-control");
                        x.setAttribute("style", "width: 250px; font-size: 12px; margin-bottom: 10px;");
                        x.setAttribute("value", value.category);
                        var y = document.createElement("textarea");
                        y.setAttribute("id", "item"+j);
                        y.setAttribute("class", "form-control");
                        y.setAttribute("rows", "4");
                        y.setAttribute("style", "width: 250px; font-size: 12px; margin-left: 10px; margin-top: 52px; margin-bottom: 10px; resize: none;");
                        var z = document.createElement("select");
                        z.setAttribute("id", "location"+j);
                        z.setAttribute("class", "form-control");
                        z.setAttribute("style", "width: 150px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
                        var qty = document.createElement("input");
                        qty.setAttribute("id", "qty"+j);
                        qty.setAttribute("type", "number");
                        qty.setAttribute("class", "form-control qty");
                        qty.setAttribute("style", "width: 100px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
                        qty.setAttribute("max", l);
                        qty.setAttribute("min", '1');
                        qty.setAttribute("value", l);
                        var uom = document.createElement("input");
                        uom.setAttribute("id", "uom"+j);
                        uom.setAttribute("type", "text");
                        uom.setAttribute("class", "form-control");
                        uom.setAttribute("style", "width: 100px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
                        uom.setAttribute("value", value.uom);
                        document.getElementById("reqContents").appendChild(id);
                        document.getElementById("reqContents").appendChild(x);
                        document.getElementById("reqContents").appendChild(y);
                        document.getElementById("reqContents").appendChild(qty);
                        document.getElementById("reqContents").appendChild(uom);
                        document.getElementById("reqContents").appendChild(z);
                        $("#item"+j).html(value.item); 
                        $("#category"+j).prop('readonly', true);
                        $("#item"+j).prop('readonly', true);
                        $("#uom"+j).prop('readonly', true);
                        $("#location"+j).prop('disabled', true);
                        $("#location"+j).append("<option value='8' selected>MAIN BRANCH</option>");
                        j++;
                    }
                    else{
                        for(var k=0; k < l; k++){
                            var id = document.createElement("input");
                            id.setAttribute("id", "item_id"+j);
                            id.setAttribute("type", "hidden");
                            id.setAttribute("value", value.item_id);
                            var x = document.createElement("input");
                            x.setAttribute("id", "category"+j);
                            x.setAttribute("type", "text");
                            x.setAttribute("class", "form-control");
                            x.setAttribute("style", "width: 250px; font-size: 12px; margin-bottom: 10px;");
                            x.setAttribute("value", value.category);
                            var y = document.createElement("textarea");
                            y.setAttribute("id", "item"+j);
                            y.setAttribute("class", "form-control");
                            y.setAttribute("rows", "4");
                            y.setAttribute("style", "width: 250px; font-size: 12px; margin-left: 10px; margin-top: 52px; margin-bottom: 10px; resize: none;");
                            var z = document.createElement("select");
                            z.setAttribute("id", "location"+j);
                            z.setAttribute("class", "form-control");
                            z.setAttribute("style", "width: 100px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
                            var qty = document.createElement("input");
                            qty.setAttribute("id", "qty"+j);
                            qty.setAttribute("type", "number");
                            qty.setAttribute("class", "form-control");
                            qty.setAttribute("style", "width: 100px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
                            qty.setAttribute("value", '1');
                            var uom = document.createElement("input");
                            uom.setAttribute("id", "uom"+j);
                            uom.setAttribute("type", "text");
                            uom.setAttribute("class", "form-control");
                            uom.setAttribute("style", "width: 100px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
                            uom.setAttribute("value", value.uom);
                            var serial = document.createElement("select");
                            serial.setAttribute("id", "serial"+j);
                            serial.setAttribute("class", "form-control serials");
                            serial.setAttribute("style", "width: 200px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
                            document.getElementById("reqContents").appendChild(id);
                            document.getElementById("reqContents").appendChild(x);
                            document.getElementById("reqContents").appendChild(y);
                            document.getElementById("reqContents").appendChild(qty);
                            document.getElementById("reqContents").appendChild(uom);
                            document.getElementById("reqContents").appendChild(serial);
                            document.getElementById("reqContents").appendChild(z);
                            $("#item"+j).html(value.item); 
                            $("#category"+j).prop('readonly', true);
                            $("#item"+j).prop('readonly', true);
                            $("#qty"+j).prop('readonly', true);
                            $("#uom"+j).prop('readonly', true);
                            $("#location"+j).prop('disabled', true);
                            $("#serial"+j).append("<option value='' selected>Select Serial</option>");
                            let vid = "#serial"+j;
                            $.ajax({ 
                                type:'get', 
                                url:'/setserials', 
                                data:{
                                    'item_id': value.item_id
                                }, 
                                success:function(d) {   
                                    var s = $.map(d, function(v) { 
                                        return [v];
                                    });
                
                                    s.forEach(v => {
                                        if(v.serial == 'N/A'){
                                            $(vid).append($('<option>', {
                                                value: v.id,
                                                text: v.serial+' - '+v.location
                                            }));
                                        }
                                        else{
                                            $(vid).append($('<option>', {
                                                value: v.id,
                                                text: v.serial
                                            }));
                                        }
                                    });
                                    $(vid).chosen();
                                },
                                error: function (data) {
                                    if(data.status == 401) {
                                        window.location.href = '/stockrequest';
                                    }
                                    alert(data.responseText);
                                }
                            });
                            j++;
                        }
                    }
                });
                $('.serials').on('change', function(){
                    $('.serials option').show();
                    $('.serials').each(function(){
                        var $this = $(this);
                        $('.serials').not($this).find('option').each(function(){
                            if($(this).attr('value') == $this.val()){
                                $(this).hide();
                            }
                        });
                    });
                    $('select option:contains("Select Serial")').show();
                    $('.serials').trigger("chosen:updated");
                });
                for(var m=0; m < j; m++){
                    let id = '#location'+m;
                    $('#serial'+m).on('change', function(){
                        if($('.serials').filter(function() { return !!this.value; }).length == 0) {
                            $('#btnSubmit').prop('disabled', true);
                        }
                        else{
                            $('#btnSubmit').prop('disabled', false);
                        }
                        var serial_id = $(this).val();
                        $.ajax({
                            type:'get', 
                            url:'/setlocation', 
                            data:{
                                'serial_id': serial_id
                            }, 
                            success:function(data) {
                                $(id).find('option').remove().end()
                                $(id).append($('<option>', {
                                    value: data.location_id,
                                    text: data.location
                                }));
                            },
                            error: function (data) {
                                if(data.status == 401) {
                                    window.location.href = '/stockrequest';
                                }
                                alert(data.responseText);
                            }
                        });
                    });
                }
                $("#btnSubmit").unbind('click').click(function(){
                    if(!$("#schedOn").val()){
                        swal('Scheduled On is required!','Select within date range from today up to Date Needed.','error');
                        return false;
                    }
                    else if($("#schedOn").val() < minDate){
                        swal('Minimum Date is today!','Select within date range from today up to Date Needed.','error');
                        return false;
                    }
                    else if($("#schedOn").val() > maxDate){
                        swal('Exceed Date Needed deadline!','Select within date range from today up to Date Needed.','error');
                        return false;
                    }
                    else{
                        swal({
                            title: "SCHEDULE STOCK REQUEST?",
                            text: "You are about to SCHEDULE this STOCK REQUEST!",
                            icon: "warning",
                            buttons: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {
                                for(var n=0; n < j; n++){
                                    if($('#serial'+n).val() != ''){
                                        $.ajax({
                                            type:'post',
                                            url:'/prepareItems',
                                            headers: {
                                                'X-CSRF-TOKEN': $("#csrf").val()
                                            },
                                            data:{
                                                'request_number': reqnum,
                                                'item_id': $('#item_id'+n).val(),
                                                'category': $('#category'+n).val(),
                                                'item': $('#item'+n).val(),
                                                'qty': $('#qty'+n).val(),
                                                'stock_id': $('#serial'+n).val(),
                                                'serial': $('#serial'+n).find('option:selected').text(),
                                                'location': $('#location'+n).val(),
                                                'schedOn': $('#schedOn').val()
                                            },
                                            success: function (data){
                                                if(data == 'true'){
                                                    return true;
                                                }
                                                else{
                                                    return false;
                                                }
                                            },
                                            error: function (data) {
                                                if(data.status == 401) {
                                                    window.location.href = '/stockrequest';
                                                }
                                                alert(data.responseText);
                                            }
                                        });
                                    }
                                }
                                $.ajax({
                                    type:'post',
                                    url:'/logSched',
                                    headers: {
                                        'X-CSRF-TOKEN': $("#csrf").val()
                                    },
                                    data:{
                                        'request_number': reqnum,
                                        'schedOn': $('#schedOn').val()
                                    },
                                    success: function (){
                                        $('#stockRequestDetails').hide();
                                        sweetAlert("SCHEDULED SUCCESS", "STOCK REQUEST", "success");
                                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                                    },
                                    error: function (data) {
                                        if(data.status == 401) {
                                            window.location.href = '/stockrequest';
                                        }
                                        alert(data.responseText);
                                    }
                                });
                            }
                        }); 
                    }
                });
            },
            error: function (data) {
                if(data.status == 401) {
                    window.location.href = '/stockrequest';
                }
                alert(data.responseText);
            }
        }); 
    }
});

$("#btnBack").on('click', function(){
    $("#stockDetailsrequest *").prop('disabled', false);
    $('#btnSubmit').prop('disabled', true);
    $("#requestItems").hide();
    $("#btnProceed").show();
    $("#reqContents").empty();
});

$('table.stock_request').dataTable().fnDestroy();
$('#loading').show(); Spinner(); Spinner.show();
$('table.stock_request').DataTable({ 
    columnDefs: [
        {
            "targets": [1],
            "render": $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'MMM. D, YYYY')
        },
        {
            "targets": [0],
            "render": $.fn.dataTable.render.moment('YYYY-MM-DD', 'MMM. D, YYYY')
        },
        {
            "targets": [7,8,9,10,11,12,13,14],
            "visible": false,
            "searchable": false
        }
    ],
    language: {
        processing: "Loading...",
        emptyTable: "No data found!"
    },
    order: [],
    ajax: {
        url: '/request_data',
    },
    columns: [
        {
            data: 'needdate',
            "render": function(data, type, row){
                if(row.status_id == '7' || row.status_id == '8'){
                    return moment(row.needdate).format('MMM. D, YYYY');
                }
                else{
                    var a = new Date(minDate);
                    var b = new Date(row.needdate);
                    var difference = dateDiffInDays(a, b);
                    if(difference >= 0 && difference <= 3){
                        return "<span style='color: Blue; font-weight: bold;'>"+moment(row.needdate).format('MMM. D, YYYY')+'&nbsp;&nbsp;&nbsp;'+"<i style='zoom: 150%; color: blue;' class='fa fa-exclamation-triangle'></i></span>";
                    }
                    else if(difference < 0){
                        return "<span style='color: Red; font-weight: bold;'>"+moment(row.needdate).format('MMM. D, YYYY')+'&nbsp;&nbsp;&nbsp;'+"<i style='zoom: 150%; color: red;' class='fa fa-exclamation-circle'></i></span>";
                    }
                    else{
                        return moment(row.needdate).format('MMM. D, YYYY');
                    }
                }
            }
        },
        { data: 'date'},
        { data: 'req_num'},
        { data: 'reference'},
        { data: 'req_by'},
        { data: 'req_type'},
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
                else if(row.status_id == '8'){
                    return "<span style='color: Blue; font-weight: bold;'>"+row.status+"</span>";
                }
                else{
                    return "<span style='color: Gray; font-weight: bold;'>"+row.status+"</span>";
                }
            }
        },
        { data: 'req_type_id'},
        { data: 'status_id'},
        { data: 'prep_by'},
        { data: 'sched'},
        { data: 'user_id'},
        { data: 'client_name'},
        { data: 'location'},
        { data: 'reason'}
    ],
    initComplete: function (){
        $('#loading').hide(); Spinner.hide();
    }
});
        
$(".add-row").on('click', function(){                  
    var category = $("#categoryReq option:selected").text();
    var item = $("#itemReq option:selected").text();
    let qty = $("#qtyReq").val();
    var uom = $("#uom").val();
    var markup = "<tr><td>" + category + "</td><td>" + item + "</td><td>" + qty + "</td><td>" + uom + "</td><td> <button type='button' class='delete-row btn-primary btn-xs bp'>REMOVE</button> </td></tr>";
    var ctr='false';
    if(category == "Select Category" || item == "Select Item" || qty == "" || qty == "0" || uom == ""){
        swal('REQUIRED','Please select an item!','error');
        return false;
    }
    else{
        var table = document.getElementById('stockRequestTable');
        var count = table.rows.length;
        for (i = 1; i < count; i++) {

            var objCells = table.rows.item(i).cells;

            if(item==objCells.item(1).innerHTML){
                objCells.item(2).innerHTML = parseInt(objCells.item(2).innerHTML) + parseInt(qty);
                ctr='true';
                category = $("#categoryReq").val('Select Category');
                item = $("#itemReq").find('option').remove().end().append('<option value="0">Select Item</option>').val()
                qty = $("#qtyReq").val('');
                uom = $('#uom').val('');
                return false;
            }
            else {
                ctr='false';
            }
        }
        if(ctr=='false')
        { $("#stockRequestTable tbody").append(markup); }
        category = $("#categoryReq").val('Select Category');
        item = $("#itemReq").find('option').remove().end().append('<option value="0">Select Item</option>').val()
        qty = $("#qtyReq").val('');
        uom = $('#uom').val('');
        $('#stockRequestTable').show();
        $('#stockRequestDiv').toggle();
        $('#requestClose').show();
        $('#requestSave').show();
    } 
});

// $(".add-row").on('click', function(){                   
//     var item_id = $("#itemReq option:selected").val();
//     var category = $("#categoryReq option:selected").text();
//     var item = $("#itemReq option:selected").text();
//     var qty = $("#qtyReq").val();
            
//     if(category == "Select Category" || item == "Select Item" || qty == ""){
//         alert('Please select item!');
//         return false;
//     }
//     else{
//         $.ajax({
//             url:'/itemsstock',
//             type:'get',
//             data:{
//                 'item_id': item_id,
//                 'item': item,
//                 'qty': qty,
//             },
//             success:function(getData){
//                 alert(getData);
//                 var stock = getData;
//                 var markup = "<tr><td>" + category + "</td><td>" + item + "</td><td>" + qty + "</td><td>"+ stock +"</td><td> <button type='button' class='delete-row btn-primary btn-xs bp'>REMOVE</button> </td></tr>";
//                 $('#qtyStock').val('');
//                 $("#stockRequestTable tbody").append(markup);
//                 category = $("#categoryReq").val('Select Category');
//                 item = $("#itemReq").find('option').remove().end().append('<option value="0">Select Item</option>').val()
//                 qty = $("#qtyReq").val('');
//                 $('#stockRequestTable').show();
//                 $('#stockRequestDiv').toggle();
//                 $('#requestClose').show();
//                 $('#requestSave').show();
//             }
//         });
//     } 
// });

$("#stockRequestTable").on('click', '.delete-row', function(){
    $(this).closest("tr").remove();
    if ($('#stockRequestTable tbody').children().length==0) {
        $('#stockRequestTable').hide();
        $('#stockRequestDiv').removeClass();
        $('#requestClose').hide();
        $('#requestSave').hide();
    }
});

$(document).on('click', '#close', function(){
    window.location.href = '/stockrequest';
});

$(document).on('click', '#requestClose', function(){
    window.location.href = '/stockrequest';
});

$(document).on('click', '#modalClose', function(){
    window.location.href = '/stockrequest';
});

$(document).on('click', '#requestSave', function(){
    if($('#needdate').val() && $('#request_type').val() && $('#client_name').val() && $('#location').val())
    {
        if($("#needdate").val() < minDate){
            swal('Minimum Date is today!','Select within date range from today onwards.','error');
            return false;
        }
        else{
            swal({
                title: "SUBMIT STOCK REQUEST?",
                text: "You are about to SUBMIT this STOCK REQUEST!",
                icon: "warning",
                buttons: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        type:'post',
                        url:'/saveReqNum',
                        headers: {
                            'X-CSRF-TOKEN': $("#csrf").val(),
                        },
                        data:{
                            'request_number': $('#request_num').val(),
                            'requested_by': $('#requested_by').val(),
                            'needdate': $('#needdate').val(),
                            'request_type': $('#request_type').val(),
                            'client_name': $('#client_name').val(),
                            'location': $('#location').val(),
                            'reference': $('#reference').val(),
                        },
                        success: function (data){
                            if(data == 'true'){
                                var myTable = $('#stockRequestTable').DataTable();
                                var form_data  = myTable.rows().data();
                                $.each( form_data, function( key, value ) {
                                    $.ajax({
                                        type:'post',
                                        url:'/saveRequest',
                                        headers: {
                                            'X-CSRF-TOKEN': $("#csrf").val(),
                                        },
                                        data:{
                                            'request_number': $('#request_num').val(),
                                            'category': value[0],
                                            'item': value[1],
                                            'quantity': value[2]
                                        },
                                        success: function (data){
                                            if(data == 'true'){
                                                return true;
                                            }
                                            else{
                                                return false;
                                            }
                                        },
                                        error: function (data) {
                                            if(data.status == 401) {
                                                window.location.href = '/stockrequest';
                                            }
                                            alert(data.responseText);
                                        }
                                    });
                                });
                                scrollReset();
                                $('#newStockRequest').hide();
                                $('#newStockRequest').modal('dispose');
                                $('#loading').show(); Spinner(); Spinner.show();
                                $.ajax({
                                    type:'post',
                                    url:'/logSave',
                                    headers: {
                                        'X-CSRF-TOKEN': $("#csrf").val()
                                    },
                                    data:{
                                        'request_number': $('#request_num').val(),
                                    },
                                    success: function (data){
                                        if(data == 'true'){
                                            $('#loading').hide(); Spinner.hide();
                                            sweetAlert("SUBMIT SUCCESS", "STOCK REQUEST", "success");
                                            setTimeout(function(){location.href="/stockrequest"}, 2000);
                                        }
                                        else{
                                            return false;
                                        }
                                    },
                                    error: function (data) {
                                        if(data.status == 401) {
                                            window.location.href = '/stockrequest';
                                        }
                                        alert(data.responseText);
                                    }
                                });
                            }
                            else{
                                $('#newStockRequest').hide();
                                sweetAlert("SUBMIT FAILED", "STOCK REQUEST", "error");
                                setTimeout(function(){location.href="/stockrequest"}, 2000);
                            }
                        },
                        error: function (data){
                            if(data.status == 401) {
                                window.location.href = '/stockrequest';
                            }
                            alert(data.responseText);
                        }
                    });
                }
            });
        }
    }
    else{
        var required_fields = [];
        var required_list;
        if(!$('#needdate').val()){
            required_fields.push('*Date Needed');
        }
        if(!$('#request_type').val()){
            required_fields.push('*Request Type');
        }
        if(!$('#client_name').val()){
            required_fields.push('*Client Name');
        }
        if(!$('#location').val()){
            required_fields.push('*Address / Branch');
        }
        required_list = required_fields.join("\r\n");
        swal('Fill up all required fields!', required_list, 'error');
        return false;
    }   
});

$('#categoryReq').on('change', function(){
    var id = $('#categoryReq').val();
    var descOp = " ";
    $('#uom').val('');
    $.ajax({ 
        type:'get', 
        url:'/itemsreq', 
        data:{'category_id':id}, 
        success:function(data) 
            {
                var itemcode = $.map(data, function(value, index) { 
                    return [value];
                });
                descOp+='<option selected disabled>Select Item</option>'; 
                itemcode.forEach(value => {
                    descOp+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>'; 
                });
                
                $("#itemReq").find('option').remove().end().append(descOp);                 
            },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/stockrequest';
            }
            alert(data.responseText);
        }
    });    
});

// $('#itemReq').on('change', function(){ 
//     $('#qtyReq').val('');
//     var id=$('#itemReq').val();
//     $.ajax({ 
//         type:'get', 
//         url:'/itemsqty', 
//         data:{
//             item_id: id
//         },
//         success: function(dataResult){      
//             $('#qtyStock').val(dataResult);
//             $('#qtyReq').attr({
//                 "max" : dataResult,
//                 "min" : 0
//             });
//         },
//         error: function (data) {
//             if(data.status == 401) {
//                 window.location.href = '/login';
//             }
//             alert(data.responseText);
//         }
//     })
// });

$('#itemReq').on('change', function(){
    var item_id = $(this).val();
    $.ajax({
        type:'get', 
        url:'/setuom', 
        data:{
            'item_id': item_id,
        }, 
        success:function(data) {
            $('#uom').val(data);
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/stockrequest';
            }
            alert(data.responseText);
        }
    });
});

if(window.location.href != 'https://lance.idsi.com.ph/stockrequest'){
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
        success: function(data) {
            $('#stockRequestDetails').modal({
                backdrop: 'static',
                keyboard: false
            });
            var reqitem = $.map(data.data, function(value, index) { 
                return [value];
            });
            reqitem.forEach(value => {
                var req_date = value.date;
                    req_date = moment(req_date).format('dddd, MMMM D, YYYY, h:mm A');
                    $('#daterequestdetails').val(req_date);
                var need_date = value.needdate;
                    maxDate = need_date;
                    need_date = moment(need_date).format('dddd, MMMM D, YYYY');
                    $('#needdate_details').val(need_date);
                var req_num = value.req_num;
                    $('#request_num_details').val(req_num);
                var req_by = value.req_by;
                    $('#requested_by_details').val(req_by);
                var req_type = value.req_type;
                    $('#request_type_details').val(req_type);
                var status = value.status;
                    $('#status_details').val(status);
                var prep_by = value.prep_by;
                    $('#prep_by').val(prep_by);
                    $('#prep_by1').val(prep_by);
                var sched = value.sched;
                    sched = moment(sched).format('dddd, MMMM D, YYYY');
                    $('#sched').val(sched);
                    $('#sched1').val(sched);
                var client_name = value.client_name;
                    $('#client_name_details').val(client_name);
                var location = value.location;
                    $('#location_details').val(location);
                var reference = value.reference;
                    $('#reference_details').val(reference);
                var reason = value.reason;
                    $('#reason_details').val(reason);
            
                    $('.modal-body').html();
                    $('#stockRequestDetails').modal('show');
                    if(value.req_type_id == '1'){
                        $("#client_name_label").hide();
                        $("#client_name_details").hide();
                        $("#location_label").hide();
                        $("#location_details").hide();
                        $("#reference_label").hide();
                        $("#reference_details").hide();
                    }
                    if(value.status_id != '6'){
                        $("#btnApprove").hide();
                        $("#btnDisapprove").hide();
                    }
                    if(value.status_id == '7'){
                        $("#btnApprove").show();
                        $("#btnDisapprove").hide();
                        $("#reason_label").show();
                        $("#reason_details").show();
                    }
                    if(value.status_id == '1'|| value.status_id == '2'|| value.status_id == '3' || value.status_id == '4' || value.status_id == '5' || value.status_id == '8'){
                        $("#btnDelete").hide();
                    }
                    if(value.status_id == '2' || value.status_id == '3' || value.status_id == '4' || value.status_id == '6' || value.status_id == '7' || value.status_id == '8'){
                        $("#btnProceed").hide();
                    }
                    if(value.status_id == '2' || value.status_id == '5'){
                        $("#schedItemsModal").show();
                    }
                    if(value.status_id == '3' || value.status_id == '4'){
                        $("#transitItemsModal").show();
                    }
                    if(value.status_id == '8'){
                        $("#transitItemsModal").show();
                        $("#btnReceive").hide();
                        document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
                    }
                    if(value.user_id != $('#current_user').val()){
                        $("#btnReceive").hide();
                    }
                    if(value.user_id == $('#current_user').val() && $("#current_role").val() == '["sales"]'){
                        $("#sd2").show();
                        $("#sd1").hide();
                    }
                    else{
                        $("#btnDelete").hide();
                        $("#sd1").show();
                        $("#sd2").hide();
                    }
                    if(value.status_id == '1'|| value.status_id == '2'|| value.status_id == '3' || value.status_id == '4' || value.status_id == '5' || value.status_id == '8'){
                        $("#sd1").show();
                        $("#sd2").hide();
                    }
                    
                $('table.stockDetails').dataTable().fnDestroy();    
                $('table.stockDetails').DataTable({ 
                    columnDefs: [
                        {
                            "targets": [6,7,8,9,10],
                            "visible": false,
                            "searchable": false
                        },
                        {
                            render: function(data,type,full,meta){
                                return "<div class='text-wrap' style='width: 200px; text-align: center;'>"+data+"</div>";
                            },
                            targets:[0]
                        },
                        {
                            render: function(data,type,full,meta){
                                return "<div class='text-wrap' style='width: 300px; text-align: center;'>"+data+"</div>";
                            },
                            targets:[1]
                        },
                        {
                            render: function(data,type,full,meta){
                                return "<div style='text-align: center;'>"+data+"</div>";
                            },
                            targets:[2,3,4,5,6,7,8,9,10]
                        },
                        {
                            render: function(data,type,full,meta){
                                return "<div style='text-align: center; color: red;'>"+data+"</div>";
                            },
                            targets:[11,12]
                        }
                    ],
                    scrollX: true,
                    paging: false,
                    ordering: false,
                    info: false,
                    language: {
                        "emptyTable": "No data found!",
                        "processing": "Loading",
                    },
                    processing: true,
                    serverSide: false,
                    
                    ajax: {
                        url: '/requestDetails',
                        data: {
                            reqnum: req_num,
                        },
                        dataType: 'json',
                        error: function(data) {
                            if(data.status == 401) {
                                window.location.href = '/stockrequest';
                            }
                            alert(data.responseText);
                        },
                    },
                    columns: [
                        { data: 'category'},
                        { data: 'item'},
                        { data: 'uom'},
                        { data: 'quantity'},
                        { data: 'pending'},
                        { data: 'qtystock'},
                        { data: 'item_id'},
                        { data: 'qtya1'},
                        { data: 'qtya2'},
                        { data: 'qtya3'},
                        { data: 'qtya4'},
                        { data: 'qtybal'},
                        { data: 'qtymal'},
            
                    ],
                    orderCellsTop: true,
                    fixedHeader: true,            
                }); 
                
                $('table.stockDetails1').dataTable().fnDestroy();    
                $('table.stockDetails1').DataTable({ 
                    columnDefs: [
                        {
                            "targets": [5],
                            "visible": false,
                            "searchable": false
                        }
                    ],
                    paging: false,
                    ordering: false,
                    info: false,
                    language: {
                        "emptyTable": "No data found!",
                        "processing": "Loading",
                    },
                    processing: true,
                    serverSide: false,
                    
                    ajax: {
                        url: '/requestDetails',
                        data: {
                            reqnum: req_num,
                        },
                        dataType: 'json',
                        error: function(data) {
                            if(data.status == 401) {
                                window.location.href = '/stockrequest';
                            }
                            alert(data.responseText);
                        },
                    },
                    columns: [
                        { data: 'category'},
                        { data: 'item'},
                        { data: 'uom'},
                        { data: 'quantity'},
                        { data: 'pending'},
                        { data: 'item_id'}
                    ],
                    orderCellsTop: true,
                    fixedHeader: true,            
                });
                
                $('table.stockDetails2').dataTable().fnDestroy();    
                $('table.stockDetails2').DataTable({ 
                    columnDefs: [
                        {
                            "targets": [6],
                            "visible": false,
                            "searchable": false
                        },
                        {   
                            "render": function (data, type, row, meta) {
                                    return '<button class="btn-primary bp btndelItem" id="'+ meta.row +'">REMOVE</button>';
                            },
                            "defaultContent": '',
                            "data": null,
                            "targets": [5]
                        }
                    ],
                    paging: false,
                    ordering: false,
                    info: false,
                    language: {
                        "emptyTable": "No data found!",
                        "processing": "Loading",
                    },
                    processing: true,
                    serverSide: false,
                    
                    ajax: {
                        url: '/requestDetails',
                        data: {
                            reqnum: req_num,
                        },
                        dataType: 'json',
                        error: function(data) {
                            if(data.status == 401) {
                                window.location.href = '/stockrequest';
                            }
                            alert(data.responseText);
                        },
                    },
                    columns: [
                        { data: 'category'},
                        { data: 'item'},
                        { data: 'uom'},
                        { data: 'quantity'},
                        { data: 'pending'},
                        { data: 'item_id'},
                        { data: 'item_id'}
                    ],
                    orderCellsTop: true,
                    fixedHeader: true,            
                });
            
                $('table.schedItems').DataTable({
                    columnDefs: [
                        {
                            "targets": [6,7],
                            "visible": false,
                            "searchable": false
                        },
                        {   
                            "render": function ( data, type, row, meta ) {
                                    return '<button class="btn-primary bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                            },
                            "defaultContent": '',
                            "data": null,
                            "targets": [6]
                        }
                    ],
                    paging: false,
                    ordering: false,
                    info: false,
                    language: {
                        processing: "Loading...",
                        emptyTable: "No data found!"
                    },
                    order: [],
                    ajax: {
                        url: '/schedItems',
                        data: {
                            request_number: req_num,
                        }
                    },
                    columns: [
                        { data: 'category'},
                        { data: 'item'},
                        { data: 'qty'},
                        { data: 'uom'},
                        { data: 'serial'},
                        { data: 'location'},
                        { data: 'id'},
                        { data: 'id'}
                    ]
                });
            
                $('table.schedItems1').DataTable({
                    paging: false,
                    ordering: false,
                    info: false,
                    language: {
                        processing: "Loading...",
                        emptyTable: "No data found!"
                    },
                    order: [],
                    ajax: {
                        url: '/schedItems',
                        data: {
                            request_number: req_num,
                        }
                    },
                    columns: [
                        { data: 'category'},
                        { data: 'item'},
                        { data: 'qty'},
                        { data: 'uom'},
                        { data: 'serial'},
                        { data: 'location'}
                    ]
                });
            
                $('table.transItems').DataTable({
                    columnDefs: [
                        {
                            "targets": [5],
                            "visible": false,
                            "searchable": false
                        },
                        {   
                            "render": function ( data, type, row, meta ) {
                                    return '<button class="btn-primary bp btnReceive" id="'+ meta.row +'">RECEIVE</button>';
                            },
                            "defaultContent": '',
                            "data": null,
                            "targets": [4]
                        }
                    ],
                    paging: false,
                    ordering: false,
                    info: false,
                    language: {
                        processing: "Loading...",
                        emptyTable: "No data found!"
                    },
                    order: [],
                    ajax: {
                        url: '/schedItems',
                        data: {
                            request_number: req_num,
                        }
                    },
                    columns: [
                        { data: 'category'},
                        { data: 'item'},
                        { data: 'qty'},
                        { data: 'uom'},
                        { data: 'serial'},
                        { data: 'location'},
                        { data: 'serial'},
                        { data: 'item_id'}
                    ]
                });
            
                $('table.transItems1').DataTable({
                    paging: false,
                    ordering: false,
                    info: false,
                    language: {
                        processing: "Loading...",
                        emptyTable: "No data found!"
                    },
                    order: [],
                    ajax: {
                        url: '/schedItems',
                        data: {
                            request_number: req_num,
                        }
                    },
                    columns: [
                        { data: 'category'},
                        { data: 'item'},
                        { data: 'qty'},
                        { data: 'uom'},
                        { data: 'serial'},
                        { data: 'location'}
                    ]
                });
            });
        },
        error: function(data) {
            alert(data.responseText);
        }
    });
}

$('#stockreqDetails tbody').on('click', 'tr', function(){
    $('#stockRequestDetails').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table =  $('table.stock_request').DataTable(); 
    var data = table.row( this ).data();
    var req_date = data.date;
        req_date = moment(req_date).format('dddd, MMMM D, YYYY, h:mm A');
        $('#daterequestdetails').val(req_date);
    var need_date = data.needdate;
        maxDate = need_date;
        need_date = moment(need_date).format('dddd, MMMM D, YYYY');
        $('#needdate_details').val(need_date);
    var req_num = data.req_num;
        $('#request_num_details').val(req_num);
    var req_by = data.req_by;
        $('#requested_by_details').val(req_by);
    var req_type = data.req_type;
        $('#request_type_details').val(req_type);
    var status = data.status;
        $('#status_details').val(status);
    var prep_by = data.prep_by;
        $('#prep_by').val(prep_by);
        $('#prep_by1').val(prep_by);
    var sched = data.sched;
        sched = moment(sched).format('dddd, MMMM D, YYYY');
        $('#sched').val(sched);
        $('#sched1').val(sched);
    var client_name = data.client_name;
        $('#client_name_details').val(client_name);
    var location = data.location;
        $('#location_details').val(location);
    var reference = data.reference;
        $('#reference_details').val(reference);
    var reason = data.reason;
        $('#reason_details').val(reason);

        $('.modal-body').html();
        $('#stockRequestDetails').modal('show');
        if(data.req_type_id == '1'){
            $("#client_name_label").hide();
            $("#client_name_details").hide();
            $("#location_label").hide();
            $("#location_details").hide();
            $("#reference_label").hide();
            $("#reference_details").hide();
        }
        if(data.status_id != '6'){
            $("#btnApprove").hide();
            $("#btnDisapprove").hide();
        }
        if(data.status_id == '7'){
            $("#btnApprove").show();
            $("#btnDisapprove").hide();
            $("#reason_label").show();
            $("#reason_details").show();
        }
        if(data.status_id == '1'|| data.status_id == '2'|| data.status_id == '3' || data.status_id == '4' || data.status_id == '5' || data.status_id == '8'){
            $("#btnDelete").hide();
        }
        if(data.status_id == '2' || data.status_id == '3' || data.status_id == '4' || data.status_id == '6' || data.status_id == '7' || data.status_id == '8'){
            $("#btnProceed").hide();
        }
        if(data.status_id == '2' || data.status_id == '5'){
            $("#schedItemsModal").show();
        }
        if(data.status_id == '3' || data.status_id == '4'){
            $("#transitItemsModal").show();
        }
        if(data.status_id == '8'){
            $("#transitItemsModal").show();
            $("#btnReceive").hide();
            document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
        }
        if(data.user_id != $('#current_user').val()){
            $("#btnReceive").hide();
        }
        if(data.user_id == $('#current_user').val() && $("#current_role").val() == '["sales"]'){
            $("#sd2").show();
            $("#sd1").hide();
        }
        else{
            $("#btnDelete").hide();
            $("#sd1").show();
            $("#sd2").hide();
        }
        if(data.status_id == '1'|| data.status_id == '2'|| data.status_id == '3' || data.status_id == '4' || data.status_id == '5' || data.status_id == '8'){
            $("#sd1").show();
            $("#sd2").hide();
        }
        
    $('table.stockDetails').dataTable().fnDestroy();    
    $('table.stockDetails').DataTable({ 
        columnDefs: [
            {
                "targets": [6,7,8,9,10],
                "visible": false,
                "searchable": false
            },
            {
                render: function(data,type,full,meta){
                    return "<div class='text-wrap' style='width: 200px; text-align: center;'>"+data+"</div>";
                },
                targets:[0]
            },
            {
                render: function(data,type,full,meta){
                    return "<div class='text-wrap' style='width: 300px; text-align: center;'>"+data+"</div>";
                },
                targets:[1]
            },
            {
                render: function(data,type,full,meta){
                    return "<div style='text-align: center;'>"+data+"</div>";
                },
                targets:[2,3,4,5,6,7,8,9,10]
            },
            {
                render: function(data,type,full,meta){
                    return "<div style='text-align: center; color: red;'>"+data+"</div>";
                },
                targets:[11,12]
            }
        ],
        scrollX: true,
        paging: false,
        ordering: false,
        info: false,
        language: {
            "emptyTable": "No data found!",
            "processing": "Loading",
        },
        processing: true,
        serverSide: false,
        
        ajax: {
            url: '/requestDetails',
            data: {
                reqnum: req_num,
            },
            dataType: 'json',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/stockrequest';
                }
                alert(data.responseText);
            },
        },
        columns: [
            { data: 'category'},
            { data: 'item'},
            { data: 'uom'},
            { data: 'quantity'},
            // { data: 'served'},
            { data: 'pending'},
            { data: 'qtystock'},
            { data: 'item_id'},
            { data: 'qtya1'},
            { data: 'qtya2'},
            { data: 'qtya3'},
            { data: 'qtya4'},
            { data: 'qtybal'},
            { data: 'qtymal'},

        ],
        orderCellsTop: true,
        fixedHeader: true,            
    }); 
    
    $('table.stockDetails1').dataTable().fnDestroy();    
    $('table.stockDetails1').DataTable({ 
        columnDefs: [
            {
                "targets": [5],
                "visible": false,
                "searchable": false
            }
        ],
        paging: false,
        ordering: false,
        info: false,
        language: {
            "emptyTable": "No data found!",
            "processing": "Loading",
        },
        processing: true,
        serverSide: false,
        
        ajax: {
            url: '/requestDetails',
            data: {
                reqnum: req_num,
            },
            dataType: 'json',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/stockrequest';
                }
                alert(data.responseText);
            },
        },
        columns: [
            { data: 'category'},
            { data: 'item'},
            { data: 'uom'},
            { data: 'quantity'},
            // { data: 'served'},
            { data: 'pending'},
            { data: 'item_id'}
        ],
        orderCellsTop: true,
        fixedHeader: true,            
    });
    
    $('table.stockDetails2').dataTable().fnDestroy();    
    $('table.stockDetails2').DataTable({ 
        columnDefs: [
            {
                "targets": [6],
                "visible": false,
                "searchable": false
            },
            {   
                "render": function (data, type, row, meta) {
                        return '<button class="btn-primary bp btndelItem" id="'+ meta.row +'">REMOVE</button>';
                },
                "defaultContent": '',
                "data": null,
                "targets": [5]
            }
        ],
        paging: false,
        ordering: false,
        info: false,
        language: {
            "emptyTable": "No data found!",
            "processing": "Loading",
        },
        processing: true,
        serverSide: false,
        
        ajax: {
            url: '/requestDetails',
            data: {
                reqnum: req_num,
            },
            dataType: 'json',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/stockrequest';
                }
                alert(data.responseText);
            },
        },
        columns: [
            { data: 'category'},
            { data: 'item'},
            { data: 'uom'},
            { data: 'quantity'},
            // { data: 'served'},
            { data: 'pending'},
            { data: 'item_id'},
            { data: 'item_id'}
        ],
        orderCellsTop: true,
        fixedHeader: true,            
    });

    $('table.schedItems').DataTable({
        columnDefs: [
            {
                "targets": [6,7],
                "visible": false,
                "searchable": false
            },
            {   
                "render": function ( data, type, row, meta ) {
                        return '<button class="btn-primary bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                },
                "defaultContent": '',
                "data": null,
                "targets": [6]
            }
        ],
        paging: false,
        ordering: false,
        info: false,
        language: {
            processing: "Loading...",
            emptyTable: "No data found!"
        },
        order: [],
        ajax: {
            url: '/schedItems',
            data: {
                request_number: req_num,
            }
        },
        columns: [
            { data: 'category'},
            { data: 'item'},
            { data: 'qty'},
            { data: 'uom'},
            { data: 'serial'},
            { data: 'location'},
            { data: 'id'},
            { data: 'id'}
        ]
    });

    $('table.schedItems1').DataTable({
        paging: false,
        ordering: false,
        info: false,
        language: {
            processing: "Loading...",
            emptyTable: "No data found!"
        },
        order: [],
        ajax: {
            url: '/schedItems',
            data: {
                request_number: req_num,
            }
        },
        columns: [
            { data: 'category'},
            { data: 'item'},
            { data: 'qty'},
            { data: 'uom'},
            { data: 'serial'},
            { data: 'location'}
        ]
    });

    $('table.transItems').DataTable({
        columnDefs: [
            {
                "targets": [5],
                "visible": false,
                "searchable": false
            },
            {   
                "render": function ( data, type, row, meta ) {
                        return '<button class="btn-primary bp btnReceive" id="'+ meta.row +'">RECEIVE</button>';
                },
                "defaultContent": '',
                "data": null,
                "targets": [4]
            }
        ],
        paging: false,
        ordering: false,
        info: false,
        language: {
            processing: "Loading...",
            emptyTable: "No data found!"
        },
        order: [],
        ajax: {
            url: '/schedItems',
            data: {
                request_number: req_num,
            }
        },
        columns: [
            { data: 'category'},
            { data: 'item'},
            { data: 'qty'},
            { data: 'uom'},
            { data: 'serial'},
            { data: 'location'},
            { data: 'serial'},
            { data: 'item_id'}
        ]
    });

    $('table.transItems1').DataTable({
        paging: false,
        ordering: false,
        info: false,
        language: {
            processing: "Loading...",
            emptyTable: "No data found!"
        },
        order: [],
        ajax: {
            url: '/schedItems',
            data: {
                request_number: req_num,
            }
        },
        columns: [
            { data: 'category'},
            { data: 'item'},
            { data: 'qty'},
            { data: 'uom'},
            { data: 'serial'},
            { data: 'location'}
        ]
    });
});

$(document).on("click", ".btndelItem", function() {
    var id = $(this).attr("id");
    var data = $('table.stockDetails2').DataTable().row(id).data();
    $.ajax({
        url: '/delReqItem',
        headers: {
            'X-CSRF-TOKEN': $("#csrf").val(),
        },
        dataType: 'json',
        type: 'DELETE',
        data: {
            req_num: $('#request_num_details').val(),
            item_id: data.item_id
        },
        success: function(data) {
            if(data.result == 'false'){
                $('#stockRequestDetails').hide();
                sweetAlert("DELETE FAILED", "STOCK REQUEST", "error");
                setTimeout(function(){window.location.reload()}, 2000);
            }
            else{
                if(data.count == 0){
                    $('#stockRequestDetails').hide();
                    sweetAlert("DELETE SUCCESS", "STOCK REQUEST", "success");
                    setTimeout(function(){window.location.reload()}, 2000);
                }
                else{
                    $('table.stockDetails2').DataTable().ajax.reload();
                }
            }
        },
        error: function(data) {
            alert(data.responseText);
        }
    });
});

$(document).on("click", ".btnEditSerial", function() {
    var id = $(this).attr("id");
    var data = $('table.schedItems').DataTable().row(id).data();

    $('#editSerialModal').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('#editSerialModal').modal('show');
    $('#x_id').val(data.id);
    $('#x_category').val(data.category);
    $('#x_item').val(data.item);
    $('#x_serial').val(data.serial);
});

$(document).on("click", "#btnEdit", function() {
    $.ajax({
        url: '/editSerial',
        headers: {
            'X-CSRF-TOKEN': $("#csrf").val(),
        },
        dataType: 'json',
        type: 'PATCH',
        data: {
            id: $('#x_id').val(),
            serial: $('#x_serial').val()
        },
        success: function(data) {
            if(data == 'false'){
                $('#editSerialModal').hide();
                swal({
                    title: "EDIT FAILED",
                    text: "ITEM SERIAL",
                    icon: "error",
                    timer: 2000
                });
                $('table.schedItems').DataTable().ajax.reload();
            }
            else{
                $('#editSerialModal').hide();
                swal({
                    title: "EDIT SUCCESS",
                    text: "ITEM SERIAL",
                    icon: "success",
                    timer: 2000
                });
                $('table.schedItems').DataTable().ajax.reload();
            }
        },
        error: function(data) {
            alert(data.responseText);
        }
    });
});

$(document).on('click', '#btnDelete', function(){
    swal({
        title: "DELETE STOCK REQUEST?",
        text: "You are about to DELETE your STOCK REQUEST!\n This will be permanently deleted from the system.",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {       
            $.ajax({
                type:'get', 
                url:'/deleteRequest', 
                data:{
                    'request_number': $('#request_num_details').val()
                },
                success: function (data){
                    if(data == 'true'){
                        $('#stockRequestDetails').hide();
                        sweetAlert("DELETE SUCCESS", "STOCK REQUEST", "success");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                    else{
                        $('#stockRequestDetails').hide();
                        sweetAlert("DELETE FAILED", "STOCK REQUEST", "error");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                },
                error: function (data) {
                    if(data.status == 401) {
                        window.location.href = '/stockrequest';
                    }
                    alert(data.responseText);
                }
            });
        }
    });   
});

$(document).on('click', '#btnApprove', function(){
    swal({
        title: "APPROVE STOCK REQUEST?",
        text: "You are about to APPROVE this STOCK REQUEST!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if (willDelete) {      
            $.ajax({
                type:'get',
                url:'/approveRequest',
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                        },
                data:{
                    'request_number': $('#request_num_details').val()
                },
                success: function (data){
                    if(data == 'true'){
                        $('#stockRequestDetails').hide();
                        sweetAlert("APPROVE SUCCESS", "STOCK REQUEST", "success");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                    else{
                        $('#stockRequestDetails').hide();
                        sweetAlert("APPROVE FAILED", "STOCK REQUEST", "error");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                },
                error: function (data) {
                    if(data.status == 401) {
                        window.location.href = '/stockrequest';
                    }
                    alert(data.responseText);
                }
            });
        }
    }); 
});

$(document).on("click", "#btnDisapprove", function() {
    $('#reasonModal').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#reasonModal').modal('show');
});

$(document).on('click', '#btnReason', function(){
    if(!$('#reason').val()){
        sweetAlert("REASON REQUIRED!", "Please provide a reason for disapproving the request.", "error");
        return false;
    }
    else{
        swal({
            title: "DISAPPROVE STOCK REQUEST?",
            text: "You are about to DISAPPROVE this STOCK REQUEST!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type:'get',
                    url:'/disapproveRequest',
                    headers: {
                        'X-CSRF-TOKEN': $("#csrf").val(),
                            },
                    data:{
                        'request_number': $('#request_num_details').val(),
                        'reason': $('#reason').val()
                    },
                    success: function (data){
                        if(data == 'true'){
                            scrollReset();
                            $('#reasonModal').hide();
                            $('#reasonModal').modal('dispose');
                            $('#stockRequestDetails').hide();
                            $('#stockRequestDetails').modal('dispose');
                            $('#loading').show(); Spinner(); Spinner.show();
                            $.ajax({
                                type:'get',
                                url:'/logDisapprove',
                                headers: {
                                    'X-CSRF-TOKEN': $("#csrf").val(),
                                        },
                                data:{
                                    'request_number': $('#request_num_details').val(),
                                    'reason': $('#reason').val()
                                },
                                success: function (data){
                                    if(data == 'true'){
                                        $('#loading').hide(); Spinner.hide();
                                        sweetAlert("DISAPPROVE SUCCESS", "STOCK REQUEST", "success");
                                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                                    }
                                    else{
                                        return false;
                                    }
                                },
                                error: function (data) {
                                    if(data.status == 401) {
                                        window.location.href = '/stockrequest';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else{
                            $('#reasonModal').hide();
                            $('#stockRequestDetails').hide();
                            sweetAlert("DISAPPROVE FAILED", "STOCK REQUEST", "error");
                            setTimeout(function(){location.href="/stockrequest"}, 2000);
                        }
                    },
                    error: function (data) {
                        if(data.status == 401) {
                            window.location.href = '/stockrequest';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
});

$(document).on('click', '#btnTransit', function(){
    swal({
        title: "FOR RECEIVING?",
        text: "You are about to move these items FOR RECEIVING!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                type:'get',
                url:'/inTransit',
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                        },
                data:{
                    'request_number': $('#request_num_details').val()
                },
                success: function (data){
                    if(data == 'true'){
                        $('#stockRequestDetails').hide();
                        sweetAlert("FOR RECEIVING SUCCESS", "STOCK REQUEST", "success");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                    else{
                        $('#stockRequestDetails').hide();
                        sweetAlert("FOR RECEIVING FAILED", "STOCK REQUEST", "error");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                },
                error: function (data) {
                    if(data.status == 401) {
                        window.location.href = '/stockrequest';
                    }
                    alert(data.responseText);
                }
            });
        }
    });    
});

$(document).on('click', '#btnReceive', function(){
    swal({
        title: "RECEIVE STOCK REQUEST?",
        text: "You are about to RECEIVE this Stock Request!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                type:'get',
                url:'/receiveRequest',
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                        },
                data:{
                    'request_number': $('#request_num_details').val()
                },
                success: function (data){
                    if(data == 'true'){
                        scrollReset();
                        $('#stockRequestDetails').hide();
                        $('#stockRequestDetails').modal('dispose');
                        $('#loading').show(); Spinner(); Spinner.show();
                        $.ajax({
                            type:'get',
                            url:'/logReceive',
                            headers: {
                                'X-CSRF-TOKEN': $("#csrf").val(),
                                    },
                            data:{
                                'request_number': $('#request_num_details').val()
                            },
                            success: function (data){
                                if(data == 'true'){
                                    $('#loading').hide(); Spinner.hide();
                                    sweetAlert("RECEIVE SUCCESS", "STOCK REQUEST", "success");
                                    setTimeout(function(){location.href="/stockrequest"}, 2000);
                                }
                                else{
                                    return false;
                                }
                            },
                            error: function (data) {
                                if(data.status == 401) {
                                    window.location.href = '/stockrequest';
                                }
                                alert(data.responseText);
                            }
                        });
                    }
                    else{
                        $('#stockRequestDetails').hide();
                        sweetAlert("RECEIVE FAILED", "STOCK REQUEST", "error");
                        setTimeout(function(){location.href="/stockrequest"}, 2000);
                    }
                },
                error: function (data) {
                    if(data.status == 401) {
                        window.location.href = '/stockrequest';
                    }
                    alert(data.responseText);
                }
            });
        }
    });    
});

$(document).on('click', '.btnPrint', function(){
    window.location.href = '/printRequest?request_number='+$('#request_num_details').val();
});

$(document).on('click', '#btnPrint', function(){
    var printContents=document.getElementById('printPage').innerHTML;
    var originalContents=document.body.innerHTML;
    document.body.innerHTML=printContents;
    window.print();
    document.body.innerHTML=originalContents;
});

$(document).on('click', '#btnSavePDF', function(){
    swal({
        title: "SAVE AS PDF?",
        text: "You are about to SAVE this Stock Request as PDF!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            var content = document.getElementById('printPage');
            var options = {
                margin:       0.5,
                filename:     $('#req_num').val()+'.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf(content, options);
        }
    });  
});