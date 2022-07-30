<div class="modal fade in" id="reportModal">
    <div class="modal-dialog modal-dialog-centered modal-m">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">REPORT A PROBLEM</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <div class="form-inline mb-2">
                <label class="form-control form-control-sm" style="width: 160px;">Tracking Ticket Number</label>
                <input class="form-control form-control-sm" id="ticket_number" onclick="copyTicketNum()" style="width: 308px;" type="text" readonly>
            </div>
            <div class="form-inline mb-2">
                <label class="form-control form-control-sm" style="width: 160px;">Report Category</label>
                <select class="form-control-sm form-select-sm" id="report_category" style="padding: 0.25rem 0.5rem; height: 30px !important; width: 308px;">
                    <option value="" selected disabled>Select Category</option>
                    <option value="Home">Home</option>
                    <option value="Stocks">Stocks</option>
                    <option value="Stock Request">Stock Request</option>
                    <option value="Stock Transfer">Stock Transfer</option>
                    <option value="Merchant">Merchant</option>
                    <option value="Assembly">Assembly</option>
                    <option value="Defective">Defective</option>
                    <option value="Maintenance - Items">Maintenance - Items</option>
                    <option value="Maintenance - Assembled Items">Maintenance - Assembled Items</option>
                    <option value="Maintenance - Categories">Maintenance - Categories</option>
                    <option value="Maintenance - Locations">Maintenance - Locations</option>
                    <option value="Maintenance - Warranty">Maintenance - Warranty</option>
                    <option value="Users">Users</option>
                    <option value="Email Notifications">Email Notifications</option>
                    <option value="Others...">Others...</option>
                </select>
            </div>
            <div class="specify form-inline mb-2" style="display: none;">
                <label class="form-control form-control-sm" style="width: 160px;">Others (Please specify):</label>
                <input class="form-control form-control-sm" id="specify" style="width: 308px;" type="text" placeholder="Required Field">
            </div>
            <textarea style="margin-bottom: 8px; font-size: 14px; resize: none;" class="form-control" rows="5" name="report" id="report" maxlength="300" autocomplete="off" placeholder="Please describe the error or bug that you have encountered."></textarea>
            <span style="color: Red; font-size: 12px;">*Required Field</span><br>
            <span id='textlimit' style="font-size: 12px;"></span>
            <button type="button" id="btnSupport" class="btn btn-primary bp" style="zoom: 85%; float: right;">SUBMIT</button>
            <button type="button" id="btnResetReport" class="btn btn-primary bp" style="zoom: 85%; float: right; margin-right: 5px;">RESET</button><br><br>
            <span style="font-size: 14px;">You may also contact us at our Viber numbers below for chat support.</span>
            <div class="my-2" style="height: 50px; line-height: 50px;">
                <img src="{{asset('/inc/viber.png')}}" style="width: auto; height: 50px;">
                <span class="ml-2" style="font-size: 14px;">0999-220-6507 / 0998-848-8624 / 0946-5656-535</span>
            </div>
        </div>
    </div>
    </div>
</div>
<div class="modal fade in" id="changePassword">
    <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">CHANGE PASSWORD</h6>
            <button type="button" class="btn-close btn-close-white close closePassword" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <form id="form_changepassword">
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control" type="password" id="pass1" name="pass1" minlength="8" maxlength="30" placeholder=" " required autofocus>
                        <label class="formlabel form-label" for="pass1">Current Password</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control" type="password" id="pass2" name="pass2" minlength="8" maxlength="30" placeholder=" " required>
                        <label class="formlabel form-label" for="pass2">New Password</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control" type="password" id="pass3" name="pass3" minlength="8" maxlength="30" placeholder=" " required>
                        <label class="formlabel form-label" for="pass3">Confirm Password</label>
                    </div>
                </div>
                <div style="zoom: 85%;">
                    <button type="reset" id="btnResetChange" class="btn btn-primary bp" onclick="$('#pass1').focus();">RESET</button>
                    <button type="button" id="btnChangePassword" class="btn btn-primary float-right bp">UPDATE</button>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>
<script>
$(document).ready(function(){
    var max = 300;
    $('#textlimit').html(max + ' characters remaining');

    $('#report').keyup(function(){
        var text_length = $('#report').val().length;
        var text_remaining = max - text_length;

        $('#textlimit').html(text_remaining + ' characters remaining');
    });
});

