@extends('layouts.app')
@section('content')
<div class="container-fluid">
    @role('assembler') {{---ROLES---}}
    <button class="btn btn-primary bp btnNewAssembly float-right mb-2" type="button">NEW ASSEMBLY</button>
    <br><br>
    @endrole
    <br><br>
    <a href="/assembly">
        <div class="text-center" style="background-color: #0d1a80; color: white; font-size: 20px; font-weight: bold; height: 45px; line-height: 45px;">
            ASSEMBLY STOCK REQUESTS
        </div>
    </a>
    <table id="assemblyTable" class="table assemblyTable table-hover display" style="width: 100%; zoom: 80%; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white; font-size: 15px;"> 
            <tr>
                <th style="width: 150px;">DATE REQUESTED</th>
                <th style="width: 150px;">DATE NEEDED</th>
                <th style="width: 150px;">REQUEST NUMBER</th>
                <th style="width: 250px;"> REQUESTED BY</th>
                <th style="width: 150px;">REQUEST TYPE</th>
                <th>ITEM DESCRIPTION</th>
                <th style="width: 150px;">QUANTITY</th>
                <th style="width: 200px;">STATUS</th>
                <th class="d-none">REQUEST TYPE ID</th>
                <th class="d-none">ITEM ID</th>
                <th class="d-none">STATUS ID</th>
                <th class="d-none">PREPARED BY</th>
                <th class="d-none">SCHEDULE</th>
                <th class="d-none">USER ID</th>
                <th class="d-none">ASSEMBLY NUMBER</th>
            </tr>
        </thead>
    </table>
</div>
<style>
    #assemblyTable_length, #assemblyTable_filter{
        margin-top: -90px;
    }
</style>
@include('pages.assembly.newAssembly')
@include('pages.assembly.detailsAssembly')
@include('pages.report')
@endsection
