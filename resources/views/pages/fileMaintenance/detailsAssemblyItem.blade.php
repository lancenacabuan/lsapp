<div class="modal fade in" id="detailsAssemblyItem">
    <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">ASSEMBLY ITEM DETAILS</h6>    
            <button type="button" class="btn-close btn-close-white close" data-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">                
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <input type="hidden" name="aim_item_id" id="aim_item_id">
            <input type="hidden" name="aim_category_name_details_original" id="aim_category_name_details_original">
            <input type="hidden" name="aim_item_category_details_original" id="aim_item_category_details_original">
            <input type="hidden" name="aim_item_name_details_original" id="aim_item_name_details_original">
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" style="width: 160px;">Created By</label>
                <input class="form-control form-control-sm" style="width: 280px; margin-right: 10px;" type="text" readonly value="{{auth()->user()->name}}">
                <label class="form-control form-control-sm" style="width: 160px;">Item Category</label>
                <select class="form-select" id="aim_item_category_details" class="form-control-sm" style="width: 280px; margin-right: 10px; font-size: 12px;" required>
                    <option value="" selected disabled>Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{$category->id}}">{{strtoupper($category->category)}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Item Description</label>
                <input class="form-control form-control-sm" style="width: 730px; margin-right: 10px;" name="aim_item_name_details" id="aim_item_name_details" required></textarea>
            </div>
        </div>
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">PARTS DETAILS</h6>
        </div>
        <div class="container-fluid mt-2"  id="#divItemDetails">
            <table id='tblItemDetails' class="table tblItemDetails" style="cursor: pointer; font-size: 12px; width: 100%;">
                <thead>                            
                    <tr>
                        <th>CATEGORY</th>
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
            <button type="submit" class="btn btn-primary bp" data-bs-dismiss="modal">BACK</button>
            <button type="submit" id="btnUpdate" class="btn btn-primary float-right bp" style="display: none;">UPDATE</button>
        </div>
    </div>
    </div>
</div>