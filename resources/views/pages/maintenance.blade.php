@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <ul class="nav nav-pills">
        <li class="nav-item-link" style="border: 3px solid #0d1a80; border-radius: 10px 10px 0px 0px !important;">
            <a class="nav-link" id="nav1" href="{{ url('/maintenance') }}"><strong>ITEMS</strong></a>
        </li>
        <li class="nav-item-link" style="margin-left: 5px; border: 3px solid #0d1a80; border-radius: 10px 10px 0px 0px !important;">
            <a class="nav-link" id="nav2" href="{{ url('/maintenance?tbl=assemblyitems') }}"><strong>ASSEMBLY ITEMS</strong></a>
        </li>
        <li class="nav-item-link" style="margin-left: 5px; border: 3px solid #0d1a80; border-radius: 10px 10px 0px 0px !important;">
            <a class="nav-link" id="nav3" href="{{ url('/maintenance?tbl=categories') }}"><strong>CATEGORIES</strong></a>
        </li>
        <li class="nav-item-link" style="margin-left: 5px; border: 3px solid #0d1a80; border-radius: 10px 10px 0px 0px !important;">
            <a class="nav-link" id="nav4" href="{{ url('/maintenance?tbl=locations') }}"><strong>LOCATIONS</strong></a>
        </li>
    </ul>
    <div style="margin-top: -3px; color: white; height: 20px; background-color: #0d1a80;"></div>
    <br>
    <div id="maintenance" class="alert alert-warning alert-dismissible" style="display: none;" role="alert">
        <i class='fa fa-exclamation-triangle'></i>
        <strong>Note:</strong> This webpage module is still currently under maintenance. Thank you very much for understanding...
    </div>
    <table class="table-hover table itemTable" id="itemTable" style="width: 100%; display: none; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white;">
            <tr>
                <th>ITEM ID</th>
                <th>CATEGORY NAME</th>
                <th>ITEM DESCRIPTION</th>
                <th>CATEGORY ID</th>
                <th>UOM</th>
            </tr>
        </thead>
    </table>
    <table class="table-hover table assemblyitemTable" id="assemblyitemTable" style="width: 100%; display: none; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white;">
            <tr>
                <th>ITEM ID</th>
                <th>CATEGORY NAME</th>
                <th>ITEM DESCRIPTION</th>
                <th>CATEGORY ID</th>
                <th>UOM</th>
            </tr>
        </thead>
    </table>
    <table class="table-hover table categoryTable" id="categoryTable" style="width: 100%; display: none; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white;">
            <tr>
                <th>CATEGORY ID</th>
                <th>CATEGORY NAME</th>
            </tr>
        </thead>
    </table>
    <table class="table-hover table locationTable" id="locationTable" style="width: 100%; display: none; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white;">
            <tr>
                <th>LOCATION ID</th>
                <th>LOCATION NAME</th>
                <th>LOCATION STATUS</th>
            </tr>
        </thead>
    </table>
    @role('admin') {{---ROLES---}}
    <hr>
    <button class="btn btn-primary bp btnNewItem" type="button" data-toggle="modal" data-target="#newItem" data-backdrop="static" data-keyboard="false" style="display: none;">NEW ITEM</button>
    <button class="btn btn-primary bp btnCreateItem" type="button" data-toggle="modal" data-target="#createItem" data-backdrop="static" data-keyboard="false" style="display: none;">CREATE ITEM</button>
    <button class="btn btn-primary bp btnNewCategory" type="button" data-toggle="modal" data-target="#newCategory" data-backdrop="static" data-keyboard="false" style="display: none;">NEW CATEGORY</button>
    <button class="btn btn-primary bp btnNewLocation" type="button" style="display: none;">REQUEST NEW LOCATION</button>
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
@include('pages.fileMaintenance.createItem')
@include('pages.fileMaintenance.detailsAssemblyItem')
@include('pages.fileMaintenance.newCategory')
@include('pages.fileMaintenance.detailsCategory')
@include('pages.fileMaintenance.newLocation')
@include('pages.fileMaintenance.detailsLocation')
@endsection
