@extends('layouts.app')
@section('content')
<div class="container-fluid">
    @role('admin|encoder') {{---ROLES---}}
    <button class="btn btn-primary bp" id="backBtn" type="button" style="display: none;">BACK</button>
    <button class="btn btn-primary bp" id="btnBack" type="button" style="display: none;">BACK</button>
    <button class="btn btn-primary bp float-right mb-4" type="button" data-target="#addStock" data-toggle="modal" data-backdrop="static" data-keyboard="false">ADD STOCK</button>
    {{-- <button class="btn btn-primary bp" type="button" data-target="#stocktrans" data-toggle="modal" data-backdrop="static" data-keyboard="false">STOCK TRANSFER</button> --}}
    {{-- <button class="btn btn-primary bp" type="button">IMPORT</button> --}}
    <br><br>
    @endrole
</div>
<div class="container-fluid">
    <div id="CategoryTableDiv">
        <table id="CategoryTable" class="table-hover table CategoryTable display" style="width: 100%; font-size: 90%; cursor: pointer;">
            <thead style="background-color: #0d1a80 !important; color: white !important;">                            
                <tr>
                    <th>CATEGORY</th>
                    <th>A1</th>
                    <th>A2</th>
                    <th>A3</th>
                    <th>A4</th>
                    <th>BALINTAWAK</th> 
                    <th>MALABON</th>
                    <th>TOTAL STOCKS</th>
                </tr>
            </thead>
            <tbody>
            </tbody>      
        </table>
    </div>
    <div id="ItemTableDiv" style="display:none">
        <center><h3 id="itemCat"></h3></center>
        <table id="ItemTable" class="table-hover table ItemTable display" style="width: 100%; font-size: 90%; cursor: pointer;">
            <thead style="background-color: #0d1a80 !important; color: white !important;">                            
                <tr>
                    <th>ITEM DESCRIPTION</th>
                    <th>A1</th>
                    <th>A2</th>
                    <th>A3</th>
                    <th>A4</th>
                    <th>BALINTAWAK</th> 
                    <th>MALABON</th>
                    <th>TOTAL STOCKS</th>
                </tr>
            </thead>
            <tbody>
            </tbody>      
        </table>
    </div>
    <div id="ItemSerialTableDiv" style="display:none">
        <center><h3 id="itemName"></h3></center>
        <table id="ItemSerialTable" class="table-hover table ItemSerialTable display" style="width: 100%; font-size: 90%; cursor: pointer;">
            <thead style="background-color: #0d1a80 !important; color: white !important;">                            
                <tr>
                    <th>ITEM DESCRIPTION</th>
                    <th>SERIAL</th>
                    <th>LOCATION</th>
                    <th>RACK NO.</th>
                    <th>ROW NO.</th>
                </tr>
            </thead>
            <tbody>
            </tbody>      
        </table>
    </div>
</div>
@include('modal.addstock')
@include('modal.stock')
@include('modal.stocktransfer')
@endsection
