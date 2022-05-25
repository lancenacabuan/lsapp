@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <button class="btn btn-primary bp" id="backBtn" type="button" style="display: none;">BACK</button>
    <button class="btn btn-primary bp" id="btnBack" type="button" style="display: none;">BACK</button>
    @role('admin|encoder') {{---ROLES---}}
    <button class="btn btn-primary bp float-right mb-2" type="button" data-target="#addStock" data-toggle="modal" data-backdrop="static" data-keyboard="false">ADD STOCK</button>
    <span class="float-right" style="margin-right: 5px;">&nbsp;</span>
    <button class="btn btn-primary bp float-right mb-2" type="button" id="btnImport">IMPORT</button>
    <br><br>
    @endrole
    <input type="hidden" id="current_role" value="{{auth()->user()->getRoleNames()}}">
    <div id="CategoryTableDiv">
        <table id="CategoryTable" class="table-hover table CategoryTable display" style="zoom: 80%; width: 100%; font-size: 90%; cursor: pointer;">
            <thead style="background-color: #0d1a80; color: white;">                            
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
        <h3 id="itemCat" class="text-center"></h3>
        <table id="ItemTable" class="table-hover table ItemTable display" style="zoom: 80%; width: 100%; font-size: 90%; cursor: pointer;">
            <thead style="background-color: #0d1a80; color: white;">                            
                <tr>
                    <th>ITEM DESCRIPTION</th>
                    <th>ITEM CODE</th>
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
        <h3 id="itemName" class="text-center"></h3>
        <table id="ItemSerialTable" class="table-hover table ItemSerialTable display" style="zoom: 80%; width: 100%; font-size: 90%; cursor: pointer;">
            <thead style="background-color: #0d1a80; color: white;">                            
                <tr>
                    <th>DATE ADDED</th>
                    <th>DATE MODIFIED</th>
                    <th>RESPONSIBLE USER</th>
                    <th>SERIAL</th>
                    <th>LOCATION</th>
                    <th>RACK NO.</th>
                    <th>ROW NO.</th>
                </tr>
            </thead>
        </table>
    </div>
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
@include('pages.stocks.addStock')
@include('pages.stocks.importStock')
@endsection
