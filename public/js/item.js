var LocOption = $('#locationto option').sort().clone();

$(document).on('change', '#categories', function(){
    var id=$('#categories').val();
    $('#quantityst').val('');
    $('#strans').val('');
    $('#locationto').empty().append('<option value="" selected disabled>Select Location</option>');
    $('#locationfrom').empty().append('<option value="" selected disabled>Select Location</option>');
    var descOp = " ";
        $.ajax({
            type: 'get',
            url: 'items',
            data:{
                'category_id': id
            },
            success: function(data){
                var itemcode = $.map(data, function(value, index) {
                    return [value];
                });
                descOp+='<option value="" selected disabled>Select Item</option>'; 
                itemcode.forEach(value => {
                    descOp+='<option value="'+value.item_id+'">'+value.item.toUpperCase()+'</option>';
                });
                
                $("#items").find('option').remove().end().append(descOp);                
            },
            error: function(data){
                if(data.status == 401) {
                    window.location.href = '/stocks';
                }
            alert(data.responseText);
            }
        });
});

$(document).on('change', '#items', function(){
    var id=$('#items').val();
    $('#strans').val('');
    $('#quantityst').val('');
    $('#locationto').empty().append('<option value="" selected disabled>Select Location</option>');
    $('#locationfrom').empty().append('<option value="" selected disabled>Select Location</option>');
    var loc = " ";
        $.ajax({
            type:'get',
            url:'locations',
            data:{
                'item_id': id
            },
            dataType: 'json',           
            success: function(data){                    
                var locationcode = $.map(data, function(value, index) {
                    return [value];
                });
                loc+='<option value="" selected disabled>Select location</option>';
                locationcode.forEach(value => {
                    loc+='<option value="'+value.location_id+'">'+value.location.toUpperCase()+'</option>';
                });
                
                console.log(loc);
                $("#locationfrom").find('option').remove().end().append(loc);                 
            },
            error: function(data){
                if(data.status == 401) {
                    window.location.href = '/stocks';
                }
            alert(data.responseText);
            }
        });
});

$(document).on('change', '#locationfrom', function(){
    var id=$('#locationfrom').val();
    $('#quantityst').val('');
    var category=$('#categories').val();
    $('#locationto').find('option').remove().end().append(LocOption);
    $('#locationto option[value="'+$(this).val()+'"]').remove();
    $('#locationto').val('');
    var item=$('#items').val();
        $.ajax({
            type:'get',
            url:'stocksAvailable',
            data:{'location_id':id,'category_id':category,'item_id':item},
            dataType: 'json',           
            success:function(data)
                {                         
                    $("#strans").val(data);                 
                },
            error: function(data){
                if(data.status == 401) {
                    window.location.href = '/stocks';
                }
                alert(data.responseText);
            }
        });
});

$('#quantityst').on('change', function(e){    
    var qty = $('#quantityst').val();
    var sqty = $('#strans').val();
    if(parseInt(qty) > parseInt(sqty)){         
        $("#quantityst").val(sqty);
        return false; 
    }
});

$('#buttrans').on('click', function(){
    var stocktransForm = $('#stocktransForm');
    var category = $('#categories').val();
    var item = $('#items').val();
    var locationfrom = $('#locationfrom').val();
    var locationto= $('#locationto').val();
    var qty = $('#quantityst').val();
    var sqty = $('#strans').val();
    if(parseInt(qty) > parseInt(sqty)){
        alert('Quantity must be less than to available stocks!');
        return false;
    }  
    if(category!="Select Category" && item!="Select Item" && locationfrom!="Select location" && locationto!="Select Location"&& qty!=""){
        if(locationto == "Select Location"){
            alert('Please Select location.');
            return false;
        }  
            $.ajax({
                url: "stocks/update",
                type: "POST",
                headers: {
                'X-CSRF-TOKEN': $("#csrf").val(),
                    },
                data: {
                    _token: $("#csrf").val(),
                    category: category,
                    item: item,
                    locationfrom: locationfrom,
                    locationto: locationto,
                    qty: qty
                },
                dataType: 'json',           
                success: function(dataResult){                         
                    $('#stocktrans').hide();
                    sweetAlert("TRANSFERED", "ITEM SUCCESFULLY TRANSFER", "success");
                    setTimeout(function(){window.location.href="/stocks"} , 2000);  
                },
                error: function(data){
                    if(data.status == 401) {
                        window.location.href = '/';
                    }
                    alert(data.responseText);
                }
            });
    }
    else{
        stocktransForm[0].reportValidity();
    }
});