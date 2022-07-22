<div class="modal fade in" id="createItem">
    <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">CREATE NEW ASSEMBLED ITEM</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <div class="form-inline">
                <label class="form-control form-control-sm" style="width: 85px;">Item Code</label>
                <input class="form-control form-control-sm" style="width: 215px; margin-right: 10px;" name="aic_item_code" id="aic_item_code" maxlength="100" placeholder="Please enter item code" required>
                <label class="form-control form-control-sm" style="width: 120px;">Item Description</label>
                <input class="form-control form-control-sm" style="width: 477px; margin-right: 10px;" name="aic_item_description" id="aic_item_description" maxlength="255" placeholder="Please enter assembled item description to proceed" required>
                <label class="form-control form-control-sm" style="width: 110px;">Minimum Stock</label>
                <input class="form-control form-control-sm" style="width: 80px;" name="aic_minimum" id="aic_minimum" type="number" min="1" placeholder="Qty" onkeyup="if(value<1) value=1;" required>
            </div>
            <div class="create_label alert alert-primary mt-4" role="alert">
                <i class='fa fa-exclamation-triangle'></i>
                <strong>NOTE:</strong> Please fill up all required fields to proceed.
            </div>
        </div>
        <div id="partsDetails" style="display: none;">
            <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
                <h6 class="modal-title w-100">PARTS DETAILS</h6>
            </div>
            <div class="modal-body" style="background-color: white; color: black;">
                <form class="mt-2 mb-2">
                    <div class="form-inline">                        
                        <div class="f-outline">
                            <select class="forminput form-control form-select" id="categoryAssembly" name="categoryAssembly" style="width: 300px;" placeholder=" " required>
                                <option value="" selected disabled>Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{$category->id}}">{{strtoupper($category->category)}}</option>
                                @endforeach
                            </select>
                            <label for="categoryAssembly" class="formlabel form-label">Category</label>
                        </div>
                        <div class="f-outline" style="margin-left: 10px;">
                            <select class="forminput form-control form-select" id="itemAssembly" name="itemAssembly" style="width: 540px;" placeholder=" ">
                                <option value="" selected disabled>Select Item</option>
                            </select>
                            <label for="itemAssembly" class="formlabel form-label">Item Description</label>
                        </div>
                        <input class="d-none" id="prodcodeAssembly" type="hidden"/>
                        <div class="f-outline" style="margin-left: 10px;">
                            <input class="forminput form-control" id="uomAssembly" name="uomAssembly" style="background-color: white; width: 70px;" type="text" placeholder=" " disabled>
                            <label for="uomAssembly" class="formlabel form-label">UOM</label>
                        </div>
                        <div class="f-outline" style="margin-left: 10px;">
                            <input class="forminput form-control" id="qtyAssembly" name="qtyAssembly" min="1" max="" style="width: 70px;" type="number" placeholder=" " onkeyup="if(value<1) value=1;">
                            <label for="qtyAssembly" class="formlabel form-label">Qty</label>
                        </div>
                        <input type="button" class="add-row btn btn-primary bp" value="ADD ITEM" style="zoom: 90%; margin-left: 10px; margin-top: -1px;">
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
                <button type="button" id="btnClose" class="btn btn-primary bp" style="display: none;" data-bs-dismiss="modal">CANCEL</button>
                <button type="button" id="btnSave" class="btn btn-primary float-right bp" style="display: none;">SUBMIT</button>
            </div>
        </div>
    </div>
    </div>
</div>