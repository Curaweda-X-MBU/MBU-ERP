@extends('templates.main')
@section('title', $title)
@section('content')
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{$title}}</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form form-horizontal" method="post" action="{{ route('data-master.bank.add') }}">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="alias" class="float-right">Alias</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="alias" class="{{$errors->has('alias')?'is-invalid':''}} form-control" name="alias" placeholder="BCA" value="{{ old('alias') }}">
                                                        @if ($errors->has('alias'))
                                                            <span class="text-danger small">{{ $errors->first('alias') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="name" class="float-right">Nama Bank</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="name" class="{{$errors->has('name')?'is-invalid':''}} form-control" name="name" placeholder="Nama Bank" value="{{ old('name') }}">
                                                        @if ($errors->has('name'))
                                                            <span class="text-danger small">{{ $errors->first('name') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="account_number" class="float-right">Nomor Rekening</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="account_number" class="{{$errors->has('account_number')?'is-invalid':''}} form-control" name="account_number" placeholder="Nomor Rekening" value="{{ old('account_number') }}">
                                                        @if ($errors->has('account_number'))
                                                            <span class="text-danger small">{{ $errors->first('account_number') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="owner" class="float-right">Atas Nama</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="owner" class="{{$errors->has('owner')?'is-invalid':''}} form-control" name="owner" placeholder="Nama Pemilik" value="{{ old('owner') }}">
                                                        @if ($errors->has('owner'))
                                                            <span class="text-danger small">{{ $errors->first('owner') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 offset-sm-3">
                                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Simpan</button>
                                                <a href="{{ route('data-master.bank.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
@endsection