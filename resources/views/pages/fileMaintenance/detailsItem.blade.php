<div class="modal fade in" id="detailsItem">
    <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">UPDATE ITEM DETAILS</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <input type="hidden" name="item_id" id="item_id">
            <input type="hidden" name="category_name_details_original" id="category_name_details_original">
            <input type="hidden" name="item_category_details_original" id="item_category_details_original">
            <input type="hidden" name="item_name_details_original" id="item_name_details_original">
            <input type="hidden" name="prodcode_details_original" id="prodcode_details_original">
            <input type="hidden" name="item_uom_details_original" id="item_uom_details_original">
            <input type="hidden" name="serialize_details_original" id="serialize_details_original">
            <div class="form-inline">
                <label class="form-control form-control-sm" style="width: 168px;">Category Name</label>
                <select class="form-control-sm form-select-sm" id="item_category_details" style="padding: 0.25rem 0.5rem; height: 30px !important; width: 600px;">
                    <option value="" selected disabled>Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{$category->id}}">{{strtoupper($category->category)}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-inline" style="margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 168px;">Item Code</label>
                <input class="form-control form-control-sm" id="prodcode_details" style="width: 600px;" type="text" maxlength="255" placeholder="Please enter item code">
            </div>
            <div class="form-inline" style="margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 168px;">Item Description</label>
                <input class="form-control form-control-sm" id="item_name_details" style="width: 600px;" type="text" maxlength="255" placeholder="Please enter item description">
            </div>
            <div class="form-inline" style="margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 168px;">Unit of Measure (UOM)</label>
                <select class="form-control-sm form-select-sm" id="item_uom_details" style="padding: 0.25rem 0.5rem; height: 30px !important; width: 600px;">
                    <option value="" selected disabled>Select UOM</option>
                    <option value="Unit">Unit</option>
                    <option value="Pc">Pc</option>
                    <option value="Meter">Meter</option>
                </select>
            </div>
            <div class="form-inline divSerial" style="margin-top: 10px; display: none;">
                <label class="form-control form-control-sm" style="width: 178px;">Has Serial? (YES/NO)</label>
                <label class="switch" style="height: 30px; width: 100px; margin-left: -10px;">
                    <input type="checkbox" id="serialize_details" class="togBtn" value="ACTIVE">
                    <div class="slider round" style="zoom: 90%;">
                        <span class="on" style="zoom: 175%;">YES</span>
                        <span class="off" style="zoom: 175%;">NO</span>
                    </div>
                </label>
            </div>
            <button type="button" id="btnUpdateItem" class="btn btn-primary float-right bp" style="margin-top: 10px;">UPDATE</button>
        </div>
    </div>
    </div>
</div>