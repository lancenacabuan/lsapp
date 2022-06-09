@extends('layouts.app')
@section('content')
<div class="container-fluid">
    @role('assembler') {{---ROLES---}}
    <button class="btn btn-primary bp btnNewAssembly float-right mb-2" type="button">NEW ASSEMBLY</button>
    <br><br>
    @endrole
    <table id="assemblyTable" class="table assemblyTable table-hover display" style="width: 100%; zoom: 80%; cursor: pointer;">
        <thead style="background-color: #0d1a80; color: white;"> 
            <tr>
                <td colspan="7">
                    <a href="/assembly">
                        <div class="text-center" style="background-color: #0d1a80; color: white; font-size: 25px; font-weight: bold; height: 43px; line-height: 43px;">
                            ASSEMBLY STOCK REQUESTS
                        </div>
                    </a>
                </td>
            </tr>
            <tr>
                <th style="width: 150px;">DATE REQUESTED</th>
                <th style="width: 150px;">DATE NEEDED</th>
                <th style="width: 150px;">REQUEST NUMBER</th>
                <th style="width: 150px;">REQUEST TYPE</th>
                <th>ITEM DESCRIPTION</th>
                <th style="width: 150px;">QUANTITY</th>
                <th style="width: 200px;">STATUS</th>
            </tr>
        </thead>
    </table>
</div>
@include('pages.assembly.newAssembly')
@include('pages.assembly.detailsAssembly')
@include('pages.include')
@endsection
