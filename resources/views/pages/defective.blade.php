@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <br><br>
    <a href="/defective">
        <div class="text-center" style="background-color: #0d1a80; color: white; font-size: 20px; font-weight: bold; height: 45px; line-height: 45px;">
            DEFECTIVE ITEMS
        </div>
    </a>
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
        <input type="hidden" name="return_number" id="return_number">
    </form>
</div>
<style>
    #defectiveTable_length, #defectiveTable_filter{
        margin-top: -90px;
    }
</style>
@include('pages.include')
@endsection