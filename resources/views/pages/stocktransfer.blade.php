@extends('layouts.app')
@section('content')
<div class="container-fluid">
    @role('admin|encoder') {{---ROLES---}}
    <button class="btn btn-primary bp btnNewStockTransfer" type="button">NEW STOCK TRANSFER</button>
    <br><br>
    @endrole
</div>
<div class="container-fluid">
    <input type="hidden" id="current_user" value="{{auth()->user()->id}}">
    <input type="hidden" id="current_role" value="{{auth()->user()->getRoleNames()}}">
    <table id="stocktransferTable" class="table stocktransferTable table-hover display" style="width: 100%; zoom: 80%; cursor: pointer;">
        <thead style="background-color: #0d1a80 !important; color: white !important; font-weight: bold !important; font-size: 15px;">
            <tr>
                <th>DATE NEEDED</th>
                <th>DATE REQUESTED</th>
                <th>REQUEST NUMBER</th>
                <th>REQUESTED BY</th>
                <th>FROM LOCATION</th>
                <th>TO NEW LOCATION</th>
                <th>STATUS</th>
                <th class="d-none">STATUS ID</th>
                <th class="d-none">FROM LOCATION</th>
                <th class="d-none">TO NEW LOCATION</th>
                <th class="d-none">PREPARED BY</th>
                <th class="d-none">SCHEDULE</th>
                <th class="d-none">USER ID</th>
                <th class="d-none">REASON</th>
            </tr>
        </thead>
    </table>
</div>
@include('pages.stockTransfer.newStockTransfer')
@include('pages.stockTransfer.detailsStockTransfer')
@endsection
