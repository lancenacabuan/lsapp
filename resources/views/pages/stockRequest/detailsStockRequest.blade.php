<div class="container">
    <div class="modal fade in" id="detailsStockRequest">
    <div class="modal-dialog modal-xl" >
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">STOCK REQUEST DETAILS</h6>            
            <button type="button" class="close" id='modalClose' data-bs-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">                          
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <div class="form-inline" style="margin-left:35px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Requested</label>
                <input class="form-control form-control-sm"  id="daterequestdetails"style="width: 280px; margin-right: 10px;" type="text" readonly>
                <label class="form-control form-control-sm" style="width: 160px;">Stock Request No.</label>
                <input class="form-control form-control-sm" id="request_num_details" onclick="copyReqNum()" style="width: 280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left:35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Needed</label>
                <input class="form-control form-control-sm"  id="needdate_details"style="width: 280px; margin-right: 10px;" type="text" readonly>
                <label class="form-control form-control-sm" id="client_name_label" style="width: 160px;">Client Name</label>
                <input class="form-control form-control-sm" id="client_name_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left:35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Requested By</label>
                <input class="form-control form-control-sm" id="requested_by_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                <label class="form-control form-control-sm" id="location_label" style="width: 160px;">Address / Branch</label>
                <input class="form-control form-control-sm" id="location_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left:35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Request Type</label>
                <input class="form-control form-control-sm" id="request_type_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                <label class="form-control form-control-sm" id="reference_label" style="width: 160px;">Reference SO/PO No.</label>
                <input class="form-control form-control-sm" id="reference_details" onclick="copyRefNum()" style="width: 280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left:35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Status</label>
                <input class="form-control form-control-sm" id="status_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left:35px; margin-top: 10px;">
                <label name="reason_label" id="reason_label" class="form-control form-control-sm" style="margin-top: -56px; width: 160px; display: none;">Disapproval Reason</label>
                <textarea style="width: 280px; margin-right: 10px; font-size: 12px; resize: none; display: none;" class="form-control" rows="4" name="reason_details" id="reason_details" readonly></textarea>
            </div>
        </div>
        <div class="modal-header text-center" style="border-radius: 0px; background-color:#0d1a80; color:white;height:45px;">
            <h6 class="modal-title w-100">REQUEST DETAILS</h6>
        </div><br>      
        <div class="modal-body">
            @role('sales|approver - sales') {{---ROLES---}}
            <div id="sd1" style="display: none;">
            <table id="stockDetailsrequest" class="table stockDetails1 display" style="cursor:pointer; border:none; font-size:12px; width: 100%;">
                <thead>                            
                    <tr>
                        <th>CATEGORY</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>UOM</th>
                        <th>QTY REQUESTED</th>
                        {{-- <th>QTY SERVED</th> --}}
                        <th>QTY PENDING</th>
                        <th></th>
                    </tr>
                </thead>    
            </table>
            </div>
            <div id="sd2" style="display: none;">
            <table id="stockDetailsrequest" class="table stockDetails2 display" style="cursor:pointer; border:none; font-size:12px; width: 100%;">
                <thead>                            
                    <tr>
                        <th>CATEGORY</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>UOM</th>
                        <th>QTY REQUESTED</th>
                        {{-- <th>QTY SERVED</th> --}}
                        <th>QTY PENDING</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>    
            </table>
            </div>
            @endrole
            @role('admin|encoder|viewer')  {{---ROLES---}}
            <table id="stockDetailsrequest" class="table stockDetails table-hover display nowrap" style="cursor:pointer; border:none; font-size:12px; width: 100%;">
                <thead>                            
                    <tr>
                        <th style="text-align: center;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            CATEGORY&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </th>
                        <th style="text-align: center;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            ITEM DESCRIPTION&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </th>
                        <th style="text-align: center;">UOM</th>
                        <th style="text-align: center;">QTY REQUESTED</th>
                        {{-- <th style="text-align: center;">QTY SERVED</th> --}}
                        <th style="text-align: center;">QTY PENDING</th>
                        <th style="text-align: center;">QTY IN STOCK<br>TOTAL MAIN BRANCH</th>
                        <th class="d-none"></th>
                        <th class="d-none" style="text-align: center;">QTY IN STOCK<br>A1</th>
                        <th class="d-none" style="text-align: center;">QTY IN STOCK<br>A2</th>
                        <th class="d-none" style="text-align: center;">QTY IN STOCK<br>A3</th>
                        <th class="d-none" style="text-align: center;">QTY IN STOCK<br>A4</th>
                        <th style="text-align: center; color: red;">QTY IN STOCK<br>BALINTAWAK</th>
                        <th style="text-align: center; color: red;">QTY IN STOCK<br>MALABON</th>
                    </tr>
                </thead>    
            </table>
            @endrole
            <br>
            @role('approver - sales')  {{---ROLES---}}
            <input type="button" class="btn btn-primary float-right bp" id="btnApprove" value="APPROVE">
            <input type="button" class="btn btn-primary bp" id="btnDisapprove" value="DISAPPROVE">
            @endrole
            @role('admin|encoder')  {{---ROLES---}}
            <input type="button" class="btn btn-primary float-right bp" id="btnProceed" value="PROCEED" disabled>
            @endrole
            @role('sales')  {{---ROLES---}}
            <button type="button" id="btnDelete" class="btn btn-dark bp">DELETE</button>
            @endrole
            <br>
        </div>
        <div id="requestItems" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color:#0d1a80; color:white;height:45px;">
            <h6 class="modal-title w-100">REQUESTED ITEMS</h6>
        </div><br>      
        <div class="modal-body">
            <div class="form-inline" style="margin-left:35px;">
                <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Scheduled By</label>
                <input class="form-control form-control-sm" style="width: 280px; margin-bottom: 10px;" type="text" value="{{auth()->user()->name}}" readonly>
            </div>
            <div class="form-inline" style="margin-left:35px;">
                <label class="form-control form-control-sm" style="width: 160px;">Scheduled On</label>
                <input class="form-control form-control-sm" id="schedOn" style="width: 280px;" type="date">
            </div>
            <div class="form-inline"  style="margin-left:35px;" id="reqContents"></div>
            <hr>
            <input type="button" class="btn btn-primary bp" id="btnBack" class="button" value="BACK">
            <input type="button" class="btn btn-primary float-right bp" id="btnSubmit" class="button" value="SCHEDULE" disabled>
            <br>
        </div>
        </div>
        <div id="schedItemsModal" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color:#0d1a80; color:white;height:45px;">
            <h6 class="modal-title w-100">SCHEDULED ITEM DETAILS</h6>
        </div><br>      
        <div class="modal-body">
            <div class="form-inline" style="margin-left:35px;">
                <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Scheduled By</label>
                <input class="form-control form-control-sm" id="prep_by" style="width: 280px; margin-bottom: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left:35px;">
                <label class="form-control form-control-sm" style="width: 160px;">Scheduled On</label>
                <input class="form-control form-control-sm" id="sched" style="width: 280px;" type="text" readonly>
            </div>
            <br>
            @role('admin|encoder')  {{---ROLES---}}
            <table id="schedItems" class="table schedItems display" style="cursor:pointer; border:none; font-size:12px; width: 100%;">
                <thead>                            
                    <tr>
                        <th>CATEGORY</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>QTY</th>
                        <th>UOM</th>
                        <th>SERIAL</th>
                        <th>LOCATION</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>    
            </table>
            @endrole
            @role('sales|viewer|approver - sales')  {{---ROLES---}}
            <table id="schedItems1" class="table schedItems1 display" style="cursor:pointer; border:none; font-size:12px; width: 100%;">
                <thead>                            
                    <tr>
                        <th>CATEGORY</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>QTY</th>
                        <th>UOM</th>
                        <th>SERIAL</th>
                        <th>LOCATION</th>
                    </tr>
                </thead>    
            </table> 
            @endrole
            <br>
            <hr>
            @role('admin|encoder')           
            <input type="button" class="btn btn-primary float-right bp" id="btnTransit" class="button" value="FOR RECEIVING">
            @endrole
            <button type="button" class="btnPrint btn btn-primary bp">PRINT PREVIEW</button>
            <br>
        </div>
        </div>
        <div id="transitItemsModal" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color:#0d1a80; color:white;height:45px;">
            <h6 id="modalheader" class="modal-title w-100">FOR RECEIVING ITEM DETAILS</h6>
        </div><br>      
        <div class="modal-body">
            <div class="form-inline" style="margin-left:35px;">
                <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Scheduled By</label>
                <input class="form-control form-control-sm" id="prep_by1" style="width: 280px; margin-bottom: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left:35px;">
                <label class="form-control form-control-sm" style="width: 160px;">Scheduled On</label>
                <input class="form-control form-control-sm" id="sched1" style="width: 280px;" type="text" readonly>
            </div>
            <br>
            @role('client')  {{---ROLES---}}
            <table id="transItems" class="table transItems display" style="cursor:pointer; border:none; font-size:12px; width: 100%;">
                <thead>                            
                    <tr>
                        <th>CATEGORY</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>QTY</th>
                        <th>UOM</th>
                        <th>SERIAL</th>
                        <th>LOCATION</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>    
            </table>
            @endrole
            @role('admin|encoder|sales|viewer|approver - sales')  {{---ROLES---}}
            <table id="transItems1" class="table transItems1 display" style="cursor:pointer; border:none; font-size:12px; width: 100%;">
                <thead>                            
                    <tr>
                        <th>CATEGORY</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>QTY</th>
                        <th>UOM</th>
                        <th>SERIAL</th>
                        <th>LOCATION</th>
                    </tr>
                </thead>    
            </table> 
            @endrole
            <br>
            <hr>
            @role('sales')  {{---ROLES---}}
            <button type="button" id="btnReceive" class="btn btn-primary float-right bp">RECEIVE</button>
            @endrole
            <button type="button" class="btnPrint btn btn-primary bp">PRINT PREVIEW</button>
            <br>
        </div>
        </div>
    </div>
    </div>
    </div>
