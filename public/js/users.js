$(document).ready(function(){
    $('table.userTable').dataTable().fnDestroy();
    $('#loading').show(); Spinner(); Spinner.show();
    $('table.userTable').DataTable({ 
        language: {
            processing: "Loading...",
            emptyTable: "No data available in table"
        },
        serverSide: true,
        ajax: {
            url: '/users_data',
        },
        columns: [
            { data: 'user_id' },
            { data: 'user_name' },
            { data: 'user_email' },
            { data: 'role_name' },
            { data: 'user_status' }
        ],
        initComplete: function(){
            $('#loading').hide(); Spinner.hide();
        }
    });

    $('#btnAddUser').on('click', function(){
        $('#addUser').modal({
            backdrop: 'static',
            keyboard: false
        });

        $('.modal-body').html();
        $('#addUser').modal('show');
    });

    $('#btnSave').on('click', function(){
        var name = $('#name').val();
        var email = $('#email').val();
        var role = $('#role').val();

        if(name != "" && email != "" && $('#role').find('option:selected').text() != "Select User Level"){
            swal({
                title: "ADD NEW USER?",
                text: "You are about to ADD a new user!",
                icon: "warning",
                buttons: true,
            })
            .then((willDelete) => {
                if (willDelete) {
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
                            role: role
                        },
                        success: function(data){
                            if(data.result == 'true'){
                                scrollReset();
                                $('#addUser').hide();
                                $('#addUser').modal('dispose');
                                $('#loading').show(); Spinner(); Spinner.show();
                                $.ajax({
                                    url: "/logNewUser",
                                    type: "POST",
                                    headers: {
                                    'X-CSRF-TOKEN': $("#csrf").val(),
                                    },
                                    data: {
                                        id: data.id,
                                        name: data.name,
                                        email: data.email
                                    },
                                    success: function(data){
                                        if(data == 'true'){
                                            $('#loading').hide(); Spinner.hide();
                                            sweetAlert("SAVE SUCCESS", "USER ACCOUNT", "success");
                                            setTimeout(function(){window.location.href="/users"}, 2000);
                                        }
                                        else{
                                            return false;
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
                            else if(data.result == 'invalid'){
                                sweetAlert("INVALID EMAIL", "USER ACCOUNT", "error");
                                return false;
                            }
                            else if(data.result == 'duplicate'){
                                sweetAlert("DUPLICATE EMAIL", "USER ACCOUNT", "error");
                                return false;
                            }
                            else{
                                $('#addUser').hide();
                                sweetAlert("SAVE FAILED", "USER ACCOUNT", "error");
                                setTimeout(function(){window.location.href="/users"}, 2000);
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
            });
        }
        else{
            swal('REQUIRED','Please fill all the fields!','error');
            return false;
        }
    });

    $('#userTable tbody').on('click', 'tr', function(){
        $('#updateUser').modal({
            backdrop: 'static',
            keyboard: false
        });
        var table = $('table.userTable').DataTable();
        var data = table.row(this).data();
            $('#id1').val(data.user_id);
            $('#name1').val(data.user_name);
            $('#name2').val(data.user_name);
            $('#email1').val(data.user_email);
            $('#email2').val(data.user_email);
            $('#role1').val(data.role_name);
            $('#role2').val(data.role_name);
            $('#status2').val(data.user_status);
            if(data.user_status == 'ACTIVE'){
                $('#status1').prop('checked', true);
            }
            else{
                $('#status1').prop('checked', false);
            }

            $('.modal-body').html();
            $('#updateUser').modal('show');
    });

    $('#btnUpdate').on('click', function(){
        if($('#status1').is(":checked")){
            var status1 = 'ACTIVE';
        }
        else{
            var status1 = 'INACTIVE';
        }
        var id1 = $('#id1').val();
        var name1 = $('#name1').val();
        var name2 = $('#name2').val();
        var email1 = $('#email1').val();
        var email2 = $('#email2').val();
        var role1 = $('#role1').val();
        var role2 = $('#role2').val();
        var status2 = $('#status2').val();

        if(name1 == "" || email1 == "" || $('#role1').find('option:selected').text() == "Select User Level"){
            swal('REQUIRED','Please fill all the fields!','error');
            return false;
        }
        else if(name1.toUpperCase() == name2.toUpperCase() && email1.toUpperCase() == email2.toUpperCase() && role1 == role2 && status1 == status2){
            swal("NO CHANGES FOUND", "User Details are all still the same!", "error");
            return false;
        }
        else if((name1.toUpperCase() != name2.toUpperCase() || email1.toUpperCase() != email2.toUpperCase() || role1 != role2) && status1 != status2){
            swal("UPDATE FAILED", "STATUS CHANGE is NOT allowed if the current User Details has been changed!", "error");
            return false;
        }
        else{
            swal({
                title: "UPDATE USER?",
                text: "You are about to UPDATE this user!",
                icon: "warning",
                buttons: true,
            })
            .then((willDelete) => {
                if (willDelete) {
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
                            name2: name2,
                            email1: email1,
                            email2: email2,
                            role1: role1,
                            role2: role2,
                            status1: status1,
                            status2: status2
                        },
                        success: function(data){
                            if(data == 'true'){
                                $('#updateUser').hide();
                                sweetAlert("UPDATE SUCCESS", "USER ACCOUNT", "success");
                                setTimeout(function(){window.location.href="/users"}, 2000);
                            }
                            else if(data == 'invalid'){
                                sweetAlert("INVALID EMAIL", "USER ACCOUNT", "error");
                            }
                            else if(data == 'duplicate'){
                                sweetAlert("DUPLICATE EMAIL", "USER ACCOUNT", "error");
                            }
                            else{
                                $('#updateUser').hide();
                                sweetAlert("UPDATE FAILED", "USER ACCOUNT", "error");
                                setTimeout(function(){window.location.href="/users"}, 2000);
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
            });
        }
    });

    $('.close').on('click', function(){
        location.reload();
    });
});