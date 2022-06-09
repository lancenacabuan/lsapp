@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <button class="btn btn-primary bp float-right" type="button" id="btnAddUser">ADD USER</button>
    <br><br>
    <table id="userTable" class="table userTable table-hover display" style="cursor: pointer; width: 100%;">
        <thead style="background-color: #0d1a80; color: white;">
            <tr>
                <td colspan="5">
                    <a href="/users">
                        <div class="text-center" style="background-color: #0d1a80; color: white; font-size: 20px; font-weight: bold; height: 30px; line-height: 30px;">
                            USER ACCOUNTS
                        </div>
                    </a>
                </td>
            </tr>
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
                <td>
                    <input type="text" class="form-control filter-input fl-4" data-column="4" style="border:1px solid #808080"/>
                </td>
            </tr>
            <tr>
                <th>FULLNAME</th>
                <th>EMAIL</th>
                <th>COMPANY</th>
                <th>USER LEVEL</th>
                <th>STATUS</th>
            </tr>
        </thead>
    </table>
</div>
@include('pages.users.addUser')
@include('pages.users.updateUser')
@include('pages.include')
@endsection