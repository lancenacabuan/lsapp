<div class="modal fade in" id="detailsStockTransfer">
    <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">STOCK TRANSFER REQUEST DETAILS</h6>            
            <button type="button" class="btn-close btn-close-white close" id='modalClose' data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">                          
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Requested</label>
                <input class="form-control form-control-sm"  id="reqdate_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                <label class="form-control form-control-sm" style="width: 200px;">Stock Transfer Request No.</label>
                <input class="form-control form-control-sm" id="request_num_details" onclick="copyReqNum()" style="width: 280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Needed</label>
                <input class="form-control form-control-sm"  id="needdate_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                <label class="form-control form-control-sm" style="width: 200px;">FROM Location</label>
                <select class="form-select form-control-sm" id="locfrom_details" style=" margin-right: 10px; font-size: .85rem; padding: 0.25rem 0.5rem; height:30px !important; width: 280px;" disabled>
                    <option value="" selected disabled>Select Location</option>
                    <option value="5">BALINTAWAK</option>
                    <option value="6">MALABON</option>
                    {{-- @foreach($locations as $location)
                        <option value="{{$location->id}}">{{$location->location}}</option>
                    @endforeach --}}
                </select>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Requested By</label>
                <input class="form-control form-control-sm" id="reqby_details" style="width: 280px; margin-right: 10px;" type="text" readonly value="{{auth()->user()->name}}">
                <label class="form-control form-control-sm" style="width: 200px;">TO New Location</label>
                <select class="form-select form-control-sm" id="locto_details" style=" margin-right: 10px; font-size: .85rem; padding: 0.25rem 0.5rem; height:30px !important; width: 280px;" disabled>
                    <option value="" selected disabled>Select Location</option>
                    <option value="1">A1</option>
                    <option value="2">A2</option>
                    <option value="3">A3</option>
                    <option value="4">A4</option>
                    {{-- @foreach($locations as $location)
                        <option value="{{$location->id}}">{{$location->location}}</option>
                    @endforeach --}}
                </select>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" id="status_label" style="width: 160px;">Status</label>
                <input class="form-control form-control-sm" id="status_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" name="reason_label" id="reason_label" style="margin-top: -56px; width: 160px; display: none;">Disapproval Reason</label>
                <textarea class="form-control" name="reason_details" id="reason_details" style="width: 280px; margin-right: 10px; font-size: 12px; resize: none; display: none;" rows="4" readonly></textarea>
            </div>
        </div>
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">REQUEST DETAILS</h6>
        </div>
        <div class="modal-body">
            <div id="proceed_label" class="alert alert-primary" role="alert">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>NOTE:</strong> Please click table rows to select from the requested items for preparation.
            </div>
            <table id="transferDetails" class="table transferDetails table-hover display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>                            
                    <tr>
                        <th>CATEGORY</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>UOM</th>
                        <th>REQUESTED</th>
                        <th>PENDING</th>
                        <th>MAIN BRANCH</th>
                        <th class="d-none"></th>
                        <th class="d-none">A1</th>
                        <th class="d-none">A2</th>
                        <th class="d-none">A3</th>
                        <th class="d-none">A4</th>
                        <th>BALINTAWAK</th>
                        <th>MALABON</th>
                        <th></th>
                    </tr>
                </thead>    
            </table>
            <br>
            @role('approver - warehouse') {{---ROLES---}}
            <input type="button" class="btn btn-primary float-right bp" id="btnApprove" value="APPROVE">
            <input type="button" class="btn btn-primary bp" id="btnDisapprove" value="DISAPPROVE">
            @endrole
            @role('admin|encoder') {{---ROLES---}}
            <input type="button" class="btn btn-primary float-right bp" id="btnProceed" value="PROCEED" disabled>
            @endrole
            @role('admin|encoder') {{---ROLES---}}
            <button type="button" id="btnDelete" class="btn btn-dark bp">DELETE</button>
            @endrole
            <br>
        </div>
        <div id="requestItems" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">REQUESTED ITEMS</h6>
        </div>
        <div class="modal-body">
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Scheduled By</label>
                <input class="form-control form-control-sm" style="width: 280px; margin-bottom: 10px;" type="text" value="{{auth()->user()->name}}" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" style="width: 160px;">Scheduled On</label>
                <input class="form-control form-control-sm" id="schedOn" style="width: 280px;" type="date">
            </div>
            <div class="form-inline"  style="margin-left: 35px;" id="reqContents"></div>
            <hr>
            <input type="button" class="btn btn-primary bp" id="btnBack" class="button" value="BACK">
            <input type="button" class="btn btn-primary float-right bp" id="btnSubmit" class="button" value="SCHEDULE" disabled>
            <br>
        </div>
        </div>
        <div id="schedItemsModal" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">SCHEDULED ITEM DETAILS</h6>
        </div>
        <div class="modal-body">
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Scheduled By</label>
                <input class="form-control form-control-sm" id="prep_by" style="width: 280px; margin-bottom: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" style="width: 160px;">Scheduled On</label>
                <input class="form-control form-control-sm" id="sched" style="width: 280px;" type="text" readonly>
            </div>
            <br>
            <table class="table transItems display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>                            
                    <tr>
                        <th>CATEGORY</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>QTY</th>
                        <th>UOM</th>
                        <th>SERIAL</th>
                    </tr>
                </thead>    
            </table>
            <br>
            <hr>
            @role('admin|encoder') {{---ROLES---}}
            <input type="button" class="btn btn-primary float-right bp" id="btnTransit" class="button" value="FOR RECEIVING">
            @endrole
            <button type="button" class="btnPrint btn btn-primary bp">PRINT PREVIEW</button>
            <br>
        </div>
        </div>
        <div id="transitItemsModal" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 id="modalheader" class="modal-title w-100">FOR RECEIVING ITEM DETAILS</h6>
        </div>
        <div class="modal-body">
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Scheduled By</label>
                <input class="form-control form-control-sm" id="prep_by1" style="width: 280px; margin-bottom: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" style="width: 160px;">Scheduled On</label>
                <input class="form-control form-control-sm" id="sched1" style="width: 280px;" type="text" readonly>
            </div>
            <br>
            <table class="table transItems display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>                            
                    <tr>
                        <th>CATEGORY</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>QTY</th>
                        <th>UOM</th>
                        <th>SERIAL</th>
                    </tr>
                </thead>    
            </table>
            <br>
            <hr>
            @role('admin|encoder') {{---ROLES---}}
            <button type="button" id="btnReceive" class="btn btn-primary float-right bp">RECEIVE</button>
            @endrole
            <button type="button" class="btnPrint btn btn-primary bp">PRINT PREVIEW</button>
            <br>
        </div>
        </div>
    </div>
    </div>
</div>
<div class="modal fade in" id="reasonModal">
    <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">REASON FOR DISAPPROVAL</h6>            
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">                          
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <textarea style="margin-bottom: 8px; font-size: 14px; resize: none;" class="form-control" rows="4" name="reason" id="reason" maxlength="100" autocomplete="off"></textarea>
            <span style="color: Red; font-size: 12px;">*Required Field</span><br>
            <span id='limit' style="font-size: 12px;"></span>
            <button type="button" id="btnReason" class="btn btn-primary float-right bp">OK</button>
        </div>
    </div>
    </div>
</div>
<script>
$(document).ready(function(){
    var max = 100;
    $('#limit').html(max + ' characters remaining');

    $('#reason').keyup(function(){
        var text_length = $('#reason').val().length;
        var text_remaining = max - text_length;

        $('#limit').html(text_remaining + ' characters remaining');
    });
});
</script>