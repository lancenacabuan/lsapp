@extends('layouts.app')
@section('content')
@role('merchant') {{---ROLES---}}
<button class="btn btn-primary bp btnNewMerchRequest float-right mb-2" type="button">NEW MERCHANT STOCK REQUEST</button>
<br><br>
@endrole
<table id="merchantTable" class="table merchantTable table-hover display" style="width: 100%; zoom: 80%; cursor: pointer;">
    <thead style="background-color: #0d1a80; color: white;"> 
        <tr>
            <td colspan="6">
                <a href="/merchant">
                    <div class="text-center" style="background-color: #0d1a80; color: white; font-size: 25px; font-weight: bold; height: 43px; line-height: 43px;">
                        MERCHANT STOCK REQUESTS
                    </div>
                </a>
            </td>
        </tr>
        <tr>
            <th style="width: 16%;">DATE REQUESTED</th>
            <th style="width: 16%;">DATE NEEDED</th>
            <th style="width: 14%">REQUEST NUMBER</th>
            <th>REQUESTED BY</th>
            <th style="width: 14%">ORDER ID</th>
            <th style="width: 18%">STATUS</th>
        </tr>
    </thead>
</table>
@include('pages.merchant.newMerchRequest')
@include('pages.merchant.detailsMerchRequest')
@include('pages.include')
@endsection