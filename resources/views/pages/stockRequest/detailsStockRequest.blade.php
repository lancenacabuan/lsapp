<div class="modal fade in" id="detailsStockRequest">
    <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">STOCK REQUEST DETAILS</h6>
            <button type="button" class="btn-close btn-close-white close btnClose" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <input type="hidden" id="requested_by_id_details">
            <input type="hidden" id="req_type_id_details">
            <input type="hidden" id="status_id_details">
            <input type="hidden" id="item_id_details">
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" id="status_label" style="width: 160px;">Status</label>
                <input class="form-control form-control-sm" id="status_details" style="width: 280px; margin-right: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" type="text" readonly>
                <label class="form-control form-control-sm dfcshow assethide" style="width: 160px; display: none;">Original Request No.</label>
                <input class="form-control form-control-sm dfcshow assethide" id="asm_request_num_details" onclick="copyAsmReqNum()" style="width: 280px; margin-right: 10px; display: none;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Request Type</label>
                <input class="form-control form-control-sm" id="request_type_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                <label class="form-control form-control-sm" style="width: 160px;">Stock Request No.</label>
                <input class="form-control form-control-sm" id="request_num_details" onclick="copyReqNum()" style="width: 280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Needed</label>
                <input class="form-control form-control-sm"  id="needdate_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                <label class="form-control form-control-sm" style="width: 160px;">Date Requested</label>
                <input class="form-control form-control-sm"  id="reqdate_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                <label class="form-control form-control-sm assethide" id="qty_label" style="width: 60px; display: none;">Qty</label>
                <input class="form-control form-control-sm assethide" id="qty_details" style="width: 100px; display: none;" type="number" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm dfchide assethide" id="client_name_label" style="width: 160px;">Client Name</label>
                <input class="form-control form-control-sm dfchide assethide" id="client_name_details" style="width: 280px; margin-right: 10px;" type="text" placeholder="Required Field" readonly>
                <label class="form-control form-control-sm dfchide assethide" id="orderID_label" style="width: 160px; display: none;">Order ID</label>
                <input class="form-control form-control-sm dfchide assethide spChar" id="orderID_details" style="width: 280px; margin-right: 10px; display: none;" type="text" placeholder="Required Field" readonly>
                <label class="form-control form-control-sm editAssetHide" style="width: 160px;">Requested By</label>
                <input class="form-control form-control-sm editAssetHide" id="requested_by_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                <label class="form-control form-control-sm assetshow editAssetHide" style="width: 160px; display: none;">Approver Name</label>
                <input class="form-control form-control-sm assetshow editAssetHide" id="approved_by_details" style="width: 280px; margin-right: 10px; display: none;" type="text" readonly>
                <label class="form-control form-control-sm assethide" id="item_desc_label" style="width: 160px; display: none;">Assembled Item Name</label>
                <input class="form-control form-control-sm assethide" id="item_desc_details" style="width: 450px; margin-right: 10px; display: none;" type="text" readonly>
                <label class="form-control form-control-sm editAssetShow" style="width: 160px; display: none;">Requested By</label>
                <input class="form-control form-control-sm editAssetShow" id="asset_reqby_details" style="width: 280px; margin-right: 10px; display: none;" type="search" placeholder="Required Field" maxlength="100">
                <label class="form-control form-control-sm editAssetShow" style="width: 160px; display: none;">Company Name</label>
                <select class="form-select form-control editAssetShow" id="company_details" style="margin-right: 10px; zoom: 83%; width: 337px; display: none;">
                    <option value="" selected disabled>Select Company</option>
                    <option value="Apsoft" style="color: Black;">Apsoft, Inc.</option>
                    <option value="Ideaserv" style="color: Black;">Ideaserv Systems, Inc.</option>
                    <option value="NuServ" style="color: Black;">NuServ Solutions, Inc.</option>
                    <option value="Phillogix" style="color: Black;">Phillogix Systems, Inc.</option>
                </select>
            </div>
            <div class="form-inline editAssetShow" style="margin-left: 35px; margin-top: 10px; display: none;">
                <label class="form-control form-control-sm" style="width: 160px;">Requester Email Address</label>
                <input class="form-control form-control-sm" id="asset_reqby_email_details" style="width: 280px; margin-right: 10px;" type="search" placeholder="Required Field" maxlength="100" onselectstart="return false" onpaste="return false;" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false">
                <label class="form-control form-control-sm" style="width: 160px;">Approver Name</label>
                <select class="form-select form-control" id="asset_apvby_details" style="margin-right: 10px; zoom: 83%; width: 337px;">
                    <option value="" selected disabled>Select Approver</option>
                </select>
                <input type="hidden" id="asset_apvby_email_details">
            </div>
            <div class="form-inline assethide" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm dfchide" id="location_label" style="width: 160px;">Address / Branch</label>
                <input class="form-control form-control-sm dfchide" id="location_details" style="width: 280px; margin-right: 10px;" type="text" placeholder="Required Field" readonly>
                <label class="form-control form-control-sm dfchide soldShow" id="contact_label" style="width: 160px;">Contact Person</label>
                <input class="form-control form-control-sm dfchide soldShow" id="contact_details" style="width: 280px; margin-right: 10px;" type="text" placeholder="Required Field" readonly>
                <label class="form-control form-control-sm" id="warehouse_label" style="width: 160px; display: none;">TO New Location</label>
                <select class="form-select form-control-sm" id="warehouse_details" style=" margin-right: 10px; font-size: .85rem; padding: 0.25rem 0.5rem; height: 30px !important; width: 280px; display: none;">
                    <option value="" selected disabled>Select Location</option>
                    <option value="1">A1</option>
                    <option value="2">A2</option>
                    <option value="3">A3</option>
                    <option value="4">A4</option>
                </select>
            </div>
            <div class="form-inline assethide" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm dfchide soldShow" id="remarks_label" style="margin-top: -38px; width: 160px;">Remarks</label>
                <textarea class="form-control dfchide soldShow" id="remarks_details" style="width: 280px; margin-right: 10px; font-size: 12px; resize: none;" rows="3" placeholder="Optional Field" readonly></textarea>
                <label class="form-control form-control-sm dfchide sales_details soldShow" style="margin-top: -38px; width: 160px;">Reference SO/PO No.</label>
                <textarea class="form-control dfchide sales_details soldShow spChar" id="reference_details" style="width: 280px; margin-right: 10px; font-size: 12px; resize: none;" rows="3" placeholder="Please input SO/PO Number.                       (Press 'Enter' to separate multiple inputs.)          Required Field" readonly></textarea>
            </div>
            <div class="form-inline assetshow" style="margin-left: 35px; margin-top: 10px; display: none;">
                @role('admin|encoder|viewer|approver - warehouse') {{---ROLES---}}
                <button type="button" id="btnShowAttachment" class="btn btn-primary bp" style="zoom: 85%; width: 188px; margin-left: 530px;">VIEW ATTACHMENT</button>
                <button type="button" id="btnHideAttachment" class="btn btn-primary bp" style="zoom: 85%; width: 188px; margin-left: 530px; display: none;">HIDE ATTACHMENT</button>
                @endrole
            </div>
            <div class="form-inline assethide" style="margin-left: 35px; margin-top: 10px;">
                @role('sales|approver - sales|accounting') {{---ROLES---}}
                <span style="margin-top: -52px;">
                    <button type="button" id="btnShowAttachment" class="btn btn-primary bp sales_details soldShow" style="zoom: 85%; width: 188px; margin-left: 530px;">VIEW ATTACHMENT</button>
                    <button type="button" id="btnHideAttachment" class="btn btn-primary bp" style="zoom: 85%; width: 188px; margin-left: 530px; display: none;">HIDE ATTACHMENT</button>
                </span>
                @endrole
                <span id="warehouse_note" style="color: Red; font-size: 12px; display: none;">Please select location to store the Assembled Item/s.</span>
            </div>
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm reason_class" name="reason_label" id="reason_label" style="margin-top: -56px; width: 160px; display: none;">Disapproval Reason</label>
                <textarea class="form-control reason_class" name="reason_details" id="reason_details" style="width: 280px; margin-right: 10px; font-size: 12px; resize: none; display: none;" rows="4" readonly></textarea>
                <label id="lblReupload" class="form-control form-control-sm reupload classReupload" style="margin-left: 450px; width: 160px; display: none;" onclick="$('#reference_upload').click();">Attachment SO/PO</label>
                <button class="form-control btn btn-danger disupload classReupload" title="Remove Attachments" style="margin-left: -30px; height: 28px; width: 30px; padding: 0px; font-size: 18px; display: none;"><i class="fa fa-trash"></i></button>
                <button class="form-control btn btn-primary bp reupload classReupload" style="width: 280px; height: 28px; line-height: 30%; font-size: 12px; text-align: left; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: none;" onclick="$('#reference_upload').click();"><i class="fa fa-image" style="zoom: 120%;"></i>&nbsp;&nbsp;<span class="upload_label">Upload PDF or Image less than 5MB each</span></button>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <span class="reupload classReupload" style="margin-left: 450px; color: Red; font-size: 12px; display: none;">Use 'Ctrl + Left Click' to select multiple PDF and/or Image files for upload.</span>
            </div>
            <div class="valid_label alert alert-warning mt-4" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>CANNOT PROCEED:</strong> Requester Email Address has an invalid format!
            </div>
            <div class="header_label alert alert-primary mt-4" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>NOTE:</strong> Please fill up all required fields to proceed.
            </div>
            @role('admin|encoder') {{---ROLES---}}
            <div id="divAssembly" style="display: none;">
            <hr>
            <button type="button" class="btnReceiveAssembled btn btn-primary float-right bp ml-2" disabled>RECEIVE ASSEMBLED</button>
            <button type="button" id="btnShowDetails" class="btn btn-primary float-right bp">SHOW DETAILS</button>
            </div>
            @endrole
        </div>
        <div id="attachmentModal" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100"><span id="txtAttachment">ATTACHMENT SO/PO</span></h6>
        </div>
        <div class="modal-body text-center">
            <button type="button" id="btnRemoveAttachment" class="btn btn-danger mb-4" style="zoom: 85%; font-weight: bold; display: none;">REMOVE ATTACHMENTS</button>
            <div id="slidesCtrl" class="w3-center">
                <div class="w3-section">
                  <button class="w3-button w3-light-grey" onclick="plusDivs(-1)">❮ Prev</button>
                  <button class="w3-button w3-light-grey" onclick="plusDivs(1)">Next ❯</button>
                </div>
                <div id="slidesBtn"></div>
                <br>
            </div>
            <div id="slidesContent">
            </div>
            <div id="hiddenContent" style="display: none;"></div>
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
                <label class="form-control form-control-sm" style="width: 160px;">Date Received</label>
                <input class="form-control form-control-sm" id="recsched" style="width: 280px;" type="text" readonly>
            </div>
            <br>
            <table id="asmItems" class="table asmItems display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>
                    <tr>
                        <th>ITEM CODE</th>
                        <th>ITEM DESCRIPTION</th>
                        <th class="sum">QTY</th>
                        <th>UOM</th>
                        <th>SERIAL</th>
                        <th>LOCATION</th>
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
        </div>
        </div>
        <div id="request_info">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">REQUEST DETAILS</h6>
        </div>
        <div class="modal-body">
            @role('admin|encoder') {{---ROLES---}}
            <div id="proceed_label" class="alert alert-primary" role="alert">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>NOTE:</strong> Please click table rows to select from the requested items for preparation.
            </div>
            <div id="warning" class="alert alert-warning" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>CANNOT PROCEED:</strong> Available stocks of MAIN BRANCH should be equal or more than the quantity of all requested items.
            </div>
            <div id="warningdfc" class="alert alert-warning rcvDef" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>CANNOT PROCEED:</strong> Please receive first <b>defective items</b> by clicking the <b>DEFECTIVE DETAILS</b> button below before processing replacements.
            </div>
            @endrole
            @role('sales|approver - sales|accounting') {{---ROLES---}}
            <div id="sd1" style="display: none;">
            <table id="stockDetailsrequest" class="table stockDetails1 display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>
                    <tr>
                        <th>ITEM CODE</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>UOM</th>
                        <th class="sum">REQUESTED</th>
                        <th>PENDING</th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot style="font-size: 14px;">
                    <tr>
                        <th colspan="3" style="text-align: right;">TOTAL ITEM COUNT:</th>
                        <th></th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
            </div>
            <div id="sd2" style="display: none;">
            <table id="stockDetailsrequest" class="table stockDetails2 display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>
                    <tr>
                        <th>ITEM CODE</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>UOM</th>
                        <th class="sum">REQUESTED</th>
                        <th>PENDING</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot style="font-size: 14px;">
                    <tr>
                        <th colspan="3" style="text-align: right;">TOTAL ITEM COUNT:</th>
                        <th></th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
            </div>
            @endrole
            @role('admin|encoder|viewer|approver - warehouse') {{---ROLES---}}
            <table id="stockDetailsrequest" class="table stockDetails table-hover display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>
                    <tr>
                        <th>ITEM CODE</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>UOM</th>
                        <th class="sum">REQUESTED</th>
                        <th>PENDING</th>
                        <th>MAIN BRANCH</th>
                        <th></th>
                        <th>A1</th>
                        <th>A2</th>
                        <th>A3</th>
                        <th>A4</th>
                        <th style="color: red;">BALINTAWAK</th>
                        <th style="color: red;">MALABON</th>
                    </tr>
                </thead>
                <tfoot style="font-size: 14px;">
                    <tr>
                        <th colspan="3" style="text-align: right;">TOTAL ITEM COUNT:</th>
                        <th></th>
                        <th colspan="9"></th>
                    </tr>
                </tfoot>
            </table>
            @endrole
            @role('approver - sales|approver - warehouse') {{---ROLES---}}
            <input type="button" class="btn btn-primary float-right bp mt-4" id="btnApprove" value="APPROVE">
            <input type="button" class="btn btn-primary bp mt-4" id="btnDisapprove" value="DISAPPROVE">
            @endrole
            @role('admin|encoder') {{---ROLES---}}
            <button type="button" id="btnDelete" class="btn btn-outline-danger font-weight-bold mt-4" style="display: none;">DELETE</button>
            <input type="button" class="btn btn-primary float-right bp my-4" id="btnProceed" value="PROCEED" disabled>
            <button type="button" id="btnDefDetails" class="btn btn-primary float-right bp my-4 rcvDef" style="display: none;">DEFECTIVE DETAILS</button>
            @endrole
            @role('sales') {{---ROLES---}}
            <button type="button" id="btnDelete" class="btn btn-outline-danger font-weight-bold mt-4">DELETE</button>
            @endrole
            <button type="button" id="btnSaveChanges" class="btn btn-primary bp my-4 float-right ml-2" style="width: 140px; display: none;">SAVE CHANGES</button>
            <button type="button" id="btnEditDetails" class="btn btn-success bp my-4 float-right ml-2" style="width: 140px; display: none;">EDIT DETAILS</button>
            <button type="button" class="btnCancelDetails btnClose btn btn-primary bp mt-4 float-right" style="display: none;" data-bs-dismiss="modal">CANCEL</button>
        </div>
        </div>
        <div id="requestItems" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">REQUESTED ITEMS</h6>
        </div>
        <div class="modal-body">
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" id="schedBy_label" style="width: 160px; margin-bottom: 10px;">Scheduled By</label>
                <input class="form-control form-control-sm" id="schedBy" style="width: 280px; margin-bottom: 10px;" type="text" value="{{auth()->user()->name}}" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" id="schedOn_label" style="width: 160px;">Date Scheduled</label>
                <input class="form-control form-control-sm" id="schedOn" style="width: 280px;" type="date">
                <input class="form-control form-control-sm" id="schedOn_text" style="width: 280px; display: none;" type="text">
            </div>
            <div class="form-inline" style="margin-left: 35px;" id="reqContents"></div>
            <div id="schedwarning" class="alert alert-warning mt-4" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>CANNOT PROCEED:</strong> Please select all corresponding <strong>SERIALS</strong> on every item to continue scheduling request. 
            </div>
            <hr>
            <input type="button" class="btn btn-primary bp" id="btnBack" value="BACK">
            <input type="button" class="btn btn-primary float-right bp" id="btnSubmit" value="SCHEDULE" disabled>
        </div>
        </div>
        <div id="receivedItemsModal" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 id="receivedheader" class="modal-title w-100">RECEIVED ITEM DETAILS</h6>
        </div>
        <div class="modal-body">
            <br>
            <table id="receivedItems" class="table receivedItems display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>
                    <tr>
                        <th>ITEM CODE</th>
                        <th>ITEM DESCRIPTION</th>
                        <th class="sum">QTY</th>
                        <th>UOM</th>
                        <th>SERIAL</th>
                    </tr>
                </thead>
                <tfoot style="font-size: 14px;">
                    <tr>
                        <th colspan="2" style="text-align: right;">TOTAL ITEM COUNT:</th>
                        <th></th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
            <br>
            <hr>
            <button type="button" class="btnPrint btn btn-primary bp">PRINT PREVIEW</button>
        </div>
        </div>
        <div id="schedItemsModal" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 id="prepheader" class="modal-title w-100">SCHEDULED ITEM DETAILS</h6>
        </div>
        <div class="modal-body">
            <div id="defective_label" class="alert alert-warning" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>NOTE:</strong> Select table rows to confirm <b>defective items</b> then click the <b>DEFECTIVE</b> button below. Otherwise, click the <b>FOR STAGING</b> button to proceed.
            </div>
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Scheduled By</label>
                <input class="form-control form-control-sm" id="prep_by" style="width: 280px; margin-bottom: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Scheduled</label>
                <input class="form-control form-control-sm" id="sched" style="width: 280px;" type="text" readonly>
            </div>
            <br>
            @role('admin|encoder|approver - warehouse') {{---ROLES---}}
            <table id="schedItems" class="table schedItems display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>
                    <tr>
                        <th>ITEM CODE</th>
                        <th>ITEM DESCRIPTION</th>
                        <th class="sum">QTY</th>
                        <th>UOM</th>
                        <th>SERIAL</th>
                        <th>LOCATION</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot style="font-size: 14px;">
                    <tr>
                        <th colspan="2" style="text-align: right;">TOTAL ITEM COUNT:</th>
                        <th></th>
                        <th colspan="5"></th>
                    </tr>
                </tfoot>
            </table>
            @endrole
            @role('sales|viewer|approver - sales|accounting') {{---ROLES---}}
            <table id="schedItems1" class="table schedItems1 display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>
                    <tr>
                        <th>ITEM CODE</th>
                        <th>ITEM DESCRIPTION</th>
                        <th class="sum">QTY</th>
                        <th>UOM</th>
                        <th>SERIAL</th>
                        <th>LOCATION</th>
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
            @endrole
            <br>
            <hr>
            @role('sales') {{---ROLES---}}
            <button type="button" id="btnStaging" class="btn btn-primary float-right bp" style="display: none;">FOR STAGING</button>
            <button type="button" id="btnDefective" class="btn btn-primary float-right bp" style="display: none;">DEFECTIVE</button>
            @endrole
            @role('admin|encoder') {{---ROLES---}}
            <input type="button" class="btn btn-primary float-right bp btnTransit" value="FOR RECEIVING">
            @endrole
            <button type="button" class="btnPrint btn btn-primary bp">PRINT PREVIEW</button>
        </div>
        </div>
        <div id="transitItemsModal" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 id="modalheader" class="modal-title w-100">FOR RECEIVING ITEM DETAILS</h6>
        </div>
        <div class="modal-body">
            @role('sales') {{---ROLES---}}
            <div id="receive_label" class="alert alert-primary" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>NOTE:</strong> <span id="receive_text">Please select table rows to confirm <b>received items</b> then click the <b>RECEIVE</b> button below.</span>
            </div>
            <div id="demoreceive_label" class="alert alert-primary" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>NOTE:</strong> Please select table rows to confirm items <b>FOR SALE or FOR RETURN</b> then click the corresponding button below.
            </div>
            <div id="reissue_label" class="alert alert-info" role="alert" style="display: none;">
                <i class='fa fa-info-circle'></i>
                <strong>INFO:</strong> You can select table rows to <b>re-issue items</b> for another request then click the <b>RE-ISSUE</b> button below.
            </div>
            @endrole
            @role('admin|encoder') {{---ROLES---}}
            <div id="receive_label" class="alert alert-primary" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>NOTE:</strong> <span id="receive_text">Please select table rows to confirm <b>received items</b> then click the <b>RECEIVE</b> button below.</span>
            </div>
            @endrole
            <div class="prephide">
                <div class="form-inline" style="margin-left: 35px;">
                    <label class="form-control form-control-sm" id="prep_by1_label" style="width: 160px; margin-bottom: 10px;">Scheduled By</label>
                    <input class="form-control form-control-sm" id="prep_by1" style="width: 280px; margin-bottom: 10px;" type="text" readonly>
                </div>
                <div class="form-inline" style="margin-left: 35px;">
                    <label class="form-control form-control-sm" id="sched1_label" style="width: 160px;">Date Scheduled</label>
                    <input class="form-control form-control-sm" id="sched1" style="width: 280px;" type="text" readonly>
                </div>
            </div>
            <br>
            @role('sales|viewer|approver - sales|accounting') {{---ROLES---}}
            <table id="transItems" class="table transItems display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>
                    <tr>
                        <th>ITEM CODE</th>
                        <th>ITEM DESCRIPTION</th>
                        <th class="sum">QTY</th>
                        <th>UOM</th>
                        <th>SERIAL</th>
                        <th>LOCATION</th>
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
            @endrole
            @role('admin|encoder|approver - warehouse') {{---ROLES---}}
            <table id="transItems1" class="table transItems1 display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                <thead>
                    <tr>
                        <th>ITEM CODE</th>
                        <th>ITEM DESCRIPTION</th>
                        <th class="sum">QTY</th>
                        <th>UOM</th>
                        <th>SERIAL</th>
                        <th>LOCATION</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot style="font-size: 14px;">
                    <tr>
                        <th colspan="2" style="text-align: right;">TOTAL ITEM COUNT:</th>
                        <th></th>
                        <th colspan="5"></th>
                    </tr>
                </tfoot>
            </table> 
            @endrole
            <br>
            <div class="prephide soldhide">
            <hr>
            @role('sales') {{---ROLES---}}
            <input type="button" class="btn btn-primary float-right bp ml-2 btnReceive" id="btnReceive" value="RECEIVE" disabled>
            <input type="button" class="btn btn-primary float-right bp ml-2 btnReceiveRpl btnReceive" style="display: none;" id="btnReceiveRpl" value="RECEIVE" disabled>
            <input type="button" class="btn btn-primary float-right bp btnReissue" style="display: none;" value="RE-ISSUE" disabled>
            <button type="button" id="btnSale" class="btn btn-primary float-right bp ml-2" style="display: none;" disabled>SALE</button>
            <button type="button" id="btnReturn" class="btn btn-primary float-right bp" style="display: none;" disabled>RETURN</button>
            @endrole
            @role('admin|encoder') {{---ROLES---}}
            <input type="button" class="btn btn-primary float-right bp btnReceive" id="btnReceive" style="display: none;" value="RECEIVE" disabled>
            <button type="button" class="btnReceiveAssembled btn btn-primary float-right bp ml-2" style="display: none;" disabled>RECEIVE ASSEMBLED</button>
            <button type="button" id="btnHideDetails" class="btn btn-primary float-right bp" style="display: none;">HIDE DETAILS</button>
            @endrole
            <button type="button" class="btnPrint btn btn-primary bp">PRINT PREVIEW</button>
            <input type="button" class="btn btn-outline-danger font-weight-bold ml-2" id="btnCancelRequest" style="display: none;" value="CANCEL REQUEST">
            </div>
            <div class="pendshow" style="display: none;">
            <hr>
            <button type="button" class="btnPrint btn btn-primary bp">PRINT PREVIEW</button>
            <button type="button" id="btnPending" class="btn btn-primary float-right bp">PENDING DETAILS</button>
            </div>
        </div>
        </div>
        <div id="incItemsModal" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 id="incmodalheader" class="modal-title w-100">INCOMPLETE ITEM DETAILS</h6>
        </div>
        <div class="modal-body">
            @role('sales') {{---ROLES---}}
            <div id="increceive_label" class="alert alert-primary" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>NOTE:</strong> Please select table rows to confirm <b>received items</b> then click the <b>RECEIVE</b> button below.
            </div>
            @endrole
            @role('admin|encoder') {{---ROLES---}}
            <div id="increceive_label" class="alert alert-primary" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>NOTE:</strong> Please select table rows to confirm <b>received items</b> then click the <b>RECEIVE</b> button below.
            </div>
            @endrole
            @role('admin|encoder') {{---ROLES---}}
            <div id="retreceive_label" class="alert alert-primary" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>NOTE:</strong> Please select table rows to confirm <b>received items</b> then click the <b>RECEIVE</b> button below.
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
                        <th>LOCATION</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot style="font-size: 14px;">
                    <tr>
                        <th colspan="2" style="text-align: right;">TOTAL ITEM COUNT:</th>
                        <th></th>
                        <th colspan="5"></th>
                    </tr>
                </tfoot>
            </table>
            <br>
            @role('admin|encoder') {{---ROLES---}}
            <div id="incFooter">
            <hr>
            <button type="button" class="btn btn-primary float-right bp btnReceiveReturned" style="display: none;" disabled>RECEIVE</button>
            <input type="button" class="btn btn-primary float-right bp" id="btnReschedule" style="display: none;" value="RESCHEDULE">
            <input type="button" class="btn btn-primary float-right bp btnTransit" style="display: none;" value="FOR RECEIVING">
            <input type="button" class="btn btn-primary float-right bp rcvShow ml-2" id="btnReceiveDfc" style="display: none;" value="RECEIVE DEFECTIVE" disabled>
            <input type="button" class="btn btn-primary float-right bp rcvShow" id="showMore" style="display: none;" value="SHOW DETAILS">
            <input type="button" class="btn btn-primary float-right bp rcvShow" id="showLess" style="display: none;" value="HIDE DETAILS">
            <div class="mb-3"><br></div>
            </div>
            @endrole
            @role('admin|encoder') {{---ROLES---}}
            <div id="inc2Footer" style="display: none">
            <hr>
            <input type="button" class="btn btn-primary float-right bp btnReceive" id="btnReceiveInc" value="RECEIVE" disabled>
            <div class="mb-3"><br></div>
            </div>
            @endrole
            @role('sales') {{---ROLES---}}
            <div id="inc2Footer" style="display: none">
            <hr>
            <button type="button" class="btn btn-primary float-right bp btnReceiveRpl btnReceive" style="display: none;" id="btnReceiveRplInc" disabled>RECEIVE</button>
            <input type="button" class="btn btn-primary float-right bp btnReceive" id="btnReceiveInc" value="RECEIVE" disabled>
            <div class="mb-3"><br></div>
            </div>
            @endrole
        </div>
        </div>
        <div id="soldItems" style="display: none;">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">ITEMS FOR SALE DETAILS</h6>
        </div>
        <div class="modal-body">
            <div class="form-inline" style="margin-left: 35px;" id="soldContents"></div>
            <div id="soldwarning" class="alert alert-warning mt-4" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>CANNOT PROCEED:</strong> Please select all corresponding <strong>WARRANTY TYPES</strong> on every item to continue selling items. 
            </div>
            <hr>
            <input type="button" class="btn btn-primary bp" id="btnCancel" value="BACK">
            <input type="button" class="btn btn-primary float-right bp" id="btnConfirm" value="CONFIRM" disabled>
        </div>
        </div>
    </div>
    </div>
