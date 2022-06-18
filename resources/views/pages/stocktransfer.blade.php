@extends('layouts.app')
@section('content')
@role('admin|encoder') {{---ROLES---}}
<button class="btn btn-primary bp btnNewStockTransfer float-right mb-2" type="button">NEW STOCK TRANSFER</button>
<br><br>
@endrole
<table id="stocktransferTable" class="table stocktransferTable table-hover display" style="width: 100%; zoom: 80%; cursor: pointer;">
    <thead style="background-color: #0d1a80; color: white;">
        <tr>
            <td colspan="9">
                <a href="/stocktransfer">
                    <div class="text-center" style="background-color: #0d1a80; color: white; font-size: 25px; font-weight: bold; height: 43px; line-height: 43px;">
                        STOCK TRANSFER REQUESTS
                    </div>
                </a>
            </td>
        </tr>
        <tr>
            <th class="d-none">DATE REQUESTED</th>
            <th class="d-none">DATE NEEDED</th>
            <th style="width: 12%;">DATE REQUESTED</th>
            <th style="width: 12%;">DATE NEEDED</th>
            <th style="width: 12%;">REQUEST NUMBER</th>
            <th>REQUESTED BY</th>
            <th style="width: 14%;">FROM LOCATION</th>
            <th style="width: 14%;">TO NEW LOCATION</th>
            <th style="width: 18%;">STATUS</th>
        </tr>
    </thead>
</table>
@include('pages.stockTransfer.newStockTransfer')
@include('pages.stockTransfer.detailsStockTransfer')
@include('pages.include')
@endsection
