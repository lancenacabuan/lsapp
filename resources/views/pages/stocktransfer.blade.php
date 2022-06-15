@extends('layouts.app')
@section('content')
@role('admin|encoder') {{---ROLES---}}
<button class="btn btn-primary bp btnNewStockTransfer float-right mb-2" type="button">NEW STOCK TRANSFER</button>
<br><br>
@endrole
<table id="stocktransferTable" class="table stocktransferTable table-hover display" style="width: 100%; zoom: 80%; cursor: pointer;">
    <thead style="background-color: #0d1a80; color: white;">
        <tr>
            <td colspan="7">
                <a href="/stocktransfer">
                    <div class="text-center" style="background-color: #0d1a80; color: white; font-size: 25px; font-weight: bold; height: 43px; line-height: 43px;">
                        STOCK TRANSFER REQUESTS
                    </div>
                </a>
            </td>
        </tr>
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
@include('pages.stockTransfer.newStockTransfer')
@include('pages.stockTransfer.detailsStockTransfer')
@include('pages.include')
@endsection
