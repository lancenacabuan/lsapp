<div class="modal fade in" id="reportModal">
    <div class="modal-dialog modal-dialog-centered modal-m">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">REPORT A PROBLEM</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <div class="form-inline mb-2">
                <label class="form-control form-control-sm" style="width: 160px;">Tracking Ticket Number</label>
                <input class="form-control form-control-sm" id="ticket_number" onclick="copyTicketNum()" style="width: 308px;" type="text" readonly>
            </div>
            <textarea style="margin-bottom: 8px; font-size: 14px; resize: none;" class="form-control" rows="5" name="report" id="report" maxlength="300" autocomplete="off" placeholder="Please describe the error or bug that you have encountered."></textarea>
            <span style="color: Red; font-size: 12px;">*Required Field</span><br>
            <span id='textlimit' style="font-size: 12px;"></span>
            <button type="button" id="btnSupport" class="btn btn-primary bp" style="zoom: 85%; float: right;">SUBMIT</button>
            <button type="button" id="btnResetReport" class="btn btn-primary bp" style="zoom: 85%; float: right; margin-right: 5px;">RESET</button><br><br>
            <span style="font-size: 14px;">You may also contact us at our Viber numbers below for chat support.</span>
            <div class="my-2" style="height: 50px; line-height: 50px;">
                <img src="{{asset('viber.png')}}" style="width: auto; height: 50px;">
                <span class="ml-2" style="font-size: 14px;">0999-220-6507 / 0998-848-8624 / 0946-5656-535</span>
            </div>
        </div>
    </div>
    </div>
</div>
<div class="modal fade in" id="changePassword">
    <div class="modal-dialog modal-m modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">CHANGE PASSWORD</h6>
            <button type="button" class="btn-close btn-close-white close closePassword" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <form id="form_changepassword">
                <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <label class="input-group-text" style="width: 150px;">Current Password</label>
                    </div>
                    <input type="password" id="pass1" style="width: 320px;" minlength="8" maxlength="30" required autofocus>
                </div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <label class="input-group-text" style="width: 150px;">New Password</label>
                    </div>
                    <input type="password" id="pass2" style="width: 320px;" minlength="8" maxlength="30" required>
                </div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <label class="input-group-text" style="width: 150px;">Confirm Password</label>
                    </div>
                    <input type="password" id="pass3" style="width: 320px;" minlength="8" maxlength="30" required>
                </div>
                <br>
                <button type="reset" id="btnResetChange" class="btn btn-primary bp" onclick="$('#pass1').focus();">RESET</button>
                <button type="button" id="btnChangePassword" class="btn btn-primary float-right bp">UPDATE</button>
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
    swal({
        title: copyText.value,
        text: "Copied to Clipboard!",
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
    $('#report').val('');
    max = 300;
    $('#textlimit').html(max + ' characters remaining');
    generateTicket();
});

$('#btnResetReport').on('click', function(){
    $('#report').val('');
    $('#report').focus();
    max = 300;
    $('#textlimit').html(max + ' characters remaining');
});

$('#btnSupport').on('click', function(){
    var ticket_number = $('#ticket_number').val();
    var details = $.trim($('#report').val());
    if(!details){
        swal('REQUIRED','Please fill up required field!','error');
        return false;
    }
    else{
        swal({
            title: "SUBMIT REPORT?",
            text: "You are about to REPORT a suggested feature, issue, error, or bug to the technical support team!",
            icon: "warning",
            buttons: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                    url: "/report/submit",
                    type: "POST",
                    async: false,
                    headers:{
                        'X-CSRF-TOKEN': $("#csrf").val()
                    },
                    data:{
                        _token: $("#csrf").val(),
                        ticket_number: ticket_number,
                        details: details
                    },
                    success: function(data){
                        if(data == 'true'){
                            scrollReset();
                            $('#reportModal').hide();
                            $('#reportModal').modal('dispose');
                            $('#loading').show(); Spinner(); Spinner.show();
                            $.ajax({
                                url: "/report/log",
                                type: "POST",
                                headers:{
                                    'X-CSRF-TOKEN': $("#csrf").val()
                                },
                                data:{
                                    _token: $("#csrf").val(),
                                    ticket_number: ticket_number,
                                    details: details
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide(); Spinner.hide();
                                        swal("SUBMIT SUCCESS", "Report submitted successfully!", "success");
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
                            swal("SUBMIT FAILED", "Report failed to submit!", "error");
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
            swal('UPDATE ERROR','Confirm Password must be the same as New Password!','error');
            return false;
        }
        else{
            swal({
                title: "CHANGE PASSWORD?",
                text: "You are about to CHANGE your current user account password!",
                icon: "warning",
                buttons: true,
            })
            .then((willDelete) => {
                if(willDelete){
                    $.ajax({
                        url: "/changepassword",
                        type: "PUT",
                        headers:{
                            'X-CSRF-TOKEN': $("#csrf").val()
                        },
                        data:{
                            _token: $("#csrf").val(),
                            new: pass2,
                            current: pass1
                        },
                        success: function(data){
                            if(data == 'true'){
                                $('.closePassword').click();
                                swal("UPDATE SUCCESS", "User changed password successfully!", "success");
                                return true;
                            }
                            else if(data == 'false'){
                                swal("UPDATE FAILED", "User password change failed!", "error");
                                return true;
                            }
                            else{
                                swal('UPDATE ERROR','Incorrect Current Password!', 'error');
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
            });
        }
    }
});
</script>