<div class="modal fade in" id="createItem">
    <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">CREATE NEW ASSEMBLED ITEM</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" style="width: 90px;">Item Code</label>
                <input class="form-control form-control-sm" style="width: 220px; margin-right: 10px;" name="aic_item_code" id="aic_item_code" maxlength="100" placeholder="Please enter item code" required>
                <label class="form-control form-control-sm" style="width: 120px;">Item Description</label>
                <input class="form-control form-control-sm" style="width: 600px;" name="aic_item_description" id="aic_item_description" maxlength="255" placeholder="Please enter assembled item description to proceed" required>
            </div>
        </div>
        <div id="partsDetails" style="display: none;">
            <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
                <h6 class="modal-title w-100">PARTS DETAILS</h6>
            </div>
            <div class="modal-body" style="background-color: white; color: black;">
                <form class="mt-2 mb-2">
                    <div class="form-inline" style="margin-left: 35px;">
                        <select class="form-control form-select" id="categoryAssembly" style="font-size: 12px; padding: 0.25rem 0.5rem; height: 30px !important; width: 300px;" required>
                                <option value="" selected disabled>Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{$category->id}}">{{strtoupper($category->category)}}</option>
                                @endforeach
                        </select>
                        <select class="form-control form-select" id="itemAssembly" style="font-size: 12px; padding: 0.25rem 0.5rem; height: 30px !important; width: 450px; margin-left: 10px;">
                            <option value="" selected disabled>Select Item</option>
                        </select>
                        <input class="d-none" id="prodcodeAssembly" type="hidden"/>
                        <input class="form-control" id="uomAssembly" style="font-size: 12px; padding: 0.25rem 0.5rem; width: 70px; height: 30px; margin-left: 10px;" type="text" placeholder="UOM" readonly>
                        <input class="form-control" id="qtyAssembly" min="0" max="" style="font-size: 12px; padding: 0.25rem 0.5rem; width: 70px; height: 30px; margin-left: 10px;" type="number" placeholder="Qty" onkeyup="if(value<0) value=0;">
                        <input type="button" class="add-row btn btn-primary bp" value="ADD ITEM" style="zoom: 75%; margin-left: 10px; margin-top: -1px;">
                    </div>
                </form>
                <div class="container-fluid"  id="#divCreateItem">
                    <table id='tblCreateItem' class="table tblCreateItem" style="cursor: pointer; font-size: 12px; display: none;">
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
                <button type="submit" id="btnClose" class="btn btn-primary bp" style="display: none;" data-bs-dismiss="modal">CANCEL</button>
                <button type="submit" id="btnSave" class="btn btn-primary float-right bp" style="display: none;">SUBMIT</button>
            </div>
        </div>
    </div>
    </div>
</div>