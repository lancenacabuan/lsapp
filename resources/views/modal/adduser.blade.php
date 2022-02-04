<div class="container">
    <div class="modal fade in" id="addUser">
    <div class="modal-dialog  modal-lg">
    <div class="modal-content">
        <div class="modal-header" style="background-color:#0d1a80; color:white;height:50px;">
            <h6 class="modal-title">ADD USER</h6>
            <button id="modalClose" type="button" class="close" data-dismiss="modal">&times;</button>
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
                <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" style="width: 150px;">Name</label>
                    </div>
                    <input type="text" id="name" name="name"  style="width: 80%;">
                </div> 
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" style="width: 150px;">Email</label>
                    </div>
                    <input type="email" id="email" name="email"  style="width: 80%;">
                </div>
                {{-- <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" style="width: 150px;">Password</label>
                    </div>
                    <input type="password" id="password" name="password"  style="width: 80%;">
                </div> --}}
                <div class="input-group mb-3" >
                    <div class="input-group-prepend">
                        <label class="input-group-text" style="width: 150px;">Role</label>
                    </div>
                    <select class="form-select" id="role" name="role" class="form-control">
                        <option selected disabled>Select Role</option>
                        @foreach($role as $roles)
                            <option value="{{$roles->name}}">{{strtoupper($roles->name)}}</option>
                        @endforeach
                    </select>
                </div> 
                <div class="col-md-12 mb-4">
                    <button type="submit" id="usersave" class="btn btn-xs btn-dark submit float-right bp" style="width:180px; font-size:12px;">
                    ADD</button>  
                </div>    
        </div>
    </div>
    </div>
    </div>
</div>
<script>
    $(function () {
        $('#modalClose').on('click', function () {
            $('#addUser').hide();
            location.reload();
        })
    });
</script>