<div class="modal fade in" id="addUser">
    <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">ADD NEW USER</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <form>
                <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control" type="search" id="name" name="name" placeholder=" ">
                        <label for="name" class="formlabel form-label">Fullname</label>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="f-outline">
                        <input class="forminput form-control strlower" type="search" id="email" name="email" placeholder=" ">
                        <label for="email" class="formlabel form-label">Email Address</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="f-outline">
                        <select class="forminput form-control" id="company" name="company" style="color: Gray;" placeholder=" ">
                            <option value="" selected disabled>Select Company</option>
                            <option value="Apsoft" style="color: Black;">Apsoft, Inc.</option>
                            <option value="Ideaserv" style="color: Black;">Ideaserv Systems, Inc.</option>
                            <option value="NuServ" style="color: Black;">NuServ Solutions, Inc.</option>
                            <option value="Phillogix" style="color: Black;">Phillogix Systems, Inc.</option>
                        </select>
                        <label for="company" class="formlabel form-label">Company</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="f-outline">
                        <select class="forminput form-control" id="role" name="role" style="color: Gray;">
                            <option value="" selected disabled>Select User Level</option>
                            @foreach($role as $roles)
                                <option value="{{$roles->name}}" style="color: Black;">{{strtoupper($roles->name)}}</option>
                            @endforeach
                        </select>
                        <label for="role" class="formlabel form-label">User Level</label>
                    </div>
                </div>
                <div style="zoom: 85%;">
                    <button type="reset" class="btn btn-primary bp" onclick="$('#name').focus();">RESET</button>  
                    <button type="button" id="btnSave" class="btn btn-primary float-right bp">SAVE</button>  
                </div>
            </form>
        </div>
    </div>
    </div>
</div>