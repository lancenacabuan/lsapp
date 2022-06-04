$('table.userTable').dataTable().fnDestroy();
$('#loading').show(); Spinner(); Spinner.show();
var table = $('table.userTable').DataTable({ 
    language:{
        processing: "Loading...",
        emptyTable: "No data available in table"
    },
    serverSide: true,
    ajax:{
        url: '/users/data',
    },
    columns: [
        { data: 'user_name' },
        { data: 'user_email' },
        {
            data: 'company',
            "render": function(data, type, row){
                if(row.company == 'Apsoft'){
                    return 'Apsoft, Inc.';
                }
                if(row.company == 'Ideaserv'){
                    return 'Ideaserv Systems, Inc.';
                }
                if(row.company == 'NuServ'){
                    return 'NuServ Solutions, Inc.';
                }
                if(row.company == 'Phillogix'){
                    return 'Phillogix Systems, Inc.';
                }
            }
        },
        { data: 'role_name' },
        {
            data: 'user_status',
            "render": function(data, type, row){
                if(row.user_status == 'ACTIVE'){
                    return "<span class='d-none'>"+row.user_status+"</span><span style='color: Green; font-weight: bold;'>"+row.user_status+"</span>";
                }
                if(row.user_status == 'INACTIVE'){
                    return "<span class='d-none'>"+row.user_status+"</span><span style='color: Red; font-weight: bold;'>"+row.user_status+"</span>";
                }
            }
        }
    ],
    order: [],
    initComplete: function(){
        return notifyDeadline();
    }
});

$('.filter-input').on('keyup', function(){
    table.column($(this).data('column'))
        .search($(this).val())
        .draw();
});

$('#btnAddUser').on('click', function(){
    $('#addUser').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('#name').val('');
    $('#email').val('');
    $('#company').val('');
    $('#company').css({"color":"Gray"});
    $('#role').val('');
    $('#role').css({"color":"Gray"});

    $('.modal-body').html();
    $('#addUser').modal('show');
});

$('#btnSave').on('click', function(){
    var name = $.trim($('#name').val());
    var email = $.trim($('#email').val());
    var company = $('#company').val();
    var role = $('#role').val();

    if(!name || !email || !company || !role){
        swal('REQUIRED','Please fill up all required fields!','error');
        return false;
    }
    else{
        $.ajax({
            url: "/users/validate/save",
            type: "POST",
            headers:{
                'X-CSRF-TOKEN': $("#csrf").val()
            },
            data:{
                _token: $("#csrf").val(),
                name: name,
                email: email,
                company: company,
                role: role
            },
            success: function(data){
                if(data.result == 'true'){
                    swal({
                        title: "ADD NEW USER?",
                        text: "You are about to ADD a new user!",
                        icon: "warning",
                        buttons: true,
                    })
                    .then((willDelete) => {
                        if(willDelete){
                            scrollReset();
                            $('#addUser').hide();
                            $('#addUser').modal('dispose');
                            $('#loading').show(); Spinner(); Spinner.show();
                            $.ajax({
                                url: "/users/save",
                                type: "POST",
                                headers:{
                                'X-CSRF-TOKEN': $("#csrf").val()
                                },
                                data:{
                                    _token: $("#csrf").val(),
                                    name: name,
                                    email: email,
                                    company: company,
                                    role: role
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide(); Spinner.hide();
                                        swal("SAVE SUCCESS", "New user saved successfully!", "success");
                                        table.ajax.reload(null, false);
                                    }
                                    else{
                                        $('#addUser').hide();
                                        swal("SAVE FAILED", "New user save failed!", "error");
                                        table.ajax.reload(null, false);
                                    }
                                },
                                error: function(data){
                                    if(data.status == 401){
                                        window.location.href = '/users';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                    });
                }
                else if(data.result == 'invalid'){
                    swal("INVALID EMAIL", "Enter a valid email address!", "error");
                    return false;
                }
                else if(data.result == 'duplicate'){
                    swal("DUPLICATE EMAIL", "Email address already exists!", "error");
                    return false;
                }
                else{
                    $('#addUser').hide();
                    swal("SAVE FAILED", "USER ACCOUNT", "error");
                    setTimeout(function(){window.location.href="/users"}, 2000);
                }
            },
            error: function(data){
                if(data.status == 401){
                    window.location.href = '/users';
                }
                alert(data.responseText);
            }
        });
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
        $('#company1').val(data.company);
        $('#company2').val(data.company);
        $('#role1').val(data.role);
        $('#role2').val(data.role);
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
    var name1 = $.trim($('#name1').val());
    var name2 = $('#name2').val();
    var email1 = $.trim($('#email1').val());
    var email2 = $('#email2').val();
    var company1 = $('#company1').val();
    var company2 = $('#company2').val();
    var role1 = $('#role1').val();
    var role2 = $('#role2').val();
    var status2 = $('#status2').val();

    if(!name1 || !email1 || !company1 || !role1){
        swal('REQUIRED','Please fill up all required fields!','error');
        return false;
    }
    else if(name1.toUpperCase() == name2.toUpperCase() && email1.toUpperCase() == email2.toUpperCase() && company1 == company2 && role1 == role2 && status1 == status2){
        swal("NO CHANGES FOUND", "User Details are all still the same!", "error");
        return false;
    }
    else if((name1.toUpperCase() != name2.toUpperCase() || email1.toUpperCase() != email2.toUpperCase() || company1 != company2 || role1 != role2) && status1 != status2){
        swal("UPDATE FAILED", "STATUS CHANGE is NOT allowed if the current User Details has been changed!", "error");
        return false;
    }
    else{
        $.ajax({
            url: "/users/validate/update",
            type: "PUT",
            headers:{
                'X-CSRF-TOKEN': $("#csrf").val()
            },
            data:{
                _token: $("#csrf").val(),
                id1: id1,
                name1: name1,
                name2: name2,
                email1: email1,
                email2: email2,
                company1: company1,
                company2: company2,
                role1: role1,
                role2: role2,
                status1: status1,
                status2: status2
            },
            success: function(data){
                if(data == 'true'){
                    swal({
                        title: "UPDATE USER?",
                        text: "You are about to UPDATE this user!",
                        icon: "warning",
                        buttons: true,
                    })
                    .then((willDelete) => {
                        if(willDelete){
                            $.ajax({
                                url: "/users/update",
                                type: "PUT",
                                headers:{
                                    'X-CSRF-TOKEN': $("#csrf").val()
                                },
                                data:{
                                    _token: $("#csrf").val(),
                                    id1: id1,
                                    name1: name1,
                                    name2: name2,
                                    email1: email1,
                                    email2: email2,
                                    company1: company1,
                                    company2: company2,
                                    role1: role1,
                                    role2: role2,
                                    status1: status1,
                                    status2: status2
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#updateUser').hide();
                                        $('#updateUser').modal('dispose');
                                        swal("UPDATE SUCCESS", "User details updated successfully!", "success");
                                        table.ajax.reload(null, false);
                                    }
                                    else{
                                        $('#updateUser').hide();
                                        $('#updateUser').modal('dispose');
                                        swal("UPDATE FAILED", "User details update failed!", "error");
                                        table.ajax.reload(null, false);
                                    }
                                },
                                error: function(data){
                                    if(data.status == 401){
                                        window.location.href = '/users';
                                    }
                                    alert(data.responseText);
                                }
                            });
                        }
                    });
                }
                else if(data == 'invalid'){
                    swal("INVALID EMAIL", "Enter a valid email address!", "error");
                }
                else if(data == 'duplicate'){
                    swal("DUPLICATE EMAIL", "Email address already exists!", "error");
                }
                else{
                    $('#updateUser').hide();
                    swal("UPDATE FAILED", "USER ACCOUNT", "error");
                    setTimeout(function(){window.location.href="/users"}, 2000);
                }
            },
            error: function(data){
                if(data.status == 401){
                    window.location.href = '/users';
                }
                alert(data.responseText);
            }
        });
    }
});

$('#company').on('change', function(){
    var company = $('#company').val();
    if(company == ''){
        $('#company').css({"color":"Gray"});
    }
    else{
        $('#company').css({"color":"Black"});
    }
});

$('#company1').on('change', function(){
    var company1 = $('#company1').val();
    if(company1 == ''){
        $('#company1').css({"color":"Gray"});
    }
    else{
        $('#company1').css({"color":"Black"});
    }
});

$('#role').on('change', function(){
    var role = $('#role').val();
    if(role == ''){
        $('#role').css({"color":"Gray"});
    }
    else{
        $('#role').css({"color":"Black"});
    }
});

$('#role1').on('change', function(){
    var role1 = $('#role1').val();
    if(role1 == ''){
        $('#role1').css({"color":"Gray"});
    }
    else{
        $('#role1').css({"color":"Black"});
    }
});