</div>
<div class="modal fade in" id="inputSerialModal">
    <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">ASSEMBLED ITEM SERIAL</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center" style="background-color: white; color: black;">                          
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <div class="form-inline" id="serialList"></div>
            <span id="serial_note" style="color: Red; font-size: 12px;">*Please fill up all required fields.</span>
            <hr>
            <button type="button" id="btnReceiveAssembled" class="btn btn-primary float-right bp" style="zoom: 80%;">CONFIRM</button>
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
            <button type="button" id="btnEdit" class="btn btn-primary float-right bp ml-2" style="zoom: 80%;">EDIT</button>
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
<div class="modal fade in" id="referenceModal">
    <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">REFERENCE SO/PO</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <textarea class="form-control spChar" id="x_reference" style="margin-bottom: 8px; font-size: 12px; resize: none;" rows="3" placeholder="Please input SO/PO Number.                       (Press 'Enter' to separate multiple inputs.)          Required Field"></textarea>
            <button class="form-control btn btn-danger disupload1" title="Remove Attachments" style="zoom: 90%; margin-top: -8px; width: 30px; height: 30px; padding: 0px; display: none;"><i class="fa fa-trash"></i></button>
            <button id="xbtn_upload" class="form-control btn btn-primary bp" style="zoom: 90%; margin-bottom: 8px; height: 28px; line-height: 30%; font-size: 12px; text-align: left; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" onclick="$('#reference_upload').click();"><i class="fa fa-image" style="zoom: 120%;"></i>&nbsp;&nbsp;<span class="upload_label">Upload PDF or Image less than 5MB each</span></button>
            <span style="color: Red; font-size: 12px;">Use 'Ctrl + Left Click' to select multiple files.</span>
            <span id="reference_note" style="color: Red; font-size: 12px;">*SO/PO Number and Attachment are required.</span>
            <br><button type="button" id="btnReference" class="btn btn-primary mt-2 float-right bp" style="zoom: 80%;">OK</button>
        </div>
    </div>
    </div>
