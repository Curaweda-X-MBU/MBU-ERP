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
                                    <form class="form form-horizontal" method="post" action="{{ route('data-master.department.add') }}">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="name" class="float-right">Nama Departemen</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="name" class="{{$errors->has('name')?'is-invalid':''}} form-control" name="name" placeholder="Nama Departemen" value="{{ old('name') }}">
                                                        @if ($errors->has('name'))
                                                            <span class="text-danger small">{{ $errors->first('name') }}</span>
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
                                                            @if(old('company_id') && old('company_name'))
                                                                <option value="{{ old('company_id') }}" selected="selected">{{ old('company_name') }}</option>
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
                                                            @if(old('location_id') && old('location_name'))
                                                                <option value="{{ old('location_id') }}" selected="selected">{{ old('location_name') }}</option>
                                                            @endif
                                                        </select>
                                                        @if ($errors->has('location_id'))
                                                            <span class="text-danger small">{{ $errors->first('location_id') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 offset-sm-3">
                                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Simpan</button>
                                                <a href="{{ route('data-master.department.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
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

                            $('#location_id').select2({
                                placeholder: "Pilih Lokasi",
                                ajax: {
                                    url: '{{ route("data-master.location.search") }}', 
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

                            var oldValueCompany = "{{ old('company_id') }}";
                            if (oldValueCompany) {
                                var oldNameCopmany = "{{ old('company_name') }}";
                                if (oldNameCopmany) {
                                    var newOption = new Option(oldNameCopmany, oldValueCompany, true, true);
                                    $('#company_id').append(newOption).trigger('change');
                                }
                            }

                            var oldValueLocation = "{{ old('location_id') }}";
                            if (oldValueLocation) {
                                var oldNameLocation = "{{ old('location_name') }}";
                                if (oldNameLocation) {
                                    var newOption = new Option(oldNameLocation, oldValueLocation, true, true);
                                    $('#location_id').append(newOption).trigger('change');
                                }
                            }

                            @if ($errors->has('company_id'))
                                $('#company_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('location_id'))
                                $('#location_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif
                        });
                    </script>
@endsection