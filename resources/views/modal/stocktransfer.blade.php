<div class="container">
    <div class="modal fade in" id="stocktrans">
    <div class="modal-dialog  modal-lg">
    <div class="modal-content">
        <div class="modal-header" style="background-color:#0d1a80; color:white;height:50px;">
            <h6 class="modal-title">STOCK TRANSFER</h6>
            <button type="button" class="btn-close btn-close-white close" data-dismiss="modal"></button>
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
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <form id='stocktransForm'>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" >Category</label>
                    </div>
                    <select class="form-select" id="categories" name="categories" class="form-control" required>
                            <option value="" selected disabled>Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{$category->id}}">{{strtoupper($category->category)}}</option>
                        @endforeach
                    </select>
                </div> 
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" >Item</label>
                    </div>
                    <select class="form-select" id="items" name="items" required>
                            <option value="" selected disabled>Select Item</option>                     
                    </select>
                </div> 
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" >FROM</label>
                    </div>
                    <select class="form-select" id="locationfrom" name="locationfrom" required>
                            <option value="" selected disabled>Select Location</option>
                    </select>
                    &nbsp; &nbsp; &nbsp;
                    <div class="input-group-prepend">
                        <label class="input-group-text" >TO</label>
                    </div>
                    <select class="form-select" id="locationto" name="locationto" required>
                            <option value="" selected disabled>Select Location</option>
                        @foreach($locations as $location)
                            <option value="{{$location->id}}">{{$location->location}}</option>
                        @endforeach
                    </select>
                </div> 
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" >Quantity</label>
                    </div>
                    <input type="number" id="quantityst" placeholder="Enter Quantity" name="quantity" required>
                &nbsp; &nbsp; &nbsp;
                    <div class="input-group-prepend">
                        <label class="input-group-text" >Available Stocks</label>
                    </div>
                    <input type="number" readonly id='strans' name='strans' required>
                </div> 
            </form>
                <div class="col-md-12 mb-4">
                    <button type="submit" id="buttrans" class="btn btn-xs btn-dark submit float-right bp" style="width:150px; height:40px; font-size:14px;">
                    TRANSFER</button>   
                </div>    
                 
        </div>
    </div>
    </div>
    </div>
</div>
