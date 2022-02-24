<div class="container table-responsive">
    <div class="modal fade in" id="detailsStockTransfer">
    <div class="modal-dialog  modal-xl" >
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color:#0d1a80; color:white;height:45px;">
            <h6 class="modal-title w-100">STOCK TRANSFER REQUEST DETAILS</h6>            
            <button type="button" class="close" id='modalClose' data-bs-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" style="background-color:white;color:black;">                          
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <div class="form-inline" style="margin-left:35px;">
                <label class="form-control form-control-sm" style="width:160px;">Date Requested</label>
                <input class="form-control form-control-sm"  id="reqdate_details"style="width:280px; margin-right: 10px;" type="text" readonly value="{{Carbon\Carbon::now()->isoformat('dddd, MMMM D, YYYY')}}">
                <label class="form-control form-control-sm" style="width:200px;">Stock Transfer Request No.</label>
                <input class="form-control form-control-sm" id="reqnum_details" onclick="copyReqNum()" style="width:280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left:35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width:160px;">Date Needed</label>
                <input class="form-control form-control-sm"  id="needdate_details"style="width:280px; margin-right: 10px;" type="text" readonly>
                <label class="form-control form-control-sm" style="width:200px;">FROM Location</label>
                <select class="form-select form-control-sm" id="locfrom_details" style=" margin-right: 10px; font-size: .85rem; padding: 0.25rem 0.5rem; height:30px !important;width:280px;" disabled>
                    <option selected disabled>Select Location</option>
                    @foreach($locations as $location)
                        <option value="{{$location->id}}">{{$location->location}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-inline" style="margin-left:35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width:160px;">Requested By</label>
                <input class="form-control form-control-sm" id="reqby_details" style="width:280px; margin-right: 10px;" type="text" readonly value="{{auth()->user()->name}}">
                <label class="form-control form-control-sm" style="width:200px;">TO New Location</label>
                <select class="form-select form-control-sm" id="locto_details" style=" margin-right: 10px; font-size: .85rem; padding: 0.25rem 0.5rem; height:30px !important;width:280px;" disabled>
                    <option selected disabled>Select Location</option>
                    @foreach($locations as $location)
                        <option value="{{$location->id}}">{{$location->location}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-inline" style="margin-left:35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width:160px;">Status</label>
                <input class="form-control form-control-sm" id="status_details" style="width:280px; margin-right: 10px;" type="text" readonly>
            </div>
            @role('admin|encoder|approver - warehouse') {{---ROLES---}}
            <div class="form-inline" style="margin-left:35px; margin-top: 10px;">
                <label name="reason_label" id="reason_label" class="form-control form-control-sm" style="margin-top: -56px; width:160px; display: none;">Disapproval Reason</label>
                <textarea style="width:280px; margin-right: 10px; font-size: 12px; resize: none; display: none;" class="form-control" rows="4" name="reason_details" id="reason_details" readonly></textarea>
            </div>
            @endrole
        </div>
        <div class="modal-header text-center" style="border-radius: 0px; background-color:#0d1a80; color:white;height:45px;">
            <h6 class="modal-title w-100">REQUEST DETAILS</h6>
        </div><br>      
        <div class="modal-body">
            <table id="transferDetails" class="table transferDetails table-hover display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>                            
                    <tr>
                        <th>CATEGORY</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>QTY REQUESTED</th>
                        <th>QTY PENDING</th>
                        <th>QTY IN STOCK<br>TOTAL MAIN BRANCH</th>
                        <th></th>
                        <th>QTY IN STOCK<br>A1</th>
                        <th>QTY IN STOCK<br>A2</th>
                        <th>QTY IN STOCK<br>A3</th>
                        <th>QTY IN STOCK<br>A4</th>
                        <th>QTY IN STOCK<br>BALINTAWAK</th>
                        <th>QTY IN STOCK<br>MALABON</th>
                        <th></th>
                    </tr>
                </thead>    
            </table>
            <div class="col-md-12 mt-2 mb-4">
            <br>
            @role('approver - warehouse')  {{---ROLES---}}
            <input type="button" class="btn btn-primary mr-auto float-right bp" id="btnApprove" value="APPROVE">
            <input type="button" class="btn btn-primary mr-auto bp" id="btnDisapprove" value="DISAPPROVE">
            @endrole
            @role('admin|encoder')  {{---ROLES---}}
            <input type="button" class="btn btn-primary mr-auto float-right bp" id="btnProceed" value="PROCEED" disabled>
            @endrole
            @role('admin|encoder')  {{---ROLES---}}
            <button type="button" id="btnDelete" class="btn btn-dark mr-auto bp">
                DELETE</button>
            @endrole
            <br>
            </div>
        </div>
        <div id="requestItems" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color:#0d1a80; color:white;height:45px;">
            <h6 class="modal-title w-100">REQUESTED ITEMS</h6>
        </div><br>      
        <div class="modal-body">
            <div class="form-inline" style="margin-left:35px;">
                <label class="form-control form-control-sm" style="width:160px; margin-bottom: 10px;">Scheduled By</label>
                <input class="form-control form-control-sm" style="width:280px; margin-bottom: 10px;" type="text" value="{{auth()->user()->name}}" readonly>
            </div>
            <div class="form-inline" style="margin-left:35px;">
                <label class="form-control form-control-sm" style="width:160px;">Scheduled On</label>
                <input class="form-control form-control-sm" id="schedOn" style="width:280px;" type="date">
            </div>
            <br><br>
            <div class="form-inline"  style="margin-left:35px;" id="reqContents"></div>
            <br><br><br>
            <hr>
            <input type="button" class="btn btn-primary mr-auto bp" id="btnBack" class="button" value="BACK">
            <input type="button" class="btn btn-primary mr-auto float-right bp" id="btnSubmit" class="button" value="SUBMIT" disabled>
            <br><br>
        </div>
        </div>
        <div id="schedItemsModal" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color:#0d1a80; color:white;height:45px;">
            <h6 class="modal-title w-100">SCHEDULED ITEM DETAILS</h6>
        </div><br>      
        <div class="modal-body">
            <div class="form-inline" style="margin-left:35px;">
                <label class="form-control form-control-sm" style="width:160px; margin-bottom: 10px;">Scheduled By</label>
                <input class="form-control form-control-sm" id="prep_by" style="width:280px; margin-bottom: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left:35px;">
                <label class="form-control form-control-sm" style="width:160px;">Scheduled On</label>
                <input class="form-control form-control-sm" id="sched" style="width:280px;" type="text" readonly>
            </div>
            <br>
            <table class="table transItems display" style="cursor:pointer; border:none; font-size:12px; width: 100%;">
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
            @role('admin|encoder')           
            <input type="button" class="btn btn-primary mr-auto float-right bp" id="btnTransit" class="button" value="FOR RECEIVING">
            @endrole
            <button type="button" class="btnPrint btn btn-primary mr-auto bp">
                PRINT PREVIEW</button>
            <br><br>
        </div>
        </div>
        <div id="transitItemsModal" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color:#0d1a80; color:white;height:45px;">
            <h6 id="modalheader" class="modal-title w-100">FOR RECEIVING ITEM DETAILS</h6>
        </div><br>      
        <div class="modal-body">
            <div class="form-inline" style="margin-left:35px;">
                <label class="form-control form-control-sm" style="width:160px; margin-bottom: 10px;">Scheduled By</label>
                <input class="form-control form-control-sm" id="prep_by1" style="width:280px; margin-bottom: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left:35px;">
                <label class="form-control form-control-sm" style="width:160px;">Scheduled On</label>
                <input class="form-control form-control-sm" id="sched1" style="width:280px;" type="text" readonly>
            </div>
            <br>
            <table class="table transItems display" style="cursor:pointer; border:none; font-size:12px; width: 100%;">
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
            @role('admin|encoder')  {{---ROLES---}}
            <button type="button" id="btnReceive" class="btn btn-primary mr-auto float-right bp">
                RECEIVE</button>
            @endrole
            <button type="button" class="btnPrint btn btn-primary mr-auto bp">
                PRINT PREVIEW</button>
            <br><br>
        </div>
        </div>
    </div>
    </div>
    </div>
</div>
<div class="modal fade in" id="reasonModal" style="margin-top: 100px;">
    <div class="modal-dialog  modal-sm" >
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color:#0d1a80; color:white;height:45px;">
            <h6 class="modal-title w-100">REASON FOR DISAPPROVAL</h6>            
            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" style="background-color:white;color:black;">                          
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <textarea style="margin-bottom: 8px; font-size: 14px; resize: none;" class="form-control" rows="4" name="reason" id="reason" maxlength="50"></textarea><br>
            <span id='limit' style="font-size: 12px;"></span>
            <button type="button" id="btnReason" class="btn btn-primary mr-auto float-right bp">
                OK</button>
        </div>
    </div>
    </div>
</div>
<script>
$(document).ready(function() {
    var max = 50;
    $('#limit').html(max + ' characters remaining');

    $('#reason').keyup(function() {
        var text_length = $('#reason').val().length;
        var text_remaining = max - text_length;

        $('#limit').html(text_remaining + ' characters remaining');
    });
});
</script>