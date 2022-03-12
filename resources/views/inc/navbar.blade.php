<nav class="navbar navbar-expand-md shadow-sm" style="height: 60px; margin-top: -20px;">
    <div class="container-fluid">
        <div class="collapse navbar-collapse justify-content-between align-items-center w-100" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            @if(!auth()->user()->hasanyRole('sales') && !auth()->user()->hasanyRole('approver - sales') && !auth()->user()->hasanyRole('approver - warehouse')) {{---ROLES---}}
            <ul class="navbar-nav mr-auto">
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
                <li class="nav-item">
                    <a class="nav-link n {{ Request::is('assembly') ? 'active' : '' }}" href="{{ url('/assembly') }}">ASSEMBLY</a>
                </li>
                @role('admin') {{---ROLES---}}
                <li class="nav-item">
                    <a class="nav-link n {{ Request::is('maintenance*') ? 'active' : '' }}" href="{{ url('/maintenance') }}">MAINTENANCE</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link n {{ Request::is('users') ? 'active' : '' }}" href="{{ url('/users') }}">USERS</a>
                </li>
                @endrole
            </ul>
            @endif
            @if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('approver - sales')) {{---ROLES---}}
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                  <a class="nav-link n {{ Request::is('stockrequest') ? 'active' : '' }}"  href="{{ url('/stockrequest') }}">HOME</a>
                </li>
            </ul>
            @endif
            @if(auth()->user()->hasanyRole('approver - warehouse')) {{---ROLES---}}
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                  <a class="nav-link n {{ Request::is('stocktransfer') ? 'active' : '' }}"  href="{{ url('/stocktransfer') }}">HOME</a>
                </li>
            </ul>
            @endif
            <!-- Right Side Of Navbar -->
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