</div>
<div class="modal fade in" id="warrantyModal">
    <div class="modal-dialog modal-dialog-centered modal">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100 warranty_title">WARRANTY DETAILS</h6>
            <button type="button" class="btn-close btn-close-white close detailsClose" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <div style="zoom: 85%; margin-top: 5px; margin-left: 35px; line-height: 70%;">
                <div class="form-inline" style="margin-left: 35px;">
                    <label class="form-control form-control-sm" style="width: 190px; border-color: white; font-weight: bolder;">DURATION:</label>
                    <input class="form-control form-control-sm duration" style="width: 250px; margin-right: 10px; font-size: 18px; border-color: white; background-color: white; font-weight: bolder; pointer-events: none;" type="text" readonly>
                </div>
                <div class="form-inline" style="margin-left: 35px;">
                    <label class="form-control form-control-sm" style="width: 160px; border-color: white; font-weight: bolder;">INCLUSIVE:</label>
                </div>
                <div style="margin-top: -22px;">
                    <p class="phone listInclusive" style="margin-left: 220px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Phone Support</p>
                    <p class="onsite listInclusive" style="margin-left: 220px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Onsite Support</p>
                    <p class="software listInclusive" style="margin-left: 220px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Software</p>
                    <p class="hardware listInclusive" style="margin-left: 220px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Hardware</p>
                    <p class="replacement listInclusive" style="margin-left: 220px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Parts Replacement</p>
                    <p class="su listInclusive" style="margin-left: 220px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Service Unit</p>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<style>
    .mySlides{
        display:none
    }
</style>
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
<script>
    var slideIndex = 1;
    showDivs(slideIndex);
    
    function plusDivs(n) {
        showDivs(slideIndex += n);
    }
    
    function currentDiv(n) {
        showDivs(slideIndex = n);
    }
    
    function showDivs(n) {
        var i;
        var x = document.getElementsByClassName("mySlides");
        var dots = document.getElementsByClassName("demo");
        if (n > x.length) {slideIndex = 1}    
        if (n < 1) {slideIndex = x.length}
        for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";  
        }
        for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" w3-blue", "");
        }
        $(x[slideIndex-1]).show();
        $(dots[slideIndex-1]).addClass("w3-blue");
    }
</script>