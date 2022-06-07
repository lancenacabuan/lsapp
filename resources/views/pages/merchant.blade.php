@extends('layouts.app')
@section('content')
<div class="container-fluid">
    @role('merchant') {{---ROLES---}}
    <button class="btn btn-primary bp btnNewMerchRequest float-right mb-2" type="button">NEW MERCHANT STOCK REQUEST</button>
    <br><br>
    @endrole
    <br><br>
    <a href="/merchant">
        <div class="text-center" style="background-color: #0d1a80; color: white; font-size: 20px; font-weight: bold; height: 45px; line-height: 45px;">
            MERCHANT STOCK REQUESTS
        </div>
    </a>
    <table id="merchantTable" class="table merchantTable table-hover display" style="width: 100%; zoom: 80%; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white; font-size: 15px;"> 
            <tr>
                <th style="width: 150px;">DATE REQUESTED</th>
                <th style="width: 150px;">DATE NEEDED</th>
                <th style="width: 150px;">REQUEST NUMBER</th>
                <th style="width: 250px;">REQUESTED BY</th>
                <th style="width: 150px;">ORDER ID</th>
                <th style="width: 200px;">STATUS</th>
            </tr>
        </thead>
    </table>
</div>
<style>
    #merchantTable_length, #merchantTable_filter{
        margin-top: -90px;
    }
</style>
@include('pages.merchant.newMerchRequest')
@include('pages.merchant.detailsMerchRequest')
@include('pages.include')
@endsection