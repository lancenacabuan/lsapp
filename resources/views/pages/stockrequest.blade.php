@extends('layouts.app')
@section('content')
<div class="panel-body table-responsive">
    <div class="animate fadeInDown two">
        @if(auth()->user()->hasanyRole('sales'))
            <div class="col-md-12 mb-4">
                <button id="newstockreq" class="btn btn-primary bp newstockreq" data-toggle="modal" data-target="#newStockRequest"  data-backdrop="static" style="font-weight:bold;">
                NEW STOCK REQUEST</button>
            </div>
        @endif
    <div class="container-fluid"  id="stockTableMain">
    <input type="hidden" id="current_user" value="{{auth()->user()->id}}">
    <input type="hidden" id="current_role" value="{{auth()->user()->getRoleNames()}}">
    <table id="stockreqDetails" class="table stock_request display" style="zoom: 80%">
        <thead style="background-color: #0d1a80 !important; color: white !important; font-weight: bold !important; font-size: 15px;">                            
            <tr>
                <th>DATE REQUESTED</th>
                <th>REQUEST NUMBER</th>
                <th>REFERENCE SO/PO #</th>
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
            </tr>
        </thead> 
        <tbody>
        </tbody>
    </table>
    </div>
    </div>
</div>
@include('pages.stockRequest.newStockRequest')
@include('pages.stockRequest.stockRequestDetails')
@endsection
