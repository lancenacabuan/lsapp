<div class="container">
    <div class="modal fade in" id="updateUser">
    <div class="modal-dialog  modal-lg">
    <div class="modal-content">
        <div class="modal-header" style="background-color:#0d1a80; color:white;height:50px;">
            <h6 class="modal-title">UPDATE USER</h6>
            <button id="modalClose1" type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" style="background-color:white;color:black;">                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <input type="hidden" name="_token1" id="csrf1" value="{{Session::token()}}">
                <input type="hidden" name="id1" id="id1">
                <input type="hidden" name="role2" id="role2">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" style="width: 150px;">Fullname</label>
                    </div>
                    <input type="text" id="name1" name="name1"  style="width: 80%;">
                </div> 
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" style="width: 150px;">Email</label>
                    </div>
                    <input type="email" id="email1" name="email1"  style="width: 80%;">
                </div>
                {{-- <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" style="width: 150px;">Password</label>
                    </div>
                    <input type="password" id="password1" name="password1"  style="width: 80%;">
                </div> --}}
                <div class="input-group mb-3" >
                    <div class="input-group-prepend">
                        <label class="input-group-text" style="width: 150px;">Role</label>
                    </div>
                    <select class="form-select" id="role1" name="role1" class="form-control">
                        <option selected disabled>Select Role</option>
                        @foreach($role as $roles)
                            <option value="{{$roles->name}}">{{strtoupper($roles->name)}}</option>
                        @endforeach
                    </select>
                </div> 
                <div class="col-md-12 mb-4">
                    <button type="submit" id="userupdate" class="btn btn-primary float-right bp">
                    UPDATE</button>  
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