$(document).ready(function () {    
    $('table.userTable').dataTable().fnDestroy();
    $('table.userTable').DataTable({ 
        language: {
            processing: "Loading...",
            emptyTable: "No data found!"
        },
        processing: true,
        serverSide: false,
        ajax: {
            url: '/users_data',
        },
        columns: [
            { data: 'user_id' },
            { data: 'user_name' },
            { data: 'user_email' },
            { data: 'role_name' },
        ],
        orderCellsTop: true,
        fixedHeader: true,            
    });
});

$(document).ready(function () { 
    $('#userupdate').on('click', function() {
    var id1 = $('#id1').val();
    var name1 = $('#name1').val();
    var email1 = $('#email1').val();
    var password1 = $('#password1').val();
    var role1 = $('#role1').val();
    var role2 = $('#role2').val();

        if(name1!="" && email1!="" && password1!="" && role1!=""){
            $.ajax({
                url: "users/update",
                type: "PUT",
                headers: {
                    'X-CSRF-TOKEN': $("#csrf").val(),

                    },
                data: {
                    _token: $("#csrf").val(),
                    id1: id1,
                    name1: name1,
                    email1: email1,
                    password1: password1,
                    role1: role1,
                    role2: role2
                },
                success: function(data){
                    if(data == 'true'){
                        $('#updateUser').hide();
                        sweetAlert("UPDATE SUCCESS", "USER ACCOUNT", "success");
                        setTimeout(function(){window.location.href="/users"} , 2000);
                    }
                    else{
                        $('#updateUser').hide();
                        sweetAlert("UPDATE FAILED", "USER ACCOUNT", "error");
                        setTimeout(function(){window.location.href="/users"} , 2000);
                    }
                },
                error: function(data){
                    if(data.status == 401) {
                        window.location.href = '/users';
                    }
                    alert(data.responseText);
                }
            });
        }
        else{
            swal('REQUIRED','Please fill all the fields!','error');
        }
    });
});

$(document).ready(function () {      
    $('#usersave').on('click', function() {
    var name = $('#name').val();
    var email = $('#email').val();
    var password = $('#password').val();
    var role = $('#role').val();
        if(name!="" && email!="" && password!="" && role!=""){
            $.ajax({
                url: "users/save",
                type: "POST",
                headers: {
                'X-CSRF-TOKEN': $("#csrf").val(),
                },
                data: {
                    _token: $("#csrf").val(),
                    name: name,
                    email: email,
                    password: password,
                    role: role
                },
                success: function(data){
                    if(data == 'true'){
                        $('#addUser').hide();
                        sweetAlert("SAVE SUCCESS", "USER ACCOUNT", "success");
                        setTimeout(function(){window.location.href="/users"} , 2000);
                    }
                    else if(data == 'invalid'){
                        sweetAlert("INVALID EMAIL", "USER ACCOUNT", "error");
                    }
                    else if(data == 'duplicate'){
                        sweetAlert("DUPLICATE EMAIL", "USER ACCOUNT", "error");
                    }
                    else{
                        $('#addUser').hide();
                        sweetAlert("SAVE FAILED", "USER ACCOUNT", "error");
                        setTimeout(function(){window.location.href="/users"} , 2000);
                    }
                },
                error: function(data){
                    if(data.status == 401) {
                        window.location.href = '/users';
                    }
                    alert(data.responseText);
                }
            });
        }
        else{
            swal('REQUIRED','Please fill all the fields!','error');
        }
    });  
});

$('#userTable tbody').on('click', 'tr', function () {
    $('#updateUser').modal({
        backdrop: 'static',
        keyboard: false
    });
    var table = $('table.userTable').DataTable();
    var data = table.row(this).data();
        $('#id1').val(data.user_id);
        $('#name1').val(data.user_name);
        $('#email1').val(data.user_email);
        $('#role1').val(data.role_name);
        $('#role2').val(data.role_name);

        $('.modal-body').html();
        $('#updateUser').modal('show');
});