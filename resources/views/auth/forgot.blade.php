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
                                    <img src="{{asset('assets/images/logo_mbu_primary.png')}}" alt="" srcset="" width="100"><br>
                                </div>
                                <a href="javascript:void(0);" class="brand-logo">
                                    <h2 class="brand-text text-primary ml-1">MBU - ERP</h2>
                                </a>
                                @include('templates.message-validation')
                                <h4 class="card-title mb-1">Lupa Password? 🔒</h4>
                                <p class="card-text mb-2">Masukan email anda, kami akan mengirimkan instruksi untuk mereset password</p>
                                
                                <form class="auth-forgot-password-form mt-2" action="{{ route('auth.forgot') }}" method="POST">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label for="forgot-password-email" class="form-label">Email</label>
                                        <input type="text" class="{{$errors->has('email')?'is-invalid':''}} form-control" id="forgot-password-email" name="email" placeholder="john@example.com" aria-describedby="forgot-password-email" tabindex="1" autofocus />
                                        @if ($errors->has('email'))
                                            <span class="text-danger small">{{ $errors->first('email') }}</span>
                                        @endif
                                    </div>
                                    <button class="btn btn-primary btn-block" tabindex="2">Kirim</button>
                                </form>

                                <p class="text-center mt-2">
                                    <a href="{{ route('auth.login') }}"> <i data-feather="chevron-left"></i>Kembali</a>
                                </p>
                            </div>
                        </div>
@endsection