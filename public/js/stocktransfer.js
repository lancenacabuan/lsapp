function generateReqNum() {
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
        url:'/generateReqNum',
        async: false,
        data:{
            'request_number': request_number
        },
        success: function (data) {
            if (data == 'unique') {
                document.getElementById("reqnum").value = request_number;
            }
            else{
                generateReqNum();
            }
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/stocktransfer';
            }
                alert(data.responseText);
        }
    });
}

$(".btnNewStockTransfer").click(function(){
    generateReqNum();
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

    $('#needdate').attr('min', maxDate);
});

$('#locfrom').on('change', function(){
    $("#item").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
    $('#qty').val('');
    $('#qtystock').val('');
    var location_id = $(this).val();
    $.ajax({
        type:'get', 
        url:'/setcategory', 
        data:{
            'location_id': location_id
        }, 
        success:function(data) {
            $('#category').find('option').remove().end()
            $('#category').append($('<option value="" selected disabled>Select Category</option>'));
            var list = $.map(data, function(value) { 
                return [value];
            });

            list.forEach(value => {             
                $('#category').append($('<option>', {
                    value: value.category_id,
                    text: value.category
                }));
            });
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/stocktransfer';
            }
            alert(data.responseText);
        }
    });
});

$('#category').on('change', function(){
    $('#qty').prop('disabled', true);
    $("#qty").val('');
    $("#qtystock").val('');
    var category_id = $(this).val();
    $.ajax({
        type:'get', 
        url:'/setitems', 
        data:{
            'category_id': category_id,
            'location_id': $('#locfrom').val()
        }, 
        success:function(data) {
            $('#item').find('option').remove().end()
            $('#item').append($('<option value="" selected disabled>Select Item</option>'));
            var list = $.map(data, function(value) { 
                return [value];
            });

            list.forEach(value => {             
                $('#item').append($('<option>', {
                    value: value.item_id,
                    text: value.item
                }));
            });
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/stocktransfer';
            }
            alert(data.responseText);
        }
    });
});

$('#item').on('change', function(){
    $('#qty').prop('disabled', false);
    $("#qty").val('1');
    var item_id = $(this).val();
    $.ajax({
        type:'get', 
        url:'/qtystock', 
        data:{
            'item_id': item_id,
            'location_id': $('#locfrom').val()
        }, 
        success:function(data) {
            var table = document.getElementById('tblNewStockTransfer');
            var qtyminus = 0;
            if(table.rows.length > 1){
                for (var r = 1, n = table.rows.length; r < n; r++) {
                    for (var c = 0, m = table.rows[r].cells.length; c < m; c++) {
                        if(table.rows[r].cells[1].innerHTML == $("#item option:selected").text()){
                            qtyminus = table.rows[r].cells[2].innerHTML;
                        }
                    }
                }
            }
            $('#qtystock').val(data - qtyminus);
            $('#qty').attr({
                "max" : data - qtyminus,
                "min" : 0
            });
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/stocktransfer';
            }
            alert(data.responseText);
        }
    });
});

$(".add-row").click(function(){
    var category = $("#category option:selected").text();
    var item = $("#item option:selected").text();
    var qty = $("#qty").val();
    var qtystock = $("#qtystock").val();
    var markup = "<tr><td>" + category + "</td><td>" + item + "</td><td>" + qty + "</td><td> <button type='button' class='delete-row btn-primary btn-xs bp'>REMOVE</button> </td></tr>";
    var ctr='false';
    if(category == "Select Category" || item == "Select Item" || qty == "" || qty == "0"){
        swal('REQUIRED','Please select an item!','error');
        return false;
    }
    else{
        if(qty > qtystock){
            swal('EXCEED LIMIT','Item quantity exceeds available stock!','error');
            return false;
        }
        else{
            $('#locfrom').prop('disabled', true);
            var table = document.getElementById('tblNewStockTransfer');
            var count = table.rows.length;
            for (i = 1; i < count; i++) {

                var objCells = table.rows.item(i).cells;

                if(item==objCells.item(1).innerHTML){
                    objCells.item(2).innerHTML = parseInt(objCells.item(2).innerHTML) + parseInt(qty);
                    ctr='true';
                    category = $("#category").val('');
                    item = $("#item").find('option').remove().end().append('<option value="">Select Item</option>').val()
                    qty = $("#qty").val('');
                    qtystock = $("#qtystock").val('');
                    $('#qty').prop('disabled', true);
                    return false;
                }
                else {
                    ctr='false';
                }
            }
            if(ctr=='false')
            { $("#tblNewStockTransfer tbody").append(markup); }
            category = $("#category").val('');
            item = $("#item").find('option').remove().end().append('<option value="">Select Item</option>').val()
            qty = $("#qty").val('');
            qtystock = $("#qtystock").val('');
            $('#qty').prop('disabled', true);
            $('#tblNewStockTransfer').show();
            $('#divNewStockTransfer').toggle();
            $('#btnClose').show();
            $('#btnSave').show();
        }
    } 
});

