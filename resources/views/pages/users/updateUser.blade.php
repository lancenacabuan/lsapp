<div class="modal fade in" id="updateUser">
    <div class="modal-dialog modal-m">
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
            <input type="hidden" name="status2" id="status2">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 100px;">Fullname</label>
                </div>
                <input type="text" id="name1" name="name1" style="width: 370px; border-width: thin;" placeholder="Please enter account user fullname">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 100px;">Email</label>
                </div>
                <input type="email" id="email1" name="email1" style="width: 370px; border-width: thin;" placeholder="Please enter account user email">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 100px;">Company</label>
                </div>
                <select id="company1" name="company1" style="width: 370px !important; color: Black !important;">
                    <option value="" selected disabled>Select Company</option>
                    <option value="Apsoft" style="color: Black;">Apsoft, Inc.</option>
                    <option value="Ideaserv" style="color: Black;">Ideaserv Systems, Inc.</option>
                    <option value="NuServ" style="color: Black;">NuServ Solutions, Inc.</option>
                    <option value="Phillogix" style="color: Black;">Phillogix Systems, Inc.</option>
                </select>
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 100px;">User Level</label>
                </div>
                <select id="role1" name="role1" style="width: 370px !important; color: Black !important;">
                    <option value="" selected disabled>Select User Level</option>
                    @foreach($role as $roles)
                        <option value="{{$roles->name}}" style="color: Black;">{{strtoupper($roles->name)}}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 120px; height: 34px !important;">Status</label>
                </div>
                <label class="switch" style="margin-left: -20px;">
                    <input type="checkbox" id="status1" class="togBtn" value="ACTIVE">
                    <div class="slider round">
                        <span class="on">ACTIVE</span>
                        <span class="off">INACTIVE</span>
                    </div>
                </label>
            </div>
            <button type="submit" id="btnUpdate" class="btn btn-primary bp float-right">UPDATE</button>
        </div>
    </div>
    </div>
</div>