<div class="container table-responsive">
    <div class="modal fade in" id="newStockTransfer">
    <div class="modal-dialog  modal-xl">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color:#0d1a80; color:white;height:45px;">
            <h6 class="modal-title w-100">NEW STOCK TRANSFER</h6>    
            <button type="button" class="close" id="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" style="background-color:white;color:black;">                
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <div class="form-inline" style="margin-left:35px;">
                <label class="form-control form-control-sm" style="width:160px;">Date Requested</label>
                <input class="form-control form-control-sm"  id="reqdate"style="width:280px; margin-right: 10px;" type="text" readonly value="{{Carbon\Carbon::now()->isoformat('dddd, MMMM D, YYYY')}}">
                <label class="form-control form-control-sm" style="width:200px;">Stock Transfer Request No.</label>
                <input class="form-control form-control-sm" id="reqnum" style="width:280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left:35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width:160px;">Date Needed</label>
                <input class="form-control form-control-sm"  id="needdate"style="width:280px; margin-right: 10px;" type="date">
                <label class="form-control form-control-sm" style="width:200px;">FROM Location</label>
                <select class="form-select form-control-sm location" id="locfrom" style=" margin-right: 10px; font-size: .85rem; padding: 0.25rem 0.5rem; height:30px !important;width:280px;">
                    <option selected disabled>Select Location</option>
                    <option value="5">BALINTAWAK</option>
                    <option value="6">MALABON</option>
                    {{-- @foreach($locations as $location)
                        <option value="{{$location->id}}">{{$location->location}}</option>
                    @endforeach --}}
                </select>
            </div>
            <div class="form-inline" style="margin-left:35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width:160px;">Requested By</label>
                <input class="form-control form-control-sm" id="reqby" style="width:280px; margin-right: 10px;" type="text" readonly value="{{auth()->user()->name}}">
                <label class="form-control form-control-sm" style="width:200px;">TO New Location</label>
                <select class="form-select form-control-sm location" id="locto" style=" margin-right: 10px; font-size: .85rem; padding: 0.25rem 0.5rem; height:30px !important;width:280px;">
                    <option selected disabled>Select Location</option>
                    <option value="1">A1</option>
                    <option value="2">A2</option>
                    <option value="3">A3</option>
                    <option value="4">A4</option>
                    {{-- @foreach($locations as $location)
                        <option value="{{$location->id}}">{{$location->location}}</option>
                    @endforeach --}}
                </select>
            </div>
        </div>
        <div id="transrequestDetails" style="display: none;">
        <div class="modal-header text-center" style="background-color:#0d1a80; color:white;height:45px;">
            <h6 class="modal-title w-100">REQUEST DETAILS</h6>
        </div>        
        <form class="mt-2 mb-2">
            <div class="form-inline" style="margin-left:50px;">
                <select class="form-select" id="category" class="form-control" style="font-size: 12px; padding: 0.25rem 0.5rem; height:30px !important;width:300px;" required>
                        <option selected disabled>Select Category</option>
                </select>
                <select class="form-select" id="item" class="form-control" style="font-size: 12px; padding: 0.25rem 0.5rem; height:30px !important; width:450px; margin-left: 10px;">
                        <option selected disabled>Select Item</option>
                </select>
                <input class="form-control" id="qty" min="0" max="" style="font-size: .85rem; padding: 0.25rem 0.5rem; width:70px; height:30px; margin-left: 10px;" type="number" placeholder="Qty" disabled>
                <input class="form-control" id="qtystock" style="font-size: .85rem; padding: 0.25rem 0.5rem; width:70px; height:30px; margin-left: 10px;" type="text" placeholder="Stock" readonly>
                <input type="button" class="add-row btn btn-primary bp" value="ADD ITEM" style="font-size:12px; width:100px; height:30px; margin-left: 10px;">
            </div>          
        </form>
        <div class="container-fluid"  id="#divNewStockTransfer">
            <table id='tblNewStockTransfer' class="table tblNewStockTransfer" style="cursor:pointer; border:0px; font-size:12px; display:none;">
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
            <button type="submit" id="btnClose" class="btn btn-primary bp" style="display:none;">
            CLOSE</button>&nbsp;&nbsp;
            <button type="submit" id="btnSave" class="btn btn-primary float-right bp" style="display:none; margin-right: 10px;">
            SUBMIT</button>
        </div>
        </div>
    </div>
    </div>
    </div>
</div>