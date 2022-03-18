$('#btnChangePassword').on('click', function(){
var pass1 = $('#pass1').val();
var pass2 = $('#pass2').val();
var pass3 = $('#pass3').val();

    if(pass1!="" && pass2!="" && pass3!=""){
        if(pass2 != pass3){
            swal('ERROR','Confirm Password must be the same as New Password!','error');
        }
        else {
            $.ajax({
                url: "password_save",
                type: "PUT",
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),
                },
                data: {
                    _token: $("#csrf").val(),
                    new: pass2,
                    current: pass1
                },
                success: function(data){      
                    if(data == 'true'){
                        swal("UPDATE SUCCESS", "CHANGE PASSWORD", "success");
                        setTimeout(function(){window.location.href="/"} , 2000);
                    }
                    else if(data == 'false'){
                        swal("UPDATE FAILED", "CHANGE PASSWORD", "error");
                        setTimeout(function(){window.location.href="/"} , 2000);
                    }
                    else{
                        swal('ERROR','Incorrect Current Password!', 'error');
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
    else{
        swal('REQUIRED','Please fill up all required fields!','error');
    }
});