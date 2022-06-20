<nav class="navbar navbar-expand-md shadow-sm" style="height: 60px;">
    <div class="container-fluid">
        <div class="collapse navbar-collapse justify-content-between align-items-center w-100" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                <a class="nav-link n {{ Request::is('/') ? 'active' : '' }}"  href="{{ url('/') }}">HOME</a>
                </li>
                @role('admin|encoder|viewer') {{---ROLES---}}
                <li class="nav-item">
                    <a class="nav-link n {{ Request::is('stocks') ? 'active' : '' }}" href="{{ url('/stocks') }}">STOCKS</a>
                </li>
                @endrole
                @role('admin|encoder|viewer|sales|approver - sales|accounting') {{---ROLES---}}
                <li class="nav-item">
                    <a class="nav-link n {{ Request::is('stockrequest') ? 'active' : '' }}" href="{{ url('/stockrequest') }}">STOCK REQUEST</a>
                </li>
                @endrole
                @role('admin|encoder|viewer|approver - warehouse') {{---ROLES---}}
                <li class="nav-item">
                    <a class="nav-link n {{ Request::is('stocktransfer') ? 'active' : '' }}" href="{{ url('/stocktransfer') }}">STOCK TRANSFER</a>
                </li>
                @endrole
                @role('admin|encoder|viewer')
                <li class="nav-item">
                    <a class="nav-link n {{ Request::is('defective') ? 'active' : '' }}" href="{{ url('/defective') }}">DEFECTIVE</a>
                </li>
                @endrole
                @role('merchant')
                <li class="nav-item">
                    <a class="nav-link n {{ Request::is('merchant') ? 'active' : '' }}" href="{{ url('/merchant') }}">MERCHANT</a>
                </li>
                @endrole
                @role('assembler')
                <li class="nav-item">
                    <a class="nav-link n {{ Request::is('assembly') ? 'active' : '' }}" href="{{ url('/assembly') }}">ASSEMBLY</a>
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
            </ul>
            <button id="btnReport" type="button" class="btn btn-danger mr-2">
                <span class="px-2">REPORT A PROBLEM</span>
            </button>
            <ul class="navbar-nav mr-right">
                <a class="nav-link out" style="color: white; font-size: 16px; cursor: pointer;" onclick="$('#logout-form').submit();">
                    <b>LOGOUT</b>&nbsp;&nbsp;<i class="fa fa-sign-out" aria-hidden="true"></i>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </ul>
        </div>
    </div>
</nav>