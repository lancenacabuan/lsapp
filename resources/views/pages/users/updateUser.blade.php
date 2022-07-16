<div class="modal fade in" id="updateUser">
    <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">UPDATE USER DETAILS</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="_token1" id="csrf1" value="{{Session::token()}}">
            <input type="hidden" name="id1" id="id1">
            <input type="hidden" name="name2" id="name2">
            <input type="hidden" name="email2" id="email2">
            <input type="hidden" name="company2" id="company2">
            <input type="hidden" name="role2" id="role2">
            <div class="mb-3">
                <div class="f-outline">
                    <input class="forminput form-control" type="search" id="name1" name="name1" placeholder=" ">
                    <label for="name1" class="formlabel form-label">Fullname</label>
                </div>
            </div>
            <div class="mb-4">
                <div class="f-outline">
                    <input class="forminput form-control" type="search" id="email1" name="email1" placeholder=" ">
                    <label for="email1" class="formlabel form-label">Email Address</label>
                </div>
            </div>
            <div class="mb-3">
                <div class="f-outline">
                    <select class="forminput form-control" id="company1" name="company1" style="color: Black;" placeholder=" ">
                        <option value="" selected disabled>Select Company</option>
                        <option value="Apsoft" style="color: Black;">Apsoft, Inc.</option>
                        <option value="Ideaserv" style="color: Black;">Ideaserv Systems, Inc.</option>
                        <option value="NuServ" style="color: Black;">NuServ Solutions, Inc.</option>
                        <option value="Phillogix" style="color: Black;">Phillogix Systems, Inc.</option>
                    </select>
                    <label for="company1" class="formlabel form-label">Company</label>
                </div>
            </div>
            <div class="mb-3">
                <div class="f-outline">
                    <select class="forminput form-control" id="role1" name="role1" style="color: Black;">
                        <option value="" selected disabled>Select User Level</option>
                        @foreach($role as $roles)
                            <option value="{{$roles->name}}" style="color: Black;">{{strtoupper($roles->name)}}</option>
                        @endforeach
                    </select>
                    <label for="role1" class="formlabel form-label">User Level</label>
                </div>
            </div>
            <div style="zoom: 85%;">
                <button id="btnReset" type="button" class="btn btn-primary bp" onclick="$('#name1').focus();">RESET</button> 
                <button type="button" id="btnUpdate" class="btn btn-primary bp float-right">UPDATE</button>
            </div>
        </div>
    </div>
    </div>
</div>