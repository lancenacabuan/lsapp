$(document).ready(function () {      
    $('#savepassword').on('click', function() {
      var pass1 = $('#pass1').val();
      var pass2 = $('#pass2').val();
      var pass3 = $('#pass3').val();

      if(pass1!="" && pass2!="" && pass3!=""){
          if(pass2 != pass3){
              swal('','Confirm Password must be the same as New Password !','error');
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
                    pass2: pass2,
                    current: pass1
                },
                success: function(dataResult){      
                    if (dataResult == 'true') {
                        sweetAlert("SAVED", "PASSWORD SUCCESFULLY", "success");
                        setTimeout(function(){window.location.href="/"} , 2000);
                    }else{
                        swal('','Incorrect Current Password !','error');
                    }
                },
                error: function (data) {
                    if(data.status == 401) {
                        window.location.href = '/login';
                    }
                    alert(data.responseText);
                }
            });
          }
      }
      else{
          swal('','Please fill all the field !','error');
      }   
      
  });
  
});