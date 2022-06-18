@extends('layouts.app')
@section('content')
<table id="defectiveTable" class="table defectiveTable table-hover display" style="zoom: 80%; cursor: pointer; width: 100%;">
    <thead style="background-color: #0d1a80; color: white;">
        <tr>
            <td colspan="8">
                <a href="/defective">
                    <div class="text-center" style="background-color: #0d1a80; color: white; font-size: 25px; font-weight: bold; height: 43px; line-height: 43px;">
                        DEFECTIVE ITEMS
                    </div>
                </a>
            </td>
        </tr>
        <tr>
            <th class="d-none">DATE ADDED</th>
            <th style="width: 12%;">DATE ADDED</th>
            <th>ADDED BY</th>
            <th style="width: 11%;">RETURN NUMBER</th>
            <th>CATEGORY</th>
            <th>ITEM DESCRIPTION</th>
            <th style="width: 14%;">SERIAL NUMBER</th>
            <th style="width: 10%;">STATUS</th>
        </tr>
    </thead>
</table>
<form class="d-none">
    <input type="hidden" name="return_number" id="return_number">
</form>
@include('pages.include')
@endsection