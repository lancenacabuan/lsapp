var tblItem, tblAssembly, tblCategory, tblLocation, tblWarranty, wrdata;
if(
    $(location).attr('pathname')+window.location.search == '/maintenance' || 
    $(location).attr('pathname')+window.location.search == '/maintenance?import=success_without_errors' || 
    $(location).attr('pathname')+window.location.search == '/maintenance?import=success_with_errors' || 
    $(location).attr('pathname')+window.location.search == '/maintenance?import=failed'
    ){
    $('#nav1').addClass("active-link");
    $('.btnImport').show();
    $('.btnNewItem').show();
    $('#itemTable').show();
    tblItem = $('table.itemTable').DataTable({
        dom: 'Blftrip',
        buttons: ['excel'],
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        language:{
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax:{
            url: '/fm_items',
        },
        columns: [
            { data: 'category' },
            { data: 'prodcode' },
            { data: 'item' },
            { data: 'minimum' },
            { data: 'uom' },
            { data: 'serialize' }
        ],
        order: [],
        initComplete: function(){
            return notifyDeadline();
        }
    });

    var data_update;
    setInterval(function(){
        if($('#newItem').is(':hidden') && $('#detailsItem').is(':hidden') && $('#importItem').is(':hidden') && $('#reportModal').is(':hidden') && $('#changePassword').is(':hidden') && $('#loading').is(':hidden')){
            $.ajax({
                url: "/fm_items/reload",
                success: function(data){
                    if(data != data_update){
                        data_update = data;
                        tblItem.ajax.reload(null, false);
                    }
                }
            });
        }
    }, 3000);
}
else if($(location).attr('pathname')+window.location.search == '/maintenance?tbl=assembleditems'){
    $('#nav2').addClass("active-link");
    $('.btnCreateItem').show();
    $('#assemblyitemTable').show();
    tblAssembly = $('table.assemblyitemTable').DataTable({
        dom: 'Blftrip',
        buttons: ['excel'],
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        language:{
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax:{
            url: '/asm_items',
        },
        columns: [
            { data: 'prodcode' },
            { data: 'item' },
            { data: 'minimum' }
        ],
        order: [],
        initComplete: function(){
            return notifyDeadline();
        }
    });

    var data_update;
    setInterval(function(){
        if($('#createItem').is(':hidden') && $('#detailsAssemblyItem').is(':hidden') && $('#reportModal').is(':hidden') && $('#changePassword').is(':hidden') && $('#loading').is(':hidden')){
            $.ajax({
                url: "/asm_items/reload",
                success: function(data){
                    if(data != data_update){
                        data_update = data;
                        tblAssembly.ajax.reload(null, false);
                    }
                }
            });
        }
    }, 3000);
}
else if($(location).attr('pathname')+window.location.search == '/maintenance?tbl=categories'){
    $('#nav3').addClass("active-link");
    $('.btnNewCategory').show();
    $('#categoryTable').show();
    tblCategory = $('table.categoryTable').DataTable({
        dom: 'Blftrip',
        buttons: ['excel'],
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        language:{
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax:{
            url: '/fm_categories',
        },
        columns: [
            { data: 'category' }
        ],
        order: [],
        initComplete: function(){
            return notifyDeadline();
        }
    });

    var data_update;
    setInterval(function(){
        if($('#newCategory').is(':hidden') && $('#detailsCategory').is(':hidden') && $('#reportModal').is(':hidden') && $('#changePassword').is(':hidden') && $('#loading').is(':hidden')){
            $.ajax({
                url: "/fm_categories/reload",
                success: function(data){
                    if(data != data_update){
                        data_update = data;
                        tblCategory.ajax.reload(null, false);
                    }
                }
            });
        }
    }, 3000);
}
else if($(location).attr('pathname')+window.location.search == '/maintenance?tbl=locations'){
    $('#nav4').addClass("active-link");
    $('.btnNewLocation').show();
    $('#locationTable').show();
    tblLocation = $('table.locationTable').DataTable({
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        language:{
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax:{
            url: '/fm_locations',
        },
        columns: [
            { data: 'location' },
            {
                data: 'status',
                "render": function(data, type, row){
                    if(row.status == 'ACTIVE'){
                        return "<span class='d-none'>"+row.status+"</span><span style='color: Green; font-weight: bold;'>"+row.status+"</span>";
                    }
                    if(row.status == 'INACTIVE'){
                        return "<span class='d-none'>"+row.status+"</span><span style='color: Red; font-weight: bold;'>"+row.status+"</span>";
                    }
                    if(row.status == 'PENDING'){
                        return "<span class='d-none'>"+row.status+"</span><span style='color: Blue; font-weight: bold;'>"+row.status+"</span>";
                    }
                    if(row.status.includes('CHANGE REQUESTED')){
                        return "<span class='d-none'>"+row.status+"</span><span style='color: Indigo; font-weight: bold;'>"+row.status+"</span>";
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
        if($('#newLocation').is(':hidden') && $('#detailsLocation').is(':hidden') && $('#reportModal').is(':hidden') && $('#changePassword').is(':hidden') && $('#loading').is(':hidden')){
            $.ajax({
                url: "/fm_locations/reload",
                success: function(data){
                    if(data != data_update){
                        data_update = data;
                        tblLocation.ajax.reload(null, false);
                    }
                }
            });
        }
    }, 3000);
}
else if($(location).attr('pathname')+window.location.search == '/maintenance?tbl=warranty'){
    $('#nav5').addClass("active-link");
    $('.btnNewWarranty').show();
    $('#warrantyTable').show();
    tblWarranty = $('table.warrantyTable').DataTable({
        dom: 'rtp',
        language:{
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        processing: false,
        serverSide: false,
        ajax:{
            url: 'GetWarranty'
        },
        async: false,
        initComplete: function(){
            return notifyDeadline();
        },
        columns: [
            { data: 'Warranty_Name', name:'Warranty_Name'},
            { data: 'Duration', 
                render: function(data, type){
                    return data+' Months';
                }
            },
            { data: null,
                render: function(data, type, row) {
                    if (row.Inclusive) {
                        if (row.Inclusive.indexOf('Phone Support') > -1) {
                            return '<center><span class="checkbox_span" style="color:green">✓</span></center>';
                        }
                        else{
                            return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                        }
                    }else{
                        return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                    }
                },
                orderable: false,
                className: "dt-head-center"
            },
            { data: null,
                render: function(data, type, row) {
                    if (row.Inclusive) {
                        if (row.Inclusive.indexOf('Onsite Visit') > -1) {
                            return '<center><span class="checkbox_span" style="color:green">✓</span></center>';
                        }
                        else{
                            return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                        }
                    }else{
                        return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                    }
                },
                orderable: false,
                className: "dt-head-center"
            },
            { data: null,
                render: function(data, type, row) {
                    if (row.Inclusive) {
                        if (row.Inclusive.indexOf('Software') > -1) {
                            return '<center><span class="checkbox_span" style="color:green">✓</span></center>';
                        }
                        else{
                            return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                        }
                    }
                    else{
                        return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                    }
                },
                orderable: false,
                className: "dt-head-center"
            },
            { data: null,
                render: function(data, type, row) {
                    if (row.Inclusive) {
                        if (row.Inclusive.indexOf('Hardware') > -1) {
                            return '<center><span class="checkbox_span" style="color:green">✓</span></center>';
                        }
                        else{
                            return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                        }
                    }
                    else{
                        return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                    }
                },
                orderable: false,
                className: "dt-head-center"
            },
            { data: null,
                render: function(data, type, row) {
                    if (row.Inclusive) {
                        if (row.Inclusive.indexOf('Parts Replacement') > -1) {
                            return '<center><span class="checkbox_span" style="color:green">✓</span></center>';
                        }
                        else{
                            return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                        }
                    }
                    else{
                        return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                    }
                },
                orderable: false,
                className: "dt-head-center"
            },
            { data: null,
                render: function(data, type, row) {
                    if (row.Inclusive) {
                        if (row.Inclusive.indexOf('Service Unit') > -1) {
                            return '<center><span class="checkbox_span" style="color:green">✓</span></center>';
                        }
                        else{
                            return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                        }
                    }
                    else{
                        return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                    }
                },
                orderable: false,
                className: "dt-head-center"
            }
            
        ]
    });

    var data_update;
    setInterval(function(){
        if($('#AddWarranty').is(':hidden') && $('#reportModal').is(':hidden') && $('#changePassword').is(':hidden') && $('#loading').is(':hidden')){
            $.ajax({
                url: "/fm_warranty/reload",
                success: function(data){
                    if(data != data_update){
                        data_update = data;
                        tblWarranty.ajax.reload(null, false);
                    }
                }
            });
        }
    }, 3000);
}
else{
    window.location.href = '/maintenance';
}

$('.btnImport').on('click', function(){
    $('#importItem').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('.modal-body').html();
    $('#importItem').modal('show');
});

$('#btnDetach').on('click', function(){
    $('#xlsx').val('');
});

function validate_xlsx(xlsx){
    var files_length = $("#xlsx").get(0).files.length;
    var error_ext = 0;
    var error_mb = 0;
    if(files_length > 1){
        Swal.fire('EXCEEDED allowed number of file upload!', 'Please upload only ONE (1) valid EXCEL file.', 'error');      
        $('#xlsx').val('');
        $('#xlsx').focus();
        return false;
    }
    for(var i = 0; i < files_length; ++i) {
        var file1=$("#xlsx").get(0).files[i].name;
        var file_size = $("#xlsx").get(0).files[i].size;
        var ext = file1.split('.').pop().toLowerCase();
        if($.inArray(ext,['xls','xlsx'])===-1){
            error_ext++;
        }
        if(file_size > (5242880 * 2)){
            error_mb++;
        }
    }
    if(error_ext > 0 && error_mb > 0){
        Swal.fire('INVALID file type AND EXCEEDED maximum file size (10MB)!', 'Please upload an EXCEL file with valid file type like the following: xls or xlsx; AND with file size not greater than 10MB.', 'error');      
        $('#xlsx').val('');
        $('#xlsx').focus();
        return false;
    }
    else if(error_ext > 0){
        Swal.fire('INVALID file type!', 'Please upload an EXCEL file with valid file type like the following: xls or xlsx.', 'error');      
        $('#xlsx').val('');
        $('#xlsx').focus();
        return false;
    }
    else if(error_mb > 0){
        Swal.fire('EXCEEDED maximum file size (10MB)!', 'Please upload a valid EXCEL file with file size not greater than 10MB.', 'error');      
        $('#xlsx').val('');
        $('#xlsx').focus();
        return false;
    }
    return true;
}

$('#btnUpload').on('click', function(){
    if($('#xlsx')[0].files.length === 0){
        $('#btnSubmitImport').click();
    }
    else{
        Swal.fire({
            title: "UPLOAD FILE IMPORT?",
            text: "Click 'Confirm' button to ADD ITEMS via uploading import file; otherwise, click 'Cancel' button to select a different file.",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                $('#btnSubmitImport').click();
            }
        });
    }
});

$(document).on('click', '.btnNewWarranty', function(){
    $('#AddWarranty').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#WarrantyForm').trigger('reset');
    $('.cb').prop('checked', false );
    $('#txtWarranty').text('ADD NEW WARRANTY');
    $('#btnSubmit').val('SUBMIT');
    $('#AddWarranty').modal('show');
});

$(document).on("click", ".warrantyTable tbody tr", function(){
    $('#AddWarranty').modal({
        backdrop: 'static',
        keyboard: false
    });
    wrdata = tblWarranty.row(this).data();
    $('#WarrantyForm').trigger('reset');
    $('#txtWarranty').text('UPDATE WARRANTY DETAILS');
    $('#btnSubmit').val('UPDATE');
    $('#warranty').val(wrdata.Warranty_Name);
    $('#duration').val(wrdata.Duration);
    if(wrdata.Inclusive != null){
        $('#software').attr("checked", wrdata.Inclusive.indexOf('Software') > -1);
        $('#onsite').attr("checked", wrdata.Inclusive.indexOf('Onsite Visit') > -1);
        $('#phone').attr("checked", wrdata.Inclusive.indexOf('Phone Support') > -1);
        $('#hardware').attr("checked", wrdata.Inclusive.indexOf('Hardware') > -1);
        $('#replacement').attr("checked", wrdata.Inclusive.indexOf('Parts Replacement') > -1);
        $('#su').attr("checked", wrdata.Inclusive.indexOf('Service Unit') > -1);
    }
    else{
        $('#software').attr("checked", false);
        $('#onsite').attr("checked", false);
        $('#phone').attr("checked", false);
        $('#hardware').attr("checked", false);
        $('#replacement').attr("checked", false);
        $('#su').attr("checked", false);
    }
    $('#AddWarranty').modal('show');
});

$(document).on('click', '#btnSubmit', function(){
    if(!$('#warranty').val() || !$('#duration').val()){
        $('#WarrantyForm')[0].reportValidity();
        return false;
    }
    var inclusive = new Array();
    $('.cb').each(function(){
        if(this.checked)
            inclusive.push($(this).val());
    });
    if($('#btnSubmit').val() == 'SUBMIT'){
        Swal.fire({
            title: "ADD NEW WARRANTY?",
            text: "You are about to ADD this new warranty!",
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
                    url: "/AddWarranty",
                    type: "POST",
                    dataType: 'json',
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        warranty: $.trim($('#warranty').val()).toUpperCase(),
                        duration: $('#duration').val(),
                        inclusive: inclusive
                    },
                    success: function(result){
                        $('#AddWarranty').modal('hide');
                        if(result == true){
                            Swal.fire('SAVE SUCCESS', 'New Warranty has been saved successfully!', 'success');
                            tblWarranty.ajax.reload(null, false);
                        }
                        else{
                            Swal.fire('SAVE FAILED', 'New Warranty save failed!', 'error');
                            tblWarranty.ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    }
    else{
        Swal.fire({
            title: "UPDATE WARRANTY?",
            text: "You are about to UPDATE this warranty!",
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
                    url: "/UpdateWarranty",
                    type: "PUT",
                    dataType: 'json',
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        id: wrdata.id,
                        warranty: $.trim($('#warranty').val()).toUpperCase(),
                        duration: $('#duration').val(),
                        inclusive: inclusive
                    },
                    success: function(result){
                        $('#AddWarranty').modal('hide');
                        if(result == true){
                            Swal.fire('UPDATE SUCCESS', 'Warranty details has been updated successfully!', 'success');
                            tblWarranty.ajax.reload(null, false);
                        }
                        else{
                            Swal.fire('UPDATE FAILED', 'Warranty details update failed!', 'error');
                            tblWarranty.ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    }
});

$('.btnNewItem').on('click', function(){
    $('#newItem').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#item_category').val('');
    $('#prodcode').val('');
    $('#item_name').val('');
    $('#minimum').val('');
    $('#item_uom').val('');
    $('.divSerial').hide();
    $('#serialize').prop('checked', true);
    
    $('.modal-body').html();
    $('#newItem').modal('show');
});

$('#item_uom').on('change', function(){
    if($('#item_uom').val() == 'Unit'){
        $('.divSerial').show();
        $('#serialize').prop('checked', true);
    }
    else{
        $('.divSerial').hide();
        $('#serialize').prop('checked', true);
    }
});

$('#btnSaveItem').on('click', function(){
    var category_name = $('#item_category').find('option:selected').text();
    var item_category = $('#item_category').val();
    var item_name = $.trim($('#item_name').val());
    var prodcode = $.trim($('#prodcode').val());
    var minimum = $('#minimum').val();
    var item_uom = $('#item_uom').val();
    if($('#serialize').is(":checked")){
        var serialize = 'YES';
    }
    else{
        var serialize = 'NO';
    }
    if(item_uom != 'Unit'){
        serialize = 'NO';
    }
    if(!item_category || !item_name || !prodcode || !minimum || !item_uom ){
        Swal.fire('REQUIRED','Please fill up all required fields!','error');
        return false;
    }
    Swal.fire({
        title: "ADD NEW ITEM?",
        text: "You are about to ADD this new item!",
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
                url: "/saveItem",
                type: "POST",
                headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    _token: $("#csrf").val(),
                    category_name: category_name,
                    item_category: item_category,
                    item_name: item_name,
                    prodcode: prodcode,
                    minimum: minimum,
                    item_uom: item_uom,
                    serialize: serialize
                },
                success: function(data){
                    if(data.result == 'true'){
                        $('#newItem').modal('hide');
                        Swal.fire("SAVE SUCCESS", "New Item has been saved successfully!", "success");
                        tblItem.ajax.reload(null, false);
                    }
                    else if(data.result == 'duplicate'){
                        Swal.fire("DUPLICATE ITEM", "Item Description already exists!", "error");
                        return false;
                    }
                    else if(data.result == 'duplicatecode'){
                        Swal.fire("DUPLICATE ITEM CODE", "Item Code already exists!", "error");
                        return false;
                    }
                    else{
                        $('#newItem').modal('hide');
                        Swal.fire("SAVE FAILED", "New Item save failed!", "error");
                        tblItem.ajax.reload(null, false);
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/maintenance';
                    }
                    alert(data.responseText);
                }
            });
        }
    });
});

$('#itemTable tbody').on('click', 'tr', function(){
    $('#detailsItem').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table = $('table.itemTable').DataTable(); 
    var data = table.row(this).data();
    var item_id = data.id;
        $('#item_id').val(item_id);
    var category_name = data.category;
        $('#category_name_details_original').val(category_name);
    var item_category = data.category_id;
        $('#item_category_details').val(item_category);
        $('#item_category_details_original').val(item_category);
    var item_name = decodeHtml(data.item);
        $('#item_name_details').val(item_name);
        $('#item_name_details_original').val(item_name);
    var prodcode = data.prodcode;
        $('#prodcode_details').val(prodcode);
        $('#prodcode_details_original').val(prodcode);
    var minimum = data.minimum;
        $('#minimum_details').val(minimum);
        $('#minimum_details_original').val(minimum);
    var item_uom = data.uom;
        $('#item_uom_details').val(item_uom);
        $('#item_uom_details_original').val(item_uom);
    var serialize = data.serialize;
        $('#serialize_details_original').val(serialize);
        if(serialize == 'YES'){
            $('#serialize_details').prop('checked', true);
        }
        else{
            $('#serialize_details').prop('checked', false);
        }
        if(item_uom == 'Unit'){
            $('.divSerial').show();
        }
        else{
            $('.divSerial').hide();
            $('#serialize_details').prop('checked', true);
        }
    
    $('.modal-body').html();
    $('#detailsItem').modal('show');
});

$('#item_uom_details').on('change', function(){
    if($('#item_uom_details').val() == 'Unit'){
        $('.divSerial').show();
        $('#serialize_details').prop('checked', true);
    }
    else{
        $('.divSerial').hide();
        $('#serialize_details').prop('checked', true);
    }
});

$('#btnUpdateItem').on('click', function(){
    var item_id = $('#item_id').val();
    var category_name_original = $('#category_name_details_original').val();
    var item_category_original = $('#item_category_details_original').val();
    var item_name_original = $('#item_name_details_original').val();
    var prodcode_original = $('#prodcode_details_original').val();
    var minimum_original = $('#minimum_details_original').val();
    var item_uom_original = $('#item_uom_details_original').val();
    var serialize_original = $('#serialize_details_original').val();
    var category_name = $('#item_category_details').find('option:selected').text();
    var item_category = $('#item_category_details').val();
    var item_name = $.trim($('#item_name_details').val());
    var prodcode = $.trim($('#prodcode_details').val());
    var minimum = $('#minimum_details').val();
    var item_uom = $('#item_uom_details').val();
    if($('#serialize_details').is(":checked")){
        var serialize = 'YES';
    }
    else{
        var serialize = 'NO';
    }
    if(item_uom != 'Unit'){
        serialize = 'NO';
    }
    if(!item_category || !item_name || !prodcode || !minimum || !item_uom ){
        Swal.fire('REQUIRED','Please fill up all required fields!','error');
        return false;
    }
    if(item_name_original.toUpperCase() == item_name.toUpperCase() && prodcode_original == prodcode && item_category_original == item_category && minimum_original == minimum && item_uom_original == item_uom && serialize_original == serialize){
        Swal.fire("NO CHANGES FOUND", "Item Details are still all the same!", "error");
        return false;
    }
    Swal.fire({
        title: "UPDATE ITEM?",
        text: "You are about to UPDATE this item!",
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
                url: "/updateItem",
                type: "PUT",
                headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    _token: $("#csrf").val(),
                    item_id: item_id,
                    category_name_original: category_name_original,
                    item_category_original: item_category_original,
                    item_name_original: item_name_original,
                    prodcode_original: prodcode_original,
                    minimum_original: minimum_original,
                    item_uom_original: item_uom_original,
                    serialize_original: serialize_original,
                    category_name: category_name,
                    item_category: item_category,
                    item_name: item_name,
                    prodcode: prodcode,
                    minimum: minimum,
                    item_uom: item_uom,
                    serialize: serialize
                },
                success: function(data){
                    if(data.result == 'true'){
                        $('#detailsItem').modal('hide');
                        Swal.fire("UPDATE SUCCESS", "Item details has been updated successfully!", "success");
                        tblItem.ajax.reload(null, false);
                    }
                    else if(data.result == 'duplicate'){
                        Swal.fire("DUPLICATE ITEM", "Item Description already exists!", "error");
                        return false;
                    }
                    else if(data.result == 'duplicatecode'){
                        Swal.fire("DUPLICATE ITEM CODE", "Item Code already exists!", "error");
                        return false;
                    }
                    else{
                        $('#detailsItem').modal('hide');
                        Swal.fire("UPDATE FAILED", "Item details update failed!", "error");
                        tblItem.ajax.reload(null, false);
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/maintenance';
                    }
                    alert(data.responseText);
                }
            });
        }
    });
});

$(document).on('keyup', '#prodcode', function(){
    var prodcode = $('#prodcode').val().toUpperCase();
    $('#prodcode').val(prodcode);
});

$(document).on('keypress', '#prodcode', function(e){
    var k;
    document.all ? k = e.keyCode : k = e.which;
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 45 || (k >= 48 && k <= 57));
});

$(document).on('keyup', '#prodcode_details', function(){
    var prodcode_details = $('#prodcode_details').val().toUpperCase();
    $('#prodcode_details').val(prodcode_details);
});

$(document).on('keypress', '#prodcode_details', function(e){
    var k;
    document.all ? k = e.keyCode : k = e.which;
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 45 || (k >= 48 && k <= 57));
});

$('.btnNewCategory').on('click', function(){
    $('#newCategory').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#category').val('');
    
    $('.modal-body').html();
    $('#newCategory').modal('show');
});

$('#btnSaveCategory').on('click', function(){
    var category = $.trim($('#category').val());
    if(category != ""){
        Swal.fire({
            title: "ADD NEW CATEGORY?",
            text: "You are about to ADD this new category!",
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
                    url: "/saveCategory",
                    type: "POST",
                    headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        _token: $("#csrf").val(),
                        category: category
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('#newCategory').modal('hide');
                            Swal.fire("SAVE SUCCESS", "New Category has been saved successfully!", "success");
                            tblCategory.ajax.reload(null, false);
                            $.ajax({
                                url: "/logNewCategory",
                                type: "POST",
                                headers:{
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    id: data.id,
                                    category: data.category
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
                                        window.location.href = '/maintenance?tbl=categories';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else if(data.result == 'duplicate'){
                            Swal.fire("DUPLICATE CATEGORY", "Category Name already exists!", "error");
                            return false;
                        }
                        else{
                            $('#newCategory').modal('hide');
                            Swal.fire("SAVE FAILED", "New Category save failed!", "error");
                            tblCategory.ajax.reload(null, false);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/maintenance?tbl=categories';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
    else{
        Swal.fire('REQUIRED','Category Name field is required!','error');
        return false;
    }
});

$('#categoryTable tbody').on('click', 'tr', function(){
    $('#detailsCategory').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table = $('table.categoryTable').DataTable(); 
    var data = table.row(this).data();
    var category_id = data.id;
        $('#category_id').val(category_id);
    var category = data.category;
        $('#category_details').val(category);
        $('#category_original').val(category);
    
    $('.modal-body').html();
    $('#detailsCategory').modal('show');
});

$('#btnUpdateCategory').on('click', function(){
    var category_id = $('#category_id').val();
    var category_original = $('#category_original').val();
    var category_details = $.trim($('#category_details').val().toUpperCase());
    
    if(category_details == ""){
        Swal.fire('REQUIRED','Category Name field is required!','error');
        return false;
    }
    else if(category_original == category_details){
        Swal.fire("NO CHANGES FOUND", "Category Name is still the same!", "error");
        return false;
    }
    else{
        Swal.fire({
            title: "UPDATE CATEGORY?",
            text: "You are about to UPDATE this category!",
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
                    url: "/updateCategory",
                    type: "PUT",
                    headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        _token: $("#csrf").val(),
                        category_id: category_id,
                        category_original: category_original,
                        category_details: category_details
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('#detailsCategory').modal('hide');
                            Swal.fire("UPDATE SUCCESS", "Category Name has been updated successfully!", "success");
                            tblCategory.ajax.reload(null, false);
                            $.ajax({
                                url: "/logUpdateCategory",
                                type: "POST",
                                headers:{
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    category_id: data.category_id,
                                    category_original: data.category_original,
                                    category_details: data.category_details
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
                                        window.location.href = '/maintenance?tbl=categories';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else if(data.result == 'duplicate'){
                            Swal.fire("DUPLICATE CATEGORY", "Category Name already exists!", "error");
                            return false;
                        }
                        else{
                            $('#detailsCategory').modal('hide');
                            Swal.fire("UPDATE FAILED", "Category Name update failed!", "error");
                            tblCategory.ajax.reload(null, false);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/maintenance?tbl=categories';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
});

$(".btnNewLocation").on('click', function(){
    $('#newLocation').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#location').val('');

    $('.modal-body').html();
    $('#newLocation').modal('show');
});

$('#btnSaveLocation').on('click', function(){
    var location_name = $.trim($('#location').val());
    if(location_name != ""){
        Swal.fire({
            title: "REQUEST NEW LOCATION?",
            text: "You are about to REQUEST a new location!",
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
                    url: "/saveLocation",
                    type: "POST",
                    headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        _token: $("#csrf").val(),
                        location: location_name
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('#newLocation').modal('hide');
                            $('#loading').show();
                            $.ajax({
                                url: "/logNewLocation",
                                type: "POST",
                                headers:{
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    id: data.id,
                                    location: data.location
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide();
                                        Swal.fire("REQUEST SUCCESS", "New Location has been requested successfully!", "success");
                                        tblLocation.ajax.reload(null, false);
                                    }
                                    else{
                                        return false;
                                    }
                                },
                                error: function(data){
                                    if(data.status == 401){
                                        window.location.href = '/maintenance?tbl=locations';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else if(data.result == 'duplicate'){
                            Swal.fire("DUPLICATE LOCATION", "Location Name already exists!", "error");
                            return false;
                        }
                        else{
                            $('#newLocation').modal('hide');
                            Swal.fire("REQUEST FAILED", "New Location request failed!", "error");
                            tblLocation.ajax.reload(null, false);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/maintenance?tbl=locations';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
    else{
        Swal.fire('REQUIRED','Location Name field is required!','error');
        return false;
    }
});

$('#locationTable tbody').on('click', 'tr', function(){
    $('#detailsLocation').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table = $('table.locationTable').DataTable(); 
    var data = table.row(this).data();
    if(data.status.includes('CHANGE REQUESTED') || data.status.includes('PENDING')){
        return false;
    }
    else{
        var location_id = data.location_id;
            $('#location_id').val(location_id);
        var location_name = data.location;
            $('#location_details').val(location_name);
            $('#location_original').val(location_name);
        var status = data.status;
            $('#status_original').val(status);
            if(status == 'ACTIVE'){
                $('#status').prop('checked', true);
            }
            else{
                $('#status').prop('checked', false);
            }

        $('.modal-body').html();
        $('#detailsLocation').modal('show');
    }
});

$('#btnUpdateLocation').on('click', function(){
    if($('#status').is(":checked")){
        var status = 'ACTIVE';
    }
    else{
        var status = 'INACTIVE';
    }
    var location_id = $('#location_id').val();
    var location_original = $('#location_original').val();
    var location_details = $.trim($('#location_details').val().toUpperCase());
    var status_original = $('#status_original').val();

    if(location_details == ""){
        Swal.fire('REQUIRED','Location Name field is required!','error');
        return false;
    }
    if(location_original == location_details && status_original == status){
        Swal.fire("NO CHANGES FOUND", "Location Details are all still the same!", "error");
        return false;
    }
    if(location_original != location_details && status_original != status){
        Swal.fire("UPDATE FAILED", "STATUS CHANGE REQUEST is NOT allowed if the current Location Name has been changed!", "error");
        return false;
    }
    if(location_original == location_details && status != status_original){
        Swal.fire({
            title: "REQUEST STATUS CHANGE?",
            text: "You are about to request a STATUS CHANGE to this location!",
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
                    url: "/updateLocation",
                    type: "PUT",
                    headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        _token: $("#csrf").val(),
                        location_id: location_id,
                        location_details: location_details,
                        status_original: status_original,
                        status: status
                    },
                    success: function(data){
                        if(data.result == 'request'){
                            $('#detailsLocation').modal('hide');
                            $('#loading').show();
                            $.ajax({
                                url: "/requestStatusChange",
                                type: "POST",
                                headers:{
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    id: data.id,
                                    location: data.location,
                                    status_original: data.status_original,
                                    status: data.status
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide();
                                        Swal.fire("REQUEST SUCCESS", "Location Status Change has been requested successfully!", "success");
                                        tblLocation.ajax.reload(null, false);
                                    }
                                    else{
                                        return false;
                                    }
                                },
                                error: function(data){
                                    if(data.status == 401){
                                        window.location.href = '/maintenance?tbl=locations';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                        else{
                            $('#detailsLocation').modal('hide');
                            Swal.fire("REQUEST FAILED", "Location Status Change request failed!", "error");
                            tblLocation.ajax.reload(null, false);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/maintenance?tbl=locations';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });

    }
    else{
        Swal.fire({
            title: "UPDATE LOCATION NAME?",
            text: "You are about to UPDATE this location!",
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
                    url: "/updateLocation",
                    type: "PUT",
                    headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        _token: $("#csrf").val(),
                        location_id: location_id,
                        location_original: location_original,
                        location_details: location_details
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('#detailsLocation').modal('hide');
                            Swal.fire("UPDATE SUCCESS", "Location Name has been updated successfully!", "success");
                            tblLocation.ajax.reload(null, false);
                        }
                        else if(data.result == 'duplicate'){
                            Swal.fire("DUPLICATE LOCATION", "Location Name already exists!", "error");
                            return false;
                        }
                        else{
                            $('#detailsLocation').modal('hide');
                            Swal.fire("UPDATE FAILED", "Location Name update failed!", "error");
                            tblLocation.ajax.reload(null, false);
                        }
                    },
                    error: function(data){
                        if(data.status == 401){
                            window.location.href = '/maintenance?tbl=locations';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    }
});

setInterval(checkCreateItem, 0);
function checkCreateItem(){
    if($('#createItem').is(':visible')){
        var item_description = $.trim($('#aic_item_description').val());
        var item_code = $('#aic_item_code').val();
        var minimum = $('#aic_minimum').val();
        if(item_description && item_code && minimum){
            $('.create_label').hide();
            $('#partsDetails').show();
        }
        else{
            $('.create_label').show();
            $('#partsDetails').hide();
        }
    }
}

$(document).on('keyup', '#aic_item_code', function(){
    var aic_item_code = $('#aic_item_code').val().toUpperCase();
    $('#aic_item_code').val(aic_item_code);
});

$(document).on('keypress', '#aic_item_code', function(e){
    var k;
    document.all ? k = e.keyCode : k = e.which;
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 45 || (k >= 48 && k <= 57));
});

$(document).on('keyup', '#aim_item_code_details', function(){
    var aim_item_code_details = $('#aim_item_code_details').val().toUpperCase();
    $('#aim_item_code_details').val(aim_item_code_details);
});

$(document).on('keypress', '#aim_item_code_details', function(e){
    var k;
    document.all ? k = e.keyCode : k = e.which;
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 45 || (k >= 48 && k <= 57));
});

$(".btnCreateItem").on('click', function(){
    $('#createItem').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#aic_item_description').val('');
    $('#aic_item_code').val('');
    $('#aic_minimum').val('');
    $("#categoryAssembly").val('');
    $("#itemAssembly").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
    $("#qtyAssembly").val('');
    $('#uomAssembly').val('');
    $('#tblCreateItem tbody').empty();
    if($('#tblCreateItem tbody').children().length==0){
        $('#tblCreateItem').hide();
        $('#divCreateItem').removeClass();
        $('#btnClose').hide();
        $('#btnSave').hide();
        $('.submit_label').show();
    }

    $('.modal-body').html();
    $('#createItem').modal('show');
});

$('#categoryAssembly').on('change', function(){
    var id = $('#categoryAssembly').val();
    var descOp = " ";
    $.ajax({
        type:'get',
        url:'/itemsAssembly',
        data:{'category_id':id},
        success: function(data)
        {
            var itemcode = $.map(data, function(value, index){ 
                return [value];
            });
            descOp+='<option value="" selected disabled>Select Item</option>'; 
            itemcode.forEach(value => {
                descOp+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>'; 
            });
            
            $("#itemAssembly").find('option').remove().end().append(descOp);                 
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/assembly';
            }
            alert(data.responseText);
        }
    });
});

$('#itemAssembly').on('change', function(){
    var item_id = $(this).val();
    $.ajax({
        type:'get',
        url:'/uomAssembly',
        data:{
            'item_id': item_id,
        }, 
        success: function(data){
            $('#prodcodeAssembly').val(data[0].prodcode);
            $('#uomAssembly').val(data[0].uom);
        },
        error: function(data){
            if(data.status == 401){
                window.location.href = '/assembly';
            }
            alert(data.responseText);
        }
    });
});

$(".add-row").on('click', function(){
    var category = $("#categoryAssembly option:selected").text();
    var item = $("#itemAssembly option:selected").text();
    let item_id = $("#itemAssembly").val();
    var prodcode = $("#prodcodeAssembly").val();
    var uom = $("#uomAssembly").val();
    let qty = $("#qtyAssembly").val();
    var markup = "<tr><td class='d-none'>" + item_id + "</td><td>" + prodcode + "</td><td>" + item + "</td><td>" + qty + "</td><td>" + uom + "</td><td> <button type='button' style='zoom: 80%;' class='delete-row btn btn-danger bp'>REMOVE</button> </td></tr>";
    var ctr = 'false';
    if(category == "Select Category" || item == "Select Item" || qty == "" || qty == "0" || uom == ""){
        Swal.fire('REQUIRED','Please select an item!','error');
        return false;
    }
    else{
        var table = document.getElementById('tblCreateItem');
        var count = table.rows.length;
        for(i = 1; i < count; i++){
            var objCells = table.rows.item(i).cells;
            if(item==objCells.item(2).innerHTML){
                objCells.item(3).innerHTML = parseInt(objCells.item(3).innerHTML) + parseInt(qty);
                ctr = 'true';
                category = $("#categoryAssembly").val('');
                item = $("#itemAssembly").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
                prodcode = $("#prodcodeAssembly").val('');
                uom = $('#uomAssembly').val('');
                qty = $("#qtyAssembly").val('');
                return false;
            }
            else {
                ctr = 'false';
            }
        }
        if(ctr == 'false')
        { $("#tblCreateItem tbody").append(markup); }
        category = $("#categoryAssembly").val('');
        item = $("#itemAssembly").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
        prodcode = $("#prodcodeAssembly").val('');
        uom = $('#uomAssembly').val('');
        qty = $("#qtyAssembly").val('');
        $('#tblCreateItem').show();
        $('#divCreateItem').toggle();
        $('#btnClose').show();
        $('#btnSave').show();
    }
    if($('#tblCreateItem tbody').children().length==0){
        $('.submit_label').show();
    }
    else{
        $('.submit_label').hide();
    }
});

$("#tblCreateItem").on('click', '.delete-row', function(){
    $(this).closest("tr").remove();
    if($('#tblCreateItem tbody').children().length==0){
        $('#tblCreateItem').hide();
        $('#divCreateItem').removeClass();
        $('#btnClose').hide();
        $('#btnSave').hide();
        $('.submit_label').show();
    }
});

$('#btnSave').on('click', function(){
    var item_description = $.trim($('#aic_item_description').val());
    var item_code = $('#aic_item_code').val();
    var minimum = $('#aic_minimum').val();
    Swal.fire({
        title: "CREATE NEW ASSEMBLED ITEM?",
        text: "You are about to CREATE a new Assembled Item!",
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
                type:'post',
                url:'/createItem',
                async: false,
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    item: item_description,
                    prodcode: item_code,
                    minimum: minimum
                },
                success: function(data){
                    if(data.result == 'true'){
                        var myTable = $('#tblCreateItem').DataTable();
                        var form_data  = myTable.rows().data();
                        $.each(form_data, function(key, value){
                            $.ajax({
                                type:'post',
                                url:'/saveParts',
                                async: false,
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    item_id: data.id,
                                    part_id: value[0],
                                    quantity: value[3]
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
                            url:'/logItem',
                            async: false,
                            headers:{
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:{
                                item_id: data.id,
                                item: item_description
                            },
                            success: function(data){
                                if(data == 'true'){
                                    $('#createItem').modal('hide');
                                    Swal.fire("CREATE SUCCESS", "New Assembled Item has been created successfully!", "success");
                                    tblAssembly.ajax.reload(null, false);
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
                    else if(data.result == 'duplicate'){
                        Swal.fire("DUPLICATE ITEM", "Item Description already exists!", "error");
                        return false;
                    }
                    else if(data.result == 'dupecode'){
                        Swal.fire("DUPLICATE CODE", "Item Code already exists!", "error");
                        return false;
                    }
                    else{
                        $('#createItem').modal('hide');
                        Swal.fire("CREATE FAILED", "New Assembled Item create failed!", "error");
                        tblAssembly.ajax.reload(null, false);
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
});

setInterval(runCompare, 0);
function runCompare(){
    if($('#detailsAssemblyItem').is(':visible')){
        var item_current = $.trim($('#aim_item_name_details').val());
        var item_original = $('#aim_item_name_details_original').val();
        var code_current = $('#aim_item_code_details').val();
        var code_original = $('#aim_item_code_details_original').val();
        var minimum_current = $('#aim_minimum_details').val();
        var minimum_original = $('#aim_minimum_details_original').val();
        if((!item_current || !code_current || !minimum_current) || (item_current.toUpperCase() == item_original.toUpperCase() && code_current == code_original && minimum_current == minimum_original)){
            $('#btnUpdate').hide();
        }
        else{
            $('#btnUpdate').show();
        }
    }
}

$('#assemblyitemTable tbody').on('click', 'tr', function(){
    $('#detailsAssemblyItem').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table = $('table.assemblyitemTable').DataTable(); 
    var data = table.row(this).data();
    var item_id = data.id;
    $('#aim_item_id').val(item_id);
    var item_name = decodeHtml(data.item);
    $('#aim_item_name_details').val(item_name);
    $('#aim_item_name_details_original').val(item_name);
    var prodcode = data.prodcode;
    $('#aim_item_code_details').val(prodcode);
    $('#aim_item_code_details_original').val(prodcode);
    var minimum = data.minimum;
    $('#aim_minimum_details').val(minimum);
    $('#aim_minimum_details_original').val(minimum);
    
    $('.modal-body').html();
    $('#detailsAssemblyItem').modal('show');
    
    $('table.tblItemDetails').dataTable().fnDestroy();
    $('table.tblItemDetails').DataTable({
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
            url: '/itemDetails',
            data:{
                item_id: item_id
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
            { data: 'uom' }
        ],
        orderCellsTop: true,
        fixedHeader: true,            
    });
});

$('#btnUpdate').on('click', function(){
    var item_id = $('#aim_item_id').val();
    var item_name_original = $('#aim_item_name_details_original').val();
    var item_name = $.trim($('#aim_item_name_details').val());
    var item_code_original = $('#aim_item_code_details_original').val();
    var item_code = $.trim($('#aim_item_code_details').val());
    var minimum_original = $('#aim_minimum_details_original').val();
    var minimum = $.trim($('#aim_minimum_details').val());
    Swal.fire({
        title: "UPDATE ASSEMBLED ITEM?",
        text: "You are about to UPDATE this Assembled Item!",
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
                type:'post',
                url:'/changeItem',
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    item_id: item_id,
                    item_name_original: item_name_original,
                    item_name: item_name,
                    item_code_original: item_code_original,
                    item_code: item_code,
                    minimum_original: minimum_original,
                    minimum: minimum
                },
                success: function(data){
                    if(data == 'true'){
                        $('#detailsAssemblyItem').modal('hide');
                        Swal.fire("UPDATE SUCCESS", "Assembled Item Description has been updated successfully!", "success");
                        tblAssembly.ajax.reload(null, false);
                    }
                    else if(data == 'duplicate'){
                        Swal.fire("DUPLICATE ITEM", "Item Description already exists!", "error");
                        return false;
                    }
                    else if(data == 'dupecode'){
                        Swal.fire("DUPLICATE CODE", "Item Code already exists!", "error");
                        return false;
                    }
                    else{
                        $('#detailsAssemblyItem').modal('hide');
                        Swal.fire("UPDATE FAILED", "Assembled Item Description update failed!", "error");
                        tblAssembly.ajax.reload(null, false);
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
});

$(document).ready(function(){
    if($(location).attr('pathname')+window.location.search == '/maintenance?import=success_without_errors'){
        $('#loading').hide();
        Swal.fire("IMPORT SUCCESS", "ADD ITEMS via import file is successful without errors.", "success");
    }
    else if($(location).attr('pathname')+window.location.search == '/maintenance?import=success_with_errors'){
        $('#loading').hide();
        Swal.fire("IMPORT SUCCESS W/ ERRORS", "ADD ITEMS via import file is successful with some errors.", "warning");
    }
    else if($(location).attr('pathname')+window.location.search == '/maintenance?import=failed'){
        $('#loading').hide();
        Swal.fire("IMPORT FAILED", "ADD ITEMS via import file has failed.", "error");
    }
});