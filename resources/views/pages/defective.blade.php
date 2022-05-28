@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <table id="defectiveTable" class="table defectiveTable table-hover display" style="zoom: 80%; cursor: pointer; width: 100%;">
        <thead style="background-color: #0d1a80; color: white; font-size: 15px;">
            <tr>
                <th>ID</th>
                <th>DATE ADDED</th>
                <th>ADDED BY</th>
                <th>RETURN NUMBER</th>
                <th>CATEGORY</th>
                <th>ITEM DESCRIPTION</th>
                <th>SERIAL NUMBER</th>
                <th>STATUS</th>
            </tr>
        </thead>
    </table>
    <form class="d-none">
        <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
        <input type="hidden" id="current_role" value="{{auth()->user()->getRoleNames()}}">
        <input type="hidden" name="return_number" id="return_number">
    </form>
</div>
@include('pages.report')
@endsection