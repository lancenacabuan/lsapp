<div class="modal fade in" id="newStockTransfer">
    <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">NEW STOCK TRANSFER</h6>
            <button type="button" class="btn-close btn-close-white close" id="close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <div class="form-inline" style="margin-left: 20px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Needed</label>
                <input class="form-control form-control-sm"  id="needdate" style="width: 280px; margin-right: 10px;" type="date">
                <label class="form-control form-control-sm" style="width: 200px;">Stock Transfer Request No.</label>
                <input class="form-control form-control-sm" id="reqnum" style="width: 280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left: 20px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">FROM Location</label>
                <select class="form-select form-control-sm location" id="locfrom" style=" margin-right: 10px; font-size: .85rem; padding: 0.25rem 0.5rem; height:30px !important;width:280px;">
                    <option value="" selected disabled>Select Location</option>
                    <option value="5">BALINTAWAK</option>
                    <option value="6">MALABON</option>
                </select>
                <label class="form-control form-control-sm" style="width: 200px;">Date Requested</label>
                <input class="form-control form-control-sm"  id="reqdate" style="width: 280px; margin-right: 10px;" type="text" readonly value="{{Carbon\Carbon::now()->isoformat('dddd, MMMM DD, YYYY')}}">
            </div>
            <div class="form-inline" style="margin-left: 20px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">TO New Location</label>
                <select class="form-select form-control-sm location" id="locto" style=" margin-right: 10px; font-size: .85rem; padding: 0.25rem 0.5rem; height: 30px !important; width: 280px;">
                    <option value="" selected disabled>Select Location</option>
                    <option value="1">A1</option>
                    <option value="2">A2</option>
                    <option value="3">A3</option>
                    <option value="4">A4</option>
                </select>
                <label class="form-control form-control-sm" style="width: 200px;">Requested By</label>
                <input class="form-control form-control-sm" id="reqby" style="width: 280px; margin-right: 10px;" type="text" readonly value="{{auth()->user()->name}}">
            </div>
            <div class="header_label alert alert-primary mt-4" role="alert">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>NOTE:</strong> Please fill up all required fields to proceed.
            </div>
        </div>
        <div id="transrequestDetails" style="display: none;">
            <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
                <h6 class="modal-title w-100">REQUEST DETAILS</h6>
            </div>
            <div class="modal-body" style="background-color: white; color: black;">
                <form class="mt-2 mb-2">
                    <div class="form-inline" style="margin-left: 20px;">
                        <div class="f-outline">
                            <select class="forminput form-control form-select" id="category" name="category" style="width: 250px;" placeholder=" " required>
                                <option value="" selected disabled>Select Category</option>
                            </select>
                            <label for="category" class="formlabel form-label">Category</label>
                        </div>
                        <div class="f-outline" style="margin-left: 10px;">
                            <select class="forminput form-control form-select" id="item" name="item" style="width: 350px;" placeholder=" ">
                                <option value="" selected disabled>Select Item</option>
                            </select>
                            <label for="item" class="formlabel form-label">Item Description</label>
                        </div>
                        <input class="d-none" id="prodcode" type="hidden"/>
                        <div class="f-outline" style="margin-left: 10px;">
                            <input class="forminput form-control" id="uom" name="uom" style="background-color: white; width: 70px;" type="text" placeholder=" " disabled>
                            <label for="uom" class="formlabel form-label">UOM</label>
                        </div>
                        <div class="f-outline" style="margin-left: 10px;">
                            <input class="forminput form-control" id="qty" name="qty" min="1" max="" style="width: 70px;" type="number" placeholder=" " onkeyup="if(value<1) value=1;">
                            <label for="qty" class="formlabel form-label">Qty</label>
                        </div>
                        <div class="f-outline" style="margin-left: 10px;">
                            <input class="forminput form-control" id="qtystock" name="qtystock" min="1" max="" style="background-color: white; width: 70px;" type="number" placeholder=" " disabled>
                            <label for="qtystock" class="formlabel form-label">Stock</label>
                        </div>
                        <input type="button" class="add-row btn btn-primary bp" value="ADD ITEM" style="zoom: 90%; margin-left: 10px;">
                    </div>
                </form>
                <div class="container-fluid"  id="#divNewStockTransfer">
                    <table id='tblNewStockTransfer' class="table tblNewStockTransfer" style="cursor: pointer; font-size: 12px; display: none;">
                        <thead>
                            <tr>
                                <th class="d-none">ITEM ID</th>
                                <th>ITEM CODE</th>
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
                <div class="submit_label alert alert-primary mt-4" role="alert">
                    <i class='fa fa-exclamation-triangle'></i>
                    <strong>NOTE:</strong> Please fill up all required fields to proceed.
                </div>
                <button type="button" id="btnClose" class="btn btn-primary bp" style="display: none;" data-bs-dismiss="modal">CANCEL</button>
                <button type="button" id="btnSave" class="btn btn-primary float-right bp" style="display: none;">SUBMIT</button>
            </div>
        </div>
    </div>
    </div>
</div>