function copyTicketNum(){
    var copyText = document.getElementById("ticket_number");
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

function generateTicket(){
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
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    for(var i = 0; i < 3; i++){
        result += characters.charAt(Math.floor(Math.random() * 6));
    }
    var ticket_number = date+result;

    $.ajax({
        type: 'get',
        url: '/generateTicket',
        async: false,
        data:{
            'ticket_number': ticket_number
        },
        success: function(data){
            if(data == 'unique'){
                document.getElementById("ticket_number").value = ticket_number;
            }
            else{
                generateTicket();
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

$('#btnReport').on('click', function(){
    $('#reportModal').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#reportModal').modal('show');
    $('.specify').hide();
    $('#specify').val('');
    $('#report_category').val('');
    $('#report').val('');
    max = 300;
    $('#textlimit').html(max + ' characters remaining');
    generateTicket();
});

$('#btnResetReport').on('click', function(){
    $('.specify').hide();
    $('#specify').val('');
    $('#report_category').val('');
    $('#report').val('');
    $('#report_category').focus();
    max = 300;
    $('#textlimit').html(max + ' characters remaining');
});

$('#report_category').on('change', function(){
    if($('#report_category').val() == 'Others...'){
        $('.specify').show();
        $('#specify').val('');
    }
    else{
        $('.specify').hide();
        $('#specify').val('');
    }
});

$('#btnSupport').on('click', function(){
    var ticket_number = $('#ticket_number').val();
    var report_category = $('#report_category').val();
    var details = $.trim($('#report').val());
    var specify = $.trim($('#specify').val());
    if(specify){
        report_category = specify;
    }
    if(($('.specify').is(':hidden') && (!details || !report_category)) || ($('.specify').is(':visible') && (!details || !report_category || !specify))){
        Swal.fire('REQUIRED','Please fill up all required fields!','error');
        return false;
    }
    Swal.fire({
        title: "SUBMIT REPORT?",
        html: "You are about to REPORT a suggested feature, issue, error, or bug to the technical support team!",
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
                url: "/report/submit",
                async: false,
                data:{
                    ticket_number: ticket_number,
                    report_category: report_category,
                    details: details
                },
                success: function(data){
                    if(data == 'true'){
                        $('#reportModal').modal('hide');
                        $('#loading').show();
                        $.ajax({
                            url: "/report/log",
                            data:{
                                ticket_number: ticket_number,
                                report_category: report_category,
                                details: details
                            },
                            success: function(data){
                                if(data == 'true'){
                                    $('#loading').hide();
                                    Swal.fire("SUBMIT SUCCESS", "Report submitted successfully!", "success");
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
                        Swal.fire("SUBMIT FAILED", "Report failed to submit!", "error");
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
    });
});

$('#lblChangePassword').on('click', function(){
    $('#changePassword').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#changePassword').modal('show');
    $('#pass1').val('');
    $('#pass2').val('');
    $('#pass3').val('');
});

$('#btnChangePassword').on('click', function(){
    var pass1 = $('#pass1').val();
    var pass2 = $('#pass2').val();
    var pass3 = $('#pass3').val();
    if(pass1 == "" || pass2 == "" || pass3 == ""){
        $('#form_changepassword')[0].reportValidity();
        return false;
    }
    else if(pass1.length < 8 || pass2.length < 8 || pass3.length < 8){
        $('#form_changepassword')[0].reportValidity();
        return false;
    }
    else{
        if(pass2 != pass3){
            Swal.fire('UPDATE ERROR','Confirm Password must be the same as New Password!','error');
            return false;
        }
        else{
            $.ajax({
                url: "/change/validate",
                data:{
                    current: pass1
                },
                success: function(data){
                    if(data == 'true'){
                        Swal.fire({
                            title: "CHANGE PASSWORD?",
                            html: "You are about to CHANGE your current user account password!",
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
                                    url: "/change/password",
                                    data:{
                                        new: pass2
                                    },
                                    success: function(data){
                                        if(data == 'true'){
                                            $('.closePassword').click();
                                            Swal.fire("UPDATE SUCCESS", "User changed password successfully!", "success");
                                            return true;
                                        }
                                        else{
                                            Swal.fire("UPDATE FAILED", "User password change failed!", "error");
                                            return true;
                                        }
                                    },
                                    error: function(data){
                                        if(data.status == 401){
                                            window.location.href = '/';
                                        }
                                        alert(data.responseText);
                                    }
                                });
                            }
                        });
                    }
                    else{
                        Swal.fire('UPDATE ERROR','Incorrect Current Password!', 'error');
                        return false;
                    }
                },
                error: function(data){
                    if(data.status == 401){
                        window.location.href = '/';
                    }
                    alert(data.responseText);
                }
            });
        }
    }
});
$('#btnCancelRequest').on('click', function(){
    Swal.fire({
        title: "CANCEL STOCK REQUEST?",
        html: "You are about to CANCEL and RETURN all requested item/s of this STOCK REQUEST! <br><strong style='color: red;'>WARNING: This process cannot be undone! CONTINUE?</strong>",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $('#detailsStockRequest').modal('hide');
            $('#detailsMerchRequest').modal('hide');
            $('#detailsAssembly').modal('hide');
            $('#loading').show();
            $.ajax({
                type: 'post',
                url: '/cancelRequest',
                async: false,
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    'request_number': $('#request_num_details').val()
                },
                success: function(data){
                    if(data == 'true'){
                        $('#loading').hide();
                        Swal.fire("CANCEL SUCCESS", "STOCK REQUEST", "success");
                        setTimeout(function(){location.reload()}, 2000);
                    }
                    else{
                        $('#loading').hide();
                        Swal.fire("CANCEL FAILED", "STOCK REQUEST", "error");
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
});
</script>