$("#tblNewStockTransfer").on('click','.delete-row',function(){
    category = $("#category").val('');
    item = $("#item").find('option').remove().end().append('<option value="">Select Item</option>').val()
    qty = $("#qty").val('');
    qtystock = $("#qtystock").val('');
    $('#qty').prop('disabled', true);
    $(this).closest("tr").remove();
    if ($('#tblNewStockTransfer tbody').children().length==0) {
        $('#tblNewStockTransfer').hide();
        $('#divNewStockTransfer').removeClass();   
        $('#btnClose').hide();  
        $('#btnSave').hide();
        $('#locfrom').prop('disabled', false);
    }
});

$(document).on('click','#btnSave', function(){
    if($('#needdate').val() && $('#locfrom').val() && $('#locto').val())
    {
        swal({
            title: "SAVE STOCK TRANSFER REQUEST?",
            text: "You are about to SAVE this STOCK TRANSFER REQUEST!",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type:'post',
                    url:'/saveTransReqNum',
                    headers: {
                        'X-CSRF-TOKEN': $("#csrf").val(),
                    },
                    data:{
                        'request_number': $('#reqnum').val(),
                        'needdate': $('#needdate').val(),
                        'locfrom': $('#locfrom').val(),
                        'locto': $('#locto').val(),
                    },
                    success: function (data){
                        if(data == 'true'){
                            var myTable = $('#tblNewStockTransfer').DataTable();
                            var form_data  = myTable.rows().data();
                            $.each( form_data, function( key, value ) {
                                $.ajax({
                                    type:'post',
                                    url:'/saveTransRequest',
                                    headers: {
                                        'X-CSRF-TOKEN': $("#csrf").val(),
                                    },
                                    data:{
                                        'request_number': $('#reqnum').val(),
                                        'category': value[0],
                                        'item': value[1],
                                        'quantity': value[2]
                                    },
                                    success: function (data){
                                        if(data == 'true'){
                                            $('#newStockTransfer').hide();
                                            sweetAlert("SAVE SUCCESS", "STOCK TRANSFER REQUEST", "success");
                                            setTimeout(function(){location.href="/stocktransfer"} , 2000);
                                        }
                                        else{
                                            $('#newStockTransfer').hide();
                                            sweetAlert("SAVE FAILED", "STOCK TRANSFER REQUEST", "error");
                                            setTimeout(function(){location.href="/stocktransfer"} , 2000);
                                        }
                                    },
                                    error: function (data) {
                                        if(data.status == 401) {
                                            window.location.href = '/stocktransfer';
                                        }
                                        alert(data.responseText);
                                    }
                                });
                            });
                            $.ajax({
                                type:'post',
                                url:'/logTransSave',
                                headers: {
                                    'X-CSRF-TOKEN': $("#csrf").val()
                                },
                                data:{
                                    'request_number': $('#reqnum').val(),
                                },
                                success: function (){
                                    $('#newStockTransfer').hide();
                                    setTimeout(function(){location.href="/stocktransfer"} , 2000);
                                },
                                error: function (data) {
                                    if(data.status == 401) {
                                        window.location.href = '/stocktransfer';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else{
                            $('#newStockTransfer').hide();
                            sweetAlert("SAVE FAILED", "STOCK TRANSFER REQUEST", "error");
                            setTimeout(function(){location.href="/stocktransfer"} , 2000);
                        }
                    },
                    error: function (data){
                        if(data.status == 401) {
                            window.location.href = '/stocktransfer';
                        }
                        alert(data.responseText);
                    }
                });
            }
        }); 
    }
    else{
        if(!$('#needdate').val() && !$('#locfrom').val() && !$('#locto').val()){
            swal('Fill up all required fields!','*Date Needed\n*FROM Location\n*TO New Location','error');
            return false;
        }
        if(!$('#needdate').val() && !$('#locfrom').val()){
            swal('Fill up all required fields!','*Date Needed\n*FROM Location','error');
            return false;
        }
        if(!$('#locfrom').val() && !$('#locto').val()){
            swal('Fill up all required fields!','*FROM Location\n*TO New Location','error');
            return false;
        }
        if(!$('#needdate').val() && !$('#locto').val()){
            swal('Fill up all required fields!','*Date Needed\n*TO New Location','error');
            return false;
        }
        if(!$('#needdate').val()){
            swal('Fill up all required fields!','*Date Needed','error');
            return false;
        }
        if(!$('#locfrom').val()){
            swal('Fill up all required fields!','*FROM Location','error');
            return false;
        }
        if(!$('#locto').val()){
            swal('Fill up all required fields!','*TO New Location','error');
            return false;
        }
    }   
});

$(document).on('click','#close', function(){
    if (confirm("IF YOU CANCEL THE FORM, YOU WILL NOT BE ABLE TO SAVE ALL THE ENTRIES.\n\nDO YOU WANT TO PROCEED?")) {
        window.location.href = '/stocktransfer';
    }
    else {
        return false;
    }    
});

$(document).on('click','#btnClose', function(){
    if (confirm("IF YOU CANCEL THE FORM, YOU WILL NOT BE ABLE TO SAVE ALL THE ENTRIES.\n\nDO YOU WANT TO PROCEED?")) {
        window.location.href = '/stocktransfer';
    }
    else {
        return false;
    }    
});

$(document).on('click','#modalClose', function(){
    window.location.href = '/stocktransfer'; 
});

$('table.stocktransferTable').dataTable().fnDestroy();
$('table.stocktransferTable').DataTable({ 
    columnDefs: [
        {
            "targets": [0],
            "render": $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'MMM. D, YYYY, h:mm A')
        },
        {
            "targets": [1],
            "render": $.fn.dataTable.render.moment('YYYY-MM-DD', 'MMM. D, YYYY')
        },
        {
            "targets": [6,7,8,9,10,11,12],
            "visible": false
        }
    ],
    language: {
        processing: "Loading...",
        emptyTable: "No data found!"
    },
    order: [],
    ajax: {
        url: '/transfer_data',
    },
    columns: [
        { data: 'date'},
        { data: 'needdate'},
        { data: 'req_num'},
        { data: 'req_by'},
        { data: 'location'},
        { data: 'status'},
        { data: 'status_id'},
        { data: 'locfrom'},
        { data: 'locto'},
        { data: 'prep_by'},
        { data: 'sched'},
        { data: 'user_id'},
        { data: 'reason'}
    ]
});

$('#stocktransferTable tbody').on('click', 'tr', function () {
    $('#detailsStockTransfer').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table =  $('table.stocktransferTable').DataTable(); 
    var data = table.row( this ).data();
    var req_date = data.date;
        req_date = moment(req_date).format('dddd, MMMM D, YYYY, h:mm A');
        $('#reqdate_details').val(req_date);
    var need_date = data.needdate;
        need_date = moment(need_date).format('dddd, MMMM D, YYYY');
        $('#needdate_details').val(need_date);
    var req_num = data.req_num;
        $('#reqnum_details').val(req_num);
    var req_by = data.req_by;
        $('#reqby_details').val(req_by);
    var status = data.status;
        $('#status_details').val(status);
    var prep_by = data.prep_by;
        $('#prep_by').val(prep_by);
        $('#prep_by1').val(prep_by);
    var sched = data.sched;
        sched = moment(sched).format('dddd, MMMM D, YYYY');
        $('#sched').val(sched);
        $('#sched1').val(sched);
    var locfrom = data.locfrom;
        $('#locfrom_details').val(locfrom);
    var locto = data.locto;
        $('#locto_details').val(locto);
    var reason = data.reason;
        $('#reason_details').val(reason);
    var btnDel = '';
    var hideCol = '';

    $('.modal-body').html();
    $('#detailsStockTransfer').modal('show');
    if(locfrom == 5){
        hideCol = 11;
    }
    if(locfrom == 6){
        hideCol = 10;
    }
    if(data.user_id != $('#current_user').val()){
        $("#btnDelete").hide();
        btnDel = 12;
    }
    else{
        $("#btnDelete").show();
    }
    if(data.status_id == '7'){
        $("#btnDisapprove").hide();
        $("#reason_label").show();
        $("#reason_details").show();
    }
    if(data.status_id == '1'|| data.status_id == '2'|| data.status_id == '3' || data.status_id == '4' || data.status_id == '5' || data.status_id == '8'){
        $("#btnDelete").hide();
        btnDel = 12;
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

    $('table.transferDetails').DataTable({ 
        columnDefs: [
            {
                "targets": [4,5,6,7,8,9,hideCol,btnDel],
                "visible": false
            },
            {   
                "render": function (data, type, row, meta) {
                        return '<button class="btn-primary bp btndelItem" id="'+ meta.row +'">REMOVE</button>';
                },
                "defaultContent": '',
                "data": null,
                "targets": [12]
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
            url: '/transferDetails',
            data: {
                reqnum: req_num,
            },
            dataType: 'json',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/stocktransfer';
                }
                alert(data.responseText);
            },
        },
        columns: [
            { data: 'category'},
            { data: 'item'},
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
            { data: 'item_id'}

        ],
        orderCellsTop: true,
        fixedHeader: true,            
    });
});