@extends('templates.auth')
@section('content') 
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="{{asset('assets/images/logo_mbu_primary.png')}}" alt="" srcset="" width="100"><br>
                                </div>
                                <a href="javascript:void(0);" class="brand-logo">
                                    <h2 class="brand-text text-primary ml-1">MBU - ERP</h2><br>
                                </a>
                                @include('templates.message-validation')
                                <p class="card-text mb-2">Rubah password anda, pastikan password dapat mudah diingat.</p>
                                
                                <form class="auth-forgot-password-form mt-2" action="{{ route('auth.reset.send') }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="token" value="{{ $token }}">
                                    <input type="hidden" name="email" value="{{ $email }}">
                                    <div class="form-group">
                                        <label for="password" class="form-label">Password Baru</label>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input type="password" name="password" class="{{$errors->has('password')?'is-invalid':''}} form-control" id="basic-default-password1" placeholder="Password" aria-describedby="basic-default-password1">
                                            <div class="input-group-append">
                                                <span class="input-group-text cursor-pointer" style="{{$errors->has('password')?'border-color:red;':''}}"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye font-small-4"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></span>
                                            </div>
                                        </div>
                                        @if ($errors->has('password'))
                                            <span class="text-danger small">{{ $errors->first('password') }}</span>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="password" class="form-label">Ulangi Password</label>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input type="password" name="password_confirmation" class="form-control" id="basic-default-password1" placeholder="Ulangi Password" aria-describedby="basic-default-password1">
                                            <div class="input-group-append">
                                                <span class="input-group-text cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye font-small-4"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></span>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-block" tabindex="2">Ubah</button>
                                </form>

                                <p class="text-center mt-2">
                                    <a href="{{ route('auth.login') }}"> <i data-feather="chevron-left"></i>Kembali</a>
                                </p>
                            </div>
                        </div>
@endsection