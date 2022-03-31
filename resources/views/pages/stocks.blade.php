@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <button class="btn btn-primary bp" id="backBtn" type="button" style="display: none;">BACK</button>
    <button class="btn btn-primary bp" id="btnBack" type="button" style="display: none;">BACK</button>
    @role('admin|encoder') {{---ROLES---}}
    <button class="btn btn-primary bp float-right mb-4" type="button" data-target="#addStock" data-toggle="modal" data-backdrop="static" data-keyboard="false">ADD STOCK</button>
    {{-- <button class="btn btn-primary bp" type="button" data-target="#stocktrans" data-toggle="modal" data-backdrop="static" data-keyboard="false">STOCK TRANSFER</button> --}}
    {{-- <button class="btn btn-primary bp" type="button">IMPORT</button> --}}
    <br><br>
    @endrole
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
                    <th>ITEM DESCRIPTION</th>
                    <th>SERIAL</th>
                    <th>LOCATION</th>
                    <th>RACK NO.</th>
                    <th>ROW NO.</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@include('modal.addstock')
{{-- @include('modal.stock') --}}
{{-- @include('modal.stocktransfer') --}}
@endsection
