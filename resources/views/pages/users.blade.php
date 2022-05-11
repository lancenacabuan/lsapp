@extends('layouts.app')
@section('content')
<div class="container-fluid">   
    <button class="btn btn-primary bp float-right mb-2" type="button" id="btnAddUser">ADD USER</button>
    <br>
    <h3 class="text-center"><strong>USER ACCOUNTS</strong></h3>        
    <table id="userTable" class="table userTable table-hover display" style="cursor: pointer; width: 100%; font-size: 14px;">
        <thead style="background-color: #0d1a80; color: white; font-size: 15px;">
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
@include('pages.users.addUser')
@include('pages.users.updateUser')
@endsection