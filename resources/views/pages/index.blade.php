@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <center>
        <div class="row">
            <div class="col-sm-4" style="margin-left: 100px; margin-right: -100px;">
                <a href="stocks" style="text-decoration:none">
                    <img style="height: 100px;" src="{{ asset('stocks.png') }}">
                    <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 48px; height: 48px; width: 150px; font-size: 26px; border-radius: 30px;">
                        <center>{{DB::table('stocks')->whereIn('stocks.location_id',['1','2','3','4','5','6'])->get()->count();}}</center>   
                    </div>
                    <strong style="color: #0d1a80; font-size: 20px;">STOCKS</strong>
                </a>
            </div>
            <div class="col-sm-4">
                <a href="stockrequest" style="text-decoration:none">
                    <img style="height: 100px;" src="{{ asset('stockrequest.png') }}">
                    <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 48px; height: 48px; width: 150px; font-size: 26px; border-radius: 30px;">
                        <center>{{DB::table('requests')->whereNotIn('requests.status',['7','8'])->get()->count();}}</center>
                    </div>
                    <strong style="color: #0d1a80; font-size: 20px;">STOCK REQUEST</strong>
                </a>
            </div>
            <div class="col-sm-4" style="margin-left: -100px;">
                <a href="stocktransfer" style="text-decoration:none">
                    <img style="height: 100px;" src="{{ asset('stocktransfer.png') }}">
                    <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 48px; height: 48px; width: 150px; font-size: 26px; border-radius: 30px;">
                        <center>{{DB::table('request_transfer')->whereNotIn('request_transfer.status',['7','8'])->get()->count();}}</center>
                    </div>
                    <strong style="color: #0d1a80; font-size: 20px;">STOCK TRANSFER</strong>
                </a>
            </div>
        </div>
    </center>
    <br>
    <div class="panel-body table-responsive" style="width: 100%">
        <table id="user_logs" class="table user_logs display nowrap" style="width: 100%; font-size: 12px;">
            <thead style="background-color: #0d1a80 !important; color: white !important; font-weight: bold !important; font-size: 15px;">
                <tr>
                    <th>DATE</th>
                    <th>NAME</th>
                    <th>USER LEVEL</th>
                    <th>ACTIVITY</th>
                </tr>
            </thead>                 
        </table>
    </div>
</div>
@endsection
