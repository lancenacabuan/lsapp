$('table.userTable').dataTable().fnDestroy();
var table = $('table.userTable').DataTable({
    aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
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
            "render": function(data, type, row, meta){
                if(row.user_status == 'ACTIVE'){
                    return '<label class="switch" style="zoom: 80%; margin-left: -20px; margin-top: -5px; margin-bottom: -10px;"><input type="checkbox" class="togBtn" id="'+ meta.row +'" checked><div class="slider round"><span style="font-size: 110%;" class="on">ACTIVE</span><span style="font-size: 100%;" class="off">INACTIVE</span></div></label>';
                }
                if(row.user_status == 'INACTIVE'){
                    return '<label class="switch" style="zoom: 80%; margin-left: -20px; margin-top: -5px; margin-bottom: -10px;"><input type="checkbox" class="togBtn" id="'+ meta.row +'"><div class="slider round"><span style="font-size: 110%;" class="on">ACTIVE</span><span style="font-size: 100%;" class="off">INACTIVE</span></div></label>';
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
    table.column($(this).data('column')).search($(this).val()).draw();
});

document.querySelectorAll('input[type=search]').forEach(function(input){
    input.addEventListener('mouseup', function(e){
        if(input.value.length > 0){
            setTimeout(function(){
                if(input.value.length === 0){
                    $('.filter-input').keyup();
                }
            }, 0);
        }
    });
});

var data_update;
setInterval(function(){
    if($('#addUser').is(':hidden') && $('#updateUser').is(':hidden') && $('#reportModal').is(':hidden') && $('#changePassword').is(':hidden') && $('#loading').is(':hidden')){
        $.ajax({
            url: "/users/reload",
            success: function(data){
                if(data != data_update){
                    data_update = data;
                    table.ajax.reload(null, false);
                }
            }
        });
    }
}, 1000);

$(document).on('change', '.togBtn', function(){
    var id = $(this).attr("id");
    var data = table.row(id).data();
    if($(this).is(':checked')){
        var status = 'ACTIVE';
    }
    else{
        var status = 'INACTIVE';   
    }
    $.ajax({
        url: '/users/status',
        data:{
            id: data.user_id,
            name: data.user_name, 
            status: status
        },
        success: function(data){
            setTimeout(function(){table.ajax.reload(null, false)}, 1000);
        }
    });
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
    var warntext = '';
    var emailv1 = true;
    var emailv2 = true;
    var name = $.trim($('#name').val());
    var email = $.trim($('#email').val());
    var company = $('#company').val();
    var role = $('#role').val();
    $('#loading').show();
    setTimeout(function(){
        if(!name || !email || !company || !role){
            $('#loading').hide();
            Swal.fire('REQUIRED','Please fill up all required fields!','error');
            return false;
        }
        if(!validateEmail(email)){
            $('#loading').hide();
            Swal.fire("INVALID EMAIL", "Enter a valid email address format!", "error");
            return false;
        }
        if(emailProvider(email)){
            $.ajax({
                headers:{
                    Authorization: "Bearer " + apiKey
                },
                async: false,
                type: 'GET',
                url: 'https://isitarealemail.com/api/email/validate?email='+email,
                success: function(data){
                    if(data.status == 'invalid'){
                        emailv1 = false;
                    }
                    else{
                        emailv1 = true;
                    }
                }
            });
            $('#loading').hide();
            if(emailv1 == false){
                Swal.fire('NON-EXISTENT EMAIL','User Email Address does not exist!','error');
                return false;
            }
        }
        else{
            warntext = ' <br><strong style="color: red;">WARNING: This Email Address is not verified! Continue?</strong>';
        }
        $('#loading').hide();
        $.ajax({
            url: "/users/validate/save",
            type: "POST",
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                    Swal.fire({
                        title: "ADD NEW USER?",
                        html: "You are about to ADD a new user!"+warntext,
                        icon: "warning",
                        showCancelButton: true,
                        cancelButtonColor: '#3085d6',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Confirm',
                        allowOutsideClick: false
                    })
                    .then((result) => {
                        if(result.isConfirmed){
                            $('#addUser').modal('hide');
                            $('#loading').show();
                            $.ajax({
                                url: "/users/save",
                                type: "POST",
                                headers:{
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                                        $('#loading').hide();
                                        Swal.fire("SAVE SUCCESS", "New user saved successfully!", "success");
                                        table.ajax.reload(null, false);
                                    }
                                    else{
                                        $('#loading').hide();
                                        Swal.fire("SAVE FAILED", "New user save failed!", "error");
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
                    Swal.fire("INVALID EMAIL", "Enter a valid email address format!", "error");
                    return false;
                }
                else if(data.result == 'duplicate'){
                    Swal.fire("DUPLICATE EMAIL", "Email address already exists!", "error");
                    return false;
                }
                else{
                    $('#addUser').hide();
                    Swal.fire("SAVE FAILED", "USER ACCOUNT", "error");
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
    }, 500);
});

$('#userTable tbody').on('click', 'tr td:not(:nth-child(5))', function(){
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

        $('.modal-body').html();
        $('#updateUser').modal('show');
});

$('#btnReset').on('click', function(){
    $('#name1').val($('#name2').val());
    $('#email1').val($('#email2').val());
    $('#company1').val($('#company2').val());
    $('#role1').val($('#role2').val());
});

$('#btnUpdate').on('click', function(){
    var warntext = '';
    var emailv1 = true;
    var emailv2 = true;
    var id1 = $('#id1').val();
    var name1 = $.trim($('#name1').val());
    var name2 = $('#name2').val();
    var email1 = $.trim($('#email1').val());
    var email2 = $('#email2').val();
    var company1 = $('#company1').val();
    var company2 = $('#company2').val();
    var role1 = $('#role1').val();
    var role2 = $('#role2').val();
    $('#loading').show();
    setTimeout(function(){
        if(!name1 || !email1 || !company1 || !role1){
            $('#loading').hide();
            Swal.fire('REQUIRED','Please fill up all required fields!','error');
            return false;
        }
        if(name1.toUpperCase() == name2.toUpperCase() && email1.toUpperCase() == email2.toUpperCase() && company1 == company2 && role1 == role2){
            $('#loading').hide();
            Swal.fire("NO CHANGES FOUND", "User Details are all still the same!", "error");
            return false;
        }
        if(!validateEmail(email1)){
            $('#loading').hide();
            Swal.fire("INVALID EMAIL", "Enter a valid email address format!", "error");
            return false;
        }
        if(emailProvider(email1)){
            $.ajax({
                headers:{
                    Authorization: "Bearer " + apiKey
                },
                async: false,
                type: 'GET',
                url: 'https://isitarealemail.com/api/email/validate?email='+email1,
                success: function(data){
                    if(data.status == 'invalid'){
                        emailv2 = false;
                    }
                    else{
                        emailv2 = true;
                    }
                }
            });
            $('#loading').hide();
            if(emailv2 == false){
                Swal.fire('NON-EXISTENT EMAIL','User Email Address does not exist!','error');
                return false;
            }
        }
        else{
            warntext = ' <br><strong style="color: red;">WARNING: This Email Address is not verified! Continue?</strong>';
        }
        $('#loading').hide();
        $.ajax({
            url: "/users/validate/update",
            type: "PUT",
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                role2: role2
            },
            success: function(data){
                if(data == 'true'){
                    Swal.fire({
                        title: "UPDATE USER DETAILS?",
                        html: "You are about to UPDATE this user!"+warntext,
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
                                url: "/users/update",
                                type: "PUT",
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                                    role2: role2
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#updateUser').modal('hide');
                                        Swal.fire("UPDATE SUCCESS", "User details updated successfully!", "success");
                                        table.ajax.reload(null, false);
                                    }
                                    else{
                                        $('#updateUser').modal('hide');
                                        Swal.fire("UPDATE FAILED", "User details update failed!", "error");
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
                    Swal.fire("INVALID EMAIL", "Enter a valid email address format!", "error");
                }
                else if(data == 'duplicate'){
                    Swal.fire("DUPLICATE EMAIL", "Email address already exists!", "error");
                }
                else{
                    $('#updateUser').hide();
                    Swal.fire("UPDATE FAILED", "USER ACCOUNT", "error");
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
    }, 500);
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