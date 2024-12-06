@extends('templates.auth')
@section('content')
@php
    $email = '';
    $pass = '';
    if(isset($_GET['email'])) {
        $email = $_GET['email'];
        $pass = base64_decode($_GET['pass']);
    }
@endphp

<div class="card mb-0">
    <div class="card-body">
        <div class="text-center">
            <img src="{{ asset('assets/images/logo_mbu_primary.png') }}" alt="" srcset="" width="100"><br>
        </div>
        <a href="javascript:void(0);" class="brand-logo">
            <h2 class="brand-text text-primary ml-1">MBU - LTI - MANBU</h2>
        </a>
        @include('templates.message-validation')
        <form class="auth-login-form mt-2" action="{{ route('auth.login') }}" id="loginForm" method="POST">
            @csrf
            <!-- {{ csrf_field() }} -->
            <div class="form-group">
                <label for="login-email" class="form-label">Email</label>
                <input type="text" class="{{ $errors->has('email')?'is-invalid':'' }} form-control" id="email" name="email" placeholder="john@example.com" aria-describedby="login-email" tabindex="1" autofocus />
                @if ($errors->has('email'))
                    <span class="text-danger small">{{ $errors->first('email') }}</span>
                @endif
            </div>

            <div class="form-group">
                <div class="d-flex justify-content-between">
                    <label for="login-password">Password</label>
                    <a href="{{ route('auth.forgot') }}">
                        <small>Lupa Password?</small>
                    </a>
                </div>
                <div class="input-group input-group-merge form-password-toggle">
                    <input type="password" class="{{ $errors->has('password')?'is-invalid':'' }} form-control form-control-merge" id="password" name="password" tabindex="2" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="login-password" />
                    <div class="input-group-append">
                        <span class="input-group-text cursor-pointer" style="{{ $errors->has('password')?'border-color:red;':'' }}"><i data-feather="eye"></i></span>
                    </div>
                </div>
                @if ($errors->has('password'))
                    <span class="text-danger small">{{ $errors->first('password') }}</span>
                @endif
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input class="custom-control-input" type="checkbox" id="remember" name="remember" tabindex="3" {{ old('remember') ? 'checked' : '' }} />
                    <label class="custom-control-label" for="remember"> Ingatkan saya </label>
                </div>
            </div>
            <button class="btn btn-primary btn-block" tabindex="4">Masuk</button>
        </form>
    </div>
</div>

<script>
    function visible() {
        var x = document.getElementById("password");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>

<script>
    $(document).ready(function() {
        var url = window.location.href;
        if (localStorage.getItem('site') === url) {
            if (localStorage.getItem('email')) {
                $('#email').val(localStorage.getItem('email'));
            }

            if (localStorage.getItem('password')) {
                $('#password').val(localStorage.getItem('password'));
            }

            if (localStorage.getItem('remember') === 'true') {
                $('#remember').prop('checked', true);
            }
        }

        $('#loginForm').on('submit', function() {
            if ($('#remember').is(':checked')) {
                localStorage.setItem('site', url);
                localStorage.setItem('email', $('#email').val());
                localStorage.setItem('password', $('#password').val());
                localStorage.setItem('remember', 'true');
            } else {
                localStorage.removeItem('site');
                localStorage.removeItem('email');
                localStorage.removeItem('password');
                localStorage.removeItem('remember');
            }
        });
    });
    </script>

@endsection
