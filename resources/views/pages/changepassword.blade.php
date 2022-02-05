@extends('layouts.app')
@section('content')
<center>
<div class="container">
<div class="card" style="width: 700px;">   
    <div class="card-body" style="background-color:white;color:black;">
    <h2>Change Password</h2>
    <hr>
        <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
        <div class="input-group mb-2">
            <div class="input-group-prepend">
                <label class="input-group-text" style="width: 150px;">Current Password</label>
            </div>
            <input type="password" id="pass1" style="width: 500px;" autofocus>
        </div>
        <div class="input-group mb-2">
            <div class="input-group-prepend">
                <label class="input-group-text" style="width: 150px;">New Password</label>
            </div>
            <input type="password" id="pass2" style="width: 500px;">
        </div>
        <div class="input-group mb-2">
            <div class="input-group-prepend">
                <label class="input-group-text" style="width: 150px;">Confirm Password</label>
            </div>
            <input type="password" id="pass3" style="width: 500px;">
        </div>
        <br/>
        <center>
            <button type="submit" id="savepassword" class="btn btn-primary float-right bp" style="margin-right: 18px;">
            UPDATE</button>
        </center>
    </div>
</div>
</div>
<center>
@endsection
