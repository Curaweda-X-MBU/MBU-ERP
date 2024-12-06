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
                                    <form class="form form-horizontal" method="post" action="{{ route('data-master.customer.add') }}">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="name" class="float-right">Nama Pelanggan</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="name" class="{{$errors->has('name')?'is-invalid':''}} form-control" name="name" placeholder="Nama Pelanggan" value="{{ old('name') }}">
                                                        @if ($errors->has('name'))
                                                            <span class="text-danger small">{{ $errors->first('name') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="assign_to" class="float-right">Penanggung Jawab</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select name="assign_to" id="assign_to" class="form-control">
                                                            @if(old('assign_to') && old('assign_to_name'))
                                                                <option value="{{ old('assign_to') }}" selected="selected">{{ old('assign_to_name') }}</option>
                                                            @endif
                                                        </select>
                                                        @if ($errors->has('assign_to'))
                                                            <span class="text-danger small">{{ $errors->first('assign_to') }}</span>
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
                                                            <option disabled {{ !old('type')?'selected': '' }}>Pilih Tipe</option>
                                                            @foreach ($type as $key => $item)
                                                                <option value="{{$key}}" {{old('type')==$key?'selected':''}}>{{$item}}</option>
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
                                                        <label for="phone" class="float-right">No. Telp</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="phone" class="{{$errors->has('phone')?'is-invalid':''}} form-control" name="phone" placeholder="No. Telp" value="{{ old('phone') }}">
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
                                                        <input type="email" id="email" class="{{$errors->has('email')?'is-invalid':''}} form-control" name="email" placeholder="Email" value="{{ old('email') }}">
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
                                                        <textarea name="address" id="address" class="{{$errors->has('address')?'is-invalid':''}} form-control" placeholder="Alamat">{{old('address')}}</textarea>
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
                                                        <input type="text" id="tax_num" class="{{$errors->has('tax_num')?'is-invalid':''}} form-control" name="tax_num" placeholder="NPWP" value="{{ old('tax_num') }}">
                                                        @if ($errors->has('tax_num'))
                                                            <span class="text-danger small">{{ $errors->first('tax_num') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 offset-sm-3">
                                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Simpan</button>
                                                <a href="{{ route('data-master.customer.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
                    <script>
                        $(document).ready(function() {
                            $('#assign_to').select2({
                                placeholder: "Pilih Penanggung Jawab",
                                ajax: {
                                    url: '{{ route("user-management.user.search") }}', 
                                    dataType: 'json',
                                    delay: 250, 
                                    data: function(params) {
                                        return {
                                            q: params.term 
                                        };
                                    },
                                    processResults: function(data) {
                                        return {
                                            results: data
                                        };
                                    },
                                    cache: true
                                }
                            });

                            var oldValue = "{{ old('assign_to') }}";
                            if (oldValue) {
                                var oldName = "{{ old('assign_to_name') }}";
                                if (oldName) {
                                    var newOption = new Option(oldName, oldValue, true, true);
                                    $('#assign_to').append(newOption).trigger('change');
                                }
                            }

                            @if ($errors->has('assign_to'))
                                $('#assign_to').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif
                        });
                    </script>
@endsection