@extends('layouts.app')
@section('content')
<div class="container-fluid">
    @role('admin|encoder') {{---ROLES---}}
    <button class="btn btn-primary bp btnNewStockTransfer float-right mb-2" type="button">NEW STOCK TRANSFER</button>
    <br><br>
    @endrole
    <table id="stocktransferTable" class="table stocktransferTable table-hover display" style="width: 100%; zoom: 80%; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white; font-size: 15px;">
            <tr>
                <th style="width: 150px;">DATE REQUESTED</th>
                <th style="width: 150px;">DATE NEEDED</th>
                <th style="width: 150px;">REQUEST NUMBER</th>
                <th>REQUESTED BY</th>
                <th style="width: 180px;">FROM LOCATION</th>
                <th style="width: 180px;">TO NEW LOCATION</th>
                <th style="width: 250px;">STATUS</th>
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
@include('pages.report')
@endsection
