@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <button class="btn btn-primary bp float-right" type="button" id="btnAddUser">ADD USER</button>
    <br><br><br><br>
    <div class="text-center" style="background-color: #0d1a80; color: white; font-size: 20px; font-weight: bold; height: 45px; line-height: 45px;">USER ACCOUNTS</div>
    <table id="userTable" class="table userTable table-hover display" style="cursor: pointer; width: 100%;">
        <thead style="background-color: #0d1a80; color: white;">
            <tr>
                <th>ID</th>
                <th>FULLNAME</th>
                <th>EMAIL</th>
                <th>USER LEVEL</th>
                <th>STATUS</th>
            </tr>
        </thead>
    </table>
</div>
<style>
    #userTable_length, #userTable_filter{
        margin-top: -90px;
    }
</style>
@include('pages.users.addUser')
@include('pages.users.updateUser')
@include('pages.report')
@endsection