@extends('layouts.app')
@section('content')
<div class="container-fluid">
    @role('admin|encoder') {{---ROLES---}}
    <button class="btn btn-primary bp btnNewStockTransfer float-right mb-2" type="button">NEW STOCK TRANSFER</button>
    <br><br>
    @endrole
    <br><br>
    <a href="/stocktransfer">
        <div class="text-center" style="background-color: #0d1a80; color: white; font-size: 20px; font-weight: bold; height: 45px; line-height: 45px;">
            STOCK TRANSFER REQUESTS
        </div>
    </a>
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
            </tr>
        </thead>
    </table>
</div>
<style>
    #stocktransferTable_length, #stocktransferTable_filter{
        margin-top: -90px;
    }
</style>
@include('pages.stockTransfer.newStockTransfer')
@include('pages.stockTransfer.detailsStockTransfer')
@include('pages.include')
@endsection
