@extends('layouts.app')
@section('content')
@role('assembler') {{---ROLES---}}
<button class="btn btn-primary bp btnNewAssembly float-right mb-2" type="button">NEW ASSEMBLY</button>
<br><br>
@endrole
<table id="assemblyTable" class="table assemblyTable table-hover display" style="width: 100%; zoom: 80%; cursor: pointer;">
    <thead style="background-color: #0d1a80; color: white;"> 
        <tr>
            <td colspan="9">
                <a href="/assembly" title="Reload">
                    <div class="text-center" style="background-color: #0d1a80; color: white; font-size: 25px; font-weight: bold; height: 43px; line-height: 43px;">
                        ASSEMBLY STOCK REQUESTS
                    </div>
                </a>
            </td>
        </tr>
        <tr>
            <th class="d-none">DATE REQUESTED</th>
            <th class="d-none">DATE NEEDED</th>
            <th style="width: 14%;">DATE REQUESTED</th>
            <th style="width: 14%;">DATE NEEDED</th>
            <th style="width: 12%;">REQUEST NUMBER</th>
            <th style="width: 10%;">REQUEST TYPE</th>
            <th>ITEM DESCRIPTION</th>
            <th style="width: 8%;">QUANTITY</th>
            <th style="width: 18%;">STATUS</th>
        </tr>
    </thead>
</table>
@include('pages.assembly.newAssembly')
@include('pages.assembly.detailsAssembly')
@include('pages.include')
@endsection
