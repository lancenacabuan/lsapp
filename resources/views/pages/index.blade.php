@extends('layouts.app')
@section('content')
@php
$stocks = DB::table('stocks')->whereIn('status', ['in','defectives','FOR RECEIVING','demo','assembly'])->get()->count();
$belowmin = count($list);
$stockrequest = DB::table('requests')->whereNotIn('requests.status',['7','8','10','11','14','19'])->get()->count();
$stocktransfer = DB::table('request_transfer')->whereNotIn('request_transfer.status',['7','8'])->get()->count();
$defective = DB::table('stocks')->whereIn('status', ['defectives'])->get()->count();
@endphp
@role('admin|encoder|viewer') {{---ROLES---}}
<div class="row" style="text-align: center; height: 200px;">
    <div class="col-sm-1"></div>
    <div class="col-sm-2">
        <a id="hover1" href="stockrequest" style="text-decoration: none;">
            <img class="zoomout1" style="height: 100px;" src="{{ asset('index-stockrequest.png') }}">
            <img class="zoomin1" style="height: 110px; display: none;" src="{{ asset('index-stockrequest-hover.png') }}">
            <div class="box1 container" style="z-index: 100; background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 48px; height: 48px; width: 150px; text-align: center; font-size: 26px; border-radius: 30px;">
                {{number_format($stockrequest)}}
            </div>
            <strong class="text1" style="color: #0d1a80; font-size: 20px; padding-top: 10px;">STOCK REQUEST</strong>
        </a>
    </div>
    <div class="col-sm-2">
        <a id="hover2" href="stocks" style="text-decoration: none;">
            <img class="zoomout2" style="height: 100px;" src="{{ asset('index-stocks.png') }}">
            <img class="zoomin2" style="height: 110px; display: none;" src="{{ asset('index-stocks-hover.png') }}">
            <div class="box2 container" style="z-index: 100; background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 48px; height: 48px; width: 150px; text-align: center; font-size: 26px; border-radius: 30px;">
                {{number_format($stocks)}}   
            </div>
            <strong class="text2" style="color: #0d1a80; font-size: 20px; padding-top: 10px;">STOCKS</strong>
        </a>
    </div>
    <div class="col-sm-2">
        <a id="hover3" href="stocks?min=below" style="text-decoration: none;">
            <img class="zoomout3" style="height: 100px;" src="{{ asset('index-below-min.png') }}">
            <img class="zoomin3" style="height: 110px; display: none;" src="{{ asset('index-below-min-hover.png') }}">
            <div class="box3 container" style="z-index: 100; background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 48px; height: 48px; width: 150px; text-align: center; font-size: 26px; border-radius: 30px;">
                {{number_format($belowmin)}}   
            </div>
            <strong class="text3" style="color: #0d1a80; font-size: 20px; padding-top: 10px;">BELOW MIN</strong>
        </a>
    </div>
    <div class="col-sm-2">
        <a id="hover4" href="stocktransfer" style="text-decoration: none;">
            <img class="zoomout4" style="height: 100px;" src="{{ asset('index-stocktransfer.png') }}">
            <img class="zoomin4" style="height: 110px; display: none;" src="{{ asset('index-stocktransfer-hover.png') }}">
            <div class="box4 container" style="z-index: 100; background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 48px; height: 48px; width: 150px; text-align: center; font-size: 26px; border-radius: 30px;">
                {{number_format($stocktransfer)}}
            </div>
            <strong class="text4" style="color: #0d1a80; font-size: 20px; padding-top: 10px;">STOCK TRANSFER</strong>
        </a>
    </div>
    <div class="col-sm-2">
        <a id="hover5" href="defective" style="text-decoration: none;">
            <img class="zoomout5" style="height: 100px;" src="{{ asset('index-defective.png') }}">
            <img class="zoomin5" style="height: 110px; display: none;" src="{{ asset('index-defective-hover.png') }}">
            <div class="box5 container" style="z-index: 100; background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 48px; height: 48px; width: 150px; text-align: center; font-size: 26px; border-radius: 30px;">
                {{number_format($defective)}}
            </div>
            <strong class="text5" style="color: #0d1a80; font-size: 20px; padding-top: 10px;">DEFECTIVE</strong>
        </a>
    </div>
    <div class="col-sm-1"></div>
</div>
@endrole
<table id="user_logs" class="table user_logs display" style="width: 100%;">
    <thead style="background-color: #0d1a80; color: white;">
        <tr>
            <td colspan="5">
                @role('admin|encoder|viewer') {{---ROLES---}}
                <a href="/">
                @endrole
                @role('sales|approver - sales|accounting|merchant|assembler|approver - warehouse') {{---ROLES---}}
                <a href="/logs">
                @endrole
                    <div class="text-center" style="background-color: #0d1a80; color: white; font-size: 20px; font-weight: bold; height: 30px; line-height: 30px;">
                        USER ACTIVITY LOGS
                    </div>
                </a>
            </td>
        </tr>
        <tr class="tbsearch">
            <td class="d-none">
                <input type="search" class="form-control filter-input" data-column="0" style="border:1px solid #808080"/>
            </td>
            <td>
                <input type="search" class="form-control filter-input" data-column="0" style="border:1px solid #808080"/>
            </td>
            <td>
                <input type="search" class="form-control filter-input" data-column="2" style="border:1px solid #808080"/>
            </td>
            <td>
                <input type="search" class="form-control filter-input" data-column="3" style="border:1px solid #808080"/>
            </td>
            <td>
                <input type="search" class="form-control filter-input" data-column="4" style="border:1px solid #808080"/>
            </td>
        </tr>
        <tr>
            <th class="d-none">DATE & TIME</th>
            <th style="width: 15%;">DATE & TIME</th>
            <th style="width: 17%;">FULLNAME</th>
            <th style="width: 15%;">USER LEVEL</th>
            <th>ACTIVITY</th>
        </tr>
    </thead>
</table>
@include('pages.include')
@endsection
