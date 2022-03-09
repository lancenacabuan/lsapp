@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <button class="btn btn-primary bp" type="button" data-toggle="modal" style="margin-right: 10px;">NEW ASSEMBLY</button>
    <button class="btn btn-primary bp" type="button" data-toggle="modal" data-target="#createItem">CREATE ITEM</button>
</div>
<div class="container-fluid">
    <h3 class="text-center"><strong>ASSEMBLY</strong></h3>
    <table class="table-hover table assemblyTable" id="assemblyTable" style="background-color: #0d1a80 !important; color: white !important; width:100%; font-size:80%">
        <thead>
            <tr>
                <th>DATE REQUESTED</th>
                <th>REQUESTED BY</th>
                <th>ITEM DESCRIPTION</th>
                <th>STATUS</th>
            </tr>
        </thead>
    </table>
</div>
@include('pages.assembly.createItem')
{{-- @include('pages.assembly.newAssembly') --}}
@endsection
