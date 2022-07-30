<div class="modal fade in" id="detailsAssemblyItem">
    <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">UPDATE ASSEMBLED ITEM DETAILS</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <input type="hidden" name="aim_item_id" id="aim_item_id">
            <input type="hidden" name="aim_item_name_details_original" id="aim_item_name_details_original">
            <input type="hidden" name="aim_item_code_details_original" id="aim_item_code_details_original">
            <input type="hidden" name="aim_minimum_details_original" id="aim_minimum_details_original">
            <div class="form-inline">
                <label class="form-control form-control-sm" style="width: 85px;">Item Code</label>
                <input class="form-control form-control-sm" style="width: 215px; margin-right: 10px;" name="aim_item_code_details" id="aim_item_code_details" maxlength="100" placeholder="Please enter item code" type="search" required>
                <label class="form-control form-control-sm" style="width: 120px;">Item Description</label>
                <input class="form-control form-control-sm" style="width: 477px; margin-right: 10px;" name="aim_item_name_details" id="aim_item_name_details" maxlength="255" placeholder="Please enter assembled item description to proceed" type="search" required>
                <label class="form-control form-control-sm" style="width: 110px;">Minimum Stock</label>
                <input class="form-control form-control-sm" style="width: 80px;" name="aim_minimum_details" id="aim_minimum_details" type="number" min="1" placeholder="Qty" onkeyup="if(value<1) value=1;" required>
            </div>
        </div>
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">PARTS DETAILS</h6>
        </div>
        <div class="container-fluid mt-2"  id="#divItemDetails">
            <table id='tblItemDetails' class="table tblItemDetails" style="cursor: pointer; font-size: 12px; width: 100%;">
                <thead>
                    <tr>
                        <th>ITEM CODE</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>QTY</th>
                        <th>UOM</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>      
            </table>
        </div>
        <div class="col-md-12 mt-4 mb-4">
            <button type="button" class="btn btn-primary bp" data-bs-dismiss="modal">CANCEL</button>
            <button type="button" id="btnUpdate" class="btn btn-primary float-right bp" style="display: none;">UPDATE</button>
        </div>
    </div>
    </div>
</div>