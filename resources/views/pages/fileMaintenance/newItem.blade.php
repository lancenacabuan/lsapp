<div class="modal fade in" id="newItem">
    <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">ADD NEW ITEM</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <div class="form-inline">
                <label class="form-control form-control-sm" style="width: 168px;">Category Name</label>
                <select class="form-control-sm form-select-sm" id="item_category" style="padding: 0.25rem 0.5rem; height: 30px !important; width: 600px;">
                    <option value="" selected disabled>Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{$category->id}}">{{strtoupper($category->category)}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-inline" style="margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 168px;">Item Code</label>
                <input class="form-control form-control-sm" id="prodcode" style="width: 600px;" type="text" maxlength="255" placeholder="Please enter item code">
            </div>
            <div class="form-inline" style="margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 168px;">Item Description</label>
                <input class="form-control form-control-sm" id="item_name" style="width: 600px;" type="text" maxlength="255" placeholder="Please enter item description">
            </div>
            <div class="form-inline" style="margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 168px;">Minimum Stock</label>
                <input class="form-control form-control-sm" id="minimum" style="width: 600px;" type="number" min="1" placeholder="Please enter minimum stock" onkeyup="if(value<1) value=1;">
            </div>
            <div class="form-inline" style="margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 168px;">Unit of Measure (UOM)</label>
                <select class="form-control-sm form-select-sm" id="item_uom" style="padding: 0.25rem 0.5rem; height: 30px !important; width: 600px;">
                    <option value="" selected disabled>Select UOM</option>
                    <option value="Unit">Unit</option>
                    <option value="Pc">Pc</option>
                    <option value="Meter">Meter</option>
                </select>
            </div>
            <div class="form-inline divSerial" style="margin-top: 10px; display: none;">
                <label class="form-control form-control-sm" style="width: 178px;">Has Serial? (YES/NO)</label>
                <label class="switch" style="height: 30px; width: 100px; margin-left: -10px;">
                    <input type="checkbox" id="serialize" class="togBtn" value="ACTIVE" checked>
                    <div class="slider round" style="zoom: 90%;">
                        <span class="on" style="zoom: 175%;">YES</span>
                        <span class="off" style="zoom: 175%;">NO</span>
                    </div>
                </label>
            </div>
            <button type="button" id="btnSaveItem" class="btn btn-primary float-right bp" style="margin-top: 10px;">SUBMIT</button>
        </div>
    </div>
    </div>
</div>