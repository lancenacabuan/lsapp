@extends('layouts.app')
@section('content')
<div class="table-responsive">
    <button class="btn btn-primary ml-auto bp" type="button" data-toggle="modal" style="margin-right: 10px;">NEW ASSEMBLY</button>
    <button class="btn btn-primary ml-auto bp" type="button" data-toggle="modal">CREATE ITEM</button>
    <br><br>
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
@endsection
