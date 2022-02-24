@extends('layouts.app')
@section('content')
<div class="table-responsive">
<ul class="nav nav-pills">
    <li class="nav-item-link" style="border: 3px solid #0d1a80; border-radius: 10px 10px 0px 0px !important;">
        <a class="nav-link" id="nav1" href="{{ url('/maintenance') }}"><strong>ITEM</strong></a>
    </li>
    <li class="nav-item-link" style="margin-left: 5px; border: 3px solid #0d1a80; border-radius: 10px 10px 0px 0px !important;">
        <a class="nav-link" id="nav2" href="{{ url('/maintenance?tbl=category') }}"><strong>CATEGORY</strong></a>
    </li>
    <li class="nav-item-link" style="margin-left: 5px; border: 3px solid #0d1a80; border-radius: 10px 10px 0px 0px !important;">
        {{-- <a class="nav-link" id="nav3" href="{{ url('/maintenance?tbl=location') }}"><strong>LOCATION</strong></a> --}}
        <a class="nav-link" id="nav3" href="#"><strong>LOCATION</strong></a>
    </li>
</ul>
<div style="margin-top: -3px; height: 20px; background-color: #0d1a80;">
</div>
<br>
    <table class="table-hover table itemTable" id="itemTable" style="width:100%; font-size:80%; display: none; cursor: pointer;">
        <thead style="background-color: #0d1a80 !important; color: white !important;">
            {{-- <tr class="tbsearch">
                <td>
                    <input type="text" class="form-control filter-input fl-0" data-column="0" style="border:1px solid #808080"/>
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-1" data-column="1" style="border:1px solid #808080"/>
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-2" data-column="2" style="border:1px solid #808080"/>
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-3" data-column="3" style="border:1px solid #808080"/>
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-4" data-column="4" style="border:1px solid #808080"/>
                </td>
            </tr> --}}
            <tr>
                <th>ITEM ID</th>
                <th>CATEGORY NAME</th>
                <th>ITEM DESCRIPTION</th>
                <th>CATEGORY ID</th>
                <th>UOM</th>
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
    <table class="table-hover table locationTable" id="locationTable" style="width:100%; font-size:80%; display: none; cursor: pointer;">
        <thead style="background-color: #0d1a80 !important; color: white !important;">
            <tr>
                <th>LOCATION ID</th>
                <th>LOCATION NAME</th>
            </tr>
        </thead>
    </table>
    @role('admin')
    <hr>
    <button class="btn btn-primary bp btnNewItem" type="button" data-toggle="modal" data-target="#newItem" data-backdrop="static" style="display: none;">
        NEW ITEM</button>
    <button class="btn btn-primary bp btnNewCategory" type="button" data-toggle="modal" data-target="#newCategory" data-backdrop="static" style="display: none;">
        NEW CATEGORY</button>
    <button class="btn btn-primary bp btnNewLocation" type="button" data-toggle="modal" data-target="#newLocation" data-backdrop="static" style="display: none;">
        NEW LOCATION</button>
    @endrole
</div>
<style>
    .active-link{
        background-color: #0d1a80 !important;
        color: white !important;
        border-radius: 6px 6px 0px 0px !important;
    }
    .nav-item-link>a:hover {
        background-color: #0d6efd !important;
        color:white !important;
        border-radius: 6px 6px 0px 0px !important;
    }
    .nav-pills a{
        color: #0d1a80;
    }
</style>
@include('pages.fileMaintenance.newItem')
@include('pages.fileMaintenance.detailsItem')
@include('pages.fileMaintenance.newCategory')
@include('pages.fileMaintenance.detailsCategory')
@include('pages.fileMaintenance.newLocation')
{{-- @include('pages.fileMaintenance.detailsLocation') --}}
@endsection
