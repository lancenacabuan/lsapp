@extends('layouts.app')
@section('content')
<div class="container-fluid">
    @role('sales') {{---ROLES---}}
    <button class="btn btn-primary bp btnNewStockRequest float-right mb-2" type="button">NEW STOCK REQUEST</button>
    <br><br>
    @endrole
    <table id="stockrequestTable" class="table stockrequestTable table-hover display" style="width: 100%; zoom: 80%; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white;">                            
            <tr>
                <td colspan="8">
                    <a href="/stockrequest">
                        <div class="text-center" style="background-color: #0d1a80; color: white; font-size: 25px; font-weight: bold; height: 43px; line-height: 43px;">
                            STOCK REQUESTS
                        </div>
                    </a>
                </td>
            </tr>
            <tr>
                <th style="width: 150px;">DATE REQUESTED</th>
                <th style="width: 150px;">DATE NEEDED</th>
                <th>CLIENT / REQNUM</th>
                <th>ADDRESS / BRANCH</th>
                <th>SO/PO NO. / ORDER ID</th>
                <th>REQUESTED BY</th>
                <th>REQUEST TYPE</th>
                <th style="width: 200px;">STATUS</th>
                <th class="d-none">REQUEST NUMBER</th>
            </tr>
        </thead>
    </table>
</div>
@include('pages.stockRequest.newStockRequest')
@include('pages.stockRequest.detailsStockRequest')
@include('pages.include')
@endsection
