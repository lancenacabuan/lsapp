@extends('layouts.app')
@section('content')
<div class="container-fluid">
    @role('sales') {{---ROLES---}}
    <button class="btn btn-primary bp btnNewStockRequest" type="button">NEW STOCK REQUEST</button>
    <br><br>
    @endrole
</div>
<div class="container-fluid">
    <input type="hidden" id="current_user" value="{{auth()->user()->id}}">
    <input type="hidden" id="current_role" value="{{auth()->user()->getRoleNames()}}">
    <table id="stockrequestTable" class="table stockrequestTable table-hover display" style="width: 100%; zoom: 80%; cursor: pointer;">
        <thead style="background-color: #0d1a80 !important; color: white !important; font-weight: bold !important; font-size: 15px;">                            
            <tr>
                <th style="width: 150px;">DATE NEEDED</th>
                <th style="width: 150px;">DATE REQUESTED</th>
                <th style="width: 150px;">REQUEST NUMBER</th>
                <th style="width: 180px;">REFERENCE SO/PO NO.</th>
                <th>REQUESTED BY</th>
                <th style="width: 150px;">REQUEST TYPE</th>
                <th style="width: 250px;">STATUS</th>
                <th class="d-none">REQUEST TYPE ID</th>
                <th class="d-none">STATUS ID</th>
                <th class="d-none">PREPARED BY</th>
                <th class="d-none">SCHEDULE</th>
                <th class="d-none">USER ID</th>
                <th class="d-none">CLIENT NAME</th>
                <th class="d-none">ADDRESS / BRANCH</th>
                <th class="d-none">REASON</th>
            </tr>
        </thead> 
        <tbody>
        </tbody>
    </table>
</div>
@include('pages.stockRequest.newStockRequest')
@include('pages.stockRequest.detailsStockRequest')
@endsection
