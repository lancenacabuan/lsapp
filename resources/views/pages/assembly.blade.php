@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="alert alert-warning" role="alert">
        <i class='fa fa-exclamation-triangle'></i>
        <strong>Note:</strong> This webpage module is still currently under maintenance. Thank you very much for understanding.
    </div>
    @role('assembler') {{---ROLES---}}
    <button class="btn btn-primary bp btnNewAssembly" type="button">NEW ASSEMBLY</button>
    <br><br>
    @endrole
    <table id="assemblyTable" class="table assemblyTable table-hover display" style="width: 100%; zoom: 80%; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white; font-size: 15px;"> 
            <tr>
                <th style="width: 150px;">DATE NEEDED</th>
                <th style="width: 150px;">DATE REQUESTED</th>
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
@include('pages.assembly.newAssembly')
@include('pages.assembly.detailsAssembly')
@endsection
