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
                                    <form class="form form-horizontal" id="formAudit" method="post" action="{{ route('data-master.warehouse.edit', $data->warehouse_id) }}">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="name" class="float-right">Nama</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" name="name" id="name" class="{{$errors->has('name')?'is-invalid':''}} form-control" placeholder="Nama Gudang" value="{{$data->name}}">
                                                        @if ($errors->has('name'))
                                                            <span class="text-danger small">{{ $errors->first('name') }}</span>
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
                                                        <label for="company_id" class="float-right">Unit Bisnis</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select name="company_id" id="company_id" class="form-control">
                                                            @if($data->location->company->company_id && ($data->location->company->name??false))
                                                                <option value="{{ $data->location->company->company_id }}" selected="selected">{{ $data->location->company->name??'' }}</option>
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
                                                        <label for="area_id" class="float-right">Area</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select name="area_id" id="area_id" class="form-control">
                                                            <option disabled selected>Pilih Unit Bisnis terlebih dahulu</option>
                                                            @if($data->location->area->area_id && ($data->location->area->name??false))
                                                                <option value="{{ $data->location->area->area_id }}" selected="selected">{{ $data->location->area->name ?? '' }}</option>
                                                            @endif
                                                        </select>
                                                        @if ($errors->has('area_id'))
                                                            <span class="text-danger small">{{ $errors->first('area_id') }}</span>
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
                                                            <option disabled selected>Pilih Area terlebih dahulu</option>
                                                            @if($data->location_id && ($data->location->name??false))
                                                                <option value="{{ $data->location_id }}" selected="selected">{{ $data->location->name ?? ''}}</option>
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
                                                        <label for="kandang_id" class="float-right">Kandang</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select name="kandang_id" id="kandang_id" class="form-control {{$errors->has('kandang_id')?'is-invalid':''}}">
                                                            <option disabled selected>Pilih Lokasi terlebih dahulu</option>
                                                            @if($data->kandang_id && ($data->kandang->name??false))
                                                                <option value="{{ $data->kandang_id }}" selected="selected">{{ $data->kandang->name??'' }}</option>
                                                            @endif
                                                        </select>
                                                        @if ($errors->has('kandang_id'))
                                                            <span class="text-danger small">{{ $errors->first('kandang_id') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 offset-sm-3">
                                                <button id="submitForm" type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Simpan</button>
                                                <a href="{{ route('data-master.warehouse.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
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
                                $('#area_id').val(null).trigger('change');

                                $('#area_id').select2({
                                    placeholder: "Pilih Area",
                                    ajax: {
                                        url: `{{ route("data-master.area.search") }}`, 
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

                                $('#area_id').change(function (e) { 
                                    e.preventDefault();
                                    $('#location_id').val(null).trigger('change');
                                    var areaId = $('#area_id').val();
                                    var qryLocation = areaId?`?area_id=${areaId}`:'';

                                    $('#location_id').select2({
                                        placeholder: "Pilih Lokasi",
                                        ajax: {
                                            url: `{{ route("data-master.location.search") }}${qryLocation}`, 
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
                                        $('#kandang_id').val(null).trigger('change');
                                        var companyId = $('#company_id').val();
                                        var locationId = $('#location_id').val();
                                        var qryParam = locationId&&companyId?`?location_id=${locationId}&company_id=${companyId}`:'';

                                        $('#kandang_id').select2({
                                            placeholder: "Pilih Kandang",
                                            ajax: {
                                                url: `{{ route("data-master.kandang.search") }}${qryParam}`, 
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
                                });

                            });

                            var oldValueCompany = "{{ $data->location->company->company_id }}";
                            if (oldValueCompany) {
                                var oldNameCopmany = "{{ $data->location->company->name ?? '' }}";
                                if (oldNameCopmany) {
                                    var newOption = new Option(oldNameCopmany, oldValueCompany, true, true);
                                    $('#company_id').append(newOption).trigger('change');
                                }
                            }

                            var oldValueArea = "{{ $data->location->area->area_id }}";
                            if (oldValueArea) {
                                var oldNameArea = "{{ $data->location->area->name ?? '' }}";
                                if (oldNameArea) {
                                    var newOptionLoc = new Option(oldNameArea, oldValueArea, true, true);
                                    $('#area_id').append(newOptionLoc).trigger('change');
                                }
                            }
                            
                            var oldValueLoc = "{{ $data->location_id }}";
                            if (oldValueLoc) {
                                var oldNameLoc = "{{ $data->location->name ?? '' }}";
                                if (oldNameLoc) {
                                    var newOptionLoc = new Option(oldNameLoc, oldValueLoc, true, true);
                                    $('#location_id').append(newOptionLoc).trigger('change');
                                }
                            }

                            var oldValue = "{{ $data->kandang_id }}";
                            if (oldValue) {
                                var oldName = "{{ $data->kandang->name ?? '' }}";
                                if (oldName) {
                                    var newOption = new Option(oldName, oldValue, true, true);
                                    $('#kandang_id').append(newOption).trigger('change');
                                }
                            }

                            @if ($errors->has('company_id'))
                                $('#company_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('area_id'))
                                $('#area_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('location_id'))
                                $('#location_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('kandang_id'))
                                $('#kandang_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif
                        });
                    </script>
@endsection