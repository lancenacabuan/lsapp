@extends('layouts.app')
@section('content')
<div class="container-fluid">   
    <button class="btn btn-primary bp" type="button" id="btnAddUser">ADD USER</button>
</div>
<div class="panel-body table-responsive "><br>
    <h3 class="text-center">USER ACCOUNTS</h3>        
    <div class="container-fluid">
        <table id="userTable" class="table userTable table-hover display" style="cursor:pointer; width:100%; border:0px; font-size:14px;">
            <thead style="background-color: #0d1a80 !important; color: white !important; font-weight: bold !important; font-size: 15px;">
                <tr>
                    <th>ID</th>
                    <th>FULLNAME</th>
                    <th>EMAIL</th>
                    <th>USER LEVEL</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@include('modal.adduser')
@include('modal.updateuser')
@endsection