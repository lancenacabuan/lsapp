<div class="container">
    <div class="modal fade in" id="detailsLocation">
    <div class="modal-dialog modal-m">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">LOCATION DETAILS</h6>    
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">                
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <input type="hidden" name="location_id" id="location_id">
            <input type="hidden" name="location_original" id="location_original">
            <input type="hidden" name="status_original" id="status_original">
            <div class="form-inline">
                <label class="form-control form-control-sm" style="width:115px;">Location Name</label>
                <input class="form-control form-control-sm" id="location_details" style="width: 220px; margin-right: 10px;" type="text" maxlength="255">            
                <label class="switch">
                    <input type="checkbox" id="status" class="togBtn" value="ACTIVE">
                    <div class="slider round">
                        <span class="on">ACTIVE</span>
                        <span class="off">INACTIVE</span>
                    </div>
                </label>
            </div>
            <button type="button" id="btnUpdateLocation" class="btn btn-primary float-right bp" style="margin-right: 10px; margin-top: 10px;">UPDATE</button>
        </div>
    </div>
    </div>
    </div>
</div>