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
                <input type="hidden" id="status_id_details">
                <input type="hidden" id="item_id_details">
                <div class="form-inline" style="margin-left: 35px;">
                    <label class="form-control form-control-sm" style="width: 160px;">Date Requested</label>
                    <input class="form-control form-control-sm"  id="reqdate_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                    <label class="form-control form-control-sm" style="width: 160px;">Stock Request No.</label>
                    <input class="form-control form-control-sm" id="request_num_details" onclick="copyReqNum()" style="width: 280px; margin-right: 10px;" type="text" readonly>
                    <label class="form-control form-control-sm rephide" id="qty_label" style="width: 60px;">Qty</label>
                    <input class="form-control form-control-sm rephide" id="qty_details" style="width: 100px;" type="number" readonly>
                </div>
                <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                    <label class="form-control form-control-sm" style="width: 160px;">Date Needed</label>
                    <input class="form-control form-control-sm"  id="needdate_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                    <label class="form-control form-control-sm rephide" id="item_desc_label" style="width: 160px;">Assembled Item Name</label>
                    <input class="form-control form-control-sm rephide" id="item_desc_details" style="width: 450px; margin-right: 10px;" type="text" readonly>
                    <label class="form-control form-control-sm repshow" style="width: 160px; display: none;">Assembly Request No.</label>
                    <input class="form-control form-control-sm repshow" id="asm_request_num_details" onclick="copyAsmReqNum()" style="width: 280px; margin-right: 10px; display: none;" type="text" readonly>
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
            <div id="asmItemsModal" style="display: none;">
                <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
                    <h6 class="modal-title w-100">RECEIVED ASSEMBLED ITEM DETAILS</h6>
                </div>
                <div class="modal-body">
                    <div class="form-inline" style="margin-left: 35px;">
                        <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Received By</label>
                        <input class="form-control form-control-sm" id="recby" style="width: 280px; margin-bottom: 10px;" type="text" readonly>
                    </div>
                    <div class="form-inline" style="margin-left: 35px;">
                        <label class="form-control form-control-sm" style="width: 160px;">Received On</label>
                        <input class="form-control form-control-sm" id="recsched" style="width: 280px;" type="text" readonly>
                    </div>
                    <br>
                    <table id="asmItems" class="table asmItems display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
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
                    <br>
                </div>
            </div>
            <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
                <h6 class="modal-title w-100">REQUEST DETAILS</h6>
            </div>
            <div class="modal-body">
                <br>
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
                </div>
                <div class="modal-body">
                    <div id="receive_label" class="alert alert-primary" role="alert" style="display: none;">
                        <i class='fa fa-exclamation-triangle'></i>
                        <strong>NOTE:</strong> Please select table rows to confirm <b>received items</b> then click the RECEIVE button below.
                    </div>
                    <div id="defective_label" class="alert alert-warning" role="alert" style="display: none;">
                        <i class='fa fa-exclamation-triangle'></i>
                        <strong>NOTE:</strong> Select table rows to confirm <b>defective items</b> then click the DEFECTIVE button below. Otherwise, click the ASSEMBLE button to proceed.
                    </div>
                    <div class="prephide">
                        <div class="form-inline" style="margin-left: 35px;">
                            <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Scheduled By</label>
                            <input class="form-control form-control-sm" id="prep_by1" style="width: 280px; margin-bottom: 10px;" type="text" readonly>
                        </div>
                        <div class="form-inline" style="margin-left: 35px;">
                            <label class="form-control form-control-sm" style="width: 160px;">Scheduled On</label>
                            <input class="form-control form-control-sm" id="sched1" style="width: 280px;" type="text" readonly>
                        </div>
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
                    <div class="prephide">
                        <hr>
                        <button type="button" class="btn btn-primary float-right bp btnReceive" style="display: none;" disabled>RECEIVE</button>
                        <button type="button" id="btnAssemble" class="btn btn-primary float-right bp" style="display: none;">ASSEMBLE</button>
                        <button type="button" id="btnDefective" class="btn btn-primary float-right bp" style="display: none;">DEFECTIVE</button>
                        <button type="button" class="btnPrint btn btn-primary bp">PRINT PREVIEW</button>
                        <br>
                    </div>
                    <div class="pendshow" style="display: none;">
                        <hr>
                        <button type="button" id="btnPending" class="btn btn-primary float-right bp">PENDING DETAILS</button>
                        <br>
                        <br>
                    </div>
                </div>
            </div>
            <div id="incItemsModal" style="display: none;">
                <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
                    <h6 id="incmodalheader" class="modal-title w-100">INCOMPLETE ITEM DETAILS</h6>
                </div>
                <div class="modal-body">
                    <div id="increceive_label" class="alert alert-primary" role="alert" style="display: none;">
                        <i class='fa fa-exclamation-triangle'></i>
                        <strong>NOTE:</strong> Please select table rows to confirm <b>received items</b> then click the RECEIVE button below.
                    </div>
                    <div class="form-inline divResched" style="margin-left: 35px; display: none;">
                        <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Rescheduled By</label>
                        <input class="form-control form-control-sm" id="reprep_by" style="width: 280px; margin-bottom: 10px;" type="text" readonly>
                    </div>
                    <div class="form-inline divResched" style="margin-left: 35px; display: none;">
                        <label class="form-control form-control-sm" style="width: 160px;">Rescheduled On</label>
                        <input class="form-control form-control-sm" id="resched" style="width: 280px;" type="text" readonly>
                    </div>
                    <br>
                    <table id="incItems" class="table incItems display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
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
                    <br>
                    <div id="incFooter">
                        <hr>
                        <input type="button" class="btn btn-primary float-right bp btnReceive" style="display: none;" value="RECEIVE" disabled>
                        <br>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>