</div>
<div class="modal fade in" id="editSerialModal">
    <div class="modal-dialog  modal-sm" >
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">EDIT ITEM SERIAL</h6>            
            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" style="background-color:white;color:black;">                          
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <input type="hidden" name="x_id" id="x_id">
            <input style="margin-bottom: 8px; font-size: 12px;" class="form-control form-control-sm" type="text" name="x_category" id="x_category" readonly>
            <textarea style="margin-bottom: 8px; font-size: 12px; resize: none;" class="form-control" rows="5" name="x_item" id="x_item" readonly></textarea>
            <input style="margin-bottom: 8px; font-size: 12px;" class="form-control form-control-sm" type="text" name="x_serial" id="x_serial" placeholder="Input Item Serial...">
            <button type="button" id="btnEdit" class="btn btn-primary float-right bp">EDIT</button>
        </div>
    </div>
    </div>
</div>
<div class="modal fade in" id="reasonModal" style="margin-top: 100px;">
    <div class="modal-dialog  modal-sm" >
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">REASON FOR DISAPPROVAL</h6>            
            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" style="background-color:white;color:black;">                          
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <textarea style="margin-bottom: 8px; font-size: 14px; resize: none;" class="form-control" rows="4" name="reason" id="reason" maxlength="100"></textarea><br>
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