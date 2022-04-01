@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="alert alert-warning alert-dismissible" role="alert">
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
                <th>DATE NEEDED</th>
                <th>DATE REQUESTED</th>
                <th>REQUEST NUMBER</th>
                <th>REQUESTED BY</th>
                <th>ITEM DESCRIPTION</th>
                <th>STATUS</th>
            </tr>
        </thead>
    </table>
</div>
@include('pages.assembly.newAssembly')
@endsection
