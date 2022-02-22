@extends('layouts.app')
@section('content')
<ul class="nav nav-pills">
    <li class="nav-item-link">
        <a class="nav-link" id="nav1" href="{{ url('/filemaintenance') }}"><strong>ITEM</strong></a>
    </li>
    <li class="nav-item-link">
        <a class="nav-link" id="nav2" href="{{ url('/filemaintenance?tbl=category') }}"><strong>CATEGORY</strong></a>
    </li>
    @role('admin|encoder')
    <span style="position: absolute; right: 120px;">
    <button class="btn btn-primary bp btnNewItem" type="button" data-toggle="modal" data-target="#newItem" data-backdrop="static" style="display: none;">
        NEW ITEM</button>
    <button class="btn btn-primary bp btnNewCategory" type="button" data-toggle="modal" data-target="#newCategory" data-backdrop="static" style="display: none;">
        NEW CATEGORY</button>
    </span>
    @endrole
</ul>
<br>
<div class="table-responsive">
    <table class="table-hover table itemTable" id="itemTable" style="width:100%; font-size:80%; display: none; cursor: pointer;">
        <thead style="background-color: #0d1a80 !important; color: white !important;">
            <tr class="tbsearch">
                <td>
                    <input type="text" class="form-control filter-input fl-0" data-column="0" style="border:1px solid #808080"/>
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-1" data-column="1" style="border:1px solid #808080"/>
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-2" data-column="2" style="border:1px solid #808080"/>
                </td>
            </tr>
            <tr>
                <th>ITEM ID</th>
                <th>CATEGORY NAME</th>
                <th>ITEM DESCRIPTION</th>
            </tr>
        </thead>
    </table>
    <table class="table-hover table categoryTable" id="categoryTable" style="width:100%; font-size:80%; display: none; cursor: pointer;">
        <thead style="background-color: #0d1a80 !important; color: white !important;">
            <tr>
                <th>CATEGORY ID</th>
                <th>CATEGORY NAME</th>
            </tr>
        </thead>
    </table>
</div>
<style>
    .active-link{
        background-color: #0d1a80 !important;
        color: white !important;
        border-radius: 0px !important;
    }
    .nav-item-link>a:hover {
        background-color: #0d6efd !important;
        color:white !important;
        border-radius: 0px !important;
    }
</style>
{{-- @include('pages.fileMaintenance.newItem') --}}
@include('pages.fileMaintenance.newCategory')
@endsection
