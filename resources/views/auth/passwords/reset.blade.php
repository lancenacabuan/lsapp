@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">CREATE / RESET PASSWORD</div>
                <div class="card-body">
                    <form method="POST" action="/password/reset">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="mb-3">
                            <div class="f-outline">
                                <input id="email" type="email" class="fi forminput form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" placeholder=" " readonly>
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
                                <input id="password" type="password" class="forminput form-control @error('password') is-invalid @enderror" name="password" maxlength="30" required autocomplete="new-password" placeholder=" " autofocus>
                                <label for="password" class="formlabel form-label">{{ __('Password') }}</label>
                            </div>
                            @error('password')
                                <span role="alert" style="zoom: 80%; color: red;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="f-outline">
                                <input id="password-confirm" type="password" class="forminput form-control" name="password_confirmation" maxlength="30" required autocomplete="new-password" placeholder=" ">
                                <label for="password-confirm" class="formlabel form-label">{{ __('Confirm Password') }}</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary bp">
                            {{ __('Create / Reset Password') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection