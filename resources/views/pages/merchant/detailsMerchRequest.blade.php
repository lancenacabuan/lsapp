<div class="modal fade in" id="detailsMerchRequest">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
                <h6 class="modal-title w-100">MERCHANT STOCK REQUEST DETAILS</h6>
                <button type="button" class="btn-close btn-close-white close btnClose" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background-color: white; color: black;">
                <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
                <input type="hidden" id="req_type_id_details">
                <input type="hidden" id="status_id_details">
                <div class="form-inline" style="margin-left: 35px;">
                    <label class="form-control form-control-sm" id="status_label" style="width: 160px;">Status</label>
                    <input class="form-control form-control-sm" id="status_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                    <label class="form-control form-control-sm" style="width: 160px;">Stock Request No.</label>
                    <input class="form-control form-control-sm" id="request_num_details" onclick="copyReqNum()" style="width: 280px; margin-right: 10px;" type="text" readonly>
                </div>
                <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                    <label class="form-control form-control-sm" style="width: 160px;">Date Needed</label>
                    <input class="form-control form-control-sm"  id="needdate_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                    <label class="form-control form-control-sm" style="width: 160px;">Date Requested</label>
                    <input class="form-control form-control-sm"  id="reqdate_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                </div>
                <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                    <label class="form-control form-control-sm" style="width: 160px;">Order ID</label>
                    <input class="form-control form-control-sm spChar" id="orderID_details" style="width: 280px; margin-right: 10px;" type="text" placeholder="Required Field" maxlength="50" readonly>
                    <label class="form-control form-control-sm" style="width: 160px;">Requested By</label>
                    <input class="form-control form-control-sm" id="requested_by_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                </div>
                <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                    <button type="button" id="btnShowAttachment" class="btn btn-primary bp" style="zoom: 85%; width: 188px;">VIEW ATTACHMENT</button>
                    <button type="button" id="btnHideAttachment" class="btn btn-primary bp" style="zoom: 85%; width: 188px; display: none;">HIDE ATTACHMENT</button>
                </div>
            </div>
            <div id="attachmentModal" style="display: none;">
            <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
                <h6 class="modal-title w-100">ATTACHMENT REFERENCE</h6>
            </div>
            <div class="modal-body text-center">
                <button type="button" id="btnRemoveAttachment" class="btn btn-danger" style="zoom: 85%; font-weight: bold; display: none;">REMOVE ATTACHMENTS</button>
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
            <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
                <h6 class="modal-title w-100">REQUEST DETAILS</h6>
            </div>
            <div class="modal-body">
                <table id="stockDetails" class="table stockDetails display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
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
                <button type="button" id="btnDelete" class="btn btn-outline-danger font-weight-bold mt-4" style="display: none;">DELETE</button>
            </div>
            <div id="prepItemsModal" style="display: none;">
                <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
                    <h6 id="modalheader" class="modal-title w-100"></h6>
                </div>
                <div class="modal-body">
                    <div id="receive_label" class="alert alert-primary" role="alert" style="display: none;">
                        <i class='fa fa-exclamation-triangle'></i>
                        <strong>NOTE:</strong> Please select table rows to confirm <b>received items</b> then click the <b>RECEIVE</b> button below.
                    </div>
                    <div class="prephide">
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
                    <table id="prepItems" class="table prepItems display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
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
                    <hr>
                    <button type="button" class="btn btn-primary float-right bp btnReceive" style="display: none;" disabled>RECEIVE</button>
                    <button type="button" class="btnPrint btn btn-primary bp">PRINT PREVIEW</button>
                    <input type="button" class="btn btn-outline-danger font-weight-bold ml-2" id="btnCancelRequest" style="display: none;" value="CANCEL REQUEST">
                    <br>
                </div>
            </div>
            <div id="incItemsModal" style="display: none;">
                <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
                    <h6 id="incmodalheader" class="modal-title w-100">INCOMPLETE ITEM DETAILS</h6>
                </div>
                <div class="modal-body">
                    <div id="increceive_label" class="alert alert-primary" role="alert" style="display: none;">
                        <i class='fa fa-exclamation-triangle'></i>
                        <strong>NOTE:</strong> Please select table rows to confirm <b>received items</b> then click the <b>RECEIVE</b> button below.
                    </div>
                    <div class="form-inline divResched" style="margin-left: 35px; display: none;">
                        <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Rescheduled By</label>
                        <input class="form-control form-control-sm" id="reprep_by" style="width: 280px; margin-bottom: 10px;" type="text" readonly>
                    </div>
                    <div class="form-inline divResched" style="margin-left: 35px; display: none;">
                        <label class="form-control form-control-sm" style="width: 160px;">Date Rescheduled</label>
                        <input class="form-control form-control-sm" id="resched" style="width: 280px;" type="text" readonly>
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
                    <div id="incFooter" style="display: none;">
                        <hr>
                        <input type="button" class="btn btn-primary float-right bp btnReceive mb-4" value="RECEIVE" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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