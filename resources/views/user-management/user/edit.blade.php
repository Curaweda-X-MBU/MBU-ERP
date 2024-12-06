@extends('templates.main')
@section('title', $title)
@section('content')
@php
    $readOnly = auth()->user()->role->name === 'Super Admin' ? false : true;
@endphp
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{$title}}</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form form-horizontal" method="post" action="{{ route('user-management.user.edit', $data->user_id) }}" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="image" class="float-right">Foto</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        @if ($data->image)
                                                        <img class="mb-1" src="{{ route('file.show', ['filename' => $data->image]) }}" width="125" alt="user-profile">
                                                        @endif
                                                        <input type="file" id="image" class="{{$errors->has('image')?'is-invalid':''}} form-control" name="image" placeholder="Foto Profil" value="{{ old('image') }}">
                                                        @if ($errors->has('image'))
                                                            <span class="text-danger small">{{ $errors->first('image') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="npk" class="float-right">NPK</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="npk" class="{{$errors->has('npk')?'is-invalid':''}} form-control" name="npk" placeholder="NPK" value="{{ $data->npk }}" @readonly($readOnly)>
                                                        @if ($errors->has('npk'))
                                                            <span class="text-danger small">{{ $errors->first('npk') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="name" class="float-right">Nama</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="name" class="{{$errors->has('name')?'is-invalid':''}} form-control" name="name" placeholder="Nama" value="{{ $data->name }}">
                                                        @if ($errors->has('name'))
                                                            <span class="text-danger small">{{ $errors->first('name') }}</span>
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
                                                        <input type="email" id="email" class="{{$errors->has('email')?'is-invalid':''}} form-control" name="email" placeholder="Email" value="{{ $data->email }}" @readonly($readOnly)>
                                                        @if ($errors->has('email'))
                                                            <span class="text-danger small">{{ $errors->first('email') }}</span>
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
                                                        <label for="password" class="float-right">Password</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <div class="input-group input-group-merge form-password-toggle">
                                                            <input type="password" name="password" class="{{$errors->has('password')?'is-invalid':''}} form-control" id="basic-default-password1" placeholder="Password" aria-describedby="basic-default-password1">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text cursor-pointer" style="{{$errors->has('password')?'border-color:red;':''}}><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye font-small-4"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></span>
                                                            </div>
                                                        </div>
                                                        @if ($errors->has('password'))
                                                            <span class="text-danger small">{{ $errors->first('password') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="password_confirmation" class="float-right">Ulangi Password</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <div class="input-group input-group-merge form-password-toggle">
                                                            <input type="password" name="password_confirmation" class="form-control" id="basic-default-password1" placeholder="Ulangi Password" aria-describedby="basic-default-password1">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye font-small-4"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="display: {{ $readOnly?'none':'contents' }}">
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-sm-3 col-form-label">
                                                            <label for="company_id" class="float-right">Unit Bisnis</label>
                                                        </div>
                                                        <div class="col-sm-5">
                                                            <select name="company_id" id="company_id" class="form-control">
                                                                @if(($data->department->company->company_id??false) && ($data->department->company->name??false))
                                                                    <option value="{{ $data->department->company->company_id??'' }}" selected="selected">{{ $data->department->company->name??'' }}</option>
                                                                @endif
                                                            </select>
                                                            @if ($errors->has('company_id'))
                                                                <span class="text-danger small">{{ $errors->first('company_id') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-sm-3 col-form-label">
                                                            <label for="location_id" class="float-right">Lokasi</label>
                                                        </div>
                                                        <div class="col-sm-5">
                                                            <select name="location_id" id="location_id" class="form-control">
                                                                <option disabled selected>Pilih Unit Bisnis terlebih dahulu</option>
                                                                @if(($data->department->location_id??false) && ($data->department->location->name??false))
                                                                    <option value="{{ $data->department->location_id }}" selected="selected">{{ $data->department->location->name }}</option>
                                                                @endif
                                                            </select>
                                                            @if ($errors->has('location_id'))
                                                                <span class="text-danger small">{{ $errors->first('location_id') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-sm-3 col-form-label">
                                                            <label for="department_id" class="float-right">Departemen</label>
                                                        </div>
                                                        <div class="col-sm-5">
                                                            <select name="department_id" id="department_id" class="form-control">
                                                                <option disabled selected>Pilih Unit Bisnis terlebih dahulu</option>
                                                                @if($data->department_id && ($data->department->name??false))
                                                                    <option value="{{ $data->department_id }}" selected="selected">{{ $data->department->name??'' }} - {{$data->department->location->name??''}}</option>
                                                                @endif
                                                            </select>
                                                            @if ($errors->has('department_id'))
                                                                <span class="text-danger small">{{ $errors->first('department_id') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-sm-3 col-form-label">
                                                            <label for="role_id" class="float-right">Role</label>
                                                        </div>
                                                        <div class="col-sm-5">
                                                            <select name="role_id" id="role_id" class="form-control">
                                                                <option disabled selected>Pilih Unit Bisnis terlebih dahulu</option>
                                                                @if($data->role_id && ($data->role->name ?? false))
                                                                    <option value="{{ $data->role_id }}" selected="selected">{{ $data->role->name??'' }}</option>
                                                                @endif
                                                            </select>
                                                            @if ($errors->has('role_id'))
                                                                <span class="text-danger small">{{ $errors->first('role_id') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-sm-3 col-form-label">
                                                            <label for="role_id" class="float-right">Aktif</label>
                                                        </div>
                                                        <div class="col-sm-5">
                                                            <div class="custom-control custom-switch custom-switch-primary" style="margin-top: 5px;">
                                                                <input type="checkbox" class="custom-control-input" id="customSwitch10" name="is_active" {{ $data->is_active?'checked':'' }}>
                                                                <label class="custom-control-label" for="customSwitch10">
                                                                    <span class="switch-icon-left"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
                                                                    <span class="switch-icon-right"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span>
                                                                </label>
                                                            </div>
                                                            @if ($errors->has('is_active'))
                                                                <span class="text-danger small">{{ $errors->first('is_active') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 offset-sm-3">
                                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Ubah</button>
                                                @if (auth()->user()->role->hasPermissionTo('user-management.user.index'))
                                                <a href="{{ route('user-management.user.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                                                @endif
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
                            $('#company_id').select2({
                                placeholder: "Pilih Unit Bisnis",
                                ajax: {
                                    url: '{{ route("data-master.company.search") }}', 
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

                            $('#company_id').change(function (e) { 
                                e.preventDefault();
                                $('#location_id').val(null).trigger('change');
                                $('#role_id').val(null).trigger('change');
                                
                                var companyId = $(this).val();
                                var qryParam = companyId?`?company_id=${companyId}`:'';
                                
                                $('#location_id').select2({
                                    placeholder: "Pilih Lokasi",
                                    ajax: {
                                        url: `{{ route("data-master.location.search") }}${qryParam}`, 
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

                                $('#location_id').change(function (e) { 
                                    $('#department_id').val(null).trigger('change');

                                    var locationId = $(this).val();
                                    var qryParamDepartment = locationId?`?company_id=${companyId}&location_id=${locationId}`:'';
                                    $('#department_id').select2({
                                        placeholder: "Pilih Departemen",
                                        ajax: {
                                            url: `{{ route("data-master.department.search") }}${qryParamDepartment}`, 
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
                                });

                                $('#role_id').select2({
                                    placeholder: "Pilih Role",
                                    ajax: {
                                        url: `{{ route("user-management.role.search") }}${qryParam}`, 
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
                            });

                            $('#company_id').trigger('change');

                            var oldValueCompany = "{{ $data->company_id }}";
                            if (oldValueCompany) {
                                var oldNameCopmany = "{{ $data->company_name }}";
                                if (oldNameCopmany) {
                                    var newOption = new Option(oldNameCopmany, oldValueCompany, true, true);
                                    $('#company_id').append(newOption).trigger('change');
                                }
                            }

                            var oldValueLoc = "{{ $data->department->location_id??'' }}";
                            if (oldValueLoc) {
                                var oldNameLoc = "{{ $data->department->location->name??'' }}";
                                if (oldNameLoc) {
                                    var newOptionLoc = new Option(oldNameLoc, oldValueLoc, true, true);
                                    $('#location_id').append(newOptionLoc).trigger('change');
                                }
                            }

                            var oldValue = "{{ $data->department_id }}";
                            if (oldValue) {
                                var oldName = "{{ $data->department->name??'' }}"+" - "+"{{$data->department->location->name??''}}";
                                if (oldName) {
                                    var newOption = new Option(oldName, oldValue, true, true);
                                    $('#department_id').append(newOption).trigger('change');
                                }
                            }

                            var oldValueRole = "{{ $data->role_id }}";
                            if (oldValueRole) {
                                var oldNameRole = "{{ $data->role->name??'' }}";
                                if (oldNameRole) {
                                    var newOption = new Option(oldNameRole, oldValueRole, true, true);
                                    $('#role_id').append(newOption).trigger('change');
                                }
                            }

                            @if ($errors->has('company_id'))
                                $('#company_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('location_id'))
                                $('#location_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('department_id'))
                                $('#department_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('role_id'))
                                $('#role_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif
                        });
                    </script>
@endsection