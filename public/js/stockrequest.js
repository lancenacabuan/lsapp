function generatedr() {
    var today = new Date();
    var month = today.getMonth()+1;
    if(month <= 9){
        month = '0'+month;
    }
    var date = today.getFullYear()+'-'+month+today.getDate()+'-';
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
$(document).ready(function(){
    $(".newstockreq").click(function(){
        generatedr();
    });

    var items = [];
    $('#stockDetailsrequest tbody').on( 'click', 'tr', function () {
        var req = $('#stockDetailsrequest').DataTable().cell(this,2).data();
        var serv = $('#stockDetailsrequest').DataTable().cell(this,3).data();
        var stock = $('#stockDetailsrequest').DataTable().cell(this,5).data();
        var item_id = $('#stockDetailsrequest').DataTable().cell(this,6).data();

        if(stock == 0){
            swal('Item out of stock!','','error');
        }
        else if(req == serv){
            swal('Item is fullfiled!','','success');
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
    } );

    $("#btnProceed").unbind('click').click(function(){
        var reqnum = $('#request_num_details').val();
        var j=0;
        $("#stockDetailsrequest *").prop('disabled',true);
        $("#btnProceed").hide();
        $("#btnDelete").hide();
        $("#requestItems").slideDown();
        for(var i=0; i < items.length; i++){
            $.ajax({ 
                type:'get', 
                url:'/stockreq', 
                data:{
                    'reqnum': reqnum,
                    'item_id': items[i]
                }, 
                success:function(data) {
                    var reqitem = $.map(data, function(value) { 
                        return [value];
                    });

                    reqitem.forEach(value => {
                        if(value.qtystock <= value.pending){
                            var l = value.qtystock;
                        }
                        else{
                            var l = value.pending;
                        }
                        for(var k=0; k < l; k++)
                        {
                            var id = document.createElement("INPUT");
                            id.setAttribute("id", "item_id"+j);
                            id.setAttribute("type", "hidden");
                            id.setAttribute("value", value.item_id);
                            var x = document.createElement("INPUT");
                            x.setAttribute("id", "category"+j);
                            x.setAttribute("type", "text");
                            x.setAttribute("class", "form-control");
                            x.setAttribute("style", "width: 250px; font-size: 12px; margin-bottom: 10px;");
                            x.setAttribute("value", value.category);
                            var y = document.createElement("INPUT");
                            y.setAttribute("id", "item"+j);
                            y.setAttribute("type", "text");
                            y.setAttribute("class", "form-control");
                            y.setAttribute("style", "width: 550px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
                            y.setAttribute("value", value.item);
                            var z = document.createElement("INPUT");
                            z.setAttribute("id", "serial"+j);
                            z.setAttribute("type", "text");
                            z.setAttribute("class", "form-control serials");
                            z.setAttribute("style", "width: 200px; font-size: 12px; margin-left: 10px; margin-bottom: 10px;");
                            z.setAttribute("placeholder", "Input Serial Number");
                            document.getElementById("reqContents").appendChild(id);
                            document.getElementById("reqContents").appendChild(x);
                            document.getElementById("reqContents").appendChild(y);
                            document.getElementById("reqContents").appendChild(z);
                            $("#category"+j).prop('readonly', true);
                            $("#item"+j).prop('readonly', true);
                            j++;
                        }
                    });
                    for(var m=0; m < j; m++){
                        $(document).on('input', "#serial"+m, function(){
                            if($('.serials').filter(function() { return !!this.value; }).length > 0){
                                $('#btnSubmit').prop('disabled', false);
                            }
                            else{
                                $('#btnSubmit').prop('disabled', true);
                            }
                        });
                    }
                    $("#btnSubmit").unbind('click').click(function(){
                        if(!$("#schedOn").val()){
                            swal('Scheduled On is required!','','error');
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
                                                    'serial': $('#serial'+n).val(),
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
                                            setTimeout(function(){location.href="/stockrequest"} , 2000);
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
    
    $("#btnBack").click(function(){
        $("#stockDetailsrequest *").prop('disabled',false);
        $('#btnSubmit').prop('disabled', true);
        $("#requestItems").hide();
        $("#btnProceed").show();
        $("#btnDelete").show();
        $("#reqContents").empty();      
    });
    
    $('table.stock_request').dataTable().fnDestroy();
    $('table.stock_request').DataTable({ 
        columnDefs: [
            {
                "targets": [0],
                "render": $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'dddd, MMMM D, YYYY, h:mm A')
            },
            {
                "targets": [6],
                "visible": false
            },
            {
                "targets": [7],
                "visible": false
            },
            {
                "targets": [8],
                "visible": false
            },
            {
                "targets": [9],
                "visible": false
            },
            {
                "targets": [10],
                "visible": false
            },
            {
                "targets": [11],
                "visible": false
            },
            {
                "targets": [12],
                "visible": false
            },
            {
                "targets": [13],
                "visible": false
            },
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
            { data: 'date'},
            { data: 'req_num'},
            { data: 'reference'},
            { data: 'req_by'},
            { data: 'req_type'},
            { data: 'status'},
            { data: 'req_type_id'},
            { data: 'status_id'},
            { data: 'prep_by'},
            { data: 'sched'},
            { data: 'user_id'},
            { data: 'client_name'},
            { data: 'location'},
            { data: 'reason'}
        ]
    });
            
    $(".add-row").click(function(){                   
        var category = $("#categoryReq option:selected").text();
        var item = $("#itemReq option:selected").text();
        let qty = $("#qtyReq").val();
        var markup = "<tr><td>" + category + "</td><td>" + item + "</td><td>" + qty + "</td><td> <button type='button' class='delete-row btn-primary btn-xs bp'>REMOVE</button> </td></tr>";
        var ctr='false';
        if(category == "Select Category" || item == "Select Item" || qty == ""){
            swal('','Please select item!','error');
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
            $('#stockRequestTable').show();
            $('#stockRequestDiv').toggle();
            $('#requestClose').show();
            $('#requestSave').show();
        } 
    });

    // $(".add-row").click(function(){                   
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

    $("#stockRequestTable").on('click','.delete-row',function(){
        $(this).closest("tr").remove();
        if ($('#stockRequestTable tbody').children().length==0) {
            $('#stockRequestTable').hide();
            $('#stockRequestDiv').removeClass();   
            $('#requestClose').hide();  
            $('#requestSave').hide();    
        }
    });

    
    $("#success-alert").fadeTo(3000, 500).slideUp(500, function(){
        $("#success-alert").slideUp(500);    
    });    
               
});  
$(document).on('click','#close', function(){
    if(confirm("IF YOU CANCEL THE FORM, YOU WILL NOT BE ABLE TO SAVE ALL THE ENTRIES.\n\nDO YOU WANT TO PROCEED?")) {
        window.location.href = '/stockrequest';
    }
    else {
        return false;
    }               
});

$(document).on('click','#requestClose', function(){
    if (confirm("IF YOU CANCEL THE FORM, YOU WILL NOT BE ABLE TO SAVE ALL THE ENTRIES.\n\nDO YOU WANT TO PROCEED?")) {
        window.location.href = '/stockrequest';
    }
    else {
        return false;
    }    
});

$(document).on('click','#modalClose', function(){
    window.location.href = '/stockrequest'; 
});

$(document).on('click','#requestSave', function(){
    if($('#request_type').val() && $('#client_name').val() && $('#location').val())
    {
        swal({
            title: "SAVE STOCK REQUEST?",
            text: "You are about to SAVE this STOCK REQUEST!",
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
                                        'request_number':$('#request_num').val(),
                                        'category':value[0],
                                        'item':value[1],
                                        'quantity':value[2]
                                    },
                                    success: function (data){
                                        if(data == 'true'){
                                            $('#newStockRequest').hide();
                                            sweetAlert("SAVE SUCCESS", "STOCK REQUEST", "success");
                                            setTimeout(function(){location.href="/stockrequest"} , 2000);
                                        }
                                        else{
                                            $('#newStockRequest').hide();
                                            sweetAlert("SAVE FAILED", "STOCK REQUEST", "error");
                                            setTimeout(function(){location.href="/stockrequest"} , 2000);
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
                        }
                        else{
                            $('#newStockRequest').hide();
                            sweetAlert("SAVE FAILED", "STOCK REQUEST", "error");
                            setTimeout(function(){location.href="/stockrequest"} , 2000);
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
    else{
        if(!$('#request_type').val() && !$('#client_name').val() && !$('#location').val()){
            swal('Fill up all required fields!','*Request Type\n*Client Name\n*Address / Branch','error');
            return false;
        }
        if(!$('#request_type').val() && !$('#client_name').val()){
            swal('Fill up all required fields!','*Request Type\n*Client Name','error');
            return false;
        }
        if(!$('#client_name').val() && !$('#location').val()){
            swal('Fill up all required fields!','*Client Name\n*Address / Branch','error');
            return false;
        }
        if(!$('#request_type').val() && !$('#location').val()){
            swal('Fill up all required fields!','*Request Type\n*Address / Branch','error');
            return false;
        }
        if(!$('#request_type').val()){
            swal('Fill up all required fields!','*Request Type','error');
            return false;
        }
        if(!$('#client_name').val()){
            swal('Fill up all required fields!','*Client Name','error');
            return false;
        }
        if(!$('#location').val()){
            swal('Fill up all required fields!','*Address / Branch','error');
            return false;
        }
    }   
});

$(document).on('change', '#categoryReq', function(){ 
    var id=$('#categoryReq').val();
    var descOp = " ";
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

// $(document).on('change', '#itemReq', function(){ 
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

$('#stockreqDetails tbody').on('click', 'tr', function () {
        $('#stockRequestDetails').modal({
            backdrop: 'static',
            keyboard: false
        });
        var table =  $('table.stock_request').DataTable(); 
        var data = table.row( this ).data();
        var req_date = data.date;
            req_date = moment(req_date).format('dddd, MMMM D, YYYY, h:mm A');
            $('#daterequestdetails').val(req_date);
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

            if(data.user_id != $('#current_user').val()){
                $("#btnDelete").hide();
                $("#sd1").show();
                $("#sd2").hide();
            }
            else{
                $("#sd2").show();
                $("#sd1").hide();
            }
            if(data.status_id == '7'){
                $("#btnDisapprove").hide();
                $("#reason_label").show();
                $("#reason_details").show();
            }
            if(data.status_id == '2'|| data.status_id == '3' || data.status_id == '4' || data.status_id == '5' || data.status_id == '8'){
                $("#btnDelete").hide();
            }
            if(data.status_id == '6'){
                $("#btnProceed").hide();
            }
            if(data.status_id == '2' || data.status_id == '5'){
                $("#schedItemsModal").show();
            }
            if(data.status_id == '3' || data.status_id == '4'){
                $("#transitItemsModal").show();
                $("#btnProceed").hide();
            }
            if(data.status_id == '8'){
                $("#transitItemsModal").show();
                $("#btnProceed").hide();
                $("#btnReceive").hide();
                document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
            }
            if($("#current_role").val() == '["sales"]'){
                $("#sd2").show();
                $("#sd1").hide();
            }
            if(data.status_id == '2'|| data.status_id == '3' || data.status_id == '4' || data.status_id == '5' || data.status_id == '8'){
                $("#sd1").show();
                $("#sd2").hide();
            }
            else{
                $("#sd2").show();
                $("#sd1").hide();
            }
            if($("#current_role").val() != '["sales"]'){
                $("#sd1").show();
                $("#sd2").hide();
            }
        
    $('table.stockDetails').dataTable().fnDestroy();    
    $('table.stockDetails').DataTable({ 
        columnDefs: [
            {
                "targets": [6],
                "visible": false
            }
        ],
        paging: false,
        ordering: false,
        info: false,
        language: {
            "emptyTable": " ",
            "processing":"Searching",
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
            { data: 'quantity'},
            { data: 'served'},
            { data: 'pending'},
            { data: 'qtystock'},
            { data: 'item_id'}
        ],
        orderCellsTop: true,
        fixedHeader: true,            
    }); 
    
    $('table.stockDetails1').dataTable().fnDestroy();    
    $('table.stockDetails1').DataTable({ 
        columnDefs: [
            {
                "targets": [5],
                "visible": false
            }
        ],
        paging: false,
        ordering: false,
        info: false,
        language: {
            "emptyTable": " ",
            "processing":"Searching",
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
            { data: 'quantity'},
            { data: 'served'},
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
                "visible": false
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
            "emptyTable": " ",
            "processing":"Searching",
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
            { data: 'quantity'},
            { data: 'served'},
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
                "targets": [5],
                "visible": false
            },
            {   
                "render": function ( data, type, row, meta ) {
                        return '<button class="btn-primary bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
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
            { data: 'serial'},
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
            { data: 'serial'}
        ]
    });

    $('table.transItems').DataTable({
        columnDefs: [
            {
                "targets": [5],
                "visible": false
            },
            {   
                "render": function ( data, type, row, meta ) {
                        return '<button class="btn-primary bp btnReceive">RECEIVE</button>';
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
            { data: 'serial'},
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
            { data: 'serial'}
        ]
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
                req_num: req_num,
                item_id: data.item_id
            },
            success: function(data) {
                if(data.result == 'false'){
                    $('#stockRequestDetails').hide();
                    sweetAlert("DELETE FAILED", "STOCK REQUEST", "error");
                    setTimeout(function(){window.location.reload()} , 2000);
                }
                else{
                    if(data.count == 0){
                        $('#stockRequestDetails').hide();
                        sweetAlert("DELETE SUCCESS", "STOCK REQUEST", "success");
                        setTimeout(function(){window.location.reload()} , 2000);
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
                    $('#editSerialModal').modal('hide');
                    swal({
                        title: "EDIT FAILED",
                        text: "ITEM SERIAL",
                        icon: "error",
                        timer: 2000
                    });
                    $('table.schedItems').DataTable().ajax.reload();
                }
                else{
                    $('#editSerialModal').modal('hide');
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
});

$(document).on('click','#btnDelete', function(){
    swal({
        title: "DELETE STOCK REQUEST?",
        text: "You are about to DELETE your STOCK REQUEST!\n This will be permanently deleted from the system.",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            var req_num = $('#request_num_details').val();      
            $.ajax({
                type:'get', 
                url:'/deleteRequest', 
                data:{
                    'request_number': req_num
                },
                success: function (data){
                    if(data == 'true'){
                        $('#stockRequestDetails').hide();
                        sweetAlert("DELETE SUCCESS", "STOCK REQUEST", "success");
                        setTimeout(function(){location.href="/stockrequest"} , 2000);
                    }
                    else{
                        $('#stockRequestDetails').hide();
                        sweetAlert("DELETE FAILED", "STOCK REQUEST", "error");
                        setTimeout(function(){location.href="/stockrequest"} , 2000);
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

$(document).on('click','#btnApprove', function(){
    swal({
        title: "APPROVE STOCK REQUEST?",
        text: "You are about to APPROVE this STOCK REQUEST!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            var req_num = $('#request_num_details').val();      
            $.ajax({
                type:'get',
                url:'/approveRequest',
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                        },
                data:{
                    'request_number': req_num
                },
                success: function (data){
                    if(data == 'true'){
                        $('#stockRequestDetails').hide();
                        sweetAlert("APPROVE SUCCESS", "STOCK REQUEST", "success");
                        setTimeout(function(){location.href="/stockrequest"} , 2000);
                    }
                    else{
                        $('#stockRequestDetails').hide();
                        sweetAlert("APPROVE FAILED", "STOCK REQUEST", "error");
                        setTimeout(function(){location.href="/stockrequest"} , 2000);
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

$(document).on('click','#btnReason', function(){
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
                        $('#stockRequestDetails').hide();
                        sweetAlert("DISAPPROVE SUCCESS", "STOCK REQUEST", "success");
                        setTimeout(function(){location.href="/stockrequest"} , 2000);
                    }
                    else{
                        $('#stockRequestDetails').hide();
                        sweetAlert("DISAPPROVE FAILED", "STOCK REQUEST", "error");
                        setTimeout(function(){location.href="/stockrequest"} , 2000);
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

$(document).on('click','#btnTransit', function(){
    swal({
        title: "FOR RECEIVING?",
        text: "You are about to move these items FOR RECEIVING!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            var req_num = $('#request_num_details').val();
            $.ajax({
                type:'get',
                url:'/inTransit',
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                        },
                data:{
                    'request_number': req_num
                },
                success: function (data){
                    if(data == 'true'){
                        $('#stockRequestDetails').hide();
                        sweetAlert("FOR RECEIVING SUCCESS", "STOCK REQUEST", "success");
                        setTimeout(function(){location.href="/stockrequest"} , 2000);
                    }
                    else{
                        $('#stockRequestDetails').hide();
                        sweetAlert("FOR RECEIVING FAILED", "STOCK REQUEST", "error");
                        setTimeout(function(){location.href="/stockrequest"} , 2000);
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

$(document).on('click','#btnReceive', function(){
    swal({
        title: "RECEIVE STOCK REQUEST?",
        text: "You are about to RECEIVE this Stock Request!",
        icon: "warning",
        buttons: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            var req_num = $('#request_num_details').val();
            $.ajax({
                type:'get',
                url:'/receiveRequest',
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                        },
                data:{
                    'request_number': req_num
                },
                success: function (data){
                    if(data == 'true'){
                        $('#stockRequestDetails').hide();
                        sweetAlert("RECEIVE SUCCESS", "STOCK REQUEST", "success");
                        setTimeout(function(){location.href="/stockrequest"} , 2000);
                    }
                    else{
                        $('#stockRequestDetails').hide();
                        sweetAlert("RECEIVE FAILED", "STOCK REQUEST", "error");
                        setTimeout(function(){location.href="/stockrequest"} , 2000);
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

$(document).on('click','.btnPrint', function(){
    window.location.href = '/printRequest?request_number='+$('#request_num_details').val();
});

$(document).on('click','#btnPrint', function(){
    var printContents=document.getElementById('printPage').innerHTML;
    var originalContents=document.body.innerHTML;
    document.body.innerHTML=printContents;
    window.print();
    document.body.innerHTML=originalContents;
});

$(document).on('click','#btnSavePDF', function(){
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

$('table.stockDetails').DataTable().on('select', function () {
    var rowselected = stockdetails.rows( { selected: true } ).data();
    var rowcount = stockdetails.rows( { selected: true } ).count();
    if(rowselected.length > 0){
        for(var i=0;i<rowcount;i++){
            if (rowselected[i].stock == 0) {
                $('#btnProceed').prop('disabled', true);
                requestdetails.rows( { selected: true } ).deselect();
                return false;
            }else{
                $('#btnProceed').prop('disabled', false);
            }
        }  
    }
});

$(function(){
    var dtToday = new Date();
    
    var month = dtToday.getMonth() + 1;
    var day = dtToday.getDate();
    var year = dtToday.getFullYear();
    if(month < 10)
        month = '0' + month.toString();
    if(day < 10)
        day = '0' + day.toString();    
    var maxDate = year + '-' + month + '-' + day;

    $('#schedOn').attr('min', maxDate);
});

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