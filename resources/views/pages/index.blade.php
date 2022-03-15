@extends('layouts.app')
@section('content')
@php
$stocks = DB::table('stocks')->whereIn('stocks.location_id',['1','2','3','4','5','6','7','9'])->get()->count();
$stockrequest = DB::table('requests')->whereNotIn('requests.status',['7','8','10','11'])->get()->count();
$stocktransfer = DB::table('request_transfer')->whereNotIn('request_transfer.status',['7','8'])->get()->count();
@endphp
<div class="container-fluid">
    <div class="row" style="text-align: center;">
        <div class="col-sm-2"></div>
        <div class="col-sm-2">
            <a href="stocks" style="text-decoration: none;">
                <img style="height: 100px;" src="{{ asset('stocks.png') }}">
                <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 48px; height: 48px; width: 150px; text-align: center; font-size: 26px; border-radius: 30px;">
                    {{number_format($stocks)}}   
                </div>
                <strong style="color: #0d1a80; font-size: 20px;">STOCKS</strong>
            </a>
        </div>
        <div class="col-sm-1"></div>
        <div class="col-sm-2">
            <a href="stockrequest" style="text-decoration: none;">
                <img style="height: 100px;" src="{{ asset('stockrequest.png') }}">
                <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 48px; height: 48px; width: 150px; text-align: center; font-size: 26px; border-radius: 30px;">
                    {{number_format($stockrequest)}}
                </div>
                <strong style="color: #0d1a80; font-size: 20px;">STOCK REQUEST</strong>
            </a>
        </div>
        <div class="col-sm-1"></div>
        <div class="col-sm-2">
            <a href="stocktransfer" style="text-decoration: none;">
                <img style="height: 100px;" src="{{ asset('stocktransfer.png') }}">
                <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 48px; height: 48px; width: 150px; text-align: center; font-size: 26px; border-radius: 30px;">
                    {{number_format($stocktransfer)}}
                </div>
                <strong style="color: #0d1a80; font-size: 20px;">STOCK TRANSFER</strong>
            </a>
        </div>
        <div class="col-sm-2"></div>
    </div>
    <br>
    <table id="user_logs" class="table user_logs display nowrap" style="width: 100%; font-size: 12px;">
        <thead style="background-color: #0d1a80; color: white; font-size: 14px;">
            <tr>
                <th>DATE</th>
                <th>NAME</th>
                <th>USER LEVEL</th>
                <th>ACTIVITY</th>
            </tr>
        </thead>
    </table>
</div>
@endsection
