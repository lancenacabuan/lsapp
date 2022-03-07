@extends('layouts.app')
@section('content')
<div class="table-responsive">
    @role('admin|encoder')
    <button class="btn btn-primary ml-auto bp btnNewStockTransfer" type="button">
        NEW STOCK TRANSFER</button>
    <br><br>
    @endrole
    <input type="hidden" id="current_user" value="{{auth()->user()->id}}">
    <input type="hidden" id="current_role" value="{{auth()->user()->getRoleNames()}}">
    <table id="stocktransferTable" class="table stocktransferTable table-hover display" style="width:100%; zoom: 80%; cursor: pointer;">
        <thead style="background-color: #0d1a80 !important; color: white !important; font-weight: bold !important; font-size: 15px;">
            <tr>
                <th>DATE REQUESTED</th>
                <th>DATE NEEDED</th>
                <th>REQUEST NUMBER</th>
                <th>REQUESTED BY</th>
                <th>FROM LOCATION</th>
                <th>TO NEW LOCATION</th>
                <th>STATUS</th>
                <th>STATUS ID</th>
                <th>FROM LOCATION</th>
                <th>TO NEW LOCATION</th>
                <th>PREPARED BY</th>
                <th>SCHEDULE</th>
                <th>USER ID</th>
                <th>REASON</th>
            </tr>
        </thead>
    </table>
</div>
@include('pages.stockTransfer.newStockTransfer')
@include('pages.stockTransfer.detailsStockTransfer')
@endsection
