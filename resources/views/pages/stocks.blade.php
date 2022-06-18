@extends('layouts.app')
@section('content')
<div class="text-center" style="margin-bottom: -35px;">
    <input class="text-center text-control spChar" style="zoom: 90%; width: 300px;" id="z_serial" type="search" placeholder="SEARCH SERIAL NUMBER" autocomplete="off"/>
</div>
<button class="btn btn-primary bp" id="backBtn" type="button" style="display: none;">BACK</button>
<button class="btn btn-primary bp" id="btnBack" type="button" style="display: none;">BACK</button>
@role('admin|encoder') {{---ROLES---}}
<button class="btn btn-primary bp mb-2" style="float: right;" type="button" data-target="#addStock" data-toggle="modal" data-backdrop="static" data-keyboard="false">ADD STOCK</button>
<button class="btn btn-primary bp mb-2 mr-2" style="float: right;" type="button" id="btnImport">IMPORT</button>
@endrole
@role('viewer') {{---ROLES---}}
<button class="btn btn-primary bp float-right mb-2 d-none" type="button">&nbsp</button>
@endrole
<br><br><br><br>
<a href="/stocks">
    <div id="stocksHeader" class="text-center" style="background-color: #0d1a80; color: white; font-size: 20px; font-weight: bold; height: 45px; line-height: 45px;">
        WAREHOUSE STOCKS
    </div>
</a>
<div id="CategoryTableDiv">
    <table id="CategoryTable" class="table-hover table CategoryTable display" style="width: 100%; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white; zoom: 85%;">                            
            <tr>
                <th>CATEGORY</th>
                <th>DEFECTIVE</th>
                <th>DEMO</th>
                <th>ASSEMBLY</th>
                <th>A1</th>
                <th>A2</th>
                <th>A3</th>
                <th>A4</th>
                <th>BALINTAWAK</th> 
                <th>MALABON</th>
                <th>TOTAL STOCKS</th>
            </tr>
        </thead>
    </table>
</div>
<div id="ItemTableDiv" style="display: none;">
    <table id="ItemTable" class="table-hover table ItemTable display" style="width: 100%; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white; zoom: 85%;">                            
            <tr>
                <th>ITEM CODE</th>
                <th>ITEM DESCRIPTION</th>
                <th>DEFECTIVE</th>
                <th>DEMO</th>
                <th>ASSEMBLY</th>
                <th>A1</th>
                <th>A2</th>
                <th>A3</th>
                <th>A4</th>
                <th>BALINTAWAK</th> 
                <th>MALABON</th>
                <th>TOTAL STOCKS</th>
            </tr>
        </thead>
    </table>
</div>
<div id="ItemSerialTableDiv" style="display: none;">
    <table id="ItemSerialTable" class="table-hover table ItemSerialTable display" style="zoom: 90%; width: 100%; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white;">                            
            <tr>
                <th class="d-none">DATE ADDED</th>
                <th class="d-none">DATE MODIFIED</th>
                <th>DATE ADDED</th>
                <th>DATE MODIFIED</th>
                <th>RESPONSIBLE USER</th>
                <th>QTY</th>
                <th>UOM</th>
                <th>SERIAL</th>
                <th>LOCATION</th>
                <th>RACK NO.</th>
                <th>ROW NO.</th>
            </tr>
        </thead>
    </table>
</div>
<div id="SerialTableDiv" style="display: none;">
    <table id="SerialTable" class="table-hover table SerialTable display" style="zoom: 90%; width: 100%; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white;">                            
            <tr>
                <th class="d-none">DATE ADDED</th>
                <th class="d-none">DATE MODIFIED</th>
                <th>DATE ADDED</th>
                <th>DATE MODIFIED</th>
                <th>RESPONSIBLE USER</th>
                <th>ITEM DESCRIPTION</th>
                <th>SERIAL</th>
                <th>LOCATION</th>
            </tr>
        </thead>
    </table>
</div>
<div class="modal fade in" id="editSerialModal">
    <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
        <div class="modal-header text-center" style="background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">EDIT ITEM SERIAL</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <input type="hidden" name="x_id" id="x_id">
            <input type="hidden" name="y_serial" id="y_serial">
            <input style="margin-bottom: 8px; font-size: 12px;" class="form-control form-control-sm" type="text" name="x_category" id="x_category" readonly>
            <textarea style="margin-bottom: 8px; font-size: 12px; resize: none;" class="form-control" rows="5" name="x_item" id="x_item" readonly></textarea>
            <input style="margin-bottom: 8px; font-size: 12px;" class="form-control form-control-sm" type="text" name="x_serial" id="x_serial" placeholder="Input Item Serial." autocomplete="off">
            <br>
            <button type="button" id="btnEdit" class="btn btn-primary float-right bp" style="zoom: 80%;">EDIT</button>
            <span class="float-right" style="width: 5px;">&nbsp;</span>
            <button type="button" id="btnClear" class="btn btn-primary float-right bp" style="zoom: 80%;">CLEAR</button>
        </div>
    </div>
    </div>
</div>
<style>
    #CategoryTable_length, #CategoryTable_filter{
        margin-top: -90px;
    }
    #ItemTable_length, #ItemTable_filter{
        margin-top: -90px;
    }
    #ItemSerialTable_length, #ItemSerialTable_filter{
        margin-top: -90px;
    }
    #SerialTable_length, #SerialTable_filter{
        margin-top: -90px;
    }
    .text-control {
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        border-radius: 0.25rem;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}
</style>
@include('pages.stocks.addStock')
@include('pages.stocks.importStock')
@include('pages.include')
@endsection
