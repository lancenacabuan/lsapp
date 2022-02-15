<div class="container">
    <div class="modal fade in" id="addStock">
    <div class="modal-dialog  modal-lg">
    <div class="modal-content">
        <div class="modal-header" style="background-color:#0d1a80; color:white;height:50px;">
            <h6 class="modal-title">ADD STOCK</h6>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" style="background-color:white;color:black;">                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            <form id="AddStockForm">
                <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
                <div class="input-group mb-3" >
                    <div class="input-group-prepend">
                        <label class="input-group-text">Category</label>
                    </div>
                    <select class="form-select" id="category" name="category" class="form-control" required>
                            <option value="" selected disabled>Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{$category->id}}">{{strtoupper($category->category)}}</option>
                        @endforeach
                    </select>
                </div> 
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text">Item</label>
                    </div>
                    <select class="form-select form-control" id="item" name="item" required>
                        <option value="">Select Item</option>                        
                    </select>
                </div> 
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text">Location</label>
                    </div>
                    <select class="form-select form-control" id="location" name="location" required>
                            <option value="" selected disabled>Select Location</option>
                        @foreach($locations as $location)
                            <option value="{{$location->id}}">{{$location->location}}</option>
                        @endforeach
                    </select>
                </div> 
                <div class="input-group mb-3" id="serialdiv">
                    <div class="input-group-prepend">
                        <label class="input-group-text">Serial</label>
                    </div>
                    <input type="text" id="serial" style="width:700px" placeholder="Enter serial" name="serial" autocomplete="off">
                </div>
                <div class="input-group mb-3" id="qtydiv">
                    <div class="input-group-prepend">
                        <label class="input-group-text">Quantity</label>
                    </div>
                    <input type="number" id="qty" min="1" style="width:680px" placeholder="0" name="qty" autocomplete="off">
                </div> 
            </form>
                <div class="col-md-12 mb-4">
                    <button type="submit" id="butsave" class="btn btn-xs btn-dark submit float-right bp" style="width:180px; font-size:12px;">
                    ADD</button>  
                </div>    
        </div>
    </div>
    </div>
    </div>
</div>