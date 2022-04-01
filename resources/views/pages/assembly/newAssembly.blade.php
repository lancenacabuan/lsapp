<div class="modal fade in" id="newAssembly">
    <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">NEW ASSEMBLY REQUEST</h6>    
            <button type="button" class="btn-close btn-close-white close" data-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">                
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <div class="form-inline" style="margin-left: 35px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Requested</label>
                <input class="form-control form-control-sm"  id="reqdate" style="width: 280px; margin-right: 10px;" type="text" readonly value="{{Carbon\Carbon::now()->isoformat('dddd, MMMM DD, YYYY')}}">
                <label class="form-control form-control-sm" style="width: 160px;">Assembly Request No.</label>
                <input class="form-control form-control-sm" id="request_num" style="width: 280px; margin-right: 10px;" type="text" readonly>
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Date Needed</label>
                <input class="form-control form-control-sm"  id="needdate" style="width: 280px; margin-right: 10px;" type="date">
                <label class="form-control form-control-sm" style="width: 160px;">Requested By</label>
                <input class="form-control form-control-sm" id="reqby" style="width: 280px; margin-right: 10px;" type="text" readonly value="{{auth()->user()->name}}">
            </div>
            <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                <label class="form-control form-control-sm" style="width: 160px;">Assembled Item Name</label>
                <select class="form-select" id="assembly" class="form-control-sm" style="width: 650px; margin-right: 10px; font-size: 12px;" required>
                    <option value="" selected disabled>Select Assembled Item</option>
                    @foreach($items as $item)
                        <option value="{{$item->id}}">{{strtoupper($item->item)}}</option>
                    @endforeach
                </select>
                <input class="form-control" id="qty" min="0" max="" style="font-size: 12px; padding: 0.25rem 0.5rem; width: 70px; height: 30px;" type="number" placeholder="Qty">
                <button type="submit" id="btnAssemblyProceed" class="btn btn-primary bp" style="zoom: 80%; margin-left: 10px; display: none;">PROCEED</button>
            </div>
        </div>
        <div id="assemblypartsDetails" style="display: none;">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">PARTS NEEDED</h6>
        </div>
        <div class="container-fluid mt-2"  id="#divPartsDetails">
            <table id='tblPartsDetails' class="table tblPartsDetails" style="cursor: pointer; font-size: 12px; width: 100%;">
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
        <div class="col-md-12 mt-2 mb-4">
            <button type="submit" id="btnAssemblyBack" class="btn btn-primary bp">BACK</button>
            <button type="submit" id="btnAssemblySave" class="btn btn-primary float-right bp">SUBMIT</button>
        </div>
        </div>
    </div>
    </div>
</div>