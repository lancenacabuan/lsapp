@extends('layouts.app')
@section('content')
<div class="table-responsive">
    @role('admin|encoder')
    <button class="btn btn-primary ml-auto bp btnNewStockTransfer" type="button" data-toggle="modal" data-target="#newStockTransfer">NEW STOCK TRANSFER</button>
    <br><br>
    @endrole
    <table class="table-hover table stocktranferTable" id="stocktranferTable" style="background-color: #0d1a80 !important; color: white !important; width:100%; font-size:80%">
        <thead>
            <tr>
                <th>DATE REQUESTED</th>
                <th>DATE NEEDED</th>
                <th>REQUEST NUMBER</th>
                <th>REQUESTED BY</th>
                <th>LOCATION</th>
                <th>STATUS</th>
            </tr>
        </thead>
    </table>
</div>
@include('pages.stockTransfer.newStockTransfer')
@endsection
