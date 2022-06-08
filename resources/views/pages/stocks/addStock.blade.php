<div class="modal fade in" id="addStock">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
                <h6 class="modal-title w-100">ADD STOCK</h6>
                <button type="button" class="btn-close btn-close-white close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background-color: white; color: black;">
                <form id="AddStockForm">
                    <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" style="width: 140px;">Category</label>
                        </div>
                        <select id="category" name="category" style="width: 630px;" required>
                                <option value="" selected disabled>Select Category (Required)</option>
                                @foreach($categories as $category)
                                    <option value="{{$category->id}}">{{strtoupper($category->category)}}</option>
                                @endforeach
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" style="width: 140px;">Item</label>
                        </div>
                        <select id="item" name="item" style="width: 630px;" required>
                            <option value="" selected disabled>Select Item (Required)</option>
                        </select>
                    </div>
                    <div class="input-group mb-3" id="prodcodediv">
                        <div class="input-group-prepend">
                            <label class="input-group-text" style="width: 140px;">Item Code</label>
                        </div>
                        <input type="text" id="prodcode" name="prodcode" style="width: 630px; border-width: thin;" disabled>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" style="width: 140px;">Location</label>
                        </div>
                        <select id="location" name="location" style="width: 630px;" required>
                                <option value="" selected disabled>Select Location (Required)</option>
                                @foreach($locations as $location)
                                    <option value="{{$location->id}}">{{$location->location}}</option>
                                @endforeach
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" style="width: 140px;">Rack No.</label>
                        </div>
                        <input type="text" id="rack" name="rack" placeholder="Enter Rack No. (Optional)" style="width: 630px; border-width: thin;" autocomplete="off">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" style="width: 140px;">Row No.</label>
                        </div>
                        <input type="text" id="row" name="row" placeholder="Enter Row No. (Optional)" style="width: 630px; border-width: thin;" autocomplete="off">
                    </div>
                    <div class="input-group mb-3" id="uomdiv">
                        <div class="input-group-prepend">
                            <label class="input-group-text" style="width: 140px;">UOM</label>
                        </div>
                        <input type="text" id="uom" name="uom" style="width: 630px; border-width: thin;" disabled>
                    </div>
                    <div class="input-group mb-3" id="qtydiv">
                        <div class="input-group-prepend">
                            <label class="input-group-text" style="width: 140px;">Quantity</label>
                        </div>
                        <input type="number" id="qty" name="qty" min="1" style="width: 630px; border-width: thin;" placeholder="0"  autocomplete="off" onkeyup="if(value<1) value=1;" required>
                    </div>
                    <div class="input-group mb-3" id="serialdiv">
                        <div class="input-group-prepend">
                            <label class="input-group-text" style="width: 140px;">Serial</label>
                        </div>
                        <input type="text" id="serial" name="serial" placeholder="Enter Serial (Required)" style="width: 630px; border-width: thin;" autocomplete="off" onkeypress="return specialChar(event)">
                    </div>
                </form>
                <hr>
                <button type="button" id="btnReset" class="btn btn-primary bp">RESET</button>
                <button type="button" id="btnSave" class="btn btn-primary bp float-right">SUBMIT</button>
            </div>
        </div>
    </div>
</div>
<style>
    input, select{
        color: black;
        border-color: black;
    }
</style>