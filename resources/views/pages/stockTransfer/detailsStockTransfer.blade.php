<div class="modal fade in" id="detailsStockTransfer">
    <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">STOCK TRANSFER REQUEST DETAILS</h6>
            <button type="button" class="btn-close btn-close-white close" id='modalClose' data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <input type="hidden" id="req_type_id_details">
            <input type="hidden" id="status_id_details">
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" id="status_label" style="width: 160px;">Status</label>
                <input class="form-control form-control-sm" id="status_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Needed</label>
                <input class="form-control form-control-sm"  id="needdate_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                <label class="form-control form-control-sm" style="width: 200px;">Stock Transfer Request No.</label>
                <input class="form-control form-control-sm" id="request_num_details" onclick="copyReqNum()" style="width: 280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">FROM Location</label>
                <select class="form-select form-control-sm" id="locfrom_details" style=" margin-right: 10px; font-size: .85rem; padding: 0.25rem 0.5rem; height:30px !important; width: 280px;" disabled>
                    <option value="" selected disabled>Select Location</option>
                    <option value="5">BALINTAWAK</option>
                    <option value="6">MALABON</option>
                    {{-- @foreach($locations as $location)
                        <option value="{{$location->id}}">{{$location->location}}</option>
                    @endforeach --}}
                </select>
                <label class="form-control form-control-sm" style="width: 200px;">Date Requested</label>
                <input class="form-control form-control-sm"  id="reqdate_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">TO New Location</label>
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
                <label class="form-control form-control-sm" style="width: 200px;">Requested By</label>
                <input class="form-control form-control-sm" id="reqby_details" style="width: 280px; margin-right: 10px;" type="text" readonly value="{{auth()->user()->name}}">
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
            <div id="warning" class="alert alert-warning" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>CANNOT PROCEED:</strong> Available stocks of <span id="warning_span">MAIN BRANCH</span> should be equal or more than the quantity of all requested items.
            </div>
            <table id="transferDetails" class="table transferDetails table-hover display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>
                    <tr>
                        <th>ITEM CODE</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>UOM</th>
                        <th class="sum">REQUESTED</th>
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
                <tfoot style="font-size: 14px;">
                    <tr>
                        <th colspan="3" style="text-align: right;">TOTAL ITEM COUNT:</th>
                        <th></th>
                        <th colspan="10"></th>
                    </tr>
                </tfoot>
            </table>
            @role('approver - warehouse') {{---ROLES---}}
            <input type="button" class="btn btn-primary float-right bp mt-4" id="btnApprove" value="APPROVE">
            <input type="button" class="btn btn-primary bp mt-4" id="btnDisapprove" value="DISAPPROVE">
            @endrole
            @role('admin|encoder') {{---ROLES---}}
            <input type="button" class="btn btn-primary float-right bp mt-4" id="btnProceed" value="PROCEED" disabled>
            @endrole
            @role('admin|encoder') {{---ROLES---}}
            <button type="button" id="btnDelete" class="btn btn-danger bp mt-4">DELETE</button>
            @endrole
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
                <label class="form-control form-control-sm" style="width: 160px;">Date Scheduled</label>
                <input class="form-control form-control-sm" id="schedOn" style="width: 280px;" type="date">
            </div>
            <div class="form-inline" style="margin-left: 35px;" id="reqContents"></div>
            <div id="schedwarning" class="alert alert-warning mt-4" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>CANNOT PROCEED:</strong> Please select all corresponding <strong>SERIALS</strong> on every item to continue scheduling request. 
            </div>
            <hr>
            <input type="button" class="btn btn-primary bp" id="btnBack" class="button" value="BACK">
            <input type="button" class="btn btn-primary float-right bp" id="btnSubmit" class="button" value="SCHEDULE" disabled>
            <br>
        </div>
        </div>
        <div id="processModal" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 id="modalheader" class="modal-title w-100">SCHEDULED ITEM DETAILS</h6>
        </div>
        <div class="modal-body">
            @role('admin|encoder') {{---ROLES---}}
            <div class="alert alert-primary transitItemsModal receive_label" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>NOTE:</strong> Please select table rows to confirm <b>received items</b> then click the RECEIVE button below.
            </div>
            @endrole
            <div class="schedItemsModal" style="display: none;">
                <div class="form-inline" style="margin-left: 35px;">
                    <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Scheduled By</label>
                    <input class="form-control form-control-sm" id="prep_by" style="width: 280px; margin-bottom: 10px;" type="text" readonly>
                </div>
                <div class="form-inline" style="margin-left: 35px;">
                    <label class="form-control form-control-sm" style="width: 160px;">Date Scheduled</label>
                    <input class="form-control form-control-sm" id="sched" style="width: 280px;" type="text" readonly>
                </div>
            </div>
            <div class="transitItemsModal" style="display: none;">
                <div class="form-inline" style="margin-left: 35px;">
                    <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Scheduled By</label>
                    <input class="form-control form-control-sm" id="prep_by1" style="width: 280px; margin-bottom: 10px;" type="text" readonly>
                </div>
                <div class="form-inline" style="margin-left: 35px;">
                    <label class="form-control form-control-sm" style="width: 160px;">Date Scheduled</label>
                    <input class="form-control form-control-sm" id="sched1" style="width: 280px;" type="text" readonly>
                </div>
            </div>
            <br>
            <table class="table transItems display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>
                    <tr>
                        <th>ITEM CODE</th>
                        <th>ITEM DESCRIPTION</th>
                        <th class="sum">QTY</th>
                        <th>UOM</th>
                        <th>SERIAL</th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot style="font-size: 14px;">
                    <tr>
                        <th colspan="2" style="text-align: right;">TOTAL ITEM COUNT:</th>
                        <th></th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
            <br>
            <hr class="schedItemsModal transitItemsModal" style="display: none;">
            @role('admin|encoder') {{---ROLES---}}
            <button type="button" class="btnTransit btn btn-primary float-right bp schedItemsModal" style="display: none">FOR RECEIVING</button>
            <button type="button" class="btnReceive btn btn-primary float-right bp transitItemsModal" style="display: none" disabled>RECEIVE</button>
            @endrole
            <button type="button" class="btnPrint btn btn-primary bp schedItemsModal transitItemsModal" style="display: none;">PRINT PREVIEW</button>
            <br class="schedItemsModal transitItemsModal" style="display: none;">
        </div>
        </div>
        <div id="incItemsModal" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 id="incmodalheader" class="modal-title w-100">INCOMPLETE ITEM DETAILS</h6>
        </div>
        <div class="modal-body">
            @role('admin|encoder') {{---ROLES---}}
            <div id="increceive_label" class="alert alert-primary" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>NOTE:</strong> Please select table rows to confirm <b>received items</b> then click the RECEIVE button below.
            </div>
            <div class="form-inline divResched" style="margin-left: 35px; display: none;">
                <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Rescheduled By</label>
                <input class="form-control form-control-sm" id="reprep_by1" style="width: 280px; margin-bottom: 10px;" type="text" value="{{auth()->user()->name}}" readonly>
            </div>
            <div class="form-inline divResched" style="margin-left: 35px; display: none;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Rescheduled</label>
                <input class="form-control form-control-sm" id="resched" style="width: 280px;" type="date">
            </div>
            @endrole
            <div class="form-inline divResched1" style="margin-left: 35px; display: none;">
                <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Rescheduled By</label>
                <input class="form-control form-control-sm" id="reprep_by" style="width: 280px; margin-bottom: 10px;" type="text" readonly>
            </div>
            <div class="form-inline divResched1" style="margin-left: 35px; display: none;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Rescheduled</label>
                <input class="form-control form-control-sm" id="resched1" style="width: 280px;" type="text" readonly>
            </div>
            <br>
            <table id="incItems" class="table incItems display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>
                    <tr>
                        <th>ITEM CODE</th>
                        <th>ITEM DESCRIPTION</th>
                        <th class="sum">QTY</th>
                        <th>UOM</th>
                        <th>SERIAL</th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot style="font-size: 14px;">
                    <tr>
                        <th colspan="2" style="text-align: right;">TOTAL ITEM COUNT:</th>
                        <th></th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
            <br>
            @role('admin|encoder') {{---ROLES---}}
            <hr>
            <input type="button" class="btn btn-primary float-right bp divResched" id="btnReschedule" style="display: none;" value="RESCHEDULE">
            <input type="button" class="btn btn-primary float-right bp btnTransit divResched1" style="display: none;" value="FOR RECEIVING">
            <button type="button" class="btn btn-primary float-right bp btnReceive btnReceiveInc" disabled>RECEIVE</button>
            <br>
            <br>
            @endrole
        </div>
        </div>
    </div>
    </div>
</div>
<div class="modal fade in" id="editSerialModal">
    <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">EDIT ITEM SERIAL</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <input type="hidden" name="x_id" id="x_id">
            <input type="hidden" name="y_serial" id="y_serial">
            <input style="margin-bottom: 8px; font-size: 12px;" class="form-control form-control-sm" type="text" name="x_category" id="x_category" readonly>
            <textarea style="margin-bottom: 8px; font-size: 12px; resize: none;" class="form-control" rows="5" name="x_item" id="x_item" readonly></textarea>
            <input style="margin-bottom: 8px; font-size: 12px;" class="form-control form-control-sm" type="text" name="x_serial" id="x_serial" placeholder="Input Item Serial" autocomplete="off">
            <br>
            <button type="button" id="btnEdit" class="btn btn-primary float-right bp" style="zoom: 80%;">EDIT</button>
            <span class="float-right" style="width: 5px;">&nbsp;</span>
            <button type="button" id="btnClear" class="btn btn-primary float-right bp" style="zoom: 80%;">CLEAR</button>
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
            <button type="button" id="btnReason" class="btn btn-primary float-right bp" style="zoom: 80%;">OK</button>
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