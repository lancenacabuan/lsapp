@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card mt-3">
                <div style="height: 28px;" class="card-header">
                    <i class="fa fa-user-circle fa-4x rounded-circle" style="padding: 1px; margin-top: -30px; background-color: ghostwhite; color: #0d1a80;" aria-hidden="true"></i>
                </div>
                <div class="card-body">
                    <form method="POST" action="/login">
                        @csrf
                        <div class="mb-3">
                            <div class="f-outline">
                                <input id="email" type="search" class="forminput form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder=" " autofocus>
                                <label for="email" class="formlabel form-label">{{ __('E-Mail Address') }}</label>
                            </div>
                            @error('email')
                                <span role="alert" style="zoom: 80%; color: red;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="f-outline">
                                <input id="password" type="password" class="forminput form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder=" ">
                                <label for="password" class="formlabel form-label">{{ __('Password') }}</label>
                            </div>
                            @error('password')
                                <span role="alert" style="zoom: 80%; color: red;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check" style="zoom: 90%;">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-12">
                                <button id="btnLogin" type="submit" class="btn btn-primary btnLogin bp">
                                    {{ __('LOGIN') }}<i class="fa fa-sign-in ml-2" aria-hidden="true"></i>
                                </button>
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link ml-2" href="{{ route('password.request') }}" style="font-weight: bold;">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
    if($(location).attr('pathname')+window.location.search == '/login?user=inactive'){
        Swal.fire("INACTIVE ACCOUNT", "Your account is currently inactive. Please contact the administrator to resolve the issue.", "warning");
        return false;
    }
});
</script>
@endsection