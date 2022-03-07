<div class="container table-responsive">
    <div class="modal fade in" id="newItem">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">ADD NEW ITEM</h6>    
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" style="background-color:white;color:black;">                
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <div class="form-inline">
                <label class="form-control form-control-sm" style="width:165px;">Category Name</label>
                <select class="form-control-sm form-select-sm" id="item_category" style="padding: 0.25rem 0.5rem; height:30px !important; width:590px;">
                    <option selected disabled>Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{$category->id}}">{{strtoupper($category->category)}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-inline" style="margin-top: 10px;">
                <label class="form-control form-control-sm" style="width:165px;">Item Description</label>
                <input class="form-control form-control-sm" id="item_name" style="width: 590px;" type="text" maxlength="255">
            </div>
            <div class="form-inline" style="margin-top: 10px;">
                <label class="form-control form-control-sm" style="width:165px;">Unit of Measure (UOM)</label>
                <select class="form-control-sm form-select-sm" id="item_uom" style="padding: 0.25rem 0.5rem; height:30px !important; width:590px;">
                    <option selected disabled>Select UOM</option>
                    <option value="Unit">Unit</option>
                    <option value="Pc">Pc</option>
                    <option value="Meter">Meter</option>
                </select>
            </div>
            <br>
            <button type="button" id="btnSaveItem" class="btn btn-primary float-right bp" style="margin-right: 10px; margin-top: 10px;">
            SUBMIT</button>
        </div>
    </div>
    </div>
    </div>
</div>