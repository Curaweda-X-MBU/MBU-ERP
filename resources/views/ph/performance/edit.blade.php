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
                                    <form class="form form-horizontal invoice-repeater" id="formAudit" method="post" action="{{ route('ph.performance.edit', $data->ph_performance_id) }}">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-3 col-form-label">
                                                                <label for="company_id" class="float-right">Unit Bisnis</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <select name="company_id" id="company_id" class="form-control">
                                                                    @if($data->kandang->company->company_id)
                                                                        <option value="{{ $data->kandang->company->company_id }}" selected="selected">{{ $data->kandang->company->name }}</option>
                                                                    @endif
                                                                </select>
                                                                @if ($errors->has('company_id'))
                                                                    <span class="text-danger small">{{ $errors->first('company_id') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-3 col-form-label">
                                                                <label for="area_id" class="float-right">Area</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <select name="area_id" id="area_id" class="form-control">
                                                                    <option disabled selected>Pilih Unit Bisnis terlebih dahulu</option>
                                                                    @if($data->kandang->location->area_id && $data->kandang->location->area->name)
                                                                        <option value="{{ $data->kandang->location->area_id }}" selected="selected">{{ $data->kandang->location->area->name??'' }}</option>
                                                                    @endif
                                                                </select>
                                                                @if ($errors->has('area_id'))
                                                                    <span class="text-danger small">{{ $errors->first('area_id') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-3 col-form-label">
                                                                <label for="location_id" class="float-right">Lokasi</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <select name="location_id" id="location_id" class="form-control">
                                                                    <option disabled selected>Pilih Area terlebih dahulu</option>
                                                                    @if($data->kandang->location_id && $data->kandang->location->name)
                                                                        <option value="{{ $data->kandang->location_id }}" selected="selected">{{ $data->kandang->location->name??'' }}</option>
                                                                    @endif
                                                                </select>
                                                                @if ($errors->has('location_id'))
                                                                    <span class="text-danger small">{{ $errors->first('location_id') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-3 col-form-label">
                                                                <label for="kandang_id" class="float-right">Kandang</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <select name="kandang_id" id="kandang_id" class="form-control {{$errors->has('kandang_id')?'is-invalid':''}}">
                                                                    <option disabled selected>Pilih Lokasi terlebih dahulu</option>
                                                                    @if($data->kandang_id && $data->kandang->name)
                                                                        <option value="{{ $data->kandang_id }}" selected="selected">{{ $data->kandang->name??'' }}</option>
                                                                    @endif
                                                                </select>
                                                                @if ($errors->has('kandang_id'))
                                                                    <span class="text-danger small">{{ $errors->first('kandang_id') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-3 col-form-label">
                                                                <label for="supplier_id" class="float-right">Vendor</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <select name="supplier_id" id="supplier_id" class="form-control">
                                                                    @if($data->supplier_id && $data->supplier->name)
                                                                        <option value="{{ $data->supplier_id }}" selected="selected">{{ $data->supplier->name??'' }}</option>
                                                                    @endif
                                                                </select>
                                                                @if ($errors->has('supplier_id'))
                                                                    <span class="text-danger small">{{ $errors->first('supplier_id') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-3 col-form-label">
                                                                <label for="hatchery" class="float-right">Hatchery</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <select name="hatchery" id="hatchery" class="form-control">
                                                                    <option disabled selected>Pilih Vendor terlebih dahulu</option>
                                                                    @if($data->hatchery)
                                                                        <option value="{{ $data->hatchery }}" selected="selected">{{ $data->hatchery }}</option>
                                                                    @endif
                                                                </select>
                                                                @if ($errors->has('hatchery'))
                                                                    <span class="text-danger small">{{ $errors->first('hatchery') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-3 col-form-label">
                                                                <label for="chick_in_date" class="float-right">Tgl. Chick In</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control flatpickr-basic" name="chick_in_date" placeholder="Tanggal Chick In" value="{{ date('d-M-Y', strtotime($data->chick_in_date)) }}" />
                                                                @if ($errors->has('chick_in_date'))
                                                                    <span class="text-danger small">{{ $errors->first('chick_in_date') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-3 col-form-label">
                                                                <label for="population" class="float-right">Populasi</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="number" id="population" class="{{$errors->has('population')?'is-invalid':''}} form-control" name="population" placeholder="Jumlah Populasi" value="{{ $data->population }}">
                                                                @if ($errors->has('population'))
                                                                    <span class="text-danger small">{{ $errors->first('population') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-3 col-form-label">
                                                                <label for="death" class="float-right">Jumlah Mati</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="number" id="death" class="{{$errors->has('death')?'is-invalid':''}} form-control" name="death" placeholder="Jumlah Mati" value="{{ $data->death }}">
                                                                @if ($errors->has('death'))
                                                                    <span class="text-danger small">{{ $errors->first('death') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-3 col-form-label">
                                                                <label for="culling" class="float-right">Jumlah Culling</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="number" id="culling" class="{{$errors->has('culling')?'is-invalid':''}} form-control" name="culling" placeholder="Jumlah Culling" value="{{ $data->culling }}">
                                                                @if ($errors->has('culling'))
                                                                    <span class="text-danger small">{{ $errors->first('culling') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-3 col-form-label">
                                                                <label for="bw" class="float-right">Body Weight (BW)</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="number" id="bw" class="{{$errors->has('bw')?'is-invalid':''}} form-control" name="bw" placeholder="Body Weight (BW)" value="{{ $data->bw }}">
                                                                @if ($errors->has('bw'))
                                                                    <span class="text-danger small">{{ $errors->first('bw') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-2">
                                                {{-- <div class="row"> --}}
                                                    <center>
                                                        <button id="submitForm" type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Ubah</button>
                                                        <a href="{{ route('ph.performance.detail', ['month'=>$month, 'year'=>$year]) }}" class="btn btn-outline-warning waves-effect">Batal</a>
                                                    </center>
                                                {{-- </div> --}}
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
                    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
                    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
                    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
                    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">

                    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
                    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
                    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
                    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}"></script>
                    <script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
                    <script>
                        $(document).ready(function() {
                            const dateOpt = { dateFormat: 'd-M-Y' }
                            $('.flatpickr-basic').flatpickr(dateOpt);

                            $('#product_id').select2({
                                placeholder: "Pilih Produk",
                                ajax: {
                                    url: '{{ route("data-master.product.search") }}', 
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
                                    $('#culling_pic').val(null).trigger('change');
                                    $('#location_id').val(null).trigger('change');
                                    var areaId = $('#area_id').val();
                                    var qryLocation = areaId?`?area_id=${areaId}`:'';

                                    const companyID = $('#company_id').val();
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

                            $('#supplier_id').select2({
                                placeholder: "Pilih Vendor",
                                ajax: {
                                    url: '{{ route("data-master.supplier.search") }}', 
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

                            $('#supplier_id').change(function (e) { 
                                e.preventDefault();
                                $('#hatchery').val(null).trigger('change');
                                var supplierId = $(this).val();
                                var qryHatchery = supplierId?`?supplier_id=${supplierId}`:'';
                                
                                $('#hatchery').select2({
                                    placeholder: "Pilih Hatchery",
                                    ajax: {
                                        url: '{{ route("data-master.supplier.hatchery.search") }}'+qryHatchery,
                                        dataType: 'json',
                                        delay: 250, 
                                        data: function(params) {
                                            return {
                                                q: params.term 
                                            };
                                        },
                                        processResults: function(data) {
                                            $('.hatchery').val(null);
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
                                var oldNameCopmany = "{{ $data->kandang->company->name??'' }}";
                                if (oldNameCopmany) {
                                    var newOption = new Option(oldNameCopmany, oldValueCompany, true, true);
                                    $('#company_id').append(newOption).trigger('change');
                                }
                            }

                            var oldValueArea = "{{ $data->area_id }}";
                            if (oldValueArea) {
                                var oldNameArea = "{{ $data->kandang->location->area->name??'' }}";
                                if (oldNameArea) {
                                    var newOptionLoc = new Option(oldNameArea, oldValueArea, true, true);
                                    $('#area_id').append(newOptionLoc).trigger('change');
                                }
                            }

                            var oldValueLoc = "{{ $data->location_id }}";
                            if (oldValueLoc) {
                                var oldNameLoc = "{{ $data->kandang->location->name??'' }}";
                                if (oldNameLoc) {
                                    var newOptionLoc = new Option(oldNameLoc, oldValueLoc, true, true);
                                    $('#location_id').append(newOptionLoc).trigger('change');
                                }
                            }

                            var oldValue = "{{ $data->supplier_id }}";
                            if (oldValue) {
                                var oldName = "{{ $data->supplier->name }}";
                                if (oldName) {
                                    var newOption = new Option(oldName, oldValue, true, true);
                                    $('#supplier_id').append(newOption).trigger('change');
                                }
                            }

                            var oldValueHatchery = "{{ $data->hatchery }}";
                            if (oldValueHatchery) {
                                var oldNameHatchery = "{{ $data->hatchery }}";
                                if (oldNameHatchery) {
                                    var newOptionHatchery = new Option(oldNameHatchery, oldValueHatchery, true, true);
                                    $('#hatchery').append(newOptionHatchery).trigger('change');
                                }
                            }

                            var oldValueKandang = "{{ $data->kandang_id }}";
                            if (oldValueKandang) {
                                var oldNameKandang = "{{ $data->kandang->name }}";
                                if (oldNameKandang) {
                                    var newOption = new Option(oldNameKandang, oldValueKandang, true, true);
                                    $('#kandang_id').append(newOption).trigger('change');
                                }
                            }

                            @if ($errors->has('product_id'))
                                $('#product_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('company_id'))
                                $('#company_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('supplier_id'))
                                $('#supplier_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('hatchery'))
                                $('#hatchery').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('area_id'))
                                $('#area_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('location_id'))
                                $('#location_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('chick_in_date'))
                                $('#chick_in_date').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif
                        });
                    </script>
@endsection