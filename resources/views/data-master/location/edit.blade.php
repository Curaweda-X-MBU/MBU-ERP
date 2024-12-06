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
                                    <form class="form form-horizontal" method="post" action="{{ route('data-master.location.edit', $data->location_id) }}">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="name" class="float-right">Nama Lokasi</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="name" class="{{$errors->has('name')?'is-invalid':''}} form-control" name="name" placeholder="Nama Lokasi" value="{{ $data->name }}">
                                                        @if ($errors->has('name'))
                                                            <span class="text-danger small">{{ $errors->first('name') }}</span>
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
                                                        <textarea name="address" id="address" class="{{$errors->has('address')?'is-invalid':''}} form-control" placeholder="Alamat">{{ $data->address }}</textarea>
                                                        @if ($errors->has('address'))
                                                            <span class="text-danger small">{{ $errors->first('address') }}</span>
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
                                                            @if($data->company_id && $data->company->name)
                                                                <option value="{{ $data->company_id }}" selected="selected">{{ $data->company->name??'' }}</option>
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
                                                            @if($data->area_id && ($data->area->name??false))
                                                                <option value="{{ $data->area_id }}" selected="selected">{{ $data->area->name??'' }}</option>
                                                            @endif
                                                        </select>
                                                        @if ($errors->has('area_id'))
                                                            <span class="text-danger small">{{ $errors->first('area_id') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 offset-sm-3">
                                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Ubah</button>
                                                <a href="{{ route('data-master.location.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
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
                            });

                            var oldValueCompany = "{{ $data->company_id }}";
                            if (oldValueCompany) {
                                var oldNameCopmany = "{{ $data->company->name ?? '' }}";
                                if (oldNameCopmany) {
                                    var newOptionCompany = new Option(oldNameCopmany, oldValueCompany, true, true);
                                    $('#company_id').append(newOptionCompany).trigger('change');
                                }
                            }

                            var oldValue = "{{ $data->area_id }}";
                            if (oldValue) {
                                var oldName = "{{ $data->area->name ?? '' }}";
                                if (oldName) {
                                    var newOption = new Option(oldName, oldValue, true, true);
                                    $('#area_id').append(newOption).trigger('change');
                                }
                            }

                            @if ($errors->has('company_id'))
                                $('#company_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('area_id'))
                                $('#area_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif
                        });
                    </script>

@endsection