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