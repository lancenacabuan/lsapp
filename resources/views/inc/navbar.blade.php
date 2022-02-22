<nav class="navbar navbar-expand-md shadow-sm" style="height: 60px; margin-top: -10px;">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-between align-items-center w-100" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            @if(!auth()->user()->hasanyRole('sales') && !auth()->user()->hasanyRole('approver - sales') && !auth()->user()->hasanyRole('approver - warehouse'))
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
                {{-- <li class="nav-item">
                    <a class="nav-link n {{ Request::is('joborder') ? 'active' : '' }}" href="{{ url('/joborder') }}">JOB ORDER</a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link n {{ Request::is('assembly') ? 'active' : '' }}" href="{{ url('/assembly') }}">ASSEMBLY</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link n {{ Request::is('pullout') ? 'active' : '' }}" href="{{ url('/pullout') }}">PULLOUT</a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link n {{ Request::is('filemaintenance*') ? 'active' : '' }}" href="{{ url('/filemaintenance') }}">FILE MAINTENANCE</a>
                </li>
                @role('admin')
                <li class="nav-item">
                    <a class="nav-link n {{ Request::is('users') ? 'active' : '' }}" href="{{ url('/users') }}">USERS</a>
                </li>
                @endrole
            </ul>
            @endif
            @if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('approver - sales'))
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                  <a class="nav-link n {{ Request::is('stockrequest') ? 'active' : '' }}"  href="{{ url('/stockrequest') }}">HOME</a>
                </li>     
            </ul>
            @endif
            @if(auth()->user()->hasanyRole('approver - warehouse'))
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                  <a class="nav-link n {{ Request::is('stocktransfer') ? 'active' : '' }}"  href="{{ url('/stocktransfer') }}">HOME</a>
                </li>     
            </ul>
            @endif
            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav mr-right">
                <a id="impScale" class="nav-link" href="{{ route('logout') }}" style="color:white; font-size:16px; margin-right: -100px;"
                    onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                    <b>LOGOUT</b>&nbsp;&nbsp;<i class="fa fa-sign-out pr-5" aria-hidden="true"></i>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </ul>
        </div>
    </div>
</nav>