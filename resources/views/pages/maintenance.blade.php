@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <ul class="nav nav-pills">
        <li class="nav-item-link" style="border: 3px solid #0d1a80; border-radius: 10px 10px 0px 0px !important;">
            <a class="nav-link" id="nav1" href="{{ url('/maintenance') }}"><strong>ITEMS</strong></a>
        </li>
        <li class="nav-item-link" style="margin-left: 5px; border: 3px solid #0d1a80; border-radius: 10px 10px 0px 0px !important;">
            <a class="nav-link" id="nav2" href="{{ url('/maintenance?tbl=assembleditems') }}"><strong>ASSEMBLED ITEMS</strong></a>
        </li>
        <li class="nav-item-link" style="margin-left: 5px; border: 3px solid #0d1a80; border-radius: 10px 10px 0px 0px !important;">
            <a class="nav-link" id="nav3" href="{{ url('/maintenance?tbl=categories') }}"><strong>CATEGORIES</strong></a>
        </li>
        <li class="nav-item-link" style="margin-left: 5px; border: 3px solid #0d1a80; border-radius: 10px 10px 0px 0px !important;">
            <a class="nav-link" id="nav4" href="{{ url('/maintenance?tbl=locations') }}"><strong>LOCATIONS</strong></a>
        </li>
        <li class="nav-item-link" style="margin-left: 5px; border: 3px solid #0d1a80; border-radius: 10px 10px 0px 0px !important;">
            <a class="nav-link" id="nav5" href="{{ url('/maintenance?tbl=warranty') }}"><strong>WARRANTY</strong></a>
        </li>
    </ul>
    @role('admin') {{---ROLES---}}
    <div style="float: right; margin-top: -45px;">
        <button class="btn btn-primary bp btnNewItem" type="button" style="display: none;">NEW ITEM</button>
        <button class="btn btn-primary bp btnCreateItem" type="button" style="display: none;">CREATE ASSEMBLED ITEM</button>
        <button class="btn btn-primary bp btnNewCategory" type="button" style="display: none;">NEW CATEGORY</button>
        <button class="btn btn-primary bp btnNewLocation" type="button" style="display: none;">REQUEST NEW LOCATION</button>
        <button class="btn btn-primary bp btnNewWarranty" type="button" style="display: none;">NEW WARRANTY</button>
    </div>
    @endrole
    <div style="margin-top: -3px; margin-bottom: 10px; color: white; height: 20px; background-color: #0d1a80;"></div>
    <table class="table-hover table itemTable" id="itemTable" style="width: 100%; display: none; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white;">
            <tr>
                <th class="d-none">ITEM ID</th>
                <th class="d-none">CATEGORY NAME</th>
                <th>ITEM DESCRIPTION</th>
                <th class="d-none">CATEGORY ID</th>
                <th class="d-none">UOM</th>
                <th>ITEM CODE</th>
            </tr>
        </thead>
    </table>
    <table class="table-hover table assemblyitemTable" id="assemblyitemTable" style="width: 100%; display: none; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white;">
            <tr>
                <th class="d-none">ITEM ID</th>
                <th class="d-none">CATEGORY NAME</th>
                <th>ITEM DESCRIPTION</th>
                <th class="d-none">CATEGORY ID</th>
                <th class="d-none">UOM</th>
                <th>ITEM CODE</th>
            </tr>
        </thead>
    </table>
    <table class="table-hover table categoryTable" id="categoryTable" style="width: 100%; display: none; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white;">
            <tr>
                <th class="d-none">CATEGORY ID</th>
                <th>CATEGORY NAME</th>
            </tr>
        </thead>
    </table>
    <table class="table-hover table locationTable" id="locationTable" style="width: 100%; display: none; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white;">
            <tr>
                <th class="d-none">LOCATION ID</th>
                <th>LOCATION NAME</th>
                <th>LOCATION STATUS</th>
            </tr>
        </thead>
    </table>
    <table class="table-hover table warrantyTable" id="warrantyTable" style="width: 100%; display: none; cursor: pointer; margin-top: -10px;">
        <thead style="background-color: #0d1a80; color: white;">
            <tr>
                <th>WARRANTY NAME</th>
                <th>DURATION</th>
                <th>PHONE SUPPORT</th>
                <th>ONSITE SUPPORT</th>
                <th>SOFTWARE</th>
                <th>HARDWARE</th>
                <th>PARTS REPLACEMENT</th>
                <th>SERVICE UNIT</th>
            </tr>
        </thead>
    </table>
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
    .checkbox_span{
        font-weight: bolder;
        zoom: 125%;
    }
</style>
@include('pages.fileMaintenance.newItem')
@include('pages.fileMaintenance.detailsItem')
@include('pages.fileMaintenance.createItem')
@include('pages.fileMaintenance.detailsAssemblyItem')
@include('pages.fileMaintenance.newCategory')
@include('pages.fileMaintenance.detailsCategory')
@include('pages.fileMaintenance.newLocation')
@include('pages.fileMaintenance.detailsLocation')
@include('pages.fileMaintenance.warranty')
@endsection
