@extends('layouts.app')
@section('content')
@php
$stocks = DB::table('stocks')->whereIn('status', ['in','defectives','FOR RECEIVING','demo','assembly'])->get()->count();
$stockrequest = DB::table('requests')->whereNotIn('requests.status',['7','8','10','11','14','19'])->get()->count();
$stocktransfer = DB::table('request_transfer')->whereNotIn('request_transfer.status',['7','8'])->get()->count();
$defective = DB::table('stocks')->whereIn('status', ['defectives'])->get()->count();
@endphp
<div class="container-fluid">
    <div class="row" style="text-align: center; height: 200px;">
        <div class="col-sm-2"></div>
        <div class="col-sm-2">
            <a id="hover1" href="stocks" style="text-decoration: none;">
                <img class="zoom1" style="height: 100px;" src="{{ asset('index-stocks.png') }}">
                <div class="box1 container" style="z-index: 100; background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 48px; height: 48px; width: 150px; text-align: center; font-size: 26px; border-radius: 30px;">
                    {{number_format($stocks)}}   
                </div>
                <strong class="text1" style="color: #0d1a80; font-size: 20px;">STOCKS</strong>
            </a>
        </div>
        <div class="col-sm-2">
            <a id="hover2" href="stockrequest" style="text-decoration: none;">
                <img class="zoom2" style="height: 100px;" src="{{ asset('index-stockrequest.png') }}">
                <div class="box2 container" style="z-index: 100; background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 48px; height: 48px; width: 150px; text-align: center; font-size: 26px; border-radius: 30px;">
                    {{number_format($stockrequest)}}
                </div>
                <strong class="text2" style="color: #0d1a80; font-size: 20px;">STOCK REQUEST</strong>
            </a>
        </div>
        <div class="col-sm-2">
            <a id="hover3" href="stocktransfer" style="text-decoration: none;">
                <img class="zoom3" style="height: 100px;" src="{{ asset('index-stocktransfer.png') }}">
                <div class="box3 container" style="z-index: 100; background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 48px; height: 48px; width: 150px; text-align: center; font-size: 26px; border-radius: 30px;">
                    {{number_format($stocktransfer)}}
                </div>
                <strong class="text3" style="color: #0d1a80; font-size: 20px;">STOCK TRANSFER</strong>
            </a>
        </div>
        <div class="col-sm-2">
            <a id="hover4" href="defective" style="text-decoration: none;">
                <img class="zoom4" style="height: 100px;" src="{{ asset('index-defective.png') }}">
                <div class="box4 container" style="z-index: 100; background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 48px; height: 48px; width: 150px; text-align: center; font-size: 26px; border-radius: 30px;">
                    {{number_format($defective)}}
                </div>
                <strong class="text4" style="color: #0d1a80; font-size: 20px;">DEFECTIVE</strong>
            </a>
        </div>
        <div class="col-sm-2"></div>
    </div>
    <br><br>
    <a href="/">
        <div class="text-center" style="background-color: #0d1a80; color: white; font-size: 20px; font-weight: bold; height: 45px; line-height: 45px;">
            USER ACTIVITY LOGS
        </div>
    </a>
    <table id="user_logs" class="table user_logs display" style="width: 100%; font-size: 12px;">
        <thead style="background-color: #0d1a80; color: white; font-size: 14px;">
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
                <td>
                    <input type="text" class="form-control filter-input fl-3" data-column="3" style="border:1px solid #808080"/>
                </td>
            </tr>
            <tr>
                <th>DATE</th>
                <th>NAME</th>
                <th>USER LEVEL</th>
                <th>ACTIVITY</th>
            </tr>
        </thead>
    </table>
</div>
<style>
    #user_logs_length, #user_logs_filter{
        margin-top: -90px;
    }
</style>
@include('pages.include')
@endsection
