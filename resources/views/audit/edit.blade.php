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
                                    <form class="form form-horizontal" id="formAudit" method="post" action="{{ route('audit.edit', $data->audit_id) }}" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <div class="row">
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
                                                            @if(($data->department->location->location_id??false) && ($data->department->location->name??false))
                                                                <option value="{{ $data->department->location->location_id??'' }}" selected="selected">{{ $data->department->location->name??'' }}</option>
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
                                                            <option disabled selected>Pilih Lokasi terlebih dahulu</option>
                                                            @if(($data->department->department_id??false) && ($data->department->name??false))
                                                                <option value="{{ $data->department->department_id??'' }}" selected="selected">{{ $data->department->name??'' }}</option>
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
                                                        <label for="title" class="float-right">Judul</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" name="title" class="{{$errors->has('title')?'is-invalid':''}} form-control" placeholder="Judul" value="{{$data->title}}">
                                                        @if ($errors->has('title'))
                                                            <span class="text-danger small">{{ $errors->first('title') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="description" class="float-right">Deskripsi</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <textarea name="description" id="description" class="{{$errors->has('description')?'is-invalid':''}} form-control" placeholder="Deskripsi">{{ $data->description }}</textarea>
                                                        @if ($errors->has('description'))
                                                            <span class="text-danger small">{{ $errors->first('description') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="document" class="float-right">Dokumen (PDF)</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        @if ($data->document)
                                                            <a href="{{ route('file.show', ['filename' => $data->document]) }}" target="_blank">Download File</a>
                                                        @endif
                                                        <input type="file" id="document" class="{{$errors->has('document')?'is-invalid':''}} form-control" name="document" placeholder="Upload Dokumen" value="{{ $data->document }}">
                                                        @if ($errors->has('document'))
                                                            <span class="text-danger small">{{ $errors->first('document') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="category" class="float-right">Kategori</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select name="category" id="category" class="{{$errors->has('category')?'is-invalid':''}} form-control">
                                                            <option disabled {{ !$data->category?'selected': '' }}>Pilih Kategori</option>
                                                            @foreach ($category as $key => $item)
                                                                <option value="{{$key}}" {{$data->category==$key?'selected':''}}>{{$item}}</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('category'))
                                                            <span class="text-danger small">{{ $errors->first('category') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="priority" class="float-right">Prioritas</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select name="priority" id="priority" class="{{$errors->has('priority')?'is-invalid':''}} form-control">
                                                            <option disabled {{ !$data->priority?'selected': '' }}>Pilih Prioritas</option>
                                                            @foreach ($priority as $key => $item)
                                                                <option value="{{$key}}" {{$data->priority==$key?'selected':''}}>{{$item}}</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('priority'))
                                                            <span class="text-danger small">{{ $errors->first('priority') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 offset-sm-3">
                                                <button id="submitForm" type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Ubah</button>
                                                <a href="{{ route('audit.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
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
                            });

                            $('#location_id').change(function (e) { 
                                e.preventDefault();
                                $('#department_id').val(null).trigger('change');
                                var companyId = $('#company_id').val();
                                var locationId = $(this).val();
                                var qryParam = companyId&&locationId?`?company_id=${companyId}&location_id=${locationId}`:'';

                                $('#department_id').select2({
                                    placeholder: "Pilih Department",
                                    ajax: {
                                        url: `{{ route("data-master.department.search") }}${qryParam}`, 
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



                            var oldValueCompany = "{{ $data->department->company->company_id }}";
                            if (oldValueCompany) {
                                var oldNameCopmany = "{{ $data->department->company->name }}";
                                if (oldNameCopmany) {
                                    var newOption = new Option(oldNameCopmany, oldValueCompany, true, true);
                                    $('#company_id').append(newOption).trigger('change');
                                }
                            }

                            var oldValueLoc = "{{ $data->department->location->location_id }}";
                            if (oldValueLoc) {
                                var oldNameLoc = "{{ $data->department->location->name }}";
                                if (oldNameLoc) {
                                    var newOptionLoc = new Option(oldNameLoc, oldValueLoc, true, true);
                                    $('#location_id').append(newOptionLoc).trigger('change');
                                }
                            }

                            var oldValue = "{{ $data->department->department_id }}";
                            if (oldValue) {
                                var oldName = "{{ $data->department->name }}";
                                if (oldName) {
                                    var newOption = new Option(oldName, oldValue, true, true);
                                    $('#department_id').append(newOption).trigger('change');
                                }
                            }

                            @if ($errors->has('company_id'))
                                $('#company_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('department_id'))
                                $('#department_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('location_id'))
                                $('#location_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif
                        });
                    </script>
@endsection