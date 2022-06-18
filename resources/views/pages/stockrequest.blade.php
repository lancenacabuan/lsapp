@extends('layouts.app')
@section('content')
@role('sales') {{---ROLES---}}
<button class="btn btn-primary bp btnNewStockRequest float-right mb-2" type="button">NEW STOCK REQUEST</button>
<br><br>
@endrole
<table id="stockrequestTable" class="table stockrequestTable table-hover display" style="width: 100%; zoom: 80%; cursor: pointer;">
    <thead style="background-color: #0d1a80; color: white;">                            
        <tr>
            <td colspan="11">
                <a href="/stockrequest">
                    <div class="text-center" style="background-color: #0d1a80; color: white; font-size: 25px; font-weight: bold; height: 43px; line-height: 43px;">
                        STOCK REQUESTS
                    </div>
                </a>
            </td>
        </tr>
        <tr>
            <th class="d-none">DATE REQUESTED</th>
            <th class="d-none">DATE NEEDED</th>
            <th style="width: 11%;">DATE REQUESTED</th>
            <th style="width: 11%;">DATE NEEDED</th>
            <th>CLIENT / REQNUM</th>
            <th>ADDRESS / BRANCH</th>
            <th style="width: 12%;">SO/PO NO. / ORDER ID</th>
            <th>REQUESTED BY</th>
            <th style="width: 9%;">REQUEST TYPE</th>
            <th style="width: 15%;">STATUS</th>
            <th class="d-none">REQUEST NUMBER</th>
        </tr>
    </thead>
</table>
@include('pages.stockRequest.newStockRequest')
@include('pages.stockRequest.detailsStockRequest')
@include('pages.include')
@endsection
