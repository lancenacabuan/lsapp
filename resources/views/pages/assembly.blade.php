@extends('layouts.app')
@section('content')
<div class="container-fluid">
    @role('admin|encoder') {{---ROLES---}}
    <button class="btn btn-primary bp" type="button" data-toggle="modal" data-target="#newAssembly" data-backdrop="static" data-keyboard="false">NEW ASSEMBLY</button>
    <br><br>
    @endrole
    <table id="assemblyTable" class="table assemblyTable table-hover display" style="width: 100%; zoom: 80%; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white; font-size: 15px;"> 
            <tr>
                <th>DATE NEEDED</th>
                <th>DATE REQUESTED</th>
                <th>REQUESTED BY</th>
                <th>ITEM DESCRIPTION</th>
                <th>STATUS</th>
            </tr>
        </thead>
    </table>
</div>
@include('pages.assembly.newAssembly')
@endsection
