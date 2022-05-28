@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="card mx-auto" style="width: 500px;">   
        <div class="card-body" style="background-color: white; color: black; text-align: center;">
        <h2>Change Password</h2>
        <hr>
            <form id="form_changepassword">
            <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 150px;">Current Password</label>
                </div>
                <input type="password" id="pass1" style="width: 316px;" minlength="8" maxlength="30" required autofocus>
            </div>
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 150px;">New Password</label>
                </div>
                <input type="password" id="pass2" style="width: 316px;" minlength="8" maxlength="30" required>
            </div>
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 150px;">Confirm Password</label>
                </div>
                <input type="password" id="pass3" style="width: 316px;" minlength="8" maxlength="30" required>
            </div>
            </form>
            <br>
            <button type="submit" id="btnChangePassword" class="btn btn-primary float-right bp">UPDATE</button>
        </div>
    </div>
</div>
@include('pages.report')
@endsection
