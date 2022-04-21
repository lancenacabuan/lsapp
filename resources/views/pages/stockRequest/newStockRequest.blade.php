<div class="modal fade in" id="newStockRequest">
    <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">NEW STOCK REQUEST</h6>    
            <button type="button" class="btn-close btn-close-white close" id="close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Requested</label>
                <input class="form-control form-control-sm"  id="reqdate" style="width: 280px; margin-right: 10px;" type="text" readonly value="{{Carbon\Carbon::now()->isoformat('dddd, MMMM DD, YYYY')}}">
                <label class="form-control form-control-sm" style="width: 160px;">Stock Request No.</label>
                <input class="form-control form-control-sm" id="request_num" style="width: 280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Needed</label>
                <input class="form-control form-control-sm"  id="needdate" style="width: 280px; margin-right: 10px;" type="date">
                <label class="form-control form-control-sm" style="width: 160px;">Client Name</label>
                <input class="form-control form-control-sm" id="client_name" style="width: 280px; margin-right: 10px;" type="text" placeholder="Required Field">
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Requested By</label>
                <input class="form-control form-control-sm" id="requested_by" style="width: 280px; margin-right: 10px;" type="text" readonly value="{{auth()->user()->name}}">
                <label class="form-control form-control-sm" style="width: 160px;">Address / Branch</label>
                <input class="form-control form-control-sm" id="location" style="width: 280px; margin-right: 10px;" type="text" placeholder="Required Field">
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Request Type</label>
                <select class="form-select form-control-sm" id="request_type" style=" margin-right: 10px; font-size: .85rem; padding: 0.25rem 0.5rem; height: 30px !important; width: 280px;">
                    <option value="" selected disabled>Select Request Type</option>
                    @foreach($req_types as $req_type)
                        <option value="{{$req_type->id}}">{{strtoupper($req_type->name)}}</option>
                    @endforeach
                </select>
                <label class="form-control form-control-sm reference_field" style="width: 160px; display: none;">Reference SO/PO No.</label>
                <input class="form-control form-control-sm reference_field" id="reference" style="width: 280px; margin-right: 10px; display: none;" type="text" placeholder="Required Field">
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm reference_field" style="width: 160px; display: none;">Warranty Type</label>
                <select class="form-select form-control-sm reference_field" id="warranty_type" style=" margin-right: 10px; font-size: .85rem; padding: 0.25rem 0.5rem; height: 30px !important; width: 280px; display: none;">
                    <option value="" selected disabled>Select Warranty Type</option>
                    @foreach($warranty_types as $warranty_type)
                        <option value="{{$warranty_type->id}}">{{strtoupper($warranty_type->Warranty_Name)}}</option>
                    @endforeach
                </select>
                <form id="formUpload" action="{{ route('uploadFile') }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
                    <input type="hidden" name="reqnum" id="reqnum">
                    <input class="reference_field" id="reference_upload" name="reference_upload" type="file" style="zoom: 90%; display: none;" onchange="validate_fileupload(this);">
                    <button class="d-none" id="btnUpload" type="submit" form="formUpload" value="Submit">UPLOAD</button>
                </form>
            </div>
            <div id="warranty_field" style="zoom: 85%; margin-top: 5px; margin-left: 35px; line-height: 70%; display: none;">
                <div class="form-inline" style="margin-left: 35px;">
                    <label class="form-control form-control-sm" style="width: 190px; border-color: white; font-weight: bolder;">DURATION:</label>
                    <input class="form-control form-control-sm" id="duration" style="width: 250px; margin-right: 10px; font-size: 18px; border-color: white; background-color: white; font-weight: bolder; pointer-events: none;" type="text" readonly>
                </div>
                <div class="form-inline" style="margin-left: 35px;">
                    <label class="form-control form-control-sm" style="width: 160px; border-color: white; font-weight: bolder;">INCLUSIVE:</label>
                </div>
                <p id="phone" class="listInclusive" style="margin-left: 220px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Phone Support</p>
                <p id="onsite" class="listInclusive" style="margin-left: 220px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Onsite Support</p>
                <p id="software" class="listInclusive" style="margin-left: 220px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Software</p>
                <p id="hardware" class="listInclusive" style="margin-left: 220px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Hardware</p>
                <p id="replacement" class="listInclusive" style="margin-left: 220px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Parts Replacement</p>
                <p id="su" class="listInclusive" style="margin-left: 220px; display: none;"><span style="color: green; font-weight: bolder;">✓&nbsp;</span>Service Unit</p>
            </div>
        </div>
        <div id="requestDetails" style="display: none;">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">REQUEST DETAILS</h6>
        </div>
        <form class="mt-2 mb-2">
            <div class="form-inline" style="margin-left:50px;">
                <select class="form-select" id="categoryReq" class="form-control" style="font-size: 12px; padding: 0.25rem 0.5rem; height: 30px !important; width: 300px;" required>
                        <option value="" selected disabled>Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{$category->id}}">{{strtoupper($category->category)}}</option>
                        @endforeach
                </select>
                <select class="form-select" id="itemReq" class="form-control" style="font-size: 12px; padding: 0.25rem 0.5rem; height: 30px !important; width: 450px; margin-left: 10px;">
                    <option value="" selected disabled>Select Item</option>
                </select>
                <input class="form-control" id="uom" style="font-size: 12px; padding: 0.25rem 0.5rem; width: 70px; height: 30px; margin-left: 10px;" type="text" placeholder="UOM" readonly>
                <input class="form-control" id="qtyReq" min="0" max="" style="font-size: 12px; padding: 0.25rem 0.5rem; width: 70px; height: 30px; margin-left: 10px;" type="number" placeholder="Qty">
                <input type="button" class="add-row btn btn-primary bp" value="ADD ITEM" style="zoom: 75%; margin-left: 10px; margin-top: -1px;">
            </div>          
        </form>
        <div class="container-fluid"  id="#stockRequestDiv">
            <table id='stockRequestTable' class="table" style="cursor: pointer; font-size: 12px; display: none;">
                <thead>
                    <tr>
                        <th style="display: none;">CATEGORY ID</th>
                        <th style="display: none;">ITEM ID</th>
                        <th>CATEGORY</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>QTY</th>
                        <th>UOM</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>      
            </table>
        </div>
        <div class="col-md-12 mt-2 mb-4">
            <button type="submit" id="btnClose" class="btn btn-primary bp" style="display: none;" data-bs-dismiss="modal">CLOSE</button>
            <button type="submit" id="btnSave" class="btn btn-primary float-right bp" style="display: none;">SUBMIT</button>
        </div>
        </div>
    </div>
    </div>
</div>