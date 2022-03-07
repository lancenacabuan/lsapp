<div class="container">
    <div class="modal fade in" id="updateUser">
    <div class="modal-dialog  modal-lg">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">USER DETAILS</h6>
            <button id="modalClose1" type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" style="background-color:white;color:black;">
            <input type="hidden" name="_token1" id="csrf1" value="{{Session::token()}}">
            <input type="hidden" name="id1" id="id1">
            <input type="hidden" name="name2" id="name2">
            <input type="hidden" name="email2" id="email2">
            <input type="hidden" name="role2" id="role2">
            <input type="hidden" name="status2" id="status2">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 150px;">Fullname</label>
                </div>
                <input type="text" id="name1" name="name1" style="width: 600px;">
            </div> 
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 150px;">Email</label>
                </div>
                <input type="email" id="email1" name="email1" style="width: 600px;">
            </div>
            <div class="input-group mb-3" >
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 150px;">User Level</label>
                </div>
                <select id="role1" name="role1" style="width: 600px !important;">
                    <option selected disabled>Select User Level</option>
                    @foreach($role as $roles)
                        <option value="{{$roles->name}}">{{strtoupper($roles->name)}}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-group-prepend">
                <label class="input-group-text" style="width: 150px; height: 34px !important;">Status</label>
                <label class="switch">
                    <input type="checkbox" id="status1" class="togBtn" value="ACTIVE">
                    <div class="slider round">
                        <span class="on">ACTIVE</span>
                        <span class="off">INACTIVE</span>
                    </div>
                </label>
            </div>
            <div class="col-md-12 mb-4">
                <button type="submit" id="userupdate" class="btn btn-primary float-right bp">UPDATE</button>  
            </div>    
        </div>
    </div>
    </div>
    </div>
</div>
<script>
    $(function () {
        $('#modalClose1').on('click', function () {
            $('#updateUser').hide();
            location.reload();
        })
    });
</script>