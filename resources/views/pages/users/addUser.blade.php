<div class="modal fade in" id="addUser">
    <div class="modal-dialog modal-m">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">ADD NEW USER</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 100px;">Fullname</label>
                </div>
                <input type="text" id="name" name="name" style="width: 367px; border-width: thin;" placeholder="Please enter account user fullname">
            </div> 
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 100px;">Email</label>
                </div>
                <input type="email" id="email" name="email" style="width: 367px; border-width: thin;" placeholder="Please enter account user email">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 100px;">User Level</label>
                </div>
                <select id="role" name="role" style="width: 367px !important; color: Gray !important;">
                    <option value="" selected disabled>Select User Level</option>
                    @foreach($role as $roles)
                        <option value="{{$roles->name}}" style="color: Black;">{{strtoupper($roles->name)}}</option>
                    @endforeach
                </select>
            </div> 
            <button type="submit" id="btnSave" class="btn btn-primary float-right bp">SAVE</button>  
        </div>
    </div>
    </div>
</div>