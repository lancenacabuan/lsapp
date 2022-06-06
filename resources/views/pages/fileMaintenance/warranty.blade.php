<div class="modal fade in" id="AddWarranty">
    <div class="modal-dialog modal-m modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
                <h6 class="modal-title w-100">ADD NEW WARRANTY</h6>
                <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="WarrantyForm">
                    <input type="hidden" name="myid" id="myid">
                    <div class="form-group row">
                        <label for="warranty" class="col-md-5 col-form-label text-md-right" style="margin-top: -5px;">{{ __('Warranty') }}</label>
                        <div class="col-md-5">
                            <input id="warranty" type="text" class="form-control form-control-sm" name="warranty" style="color: black;" placeholder="Enter warranty name" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -10px;">
                        <label for="duration" class="col-md-5 col-form-label text-md-right" style="margin-top: -5px;">{{ __('Duration (MONTHS)') }}</label>
                        <div class="col-md-5">
                            <input id="duration" type="number" min="1" class="form-control form-control-sm" name="duration" style="color: black;" placeholder="Enter warranty duration" autocomplete="off" onkeyup="if(value<1) value=1;" required>
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -10px;">
                        <label for="inclusive" class="col-md-5 col-form-label text-md-right"><b>{{ __('INCLUSIVE:') }}</b></label>
                    </div>
                    <div class="form-group row" style="margin-top: -46px;">
                        <div class="form-check" style="text-align: left; margin-left: 192px;">
                            <input type="checkbox" class="cb" id="phone" value=" Phone Support">Phone Support<br>      
                            <input type="checkbox" class="cb" id="onsite" value=" Onsite Visit">Onsite Support<br>      
                            <input type="checkbox" class="cb" id="software" value=" Software">Software<br>      
                            <input type="checkbox" class="cb" id="hardware" value=" Hardware">Hardware<br>      
                            <input type="checkbox" class="cb" id="replacement" value=" Parts Replacement">Parts Replacement<br>      
                            <input type="checkbox" class="cb" id="su" value=" Service Unit">Service Unit<br>      
                        </div>
                    </div>
                </form>
                <hr>
                <input type="button" id="btnSubmit" class="btn btn-primary bp float-right" value="SUBMIT">
            </div>
        </div>
    </div>
</div>