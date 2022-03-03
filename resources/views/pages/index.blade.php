@extends('layouts.app')
@section('content')
<script src="{{ asset('js/home.js') }}"></script>
    <div class="container pt-3">
        <div class="container-fluid">
        <center>
            <div class="row">
                <div class="col-sm-4" style="margin-left: 100px; margin-right: -100px;">
                    <a href="stocks" style="text-decoration:none">
                        <div class="container" style="background-color: #0d1a80; color: white; border-radius: 100%; height: 150px; width: 150px; font-size: 30px; text-align: center; align-items: center; justify-content: center; vertical-align: center;">
                            <br><center>{{DB::table('stocks')->whereIn('stocks.location_id',['1','2','3','4','5','6'])->get()->count();}}</center>
                            <i class="fa fa-cart-arrow-down" style="font-size: 50px"></i>
                        </div>
                        <br>
                        <strong style="color: #0d1a80; font-size: 20px;">STOCKS</strong>
                    </a>
                </div>
                <div class="col-sm-4">
                    <a href="stockrequest" style="text-decoration:none">
                        <div class="container" style="background-color: #0d1a80; color: white; border-radius: 100%; height: 150px; width: 150px; font-size: 30px; text-align: center; align-items: center; justify-content: center;">
                            <br><center>{{DB::table('requests')->whereNotIn('requests.status',['7','8'])->get()->count();}}</center>
                            <i class="fa fa-truck" style="font-size: 50px"></i>
                        </div>
                        <br>
                        <strong style="color: #0d1a80; font-size: 20px;">STOCK REQUEST</strong>
                    </a>
                </div>
                <div class="col-sm-4" style="margin-left: -100px;">
                    <a href="stocktransfer" style="text-decoration:none">
                        <div class="container" style="background-color: #0d1a80; color: white; border-radius: 100%; height: 150px; width: 150px; font-size: 30px; text-align: center; align-items: center; justify-content: center;">
                            <br><center>{{DB::table('request_transfer')->whereNotIn('request_transfer.status',['7','8'])->get()->count();}}</center>
                            <i class="fa fa-exchange" style="font-size: 50px"></i>
                        </div>
                        <br>
                        <strong style="color: #0d1a80; font-size: 20px;">STOCK TRANSFER</strong>
                    </a>
                </div>
            </div>
        </center>
        </div>
    </div>
    <br>
    <div class="container-fluid">
        <div class="panel-body table-responsive "><br>
            <table id="user_logs" class="table user_logs display nowrap" style="width:100%;font-size:12px;">
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
