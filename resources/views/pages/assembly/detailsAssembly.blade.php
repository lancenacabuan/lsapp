<div class="modal fade in" id="detailsAssembly">
    <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">ASSEMBLY REQUEST DETAILS</h6>            
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">                          
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <input type="hidden" id="req_type_id_details">
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Requested</label>
                <input class="form-control form-control-sm"  id="reqdate_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                <label class="form-control form-control-sm" style="width: 160px;">Stock Request No.</label>
                <input class="form-control form-control-sm" id="request_num_details" onclick="copyReqNum()" style="width: 280px; margin-right: 10px;" type="text" readonly>
                <label class="form-control form-control-sm" id="qty_label" style="width: 60px;">Qty</label>
                <input class="form-control form-control-sm" id="qty_details" style="width: 100px;" type="number" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Needed</label>
                <input class="form-control form-control-sm"  id="needdate_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                <label class="form-control form-control-sm" id="item_desc_label" style="width: 160px;">Assembled Item Name</label>
                <input class="form-control form-control-sm" id="item_desc_details" style="width: 450px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Requested By</label>
                <input class="form-control form-control-sm" id="requested_by_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Request Type</label>
                <input class="form-control form-control-sm" id="request_type_details" style="width: 280px; margin-right: 10px;" type="text" value="ASSEMBLY" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" id="status_label" style="width: 160px;">Status</label>
                <input class="form-control form-control-sm" id="status_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
            </div>
        </div>
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">REQUEST DETAILS</h6>
        </div>
        <div class="modal-body">
            <table id="stockDetails" class="table stockDetails display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>                            
                    <tr>
                        <th>CATEGORY</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>REQUESTED</th>
                        <th>UOM</th>
                    </tr>
                </thead>    
            </table>
            <br>
            <br>
        </div>
        <div id="prepItemsModal" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 id="modalheader" class="modal-title w-100"></h6>
        </div><br>      
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
            <table id="prepItems" class="table prepItems display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>                            
                    <tr>
                        <th>CATEGORY</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>QTY</th>
                        <th>UOM</th>
                        <th>SERIAL</th>
                        <th>LOCATION</th>
                        <th></th>
                    </tr>
                </thead>    
            </table> 
            <br>
            <hr>
            <button type="button" id="btnReceive" class="btn btn-primary float-right bp">RECEIVE</button>
            <button type="button" class="btnPrint btn btn-primary bp">PRINT PREVIEW</button>
            <br>
        </div>
        </div>
    </div>
    </div>
</div>