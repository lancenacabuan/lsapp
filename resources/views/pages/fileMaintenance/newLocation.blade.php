<div class="modal fade in" id="newLocation">
    <div class="modal-dialog modal-m modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">NEW LOCATION REQUEST</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <div class="form-inline">
                <label class="form-control form-control-sm" style="width: 118px;">Location Name</label>
                <input class="form-control form-control-sm" id="location" style="width: 350px;" type="text" maxlength="100" placeholder="Request new location name via email">
            </div>
            <button type="button" id="btnSaveLocation" class="btn btn-primary float-right bp" style="margin-top: 10px;">SUBMIT</button>
        </div>
    </div>
    </div>
</div>