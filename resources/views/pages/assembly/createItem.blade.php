<div class="container">
    <div class="modal fade in" id="createItem">
    <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">CREATE ITEM</h6>    
            <button type="button" class="btn-close btn-close-white close" data-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">                
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <div class="form-inline" style="margin-left:35px;">
                <label class="form-control form-control-sm" style="width: 160px;">Created By</label>
                <input class="form-control form-control-sm" id="created_by" style="width: 280px; margin-right: 10px;" type="text" readonly value="{{auth()->user()->name}}">
                <label class="form-control form-control-sm" style="width: 160px;">Item Category</label>
                <select class="form-select" id="category" class="form-control-sm" style="width: 280px; margin-right: 10px; font-size: 12px;" required>
                    <option selected disabled>Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{$category->id}}">{{strtoupper($category->category)}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-inline" style="margin-left:35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Item Description</label>
                <input class="form-control form-control-sm" style="width: 730px; margin-right: 10px;" name="item_description" id="item_description" required></textarea>
            </div>
        </div>
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">PARTS DETAILS</h6>
        </div>        
        <form class="mt-2 mb-2">
            <div class="form-inline" style="margin-left:50px;">
                <select class="form-select" id="categoryAssembly" class="form-control" style="font-size: 12px; padding: 0.25rem 0.5rem; height: 30px !important; width: 300px;" required>
                        <option selected disabled>Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{$category->id}}">{{strtoupper($category->category)}}</option>
                        @endforeach
                </select>
                <select class="form-select" id="itemAssembly" class="form-control" style="font-size: 12px; padding: 0.25rem 0.5rem; height: 30px !important; width: 450px; margin-left: 10px;">
                    <option selected disabled>Select Item</option>
                </select>
                <input class="form-control" id="qtyAssembly" min="0" max="" style="font-size: .85rem; padding: 0.25rem 0.5rem; width: 70px; height: 30px; margin-left: 10px;" type="number" placeholder="Qty">
                <input type="button" class="add-row btn btn-primary bp" value="ADD ITEM" style="zoom: 75%; margin-left: 10px; margin-top: -1px;">
            </div>          
        </form>
        <div class="container-fluid"  id="#divCreateItem">
            <table id='tblCreateItem' class="table tblCreateItem" style="cursor: pointer; font-size: 12px; display: none;">
                <thead>                            
                    <tr>
                        <th>CATEGORY</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>QTY</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>      
            </table>
        </div>
        <div class="col-md-12 mt-2 mb-4">
            <button type="submit" id="btnClose" class="btn btn-primary bp" style="display: none;" data-dismiss="modal">CLOSE</button>
            <button type="submit" id="btnSave" class="btn btn-primary float-right bp" style="display: none;">SUBMIT</button>
        </div>
    </div>
    </div>
    </div>
</div>