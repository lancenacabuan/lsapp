<div class="modal fade in" id="newAssembly">
    <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">NEW ASSEMBLY REQUEST</h6>    
            <button type="button" class="btn-close btn-close-white close" data-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">                
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <div class="form-inline" style="margin-left:35px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Requested</label>
                <input class="form-control form-control-sm"  id="reqdate"style="width: 280px; margin-right: 10px;" type="text" readonly value="{{Carbon\Carbon::now()->isoformat('dddd, MMMM DD, YYYY')}}">
                <label class="form-control form-control-sm" style="width: 160px;">Assembly Request No.</label>
                <input class="form-control form-control-sm" id="request_num" style="width: 280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left:35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Needed</label>
                <input class="form-control form-control-sm"  id="needdate"style="width: 280px; margin-right: 10px;" type="date">
                <label class="form-control form-control-sm" style="width: 160px;">Requested By</label>
                <input class="form-control form-control-sm" id="reqby" style="width: 280px; margin-right: 10px;" type="text" readonly value="{{auth()->user()->name}}">
            </div>
            <div class="form-inline" style="margin-left:35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Assembly Item Name</label>
                <select class="form-select" id="category" class="form-control-sm" style="width: 650px; margin-right: 10px; font-size: 12px;" required>
                    <option selected disabled>Select Assembly Item</option>
                    @foreach($items as $item)
                        <option value="{{$item->id}}">{{strtoupper($item->item)}}</option>
                    @endforeach
                </select>
                <input class="form-control" id="qty" min="0" max="" style="font-size: 12px; padding: 0.25rem 0.5rem; width: 70px; height: 30px;" type="number" placeholder="Qty">
            </div>
        </div>
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">PARTS NEEDED</h6>
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
                <input class="form-control" id="uomAssembly" style="font-size: 12px; padding: 0.25rem 0.5rem; width: 70px; height: 30px; margin-left: 10px;" type="text" placeholder="UOM" readonly>
                <input class="form-control" id="qtyAssembly" min="0" max="" style="font-size: 12px; padding: 0.25rem 0.5rem; width: 70px; height: 30px; margin-left: 10px;" type="number" placeholder="Qty">
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
                        <th>UOM</th>
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