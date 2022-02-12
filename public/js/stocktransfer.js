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
    var item_id = $(this).val();
    $.ajax({
        type:'get', 
        url:'/qtystock', 
        data:{
            'item_id': item_id,
            'location_id': $('#locfrom').val()
        }, 
        success:function(data) {
            $('#qtystock').val(data);
            $('#qty').attr({
                "max" : data,
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
    $('#locfrom').prop('disabled', true);
    var category = $("#category option:selected").text();
    var item = $("#item option:selected").text();
    let qty = $("#qty").val();
    var markup = "<tr><td>" + category + "</td><td>" + item + "</td><td>" + qty + "</td><td> <button type='button' class='delete-row btn-primary btn-xs bp'>REMOVE</button> </td></tr>";
    var ctr='false';
    if(category == "Select Category" || item == "Select Item" || qty == "" || qty == "0"){
        swal('REQUIRED','Please select item!','error');
        return false;
    }
    else{
        var table = document.getElementById('tblNewStockTransfer');
        var count = table.rows.length;
        for (i = 1; i < count; i++) {

            var objCells = table.rows.item(i).cells;

            if(item==objCells.item(1).innerHTML){
                objCells.item(2).innerHTML = parseInt(objCells.item(2).innerHTML) + parseInt(qty);
                ctr='true';
                category = $("#category").val('Select Category');
                item = $("#item").find('option').remove().end().append('<option value="">Select Item</option>').val()
                qty = $("#qty").val('');
                qtystock = $("#qtystock").val('');
                return false;
            }
            else {
                ctr='false';
            }
        }
        if(ctr=='false')
        { $("#tblNewStockTransfer tbody").append(markup); }
        category = $("#category").val('Select Category');
        item = $("#item").find('option').remove().end().append('<option value="">Select Item</option>').val()
        qty = $("#qty").val('');
        qty = $("#qtystock").val('');
        $('#tblNewStockTransfer').show();
        $('#divNewStockTransfer').toggle();
        $('#btnClose').show();
        $('#btnSave').show();
    } 
});

$("#tblNewStockTransfer").on('click','.delete-row',function(){
    $(this).closest("tr").remove();
    if ($('#tblNewStockTransfer tbody').children().length==0) {
        $('#tblNewStockTransfer').hide();
        $('#divNewStockTransfer').removeClass();   
        $('#btnClose').hide();  
        $('#btnSave').hide();
        $('#locfrom').prop('disabled', false);
    }
});