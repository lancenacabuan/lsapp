<div class="modal fade in" id="newMerchRequest">
    <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">NEW MERCHANT STOCK REQUEST</h6>
            <button type="button" class="btn-close btn-close-white close btnClose" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <div class="form-inline" style="margin-left: 5px;">
                <label class="form-control form-control-sm" style="width: 160px;">Order ID</label>
                <input class="form-control form-control-sm spChar" id="orderID" style="width: 280px; margin-right: 10px;" type="text" placeholder="Required Field" maxlength="50">
                <label class="form-control form-control-sm" style="width: 160px;">Stock Request No.</label>
                <input class="form-control form-control-sm" id="request_num" style="width: 250px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left: 5px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Needed</label>
                <input class="form-control form-control-sm"  id="needdate" style="width: 280px; margin-right: 10px;" type="date">
                <label class="form-control form-control-sm" style="width: 160px;">Date Requested</label>
                <input class="form-control form-control-sm"  id="reqdate" style="width: 250px; margin-right: 10px;" type="text" readonly value="{{Carbon\Carbon::now()->isoformat('dddd, MMMM DD, YYYY')}}">
            </div>
            <div class="form-inline" style="margin-left: 5px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;" onclick="$('#reference_upload').click();">Attachment Reference</label>
                <button class="form-control btn btn-danger disupload" title="Remove Attachments" style="height: 28px; width: 30px; padding: 0px; font-size: 18px; display: none;"><i class="fa fa-trash"></i></button>
                <button id="btnAttach" class="form-control btn btn-primary bp" style="margin-right: 10px; width: 280px; height: 28px; line-height: 30%; font-size: 12px; text-align: left; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" onclick="$('#reference_upload').click();"><i class="fa fa-image" style="zoom: 120%;"></i>&nbsp;&nbsp;<span class="upload_label">Upload PDF or Image less than 5MB each</span></button>
                <form class="d-none" id="formUpload" action="/merchant/uploadFile" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
                    <input type="hidden" name="reqnum" id="reqnum">
                    <input type="hidden" name="action" id="action" value="SUBMIT">
                    <input id="reference_upload" name="reference_upload[]" type="file" style="zoom: 90%; display: none;" onchange="validate_fileupload(this);" multiple>
                    <button class="d-none" id="btnUpload" type="submit" form="formUpload" value="Submit">UPLOAD</button>
                </form>
                <label class="form-control form-control-sm" style="width: 160px;">Requested By</label>
                <input class="form-control form-control-sm" id="requested_by" style="width: 250px; margin-right: 10px;" type="text" readonly value="{{auth()->user()->name}}">
            </div>
            <div class="form-inline" style="margin-left: 5px; margin-top: 10px;">
                <span style="color: Red; font-size: 12px;">Use 'Ctrl + Left Click' to select multiple PDF and/or Image files for upload.</span>
            </div>
            <div id="warrantyDetails" style="zoom: 85%; height: 165px; margin-top: -165px; margin-left: 990px; line-height: 70%; display: none;">
                <div class="form-inline" style="margin-left: 35px;">
                    <input class="form-control form-control-sm warrantyName" style="width: 300px; margin-right: 10px; font-size: 18px; border-color: white; background-color: white; font-weight: bolder; pointer-events: none;" type="text" readonly>
                </div>
                <div class="form-inline" style="margin-left: 35px;">
                    <label class="form-control form-control-sm" style="width: 105px; border-color: white; font-weight: bolder;">DURATION:</label>
                    <input class="form-control form-control-sm duration" style="width: 150px; margin-right: 10px; font-size: 18px; border-color: white; background-color: white; font-weight: bolder; pointer-events: none;" type="text" readonly>
                </div>
                <div class="form-inline" style="margin-left: 35px;">
                    <label class="form-control form-control-sm" style="width: 105px; border-color: white; font-weight: bolder;">INCLUSIVE:</label>
                </div>
                <div style="margin-top: -22px;">
                    <p class="phone listInclusive" style="margin-left: 150px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Phone Support</p>
                    <p class="onsite listInclusive" style="margin-left: 150px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Onsite Support</p>
                    <p class="software listInclusive" style="margin-left: 150px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Software</p>
                    <p class="hardware listInclusive" style="margin-left: 150px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Hardware</p>
                    <p class="replacement listInclusive" style="margin-left: 150px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Parts Replacement</p>
                    <p class="su listInclusive" style="margin-left: 150px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Service Unit</p>
                </div>
            </div>
            <div class="form-inline" style="margin-left: 5px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 860px; border-color: white;">&nbsp;</label>
            </div>
            <div class="header_label alert alert-primary mt-4" role="alert">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>NOTE:</strong> Please fill up all required fields to proceed.
            </div>
        </div>
        <div id="requestDetails" style="display: none;">
            <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
                <h6 class="modal-title w-100">REQUEST DETAILS</h6>
            </div>
            <div class="modal-body" style="background-color: white; color: black;">
                <form class="mt-2 mb-2">
                    <div class="form-inline" style="margin-left: 20px;">
                        <div class="f-outline">
                            <select class="forminput form-control form-select" id="categoryReq" name="categoryReq" style="width: 250px;" placeholder=" " required>
                                <option value="" selected disabled>Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{$category->id}}">{{strtoupper($category->category)}}</option>
                                @endforeach
                            </select>
                            <label for="categoryReq" class="formlabel form-label">Category</label>
                        </div>
                        <div class="f-outline" style="margin-left: 10px;">
                            <select class="forminput form-control form-select" id="itemReq" name="itemReq" style="width: 350px;" placeholder=" ">
                                <option value="" selected disabled>Select Item</option>
                            </select>
                            <label for="itemReq" class="formlabel form-label">Item Description</label>
                        </div>
                        <input class="d-none" id="prodcode" type="hidden"/>
                        <div class="f-outline" style="margin-left: 10px;">
                            <input class="forminput form-control" id="uom" name="uom" style="background-color: white; width: 70px;" type="text" placeholder=" " disabled>
                            <label for="uom" class="formlabel form-label">UOM</label>
                        </div>
                        <div class="f-outline" style="margin-left: 10px;">
                            <input class="forminput form-control" id="qtyReq" name="qtyReq" min="1" max="" style="width: 70px;" type="number" placeholder=" " onkeyup="if(value<1) value=1;">
                            <label for="qtyReq" class="formlabel form-label">Qty</label>
                        </div>
                        <div class="f-outline" style="margin-left: 10px;">
                            <select class="forminput form-control form-select classWarranty" id="warrantyReq" name="warrantyReq" style="width: 200px;" placeholder=" ">
                                <option value="" selected disabled>Select Warranty Type</option>
                                <option value="0">NO WARRANTY</option>
                                @foreach($warranty as $warranty_type)
                                    <option value="{{$warranty_type->id}}">{{strtoupper($warranty_type->Warranty_Name)}}</option>
                                @endforeach
                            </select>
                            <label for="warrantyReq" class="formlabel form-label classWarranty">Warranty Type</label>
                        </div>
                        <input type="button" class="add-row btn btn-primary bp" value="ADD ITEM" style="zoom: 90%; margin-left: 10px;">
                    </div>
                </form>
                <div class="container-fluid"  id="#stockRequestDiv">
                    <table id='stockRequestTable' class="table" style="cursor: pointer; font-size: 12px; display: none;">
                        <thead>
                            <tr>
                                <th style="display: none;">CATEGORY ID</th>
                                <th style="display: none;">ITEM ID</th>
                                <th style="display: none;">WARRANTY ID</th>
                                <th style="display: none;">CATEGORY</th>
                                <th>ITEM CODE</th>
                                <th>ITEM DESCRIPTION</th>
                                <th>QTY</th>
                                <th>UOM</th>
                                <th class="classWarranty">WARRANTY TYPE</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="submit_label alert alert-primary mt-4" role="alert">
                    <i class='fa fa-exclamation-triangle'></i>
                    <strong>NOTE:</strong> Please fill up all required fields to proceed.
                </div>
                <button type="button" class="btnClose btnCloseCancel btn btn-primary bp" style="display: none;" data-bs-dismiss="modal">CANCEL</button>
                <button type="button" id="btnSave" class="btn btn-primary float-right bp" style="display: none;">SUBMIT</button>
            </div>
        </div>
    </div>
    </div>
</div>