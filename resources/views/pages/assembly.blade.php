@extends('layouts.app')
@section('content')
<div class="table-responsive">
    <button class="btn btn-primary ml-auto" type="btn" data-toggle="modal" style="font-weight:bold;">NEW ASSEMBLY</button>&nbsp;
    <br><br>
    <table class="table-hover table assemblyTable" id="assemblyTable" style="width:100%;font-size:80%">
        <thead class="thead-dark">
            <tr>
                <th>
                    DATE REQUESTED
                </th>
                <th>
                    REQUESTED BY
                </th>
                <th>
                    ITEM DESCRIPTION
                </th>
                <th>
                    STATUS
                </th>
            </tr>
        </thead>
    </table>
</div>      
@endsection
