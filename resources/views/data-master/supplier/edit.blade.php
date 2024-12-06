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
                                    <form class="form form-horizontal" method="post" action="{{ route('data-master.supplier.edit', $data->supplier_id) }}">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="name" class="float-right">Nama Pemasok</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="name" class="{{$errors->has('name')?'is-invalid':''}} form-control" name="name" placeholder="Nama Pemasok" value="{{ $data->name }}">
                                                        @if ($errors->has('name'))
                                                            <span class="text-danger small">{{ $errors->first('name') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="alias" class="float-right">Nama Alias</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="alias" class="{{$errors->has('alias')?'is-invalid':''}} form-control" name="alias" placeholder="Nama Alias" value="{{ $data->alias??'' }}">
                                                        @if ($errors->has('alias'))
                                                            <span class="text-danger small">{{ $errors->first('alias') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="pic_name" class="float-right">Nama PIC</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="pic_name" class="{{$errors->has('pic_name')?'is-invalid':''}} form-control" name="pic_name" placeholder="Nama PIC" value="{{ $data->pic_name }}">
                                                        @if ($errors->has('pic_name'))
                                                            <span class="text-danger small">{{ $errors->first('pic_name') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="type" class="float-right">Tipe</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select name="type" id="type" class="{{$errors->has('type')?'is-invalid':''}} form-control">
                                                            <option disabled {{ !$data->type?'selected': '' }}>Pilih Tipe</option>
                                                            @foreach ($type as $key => $item)
                                                                <option value="{{$key}}" {{$data->type==$key?'selected':''}}>{{$item}}</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('type'))
                                                            <span class="text-danger small">{{ $errors->first('type') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="hatchery" class="float-right">Hatchery</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select name="hatchery[]" id="hatchery" class="form-control" multiple>
                                                            @if ($data->hatchery)
                                                                @php
                                                                    $arrHatchery = json_decode($data->hatchery);
                                                                @endphp
                                                                @foreach ($arrHatchery as $value)
                                                                    <option value="{{ $value }}" selected>
                                                                        {{ $value }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        @if ($errors->has('hatchery'))
                                                            <span class="text-danger small">{{ $errors->first('hatchery') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="phone" class="float-right">No. Telp</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="phone" class="{{$errors->has('phone')?'is-invalid':''}} form-control" name="phone" placeholder="No. Telp" value="{{ $data->phone }}">
                                                        @if ($errors->has('phone'))
                                                            <span class="text-danger small">{{ $errors->first('phone') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="email" class="float-right">Email</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="email" id="email" class="{{$errors->has('email')?'is-invalid':''}} form-control" name="email" placeholder="Email" value="{{ $data->email }}">
                                                        @if ($errors->has('email'))
                                                            <span class="text-danger small">{{ $errors->first('email') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="address" class="float-right">Alamat</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <textarea name="address" id="address" class="{{$errors->has('address')?'is-invalid':''}} form-control" placeholder="Alamat">{{$data->address}}</textarea>
                                                        @if ($errors->has('address'))
                                                            <span class="text-danger small">{{ $errors->first('address') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="tax_num" class="float-right">NPWP</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="tax_num" class="{{$errors->has('tax_num')?'is-invalid':''}} form-control" name="tax_num" placeholder="NPWP" value="{{ $data->tax_num }}">
                                                        @if ($errors->has('tax_num'))
                                                            <span class="text-danger small">{{ $errors->first('tax_num') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 offset-sm-3">
                                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Ubah</button>
                                                <a href="{{ route('data-master.supplier.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
                    <script>
                        $(function () {
                            $('#hatchery').select2({
                                placeholder: "Input Hatchery",
                                tags: true,
                                allowClear: true
                            })
                        });
                    </script>

@endsection