$('table.stockrequestTable').dataTable().fnDestroy();
if($("#current_role").val() == 'sales'){
    var stockrequestTable = $('table.stockrequestTable').DataTable({
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        language:{
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax:{
            url: '/request_data',
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
            },
            {
                "targets": [7],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [10],
                "visible": false,
                "searchable": true
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
            {
                data: 'client_name',
                "render": function(data, type, row){
                    if(row.client_name == null && row.req_num != null){
                        return row.req_num;
                    }
                    else{
                        return row.client_name;
                    }
                }
            },
            { data: 'location' },
            {
                data: 'reference',
                "render": function(data, type, row){
                    if(row.reference == null && row.orderID != null){
                        return row.orderID;
                    }
                    else{
                        return row.reference;
                    }
                }
            },
            { data: 'req_by' },
            { data: 'req_type' },
            {
                data: 'status',
                "render": function(data, type, row){
                    if(row.status_id == '6'){
                        return "<span style='color: DarkSlateGray; font-weight: bold;'>"+row.status+'&nbsp;&nbsp;&nbsp;'+"<i style='zoom: 150%; color: DarkSlateGray;' class='fa fa-exclamation-triangle'></i></span>";
                    }
                    else if(row.status_id == '1' || row.status_id == '15' || row.status_id == '18' || row.status_id == '21' || row.status_id == '22' || row.status_id == '23' || row.status_id == '24' || row.status_id == '25' || row.status_id == '28' || row.status_id == '33'){
                        return "<span style='color: Red; font-weight: bold;'>"+row.status+"</span>";
                    }
                    else if(row.status_id == '2' || row.status_id == '5' || row.status_id == '16' || row.status_id == '30' || row.status_id == '31'){
                        return "<span style='color: Indigo; font-weight: bold;'>"+row.status+"</span>";
                    }
                    else if(row.status_id == '3' || row.status_id == '4' || row.status_id == '11' || row.status_id == '13' || row.status_id == '17' || row.status_id == '27' || row.status_id == '32' || row.status_id == '34'){
                        return "<span style='color: Green; font-weight: bold;'>"+row.status+"</span>";
                    }
                    else if(row.status_id == '8' || row.status_id == '9' || row.status_id == '12' || row.status_id == '19' || row.status_id == '20'){
                        return "<span style='color: Blue; font-weight: bold;'>"+row.status+"</span>";
                    }
                    else if(row.status_id == '10' || row.status_id == '14' || row.status_id == '26' || row.status_id == '29'){
                        return "<span style='color: DarkBlue; font-weight: bold;'>"+row.status+"</span>";
                    }
                    else{
                        return "<span style='color: Gray; font-weight: bold;'>"+row.status+"</span>";
                    }
                }
            },
            { data: 'req_num' }
        ],
        order: [],
        initComplete: function(){
            if(($(location).attr('pathname')+window.location.search).includes('submit') == true){
                return false;
            }
            else if(($(location).attr('pathname')+window.location.search).includes('asset') == true){
                return false;
            }
            else if(($(location).attr('pathname')+window.location.search).includes('status') == true){
                return false;
            }
            else if(($(location).attr('pathname')+window.location.search).includes('sale') == true){
                return false;
            }
            else{
                return notifyDeadline();
            }
        }
    });
}
else{
    var stockrequestTable = $('table.stockrequestTable').DataTable({
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        language:{
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax:{
            url: '/request_data',
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
            },
            {
                "targets": [10],
                "visible": false,
                "searchable": true
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
            {
                data: 'client_name',
                "render": function(data, type, row){
                    if(row.client_name == null && row.req_num != null){
                        return row.req_num;
                    }
                    else{
                        return row.client_name;
                    }
                }
            },
            { data: 'location' },
            {
                data: 'reference',
                "render": function(data, type, row){
                    if(row.reference == null && row.orderID != null){
                        return row.orderID;
                    }
                    else{
                        return row.reference;
                    }
                }
            },
            {
                data: 'req_by',
                "render": function(data, type, row){
                    if(row.req_type_id == '7'){
                        return row.asset_reqby;
                    }
                    else{
                        return row.req_by;
                    }
                }
            },
            { data: 'req_type' },
            {
                data: 'status',
                "render": function(data, type, row){
                    if(row.status_id == '6'){
                        return "<span style='color: DarkSlateGray; font-weight: bold;'>"+row.status+'&nbsp;&nbsp;&nbsp;'+"<i style='zoom: 150%; color: DarkSlateGray;' class='fa fa-exclamation-triangle'></i></span>";
                    }
                    else if(row.status_id == '1' || row.status_id == '15' || row.status_id == '18' || row.status_id == '21' || row.status_id == '22' || row.status_id == '23' || row.status_id == '24' || row.status_id == '25' || row.status_id == '28' || row.status_id == '33'){
                        return "<span style='color: Red; font-weight: bold;'>"+row.status+"</span>";
                    }
                    else if(row.status_id == '2' || row.status_id == '5' || row.status_id == '16' || row.status_id == '30' || row.status_id == '31'){
                        return "<span style='color: Indigo; font-weight: bold;'>"+row.status+"</span>";
                    }
                    else if(row.status_id == '3' || row.status_id == '4' || row.status_id == '11' || row.status_id == '13' || row.status_id == '17' || row.status_id == '27' || row.status_id == '32' || row.status_id == '34'){
                        return "<span style='color: Green; font-weight: bold;'>"+row.status+"</span>";
                    }
                    else if(row.status_id == '8' || row.status_id == '9' || row.status_id == '12' || row.status_id == '19' || row.status_id == '20'){
                        return "<span style='color: Blue; font-weight: bold;'>"+row.status+"</span>";
                    }
                    else if(row.status_id == '10' || row.status_id == '14' || row.status_id == '26' || row.status_id == '29'){
                        return "<span style='color: DarkBlue; font-weight: bold;'>"+row.status+"</span>";
                    }
                    else{
                        return "<span style='color: Gray; font-weight: bold;'>"+row.status+"</span>";
                    }
                }
            },
            { data: 'req_num' }
        ],
        order: [],
        initComplete: function(){
            if(($(location).attr('pathname')+window.location.search).includes('submit') == true){
                return false;
            }
            else if(($(location).attr('pathname')+window.location.search).includes('asset') == true){
                return false;
            }
            else if(($(location).attr('pathname')+window.location.search).includes('status') == true){
                return false;
            }
            else if(($(location).attr('pathname')+window.location.search).includes('sale') == true){
                return false;
            }
            else{
                return notifyDeadline();
            }
        }
    });
}

var data_update;
setInterval(function(){
    if($('#newStockRequest').is(':hidden') && $('#detailsStockRequest').is(':hidden') && $('#reportModal').is(':hidden') && $('#changePassword').is(':hidden') && $('#loading').is(':hidden')){
        $.ajax({
            url: "/stockrequest/reload",
            success: function(data){
                if(data != data_update){
                    data_update = data;
                    stockrequestTable.ajax.reload(null, false);
                }
            }
        });
    }
}, 3000);

if($(location).attr('pathname')+window.location.search != '/stockrequest'){
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
            $('#detailsStockRequest').modal({
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
                    $('#reqnum').val(req_num);
                var asm_req_num = value.assembly_reqnum;
                    $('#asm_request_num_details').val(asm_req_num);
                var req_by = value.req_by;
                    $('#requested_by_details').val(req_by);
                var req_by_id = value.user_id;
                    $('#requested_by_id_details').val(req_by_id);
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
                    $('#reprep_by').val(prep_by);
                var sched = value.sched;
                    sched = moment(sched).format('dddd, MMMM DD, YYYY');
                    $('#sched').val(sched);
                    $('#sched1').val(sched);
                    $('#resched1').val(sched);
                var orderID = value.orderID;
                    $('#orderID_details').val(orderID);
                var client_name = value.client_name;
                    $('#client_name_details').val(client_name);
                var location_name = value.location;
                    $('#location_details').val(location_name);
                var contact = value.contact;
                    $('#contact_details').val(contact);
                var remarks = value.remarks;
                    $('#remarks_details').val(remarks);
                var reference = value.reference;
                    $('#reference_details').val(reference);
                var reason = value.reason;
                    $('#reason_details').val(reason);

                    if($("#current_role").val() == 'accounting' && (req_type_id == '1' || req_type_id == '4' || req_type_id == '5' || req_type_id == '7')){
                        window.location.href = '/stockrequest';
                    }
                    if(($("#current_role").val() == 'sales' || $("#current_role").val() == 'approver - sales') && (req_type_id == '1' || (req_type_id == '4' && req_by_id != $('#current_user').val()) || req_type_id == '5' || req_type_id == '6' || req_type_id == '7')){
                        window.location.href = '/stockrequest';
                    }
                    if($("#current_role").val() == 'sales' && $('#current_user').val() != req_by_id){
                        window.location.href = '/stockrequest';
                    }

                    if($("#current_role").val() == 'sales' && (requestStatus == '6' || requestStatus == '7')){
                        $('#btnEditDetails').show();
                        $('.btnCancelDetails').show();
                        
                        $(document).on('click', '#btnEditDetails', function(){
                            editMode = true;
                            $('#btnEditDetails').hide();
                            $('#btnSaveChanges').show();
            
                            $('#needdate').val(value.needdate);
                            $('#client_name').val(client_name);
                            $('#location').val(location_name);
                            $('#contact').val(contact);
                            $('#remarks').val(remarks);
                            $('#reference').val(reference);

                            if(requestStatus == '7'){
                                $('#lblReupload').css({"margin-left": "0px"});
                                $('.classReupload').css({"margin-top": "-56px"});
                            }
                            $('#needdate_details').attr('type', 'date');
                            $('#needdate_details').val(value.needdate);
                            $('#needdate_details').prop('readonly', false);
                            $('#client_name_details').prop('readonly', false);
                            $('#location_details').prop('readonly', false);
                            $('#remarks_details').prop('readonly', false);
                            $('#contact_details').prop('readonly', false);
                            reference =  reference.replaceAll(', ', '\n');
                            $('#reference_details').val(reference);
                            $('#reference_details').prop('readonly', false);
                            $('#btnRemoveAttachment').show();
                            $('#action').val('EDIT');
                        });
                    }
                    else{
                        $('#action').val('');
                    }
                    
                    if((($("#current_role").val() == 'sales' || $("#current_role").val() == 'approver - sales' || $("#current_role").val() == 'accounting') && (req_type_id == '2' || (req_type_id == '3' && (requestStatus == '10' || requestStatus >= 27)) || req_type_id == '6' || req_type_id == '8')) || 
                    ($("#current_role").val() == 'admin' || $("#current_role").val() == 'encoder' || $("#current_role").val() == 'viewer') && req_type_id == '7'){
                        var reference_uploads = value.reference_upload.slice(1).slice(0,-1);
                        var reference_attachments = decodeHtml(reference_uploads).split(',');
                        for(var i=0; i < reference_attachments.length; i++){
                            var btn = document.createElement("input");
                            btn.setAttribute("id", "btnSlide"+(i+1));
                            btn.setAttribute("value", i+1);
                            btn.setAttribute("type", "button");
                            btn.setAttribute("class", "w3-button demo");
                            btn.setAttribute("onclick", "currentDiv("+(i+1)+")");
                            var img = document.createElement("img");
                            img.setAttribute("id", "reference_attachment"+i);
                            img.setAttribute("class", "mySlides");
                            var imgx = document.createElement("img");
                            imgx.setAttribute("id", "reference_hidden"+i);
                            document.getElementById("slidesBtn").appendChild(btn);
                            document.getElementById("slidesContent").appendChild(img);
                            document.getElementById("hiddenContent").appendChild(imgx);
                            var reference_attachment = reference_attachments[i].replace(/\"/g,'');
                
                            $.ajax({
                                type: 'get',
                                url: '/checkURL',
                                async: false,
                                data:{
                                    'reference': reference_attachment,
                                    'check': 'beta'
                                },
                                success: function(data){
                                    if(data.result == 'true'){
                                        $('#reference_attachment'+i).attr('src', data.returnURL).show();
                                        $('#reference_attachment'+i).css({'width': '100%'});
                                    }
                                    else{
                                        $.ajax({
                                            type: 'get',
                                            url: '/checkURL',
                                            async: false,
                                            data:{
                                                'reference': reference_attachment,
                                                'check': 'live'
                                            },
                                            success: function(data){
                                                if(data.result == 'true'){
                                                    $('#reference_attachment'+i).attr('src', data.returnURL).show();
                                                    $('#reference_attachment'+i).css({'width': '100%'});
                                                }
                                                else{
                                                    $('#reference_attachment'+i).attr('src', 'uploads/NA.png').show();
                                                    $('#reference_attachment'+i).css({'width': '25%'});
                                                }
                                            }
                                        });
                                    }
                                }
                            });
                        }
                        $('#btnSlide1').click();
                        if(reference_attachments.length == 1){
                            $("#slidesCtrl").hide();
                        }
                    }

                    var ajax_url = '/schedItems';
                    var rcv_url = '/schedItems';
                    var included = 'yes';

                    if(req_type_id == '7'){
                        $('#txtAttachment').html('ATTACHMENT REQUEST FORM');
                        $('.assethide').hide();
                        $('.assetshow').show();
                        $('#requested_by_details').val(value.asset_reqby);
                        $('#approved_by_details').val(value.asset_apvby);
                        if(requestStatus == '3'){
                            $('#prep_by1_label').html('Prepared By');
                            $('#sched1_label').html('Date Prepared');
                            $('#receive_label').show();
                            $('.btnReceive').show();
                            $('#btnReceive').val('SEND CONFIRMATION');
                            $('#receive_text').html('Please select item/s for receiving on the list below then click <strong>SEND CONFIRMATION</strong> button.');
                        }
                    }
                    if(req_type_id != '2' && req_type_id != '8'){
                        $(".sales_details").hide();
                        if(req_type_id == '6'){
                            $("#btnShowAttachment").show();
                            $("#btnShowAttachment").css({"margin-top": "35px", "margin-left": "0px"});
                            $("#btnHideAttachment").css({"margin-top": "35px", "margin-left": "0px"});
                        }
                    }
                    if(req_type_id == '1' || req_type_id == '5' || req_type_id == '6'){
                        $("#client_name_label").hide();
                        $("#client_name_details").hide();
                        $("#location_label").hide();
                        $("#location_details").hide();
                        $("#contact_label").hide();
                        $("#contact_details").hide();
                        $("#remarks_label").hide();
                        $("#remarks_details").hide();
                        $("#reference_label").hide();
                        $("#reference_details").hide();
                    }
                    if(req_type_id == '6'){
                        $("#orderID_label").show();
                        $("#orderID_details").show();
                    }
                    if(req_type_id == '5'){
                        $("#proceed_label").hide();
                        $("#item_desc_label").show();
                        $("#item_desc_details").show();
                        $("#qty_label").show();
                        $("#qty_details").show();
                    }
                    if(req_type_id == '4'){
                        $(".dfchide").hide();
                        $(".dfcshow").show();
                    }
                    if(req_type_id == '4' && requestStatus == '1'){
                        $.ajax({ 
                            type:'get', 
                            url:'/checkStatus', 
                            data:{
                                'assembly_reqnum': $('#asm_request_num_details').val()
                            }, 
                            success: function(data){
                                if(data == '18' || data == '21'){
                                    $("#warning").hide();
                                    $("#btnProceed").hide();
                                    $(".rcvDef").show();
                                }
                            }
                        });
                    }
                    if(requestStatus != '6'){
                        $("#btnApprove").hide();
                        $("#btnDisapprove").hide();
                    }
                    if(requestStatus == '7'){
                        $("#btnApprove").hide();
                        $("#btnDisapprove").hide();
                        $("#reason_label").show();
                        $("#reason_details").show();
                    }
                    if(requestStatus == '1'|| requestStatus == '2'|| requestStatus == '3' || requestStatus == '4' || requestStatus == '5' || requestStatus > 7){
                        $("#btnDelete").hide();
                    }
                    if(requestStatus == '2' || requestStatus == '3' || requestStatus == '4' || requestStatus == '6' || requestStatus == '7' || requestStatus > 7){
                        $("#proceed_label").hide();
                        $("#btnProceed").hide();
                    }
                    if(requestStatus == '2' || requestStatus == '5'){
                        $("#schedItemsModal").show();
                        $.ajax({
                            type:'get', 
                            url:'/checkProcessed', 
                            data:{
                                'request_number': $('#request_num_details').val()
                            }, 
                            success: function(data){
                                if(data != 0){
                                    $("#receivedItemsModal").show();
                                }
                            },
                            error: function(data){
                                if(data.status == 401){
                                    window.location.href = '/stockrequest';
                                }
                                alert(data.responseText);
                            }
                        });
                    }
                    if(requestStatus == '30' || requestStatus == '31'){
                        $("#transitItemsModal").show();
                        $("#modalheader").html('FOR STAGING ITEM DETAILS');
                        $.ajax({
                            type:'get', 
                            url:'/checkProcessed', 
                            data:{
                                'request_number': $('#request_num_details').val()
                            }, 
                            success: function(data){
                                if(data != 0){
                                    $("#receivedItemsModal").show();
                                }
                            },
                            error: function(data){
                                if(data.status == 401){
                                    window.location.href = '/stockrequest';
                                }
                                alert(data.responseText);
                            }
                        });
                        if(req_by_id == $('#current_user').val()){
                            $('#receive_label').show();
                            $("#reissue_label").show();
                            $(".btnReissue").show();
                            $('#btnReceive').val('SEND CONFIRMATION');
                            $('#receive_text').html('Please select item/s for receiving on the list below then click <strong>SEND CONFIRMATION</strong> button.');
                        }
                    }
                    if(requestStatus == '32'){
                        $("#schedItemsModal").show();
                        $("#prepheader").html('FOR INSPECTION ITEM DETAILS');
                        $.ajax({
                            type:'get', 
                            url:'/checkProcessed', 
                            data:{
                                'request_number': $('#request_num_details').val()
                            }, 
                            success: function(data){
                                if(data != 0){
                                    $("#receivedItemsModal").show();
                                }
                            },
                            error: function(data){
                                if(data.status == 401){
                                    window.location.href = '/stockrequest';
                                }
                                alert(data.responseText);
                            }
                        });
                        $('.btnTransit').hide();
                        if(req_by_id == $('#current_user').val()){
                            $('#defective_label').show();
                            $('#btnStaging').show();
                        }
                    }
                    if(requestStatus == '3' || requestStatus == '4'){
                        $("#transitItemsModal").show();
                        if(req_type_id == '2' || req_type_id == '3'){
                            $('#btnReceive').val('SEND CONFIRMATION');
                            $('#receive_text').html('Please select item/s for receiving on the list below then click <strong>SEND CONFIRMATION</strong> button.');
                        }
                        if(req_type_id == '4'){
                            $('#btnReceive').hide();
                            $('#btnReceiveRpl').show();
                        }
                        if($("#current_role").val() == 'sales'){
                            $('#receive_label').show();
                        }
                        $.ajax({
                            type:'get', 
                            url:'/checkProcessed', 
                            data:{
                                'request_number': $('#request_num_details').val()
                            }, 
                            success: function(data){
                                if(data != 0){
                                    $("#receivedItemsModal").show();
                                }
                            },
                            error: function(data){
                                if(data.status == 401){
                                    window.location.href = '/stockrequest';
                                }
                                alert(data.responseText);
                            }
                        });
                    }
                    if(requestStatus == '8' || requestStatus == '9'|| requestStatus == '12' || requestStatus == '13' || requestStatus == '14' || requestStatus == '19' || requestStatus == '20' || requestStatus == '24'){
                        var rcv_url = 'receivedItems';
                        $("#transitItemsModal").show();
                        $(".btnReceive").hide();
                        document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
                        if(req_type_id == '3' && requestStatus == '9'){
                            $('#demoreceive_label').show();
                            $("#btnSale").show();
                            $("#btnReturn").show();
                        }
                        if(requestStatus == '12' || requestStatus == '20'){
                            document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        }
                        if(requestStatus == '13'){
                            document.getElementById('modalheader').innerHTML = 'ASSEMBLED ITEM PARTS DETAILS';
                            $("#warehouse_label").show();
                            $("#warehouse_details").show();
                            $("#warehouse_note").show();
                            $(".btnReceiveAssembled").show();
                            $("#btnHideDetails").show();
                            $("#divAssembly").show();
                            $("#request_info").hide();
                            $("#transitItemsModal").hide();
                        }
                        if(requestStatus == '14'){
                            document.getElementById('modalheader').innerHTML = 'ASSEMBLED ITEM PARTS DETAILS';
                            $('#asmItemsModal').show();
                        }
                        if(requestStatus == '19'){
                            document.getElementById('modalheader').innerHTML = 'REPLACEMENT ITEM DETAILS';
                        }
                    }
                    if(requestStatus == '10' || requestStatus == '27' || requestStatus == '29'){
                        var rcv_url = 'receivedItems';
                        $("#transitItemsModal").show();
                        $(".soldShow").show();
                        $(".btnReceive").hide();
                        $("#btnSale").hide();
                        $("#btnReturn").hide();
                        document.getElementById('modalheader').innerHTML = 'SOLD ITEM DETAILS';
                        if(requestStatus == '27'){
                            var ajax_url = '/retItems';
                            $("#incItemsModal").show();
                            document.getElementById('incmodalheader').innerHTML = 'RETURNED ITEM DETAILS';
                            $('#retreceive_label').show();
                            $(".btnReceiveReturned").show();
                        }
                    }
                    if(requestStatus == '28'){
                        var rcv_url = 'receivedItems';
                        $("#transitItemsModal").show();
                        $(".soldShow").show();
                        $(".btnReceive").hide();
                        $("#btnSale").hide();
                        $("#btnReturn").hide();
                        document.getElementById('modalheader').innerHTML = 'SOLD ITEM DETAILS';
                        var ajax_url = '/retItems';
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE RETURNED ITEM DETAILS';
                        $('#retreceive_label').show();
                        $(".btnReceiveReturned").show();
                    }
                    if(requestStatus == '11'){
                        var ajax_url = '/retItems';
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'RETURNED ITEM DETAILS';
                        $('#retreceive_label').show();
                        $(".btnReceiveReturned").show();
                    }
                    if(requestStatus == '25'){
                        var ajax_url = '/retItems';
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE RETURNED ITEM DETAILS';
                        $('#retreceive_label').show();
                        $(".btnReceiveReturned").show();
                    }
                    if(requestStatus == '15' || requestStatus == '33'){
                        var ajax_url = '/incItems';
                        $("#receivedItemsModal").show();
                        document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        if(req_type_id != '5'){
                            document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
                        }
                        if(req_type_id == '8'){
                            document.getElementById('receivedheader').innerHTML = 'FOR STAGING ITEM DETAILS';
                        }
                        $("#incItemsModal").show();
                        $(".divResched").show();
                        // $('#resched').attr('max', maxDate);
                        $("#btnReschedule").show();
                        if($("#current_role").val() == 'sales'){
                            $(".divResched").hide();
                        }
                    }
                    if(requestStatus == '16'){
                        var ajax_url = '/incItems';
                        $("#receivedItemsModal").show();
                        document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        if(req_type_id != '5'){
                            document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
                        }
                        if(req_type_id == '8'){
                            document.getElementById('receivedheader').innerHTML = 'FOR STAGING ITEM DETAILS';
                        }
                        $("#incItemsModal").show();
                        $(".divResched1").show();
                        $(".btnTransit").show();
                    }
                    if(requestStatus == '17' || requestStatus == '34'){
                        var ajax_url = '/incItems';
                        $("#receivedItemsModal").show();
                        document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        if(req_type_id != '5'){
                            document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
                        }
                        if(req_type_id == '8'){
                            document.getElementById('receivedheader').innerHTML = 'FOR STAGING ITEM DETAILS';
                        }
                        $("#incItemsModal").show();
                        $(".divResched1").show();
                        $("#incFooter").hide();
                        if(req_type_id == '7'){
                            $('#increceive_label').show();
                            $("#inc2Footer").show();
                        }
                        if(requestStatus == '34'){
                            $('#btnReceiveInc').val('SEND CONFIRMATION');
                        }
                    }
                    if((requestStatus == '17' || requestStatus == '34') && $("#current_role").val() == 'sales'){
                        $('#increceive_label').show();
                        $("#inc2Footer").show();
                        if(req_type_id == '4'){
                            $("#btnReceiveInc").hide();
                            $("#btnReceiveRplInc").show();
                        }
                    }
                    if(requestStatus == '18'){
                        var ajax_url = '/dfcItems';
                        $("#receivedItemsModal").show();
                        document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        if(req_type_id != '5'){
                            document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
                        }
                        if(req_type_id == '8'){
                            document.getElementById('receivedheader').innerHTML = 'FOR STAGING ITEM DETAILS';
                        }
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'DEFECTIVE ITEM DETAILS';
                        $("#request_info").hide();
                        $("#receivedItemsModal").hide();
                        $(".rcvShow").show();
                        $("#showMore").show();
                        $("#showLess").hide();
                        if($("#current_role").val() == 'sales'){
                            $("#request_info").show();
                            $("#receivedItemsModal").show();
                        }
                    }
                    if(requestStatus == '21'){
                        var ajax_url = '/incdfcItems';
                        $("#receivedItemsModal").show();
                        document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        if(req_type_id != '5'){
                            document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
                        }
                        if(req_type_id == '8'){
                            document.getElementById('receivedheader').innerHTML = 'FOR STAGING ITEM DETAILS';
                        }
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE DEFECTIVE ITEM DETAILS';
                        $("#request_info").hide();
                        $("#receivedItemsModal").hide();
                        $(".rcvShow").show();
                        $("#showMore").show();
                        $("#showLess").hide();
                        if($("#current_role").val() == 'sales'){
                            $("#request_info").show();
                            $("#receivedItemsModal").show();
                        }
                    }
                    if(requestStatus == '22'){
                        var rcv_url = 'receivedItems';
                        var included = 'no';
                        $("#transitItemsModal").show();
                        $(".prephide").hide();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        if(req_type_id != '5'){
                            document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
                        }
                        if(req_type_id == '8'){
                            document.getElementById('modalheader').innerHTML = 'FOR STAGING ITEM DETAILS';
                        }
                        $(".pendshow").show();
                    }
                    if(requestStatus == '23'){
                        var ajax_url = '/incItems';
                        $("#receivedItemsModal").show();
                        document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        if(req_type_id != '5'){
                            document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
                        }
                        if(req_type_id == '8'){
                            document.getElementById('receivedheader').innerHTML = 'FOR STAGING ITEM DETAILS';
                        }
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE REPLACEMENT ITEM DETAILS';
                        $("#incFooter").hide();
                    }
                    if(requestStatus == '24' || requestStatus == '31'){
                        $("#proceed_label").show();
                        $("#btnProceed").show();
                    }
                    if(requestStatus == '6' && value.prepared_by > 0){
                        $("#btnDelete").hide();
                        $("#sd1").show();
                        $("#sd2").remove();
                    }
                    else{
                        if(req_by_id == $('#current_user').val() && $("#current_role").val() == 'sales'){
                            $("#sd2").show();
                            $("#sd1").remove();
                        }
                        else{
                            $("#btnDelete").hide();
                            $("#sd1").show();
                            $("#sd2").remove();
                        }
                    }
                    if(req_type_id == '7' && requestStatus == '1'){
                        $("#btnDelete").show();
                    }
                    if(requestStatus == '1'|| requestStatus == '5' || requestStatus == '6' || requestStatus == '24' || requestStatus == '31'){
                        var targetStockDetails = [6,7,8,9,10];
                        var targetStockDetails1 = [5];
                        var targetStockDetails2 = [5,6];
                        if(requestStatus == '6' && $("#current_role").val() == 'sales' && $('#current_user').val() == req_by_id){
                            targetStockDetails2 = [6];
                        }
                    }
                    else{
                        var targetStockDetails = [4,5,6,7,8,9,10,11,12];
                        var targetStockDetails1 = [4,5];
                        var targetStockDetails2 = [4,5,6];
                    }
                    if($("#current_role").val() == 'sales'){
                        $("#proceed_label").hide();
                    }
                
                $('.modal-body').html();
                $('#detailsStockRequest').modal('show');
                    
                $('table.stockDetails').dataTable().fnDestroy();    
                $('table.stockDetails').DataTable({
                    columnDefs: [
                        {
                            "targets": targetStockDetails,
                            "visible": false,
                            "searchable": false
                        },
                        {
                            "render": function(data, type, full, meta){
                                return "<div style='color: red;'>"+data+"</div>";
                            },
                            "targets": [11,12]
                        }
                    ],
                    searching: false,
                    paging: false,
                    ordering: false,
                    info: false,
                    autoWidth: false,
                    language:{
                        emptyTable: "No data available in table",
                        processing: "Loading...",
                    },
                    serverSide: true,
                    ajax:{
                        url: '/requestDetails',
                        data:{
                            reqnum: req_num,
                        },
                        dataType: 'json',
                        error: function(data){
                            if(data.status == 401){
                                window.location.href = '/stockrequest';
                            }
                            alert(data.responseText);
                        },
                    },
                    order: [],
                    columns: [
                        { data: 'prodcode', width: '100px' },
                        { data: 'item', width: '120px' },
                        { data: 'uom' },
                        { data: 'quantity' },
                        { data: 'pending' },
                        { data: 'qtystock', width: '60px' },
                        { data: 'item_id' },
                        { data: 'qtya1' },
                        { data: 'qtya2' },
                        { data: 'qtya3' },
                        { data: 'qtya4' },
                        { data: 'qtybal' },
                        { data: 'qtymal' }
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
                
                $('table.stockDetails1').dataTable().fnDestroy();    
                $('table.stockDetails1').DataTable({
                    columnDefs: [
                        {
                            "targets": targetStockDetails1,
                            "visible": false,
                            "searchable": false
                        }
                    ],
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
                        },
                        dataType: 'json',
                        error: function(data){
                            if(data.status == 401){
                                window.location.href = '/stockrequest';
                            }
                            alert(data.responseText);
                        },
                    },
                    order: [],
                    columns: [
                        { data: 'prodcode' },
                        { data: 'item' },
                        { data: 'uom' },
                        { data: 'quantity' },
                        { data: 'pending' },
                        { data: 'item_id' }
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
                
                $('table.stockDetails2').dataTable().fnDestroy();    
                $('table.stockDetails2').DataTable({
                    columnDefs: [
                        {
                            "targets": targetStockDetails2,
                            "visible": false,
                            "searchable": false
                        },
                        {   
                            "render": function(data, type, row, meta){
                                    return '<button style="zoom: 80%;" class="btn btn-danger bp btndelItem" id="'+ meta.row +'">REMOVE</button>';
                            },
                            "defaultContent": '',
                            "data": null,
                            "targets": [5]
                        }
                    ],
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
                        },
                        dataType: 'json',
                        error: function(data){
                            if(data.status == 401){
                                window.location.href = '/stockrequest';
                            }
                            alert(data.responseText);
                        },
                    },
                    order: [],
                    columns: [
                        { data: 'prodcode' },
                        { data: 'item' },
                        { data: 'uom' },
                        { data: 'quantity' },
                        { data: 'pending' },
                        { data: 'item_id' },
                        { data: 'item_id' }
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
            
                $('table.receivedItems').DataTable({
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
                        url: '/receivedItems',
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
                        { data: 'serial' }
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
            
                $('table.schedItems').DataTable({
                    columnDefs: [
                        {
                            "targets": [5,7],
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
                        url: '/schedItems',
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
                        { data: 'id' },
                        {
                            data: 'id',
                            "render": function(data, type, row, meta){
                                if(row.uom == 'Unit' && row.serialize == 'YES'){
                                    return '<button style="zoom: 80%;" class="btn btn-success bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                                }
                                else{
                                    return '';
                                }
                            }
                        },
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
            
                $('table.schedItems1').dataTable().fnDestroy();
                $('table.schedItems1').DataTable({
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
                        url: '/schedItems',
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
            
                $('table.transItems').dataTable().fnDestroy();
                $('table.transItems').DataTable({
                    columnDefs: [
                        {
                            "targets": [5],
                            "visible": false,
                            "searchable": false
                        },
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
            
                if(requestStatus == '3' || requestStatus == '4'){
                    $('table.transItems1').dataTable().fnDestroy();
                    $('table.transItems1').DataTable({
                        columnDefs: [
                            {
                                "targets": [5,7],
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
                            {
                                data: 'id',
                                "render": function(data, type, row, meta){
                                    if(row.uom == 'Unit' && row.serialize == 'YES'){
                                        return '<button style="zoom: 80%;" class="btn btn-success bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                                    }
                                    else{
                                        return '';
                                    }
                                }
                            },
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
                else{
                    $('table.transItems1').dataTable().fnDestroy();
                    $('table.transItems1').DataTable({
                        columnDefs: [
                            {
                                "targets": [5,6,7],
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
                            {
                                data: 'id',
                                "render": function(data, type, row, meta){
                                    if(row.uom == 'Unit' && row.serialize == 'YES'){
                                        return '<button style="zoom: 80%;" class="btn btn-success bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                                    }
                                    else{
                                        return '';
                                    }
                                }
                            },
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
            
                if(ajax_url != '/schedItems'){
                    if(($("#current_role").val() == 'admin' || $("#current_role").val() == 'encoder') && (requestStatus == '15' || requestStatus == '16' || requestStatus == '17')){
                        $('table.incItems').dataTable().fnDestroy();
                        $('table.incItems').DataTable({
                            columnDefs: [
                                {
                                    "targets": [5,6,7],
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
                                { data: 'id' },
                                {
                                    data: 'id',
                                    "render": function(data, type, row, meta){
                                        if(row.uom == 'Unit' && row.serialize == 'YES'){
                                            return '<button style="zoom: 80%;" class="btn btn-success bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                                        }
                                        else{
                                            return '';
                                        }
                                    }
                                },
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
                    else{
                        $('table.incItems').dataTable().fnDestroy();
                        $('table.incItems').DataTable({
                            columnDefs: [
                                {
                                    "targets": [5,6,7],
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
                                { data: 'id' },
                                {
                                    data: 'id',
                                    "render": function(data, type, row, meta){
                                        if(row.uom == 'Unit' && row.serialize == 'YES'){
                                            return '<button style="zoom: 80%;" class="btn btn-success bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                                        }
                                        else{
                                            return '';
                                        }
                                    }
                                },
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
                }

                if(requestStatus == '14'){
                    $.ajax({
                        type:'get', 
                        url:'/getReceive', 
                        data:{
                            'request_number': $('#request_num_details').val()
                        }, 
                        success: function(data){
                            document.getElementById("recby").value = data.recby;
                            document.getElementById("recsched").value = moment(data.recsched).format('dddd, MMMM DD, YYYY, h:mm A');
                        },
                        error: function(data){
                            if(data.status == 401){
                                window.location.href = '/stockrequest';
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

$('#stockrequestTable tbody').on('click', 'tr', function(){
    $('#detailsStockRequest').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table = $('table.stockrequestTable').DataTable(); 
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
        $('#reqnum').val(req_num);
    var asm_req_num = value.assembly_reqnum;
        $('#asm_request_num_details').val(asm_req_num);
    var req_by = value.req_by;
        $('#requested_by_details').val(req_by);
    var req_by_id = value.user_id;
        $('#requested_by_id_details').val(req_by_id);
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
        $('#reprep_by').val(prep_by);
    var sched = value.sched;
        sched = moment(sched).format('dddd, MMMM DD, YYYY');
        $('#sched').val(sched);
        $('#sched1').val(sched);
        $('#resched1').val(sched);
    var orderID = value.orderID;
        $('#orderID_details').val(orderID);
    var client_name = value.client_name;
        $('#client_name_details').val(client_name);
    var location_name = value.location;
        $('#location_details').val(location_name);
    var contact = value.contact;
        $('#contact_details').val(contact);
    var remarks = value.remarks;
        $('#remarks_details').val(remarks);
    var reference = value.reference;
        $('#reference_details').val(reference);
    var reason = value.reason;
        $('#reason_details').val(reason);

        if($("#current_role").val() == 'accounting' && (req_type_id == '1' || req_type_id == '4' || req_type_id == '5' || req_type_id == '7')){
            window.location.href = '/stockrequest';
        }
        if(($("#current_role").val() == 'sales' || $("#current_role").val() == 'approver - sales') && (req_type_id == '1' || (req_type_id == '4' && req_by_id != $('#current_user').val()) || req_type_id == '5' || req_type_id == '6' || req_type_id == '7')){
            window.location.href = '/stockrequest';
        }
        if($("#current_role").val() == 'sales' && $('#current_user').val() != req_by_id){
            window.location.href = '/stockrequest';
        }

        if($("#current_role").val() == 'sales' && (requestStatus == '6' || requestStatus == '7')){
            $('#btnEditDetails').show();
            $('.btnCancelDetails').show();
            
            $(document).on('click', '#btnEditDetails', function(){
                editMode = true;
                $('#btnEditDetails').hide();
                $('#btnSaveChanges').show();

                $('#needdate').val(value.needdate);
                $('#client_name').val(client_name);
                $('#location').val(location_name);
                $('#contact').val(contact);
                $('#remarks').val(remarks);
                $('#reference').val(reference);

                if(requestStatus == '7'){
                    $('#lblReupload').css({"margin-left": "0px"});
                    $('.classReupload').css({"margin-top": "-56px"});
                }
                $('#needdate_details').attr('type', 'date');
                $('#needdate_details').val(value.needdate);
                $('#needdate_details').prop('readonly', false);
                $('#client_name_details').prop('readonly', false);
                $('#location_details').prop('readonly', false);
                $('#remarks_details').prop('readonly', false);
                $('#contact_details').prop('readonly', false);
                reference =  reference.replaceAll(', ', '\n');
                $('#reference_details').val(reference);
                $('#reference_details').prop('readonly', false);
                $('#btnRemoveAttachment').show();
                $('#action').val('EDIT');
            });
        }
        else{
            $('#action').val('');
        }

        if((($("#current_role").val() == 'sales' || $("#current_role").val() == 'approver - sales' || $("#current_role").val() == 'accounting') && (req_type_id == '2' || (req_type_id == '3' && (requestStatus == '10' || requestStatus >= 27)) || req_type_id == '6' || req_type_id == '8')) || 
        ($("#current_role").val() == 'admin' || $("#current_role").val() == 'encoder' || $("#current_role").val() == 'viewer') && req_type_id == '7'){
            var reference_uploads = value.reference_upload.slice(1).slice(0,-1);
            var reference_attachments = decodeHtml(reference_uploads).split(',');
            for(var i=0; i < reference_attachments.length; i++){
                var btn = document.createElement("input");
                btn.setAttribute("id", "btnSlide"+(i+1));
                btn.setAttribute("value", i+1);
                btn.setAttribute("type", "button");
                btn.setAttribute("class", "w3-button demo");
                btn.setAttribute("onclick", "currentDiv("+(i+1)+")");
                var img = document.createElement("img");
                img.setAttribute("id", "reference_attachment"+i);
                img.setAttribute("class", "mySlides");
                var imgx = document.createElement("img");
                imgx.setAttribute("id", "reference_hidden"+i);
                document.getElementById("slidesBtn").appendChild(btn);
                document.getElementById("slidesContent").appendChild(img);
                document.getElementById("hiddenContent").appendChild(imgx);
                var reference_attachment = reference_attachments[i].replace(/\"/g,'');
    
                $.ajax({
                    type: 'get',
                    url: '/checkURL',
                    async: false,
                    data:{
                        'reference': reference_attachment,
                        'check': 'beta'
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('#reference_attachment'+i).attr('src', data.returnURL).show();
                            $('#reference_attachment'+i).css({'width': '100%'});
                        }
                        else{
                            $.ajax({
                                type: 'get',
                                url: '/checkURL',
                                async: false,
                                data:{
                                    'reference': reference_attachment,
                                    'check': 'live'
                                },
                                success: function(data){
                                    if(data.result == 'true'){
                                        $('#reference_attachment'+i).attr('src', data.returnURL).show();
                                        $('#reference_attachment'+i).css({'width': '100%'});
                                    }
                                    else{
                                        $('#reference_attachment'+i).attr('src', 'uploads/NA.png').show();
                                        $('#reference_attachment'+i).css({'width': '25%'});
                                    }
                                }
                            });
                        }
                    }
                });
            }
            $('#btnSlide1').click();
            if(reference_attachments.length == 1){
                $("#slidesCtrl").hide();
            }
        }

        var ajax_url = '/schedItems';
        var rcv_url = '/schedItems';
        var included = 'yes';

        if(req_type_id == '7'){
            $('#txtAttachment').html('ATTACHMENT REQUEST FORM');
            $('.assethide').hide();
            $('.assetshow').show();
            $('#requested_by_details').val(value.asset_reqby);
            $('#approved_by_details').val(value.asset_apvby);
            if(requestStatus == '3'){
                $('#prep_by1_label').html('Prepared By');
                $('#sched1_label').html('Date Prepared');
                $('#receive_label').show();
                $('.btnReceive').show();
                $('#btnReceive').val('SEND CONFIRMATION');
                $('#receive_text').html('Please select item/s for receiving on the list below then click <strong>SEND CONFIRMATION</strong> button.');
            }
        }
        if(req_type_id != '2' && req_type_id != '8'){
            $(".sales_details").hide();
            if(req_type_id == '6'){
                $("#btnShowAttachment").show();
                $("#btnShowAttachment").css({"margin-top": "35px", "margin-left": "0px"});
                $("#btnHideAttachment").css({"margin-top": "35px", "margin-left": "0px"});
            }
        }
        if(req_type_id == '1' || req_type_id == '5' || req_type_id == '6'){
            $("#client_name_label").hide();
            $("#client_name_details").hide();
            $("#location_label").hide();
            $("#location_details").hide();
            $("#contact_label").hide();
            $("#contact_details").hide();
            $("#remarks_label").hide();
            $("#remarks_details").hide();
            $("#reference_label").hide();
            $("#reference_details").hide();
        }
        if(req_type_id == '6'){
            $("#orderID_label").show();
            $("#orderID_details").show();
        }
        if(req_type_id == '5'){
            $("#proceed_label").hide();
            $("#item_desc_label").show();
            $("#item_desc_details").show();
            $("#qty_label").show();
            $("#qty_details").show();
        }
        if(req_type_id == '4'){
            $(".dfchide").hide();
            $(".dfcshow").show();
        }
        if(req_type_id == '4' && requestStatus == '1'){
            $.ajax({ 
                type:'get', 
                url:'/checkStatus', 
                data:{
                    'assembly_reqnum': $('#asm_request_num_details').val()
                }, 
                success: function(data){
                    if(data == '18' || data == '21'){
                        $("#warning").hide();
                        $("#btnProceed").hide();
                        $(".rcvDef").show();
                    }
                }
            });
        }
        if(requestStatus != '6'){
            $("#btnApprove").hide();
            $("#btnDisapprove").hide();
        }
        if(requestStatus == '7'){
            $("#btnApprove").hide();
            $("#btnDisapprove").hide();
            $("#reason_label").show();
            $("#reason_details").show();
        }
        if(requestStatus == '1'|| requestStatus == '2'|| requestStatus == '3' || requestStatus == '4' || requestStatus == '5' || requestStatus > 7){
            $("#btnDelete").hide();
        }
        if(requestStatus == '2' || requestStatus == '3' || requestStatus == '4' || requestStatus == '6' || requestStatus == '7' || requestStatus > 7){
            $("#proceed_label").hide();
            $("#btnProceed").hide();
        }
        if(requestStatus == '2' || requestStatus == '5'){
            $("#schedItemsModal").show();
            $.ajax({
                type:'get', 
                url:'/checkProcessed', 
                data:{
                    'request_number': $('#request_num_details').val()
                }, 
                success: function(data){
                    if(data != 0){
                        $("#receivedItemsModal").show();
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stockrequest';
                    }
                    alert(data.responseText);
                }
            });
        }
        if(requestStatus == '30' || requestStatus == '31'){
            $("#transitItemsModal").show();
            $("#modalheader").html('FOR STAGING ITEM DETAILS');
            $.ajax({
                type:'get', 
                url:'/checkProcessed', 
                data:{
                    'request_number': $('#request_num_details').val()
                }, 
                success: function(data){
                    if(data != 0){
                        $("#receivedItemsModal").show();
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stockrequest';
                    }
                    alert(data.responseText);
                }
            });
            if(req_by_id == $('#current_user').val()){
                $('#receive_label').show();
                $("#reissue_label").show();
                $(".btnReissue").show();
                $('#btnReceive').val('SEND CONFIRMATION');
                $('#receive_text').html('Please select item/s for receiving on the list below then click <strong>SEND CONFIRMATION</strong> button.');
            }
        }
        if(requestStatus == '32'){
            $("#schedItemsModal").show();
            $("#prepheader").html('FOR INSPECTION ITEM DETAILS');
            $.ajax({
                type:'get', 
                url:'/checkProcessed', 
                data:{
                    'request_number': $('#request_num_details').val()
                }, 
                success: function(data){
                    if(data != 0){
                        $("#receivedItemsModal").show();
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stockrequest';
                    }
                    alert(data.responseText);
                }
            });
            $('.btnTransit').hide();
            if(req_by_id == $('#current_user').val()){
                $('#defective_label').show();
                $('#btnStaging').show();
            }
        }
        if(requestStatus == '3' || requestStatus == '4'){
            $("#transitItemsModal").show();
            if(req_type_id == '2' || req_type_id == '3'){
                $('#btnReceive').val('SEND CONFIRMATION');
                $('#receive_text').html('Please select item/s for receiving on the list below then click <strong>SEND CONFIRMATION</strong> button.');
            }
            if(req_type_id == '4'){
                $('#btnReceive').hide();
                $('#btnReceiveRpl').show();
            }
            if($("#current_role").val() == 'sales'){
                $('#receive_label').show();
            }
            $.ajax({
                type:'get', 
                url:'/checkProcessed', 
                data:{
                    'request_number': $('#request_num_details').val()
                }, 
                success: function(data){
                    if(data != 0){
                        $("#receivedItemsModal").show();
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/stockrequest';
                    }
                    alert(data.responseText);
                }
            });
        }
        if(requestStatus == '8' || requestStatus == '9'|| requestStatus == '12' || requestStatus == '13' || requestStatus == '14' || requestStatus == '19' || requestStatus == '20' || requestStatus == '24'){
            var rcv_url = 'receivedItems';
            $("#transitItemsModal").show();
            $(".btnReceive").hide();
            document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
            if(req_type_id == '3' && requestStatus == '9'){
                $('#demoreceive_label').show();
                $("#btnSale").show();
                $("#btnReturn").show();
            }
            if(requestStatus == '12' || requestStatus == '20'){
                document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            }
            if(requestStatus == '13'){
                document.getElementById('modalheader').innerHTML = 'ASSEMBLED ITEM PARTS DETAILS';
                $("#warehouse_label").show();
                $("#warehouse_details").show();
                $("#warehouse_note").show();
                $(".btnReceiveAssembled").show();
                $("#btnHideDetails").show();
                $("#divAssembly").show();
                $("#request_info").hide();
                $("#transitItemsModal").hide();
            }
            if(requestStatus == '14'){
                document.getElementById('modalheader').innerHTML = 'ASSEMBLED ITEM PARTS DETAILS';
                $('#asmItemsModal').show();
            }
            if(requestStatus == '19'){
                document.getElementById('modalheader').innerHTML = 'REPLACEMENT ITEM DETAILS';
            }
        }
        if(requestStatus == '10' || requestStatus == '27' || requestStatus == '29'){
            var rcv_url = 'receivedItems';
            $("#transitItemsModal").show();
            $(".soldShow").show();
            $(".btnReceive").hide();
            $("#btnSale").hide();
            $("#btnReturn").hide();
            document.getElementById('modalheader').innerHTML = 'SOLD ITEM DETAILS';
            if(requestStatus == '27'){
                var ajax_url = '/retItems';
                $("#incItemsModal").show();
                document.getElementById('incmodalheader').innerHTML = 'RETURNED ITEM DETAILS';
                $('#retreceive_label').show();
                $(".btnReceiveReturned").show();
            }
        }
        if(requestStatus == '28'){
            var rcv_url = 'receivedItems';
            $("#transitItemsModal").show();
            $(".soldShow").show();
            $(".btnReceive").hide();
            $("#btnSale").hide();
            $("#btnReturn").hide();
            document.getElementById('modalheader').innerHTML = 'SOLD ITEM DETAILS';
            var ajax_url = '/retItems';
            $("#incItemsModal").show();
            document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE RETURNED ITEM DETAILS';
            $('#retreceive_label').show();
            $(".btnReceiveReturned").show();
        }
        if(requestStatus == '11'){
            var ajax_url = '/retItems';
            $("#incItemsModal").show();
            document.getElementById('incmodalheader').innerHTML = 'RETURNED ITEM DETAILS';
            $('#retreceive_label').show();
            $(".btnReceiveReturned").show();
        }
        if(requestStatus == '25'){
            var ajax_url = '/retItems';
            $("#incItemsModal").show();
            document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE RETURNED ITEM DETAILS';
            $('#retreceive_label').show();
            $(".btnReceiveReturned").show();
        }
        if(requestStatus == '15' || requestStatus == '33'){
            var ajax_url = '/incItems';
            $("#receivedItemsModal").show();
            document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            if(req_type_id != '5'){
                document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
            }
            if(req_type_id == '8'){
                document.getElementById('receivedheader').innerHTML = 'FOR STAGING ITEM DETAILS';
            }
            $("#incItemsModal").show();
            $(".divResched").show();
            // $('#resched').attr('max', maxDate);
            $("#btnReschedule").show();
            if($("#current_role").val() == 'sales'){
                $(".divResched").hide();
            }
        }
        if(requestStatus == '16'){
            var ajax_url = '/incItems';
            $("#receivedItemsModal").show();
            document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            if(req_type_id != '5'){
                document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
            }
            if(req_type_id == '8'){
                document.getElementById('receivedheader').innerHTML = 'FOR STAGING ITEM DETAILS';
            }
            $("#incItemsModal").show();
            $(".divResched1").show();
            $(".btnTransit").show();
        }
        if(requestStatus == '17' || requestStatus == '34'){
            var ajax_url = '/incItems';
            $("#receivedItemsModal").show();
            document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            if(req_type_id != '5'){
                document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
            }
            if(req_type_id == '8'){
                document.getElementById('receivedheader').innerHTML = 'FOR STAGING ITEM DETAILS';
            }
            $("#incItemsModal").show();
            $(".divResched1").show();
            $("#incFooter").hide();
            if(req_type_id == '7'){
                $('#increceive_label').show();
                $("#inc2Footer").show();
            }
            if(requestStatus == '34'){
                $('#btnReceiveInc').val('SEND CONFIRMATION');
            }
        }
        if((requestStatus == '17' || requestStatus == '34') && $("#current_role").val() == 'sales'){
            $('#increceive_label').show();
            $("#inc2Footer").show();
            if(req_type_id == '4'){
                $("#btnReceiveInc").hide();
                $("#btnReceiveRplInc").show();
            }
        }
        if(requestStatus == '18'){
            var ajax_url = '/dfcItems';
            $("#receivedItemsModal").show();
            document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            if(req_type_id != '5'){
                document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
            }
            if(req_type_id == '8'){
                document.getElementById('receivedheader').innerHTML = 'FOR STAGING ITEM DETAILS';
            }
            $("#incItemsModal").show();
            document.getElementById('incmodalheader').innerHTML = 'DEFECTIVE ITEM DETAILS';
            $("#request_info").hide();
            $("#receivedItemsModal").hide();
            $(".rcvShow").show();
            $("#showMore").show();
            $("#showLess").hide();
            if($("#current_role").val() == 'sales'){
                $("#request_info").show();
                $("#receivedItemsModal").show();
            }
        }
        if(requestStatus == '21'){
            var ajax_url = '/incdfcItems';
            $("#receivedItemsModal").show();
            document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            if(req_type_id != '5'){
                document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
            }
            if(req_type_id == '8'){
                document.getElementById('receivedheader').innerHTML = 'FOR STAGING ITEM DETAILS';
            }
            $("#incItemsModal").show();
            document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE DEFECTIVE ITEM DETAILS';
            $("#request_info").hide();
            $("#receivedItemsModal").hide();
            $(".rcvShow").show();
            $("#showMore").show();
            $("#showLess").hide();
            if($("#current_role").val() == 'sales'){
                $("#request_info").show();
                $("#receivedItemsModal").show();
            }
        }
        if(requestStatus == '22'){
            var rcv_url = 'receivedItems';
            var included = 'no';
            $("#transitItemsModal").show();
            $(".prephide").hide();
            document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            if(req_type_id != '5'){
                document.getElementById('modalheader').innerHTML = 'RECEIVED ITEM DETAILS';
            }
            if(req_type_id == '8'){
                document.getElementById('modalheader').innerHTML = 'FOR STAGING ITEM DETAILS';
            }
            $(".pendshow").show();
        }
        if(requestStatus == '23'){
            var ajax_url = '/incItems';
            $("#receivedItemsModal").show();
            document.getElementById('receivedheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
            if(req_type_id != '5'){
                document.getElementById('receivedheader').innerHTML = 'RECEIVED ITEM DETAILS';
            }
            if(req_type_id == '8'){
                document.getElementById('receivedheader').innerHTML = 'FOR STAGING ITEM DETAILS';
            }
            $("#incItemsModal").show();
            document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE REPLACEMENT ITEM DETAILS';
            $("#incFooter").hide();
        }
        if(requestStatus == '24' || requestStatus == '31'){
            $("#proceed_label").show();
            $("#btnProceed").show();
        }
        if(requestStatus == '6' && value.prepared_by > 0){
            $("#btnDelete").hide();
            $("#sd1").show();
            $("#sd2").remove();
        }
        else{
            if(req_by_id == $('#current_user').val() && $("#current_role").val() == 'sales'){
                $("#sd2").show();
                $("#sd1").remove();
            }
            else{
                $("#btnDelete").hide();
                $("#sd1").show();
                $("#sd2").remove();
            }
        }
        if(req_type_id == '7' && requestStatus == '1'){
            $("#btnDelete").show();
        }
        if(requestStatus == '1'|| requestStatus == '5' || requestStatus == '6' || requestStatus == '24' || requestStatus == '31'){
            var targetStockDetails = [6,7,8,9,10];
            var targetStockDetails1 = [5];
            var targetStockDetails2 = [5,6];
            if(requestStatus == '6' && $("#current_role").val() == 'sales' && $('#current_user').val() == req_by_id){
                targetStockDetails2 = [6];
            }
        }
        else{
            var targetStockDetails = [4,5,6,7,8,9,10,11,12];
            var targetStockDetails1 = [4,5];
            var targetStockDetails2 = [4,5,6];
        }
        if($("#current_role").val() == 'sales'){
            $("#proceed_label").hide();
        }

    $('.modal-body').html();
    $('#detailsStockRequest').modal('show');
        
    $('table.stockDetails').dataTable().fnDestroy();    
    $('table.stockDetails').DataTable({
        columnDefs: [
            {
                "targets": targetStockDetails,
                "visible": false,
                "searchable": false
            },
            {
                "render": function(data, type, full, meta){
                    return "<div style='color: red;'>"+data+"</div>";
                },
                "targets": [11,12]
            }
        ],
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        autoWidth: false,
        language:{
            emptyTable: "No data available in table",
            processing: "Loading...",
        },
        serverSide: true,
        ajax:{
            url: '/requestDetails',
            data:{
                reqnum: req_num,
            },
            dataType: 'json',
            error: function(data){
                if(data.status == 401){
                    window.location.href = '/stockrequest';
                }
                alert(data.responseText);
            },
        },
        order: [],
        columns: [
            { data: 'prodcode', width: '100px' },
            { data: 'item', width: '120px' },
            { data: 'uom' },
            { data: 'quantity' },
            { data: 'pending' },
            { data: 'qtystock', width: '60px' },
            { data: 'item_id' },
            { data: 'qtya1' },
            { data: 'qtya2' },
            { data: 'qtya3' },
            { data: 'qtya4' },
            { data: 'qtybal' },
            { data: 'qtymal' }
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
    
    $('table.stockDetails1').dataTable().fnDestroy();    
    $('table.stockDetails1').DataTable({
        columnDefs: [
            {
                "targets": targetStockDetails1,
                "visible": false,
                "searchable": false
            }
        ],
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
            },
            dataType: 'json',
            error: function(data){
                if(data.status == 401){
                    window.location.href = '/stockrequest';
                }
                alert(data.responseText);
            },
        },
        order: [],
        columns: [
            { data: 'prodcode' },
            { data: 'item' },
            { data: 'uom' },
            { data: 'quantity' },
            { data: 'pending' },
            { data: 'item_id' }
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
    
    $('table.stockDetails2').dataTable().fnDestroy();    
    $('table.stockDetails2').DataTable({
        columnDefs: [
            {
                "targets": targetStockDetails2,
                "visible": false,
                "searchable": false
            },
            {   
                "render": function(data, type, row, meta){
                        return '<button style="zoom: 80%;" class="btn btn-danger bp btndelItem" id="'+ meta.row +'">REMOVE</button>';
                },
                "defaultContent": '',
                "data": null,
                "targets": [5]
            }
        ],
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
            },
            dataType: 'json',
            error: function(data){
                if(data.status == 401){
                    window.location.href = '/stockrequest';
                }
                alert(data.responseText);
            },
        },
        order: [],
        columns: [
            { data: 'prodcode' },
            { data: 'item' },
            { data: 'uom' },
            { data: 'quantity' },
            { data: 'pending' },
            { data: 'item_id' },
            { data: 'item_id' }
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

    $('table.receivedItems').DataTable({
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
            url: '/receivedItems',
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
            { data: 'serial' }
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

    $('table.schedItems').DataTable({
        columnDefs: [
            {
                "targets": [5,7],
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
            url: '/schedItems',
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
            { data: 'id' },
            {
                data: 'id',
                "render": function(data, type, row, meta){
                    if(row.uom == 'Unit' && row.serialize == 'YES'){
                        return '<button style="zoom: 80%;" class="btn btn-success bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                    }
                    else{
                        return '';
                    }
                }
            },
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

    $('table.schedItems1').dataTable().fnDestroy();
    $('table.schedItems1').DataTable({
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
            url: '/schedItems',
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

    $('table.transItems').dataTable().fnDestroy();
    $('table.transItems').DataTable({
        columnDefs: [
            {
                "targets": [5],
                "visible": false,
                "searchable": false
            },
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

    if(requestStatus == '3' || requestStatus == '4'){
        $('table.transItems1').dataTable().fnDestroy();
        $('table.transItems1').DataTable({
            columnDefs: [
                {
                    "targets": [5,7],
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
                {
                    data: 'id',
                    "render": function(data, type, row, meta){
                        if(row.uom == 'Unit' && row.serialize == 'YES'){
                            return '<button style="zoom: 80%;" class="btn btn-success bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                        }
                        else{
                            return '';
                        }
                    }
                },
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
    else{
        $('table.transItems1').dataTable().fnDestroy();
        $('table.transItems1').DataTable({
            columnDefs: [
                {
                    "targets": [5,6,7],
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
                {
                    data: 'id',
                    "render": function(data, type, row, meta){
                        if(row.uom == 'Unit' && row.serialize == 'YES'){
                            return '<button style="zoom: 80%;" class="btn btn-success bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                        }
                        else{
                            return '';
                        }
                    }
                },
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

    if(ajax_url != '/schedItems'){
        if(($("#current_role").val() == 'admin' || $("#current_role").val() == 'encoder') && (requestStatus == '15' || requestStatus == '16' || requestStatus == '17')){
            $('table.incItems').dataTable().fnDestroy();
            $('table.incItems').DataTable({
                columnDefs: [
                    {
                        "targets": [5,6,7],
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
                    { data: 'id' },
                    {
                        data: 'id',
                        "render": function(data, type, row, meta){
                            if(row.uom == 'Unit' && row.serialize == 'YES'){
                                return '<button style="zoom: 80%;" class="btn btn-success bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                            }
                            else{
                                return '';
                            }
                        }
                    },
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
        else{
            $('table.incItems').dataTable().fnDestroy();
            $('table.incItems').DataTable({
                columnDefs: [
                    {
                        "targets": [5,6,7],
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
                    { data: 'id' },
                    {
                        data: 'id',
                        "render": function(data, type, row, meta){
                            if(row.uom == 'Unit' && row.serialize == 'YES'){
                                return '<button style="zoom: 80%;" class="btn btn-success bp btnEditSerial" id="'+ meta.row +'">EDIT SERIAL</button>';
                            }
                            else{
                                return '';
                            }
                        }
                    },
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
    }

    if(requestStatus == '14'){
        $.ajax({
            type:'get', 
            url:'/getReceive', 
            data:{
                'request_number': $('#request_num_details').val()
            }, 
            success: function(data){
                document.getElementById("recby").value = data.recby;
                document.getElementById("recsched").value = moment(data.recsched).format('dddd, MMMM DD, YYYY, h:mm A');
            },
            error: function(data){
                if(data.status == 401){
                    window.location.href = '/stockrequest';
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

$(document).ready(function(){
    if(($(location).attr('pathname')+window.location.search).includes('submit') == true){
        url = window.location.search;
        reqnum = url.replace('?submit=', '');
        $.ajax({
            type:'post',
            url:'/logSave',
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                'request_number': reqnum
            },
            success: function(data){
                if(data == 'true'){
                    $('#loading').hide();
                    Swal.fire("SUBMIT SUCCESS", "STOCK REQUEST", "success");
                    setTimeout(function(){location.href="/stockrequest"}, 2000);
                }
                else{
                    $('#loading').hide();
                    Swal.fire("SUBMIT FAILED", "STOCK REQUEST", "error");
                    setTimeout(function(){location.href="/stockrequest"}, 2000);
                }
            },
            error: function(data){
                if(data.status == 401){
                    window.location.href = '/stockrequest';
                }
                alert(data.responseText);
            }
        });
    }
    if(($(location).attr('pathname')+window.location.search).includes('asset') == true){
        var url = new URL(window.location.href);
        var reqnum = url.searchParams.get("asset");
        $.ajax({
            type:'post',
            url:'/asset/logSave',
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                'request_number': reqnum
            },
            success: function(data){
                if(data == 'true'){
                    $('#loading').hide();
                    Swal.fire("SUBMIT SUCCESS", "FIXED ASSET STOCK REQUEST", "success");
                    setTimeout(function(){location.href="/stockrequest"}, 2000);
                }
                else{
                    $('#loading').hide();
                    Swal.fire("SUBMIT FAILED", "FIXED ASSET STOCK REQUEST", "error");
                    setTimeout(function(){location.href="/stockrequest"}, 2000);
                }
            },
            error: function(data){
                if(data.status == 401){
                    window.location.href = '/stockrequest';
                }
                alert(data.responseText);
            }
        });
    }
    else if($(location).attr('pathname')+window.location.search == '/stockrequest?edit=success'){
        $('#loading').hide();
        Swal.fire("EDIT SUCCESS", "STOCK REQUEST", "success");
        setTimeout(function(){location.href="/stockrequest"}, 2000);
    }
    else if(($(location).attr('pathname')+window.location.search).includes('status') == true){
        url = window.location.search;
        reqnum = url.replace('?status=7&edit=', '');
        reqstatus = 7;
        $.ajax({
            type:'post',
            url:'/logSave',
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                'request_number': reqnum,
                'reqstatus': reqstatus
            },
            success: function(data){
                if(data == 'true'){
                    $('#loading').hide();
                    Swal.fire("EDIT SUCCESS", "STOCK REQUEST", "success");
                    setTimeout(function(){location.href="/stockrequest"}, 2000);
                }
                else{
                    $('#loading').hide();
                    Swal.fire("EDIT FAILED", "STOCK REQUEST", "error");
                    setTimeout(function(){location.href="/stockrequest"}, 2000);
                }
            },
            error: function(data){
                if(data.status == 401){
                    window.location.href = '/stockrequest';
                }
                alert(data.responseText);
            }
        });
    }
    else if(($(location).attr('pathname')+window.location.search).includes('sale') == true){
        url = window.location.search;
        reqnum = url.replace('?sale=', '');
        $.ajax({
            type:'post',
            url:'/logSold',
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                'request_number': reqnum
            },
            success: function(data){
                if(data == 'true'){
                    $('#loading').hide();
                    Swal.fire("SALE SUCCESS", "STOCK REQUEST", "success");
                    setTimeout(function(){location.href="/stockrequest"}, 2000);
                }
                else{
                    $('#loading').hide();
                    Swal.fire("SALE FAILED", "STOCK REQUEST", "error");
                    setTimeout(function(){location.href="/stockrequest"}, 2000);
                }
            },
            error: function(data){
                if(data.status == 401){
                    window.location.href = '/stockrequest';
                }
                alert(data.responseText);
            }
        });
    }
});