@role('admin') {{---ROLES---}}
    @php
        $reports = DB::table('reports')->whereIn('status',['pending'])->get()->count();
    @endphp
@endrole
<nav class="navbar navbar-expand-md shadow-sm" style="height: 60px;">
    <div class="container-fluid">
        <div class="collapse navbar-collapse justify-content-between align-items-center w-100" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                @if(!auth()->user()->hasanyRole('sales') && !auth()->user()->hasanyRole('approver - sales') && !auth()->user()->hasanyRole('accounting') && !auth()->user()->hasanyRole('approver - warehouse') && !auth()->user()->hasanyRole('assembler')) {{---ROLES---}}
                    <li class="nav-item">
                    <a class="nav-link n {{ Request::is('/') ? 'active' : '' }}"  href="{{ url('/') }}">HOME</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link n {{ Request::is('stocks') ? 'active' : '' }}" href="{{ url('/stocks') }}">STOCKS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link n {{ Request::is('stockrequest') ? 'active' : '' }}" href="{{ url('/stockrequest') }}">STOCK REQUEST</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link n {{ Request::is('stocktransfer') ? 'active' : '' }}" href="{{ url('/stocktransfer') }}">STOCK TRANSFER</a>
                    </li>
                    @role('admin|encoder|viewer') {{---ROLES---}}
                    <li class="nav-item">
                        <a class="nav-link n {{ Request::is('defective') ? 'active' : '' }}" href="{{ url('/defective') }}">DEFECTIVE</a>
                    </li>
                    @endrole
                    @role('admin') {{---ROLES---}}
                    <li class="nav-item">
                        <a class="nav-link n {{ Request::is('maintenance*') ? 'active' : '' }}" href="{{ url('/maintenance') }}">MAINTENANCE</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link n {{ Request::is('users') ? 'active' : '' }}" href="{{ url('/users') }}">USERS</a>
                    </li>
                    @endrole
                @endif
            </ul>
            <button id="btnReport" type="button" class="btn btn-danger mr-2">
                <span class="px-2">REPORT A PROBLEM</span>
                @role('admin') {{---ROLES---}}
                    @if($reports > 0)
                        <span id="labelReport" class="badge rounded-pill bg-danger px-2">{{ $reports }}</span>
                    @endif
                @endrole
            </button>
            <ul class="navbar-nav mr-right">
                <a class="nav-link" style="color: white; font-size: 16px; cursor: pointer;" onclick="$('#logout-form').submit();">
                    <b>LOGOUT</b>&nbsp;&nbsp;<i class="fa fa-sign-out" aria-hidden="true"></i>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </ul>
        </div>
    </div>
</nav>