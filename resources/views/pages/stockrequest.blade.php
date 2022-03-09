@extends('layouts.app')
@section('content')
<div class="container-fluid">
    @if(auth()->user()->hasanyRole('sales')) {{---ROLES---}}
    <button id="newstockreq" class="btn btn-primary bp newstockreq" type="button">NEW STOCK REQUEST</button>
    @else
    <br>
    @endif
</div>
<div class="container-fluid"  id="stockTableMain">
    <input type="hidden" id="current_user" value="{{auth()->user()->id}}">
    <input type="hidden" id="current_role" value="{{auth()->user()->getRoleNames()}}">
    <h3 class="text-center"><strong>STOCK REQUEST</strong></h3>
    <table id="stockreqDetails" class="table stock_request table-hover display" style="zoom: 80%; cursor: pointer;">
        <thead style="background-color: #0d1a80 !important; color: white !important; font-weight: bold !important; font-size: 15px;">                            
            <tr>
                <th>DATE NEEDED</th>
                <th>DATE REQUESTED</th>
                <th>REQUEST NUMBER</th>
                <th>REFERENCE SO/PO NO.</th>
                <th>REQUESTED BY</th>
                <th>REQUEST TYPE</th>
                <th>STATUS</th>
                <th>REQUEST TYPE ID</th>
                <th>STATUS ID</th>
                <th>PREPARED BY</th>
                <th>SCHEDULE</th>
                <th>USER ID</th>
                <th>CLIENT NAME</th>
                <th>ADDRESS / BRANCH</th>
                <th>REASON</th>
            </tr>
        </thead> 
        <tbody>
        </tbody>
    </table>
</div>
@include('pages.stockRequest.newStockRequest')
@include('pages.stockRequest.stockRequestDetails')
@endsection
