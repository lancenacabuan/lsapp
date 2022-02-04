@extends('layouts.app')
@section('content')
<div class="table-responsive">
    <table class="table-hover table itemTable" id="itemTable" style="width:100%;font-size:80%">
        <thead class="thead-dark">
            <tr class="tbsearch">
                <td>
                    <input type="text" class="form-control filter-input fl-0" data-column="0" style="border:1px solid #808080"/>
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-1" data-column="1" style="border:1px solid #808080"/>
                </td>
            </tr>
            <tr>
                <th>
                    Category
                </th>
                <th>
                    Item Description
                </th>
                {{-- @if(auth()->user()->hasanyrole('Manager', 'Editor'))
                    <th title="Indicate if the item is required to have a serial number. Click Yes or No.">
                        Requires serial no.
                    </th>
                @endif --}}
            </tr>
        </thead>
    </table>
</div>
@endsection
