@extends('templates.main')
@section('title', $title)
@section('content')
<style>
    .table thead th {
        vertical-align: middle !important;
        text-align: center;
    }
</style>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{$title}}</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form form-horizontal invoice-repeater" id="formAudit" method="post" action="{{ route('ph.report-complaint.edit', $data->ph_complaint_id) }}">
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
                                                                    @if($data->kandang->company_id && $data->kandang->company->name)
                                                                        <option value="{{ $data->kandang->company_id }}" selected="selected">{{ $data->kandang->company->name??'' }}</option>
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
                                                                <label for="product_id" class="float-right">Produk</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <select name="product_id" id="product_id" class="form-control">
                                                                    @if($data->product_id && $data->product->name)
                                                                        <option value="{{ $data->product_id }}" selected="selected">{{ $data->product->name??'' }}</option>
                                                                    @endif
                                                                </select>
                                                                @if ($errors->has('product_id'))
                                                                    <span class="text-danger small">{{ $errors->first('product_id') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-3 col-form-label">
                                                                <label for="type" class="float-right">Tipe</label>
                                                            </div>
                                                            <div class="col-sm-9">
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
                                                                <label for="symptoms" class="float-right">Gejala Klinis</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <select name="symptoms[]" id="symptoms" class="form-control" multiple>
                                                                    @if ($data->symptoms)
                                                                        @php
                                                                            $arrSymptom = json_decode($data->symptoms);
                                                                        @endphp
                                                                        @foreach ($arrSymptom as $symptomId)
                                                                            <option value="{{ $symptomId }}" selected>
                                                                                {{ \App\Models\Ph\PhSymptom::find($symptomId)->name ?? '' }}
                                                                            </option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                                @if ($errors->has('symptoms'))
                                                                    <span class="text-danger small">{{ $errors->first('symptoms') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-3 col-form-label">
                                                                <label for="investigation_date" class="float-right">Tgl. Investigasi</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control flatpickr-basic" name="investigation_date" placeholder="Tanggal Inestigasi" value="{{ date('d-M-Y', strtotime($data->investigation_date)) }}" />
                                                                @if ($errors->has('investigation_date'))
                                                                    <span class="text-danger small">{{ $errors->first('investigation_date') }}</span>
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
                                                                <label for="description" class="float-right">Deskripsi</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <textarea id="description" class="{{$errors->has('description')?'is-invalid':''}} form-control" name="description" placeholder="Deskripsi Komplain" >{{ $data->description }}</textarea>
                                                                @if ($errors->has('description'))
                                                                    <span class="text-danger small">{{ $errors->first('description') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-3 col-form-label">
                                                                <label for="culling_pic" class="float-right">Manager Area</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <select name="culling_pic" id="culling_pic" class="form-control">
                                                                    <option disabled selected>Pilih Area terlebih dahulu</option>
                                                                    @if($data->culling_pic && $data->cullingpic->name)
                                                                        <option value="{{ $data->culling_pic }}" selected="selected">{{ $data->cullingpic->name??'' }}</option>
                                                                    @endif
                                                                </select>
                                                                @if ($errors->has('culling_pic'))
                                                                    <span class="text-danger small">{{ $errors->first('culling_pic') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-3" style="padding-left: 25px; padding-right: 25px;">
                                            <p><b><i data-feather="disc"></i>&nbsp;&nbsp;Data Chick In</b></p>
                                                @include('ph.report-complaint.repeater')
                                            </div>
                                            <div class="col-12 mt-3" style="padding-left: 25px; padding-right: 25px;">
                                            <p><b><i data-feather="disc"></i>&nbsp;&nbsp;Data Mortality</b></p>
                                                @include('ph.report-complaint.mortality')
                                            </div>
                                            <div class="col-12 mt-3" style="padding-left: 25px; padding-right: 25px;">
                                            <p><b><i data-feather="disc"></i>&nbsp;&nbsp;Foto Bukti</b></p>
                                                @include('ph.report-complaint.upload-image')
                                            </div>
                                            <div class="col-12 mt-2">
                                                <div class="float-right">
                                                    <button id="submitForm" type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Ubah</button>
                                                    <a href="{{ route('ph.report-complaint.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                                                </div>
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

                            $('#symptoms').select2({
                                placeholder: "Pilih Gejala Klinis",
                                ajax: {
                                    url: '{{ route("ph.symptom.search") }}', 
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
                            })

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
                                    let qryCullingPic = companyID ? `?company_id=${companyID}&role_name=Manager+Area`: '';
                                    qryCullingPic = qryCullingPic.length>0&&areaId? `${qryCullingPic}&area_id=${areaId}`:'';
                                    
                                    $('#culling_pic').select2({
                                        placeholder: "Pilih Manager Area",
                                        ajax: {
                                            url: `{{ route("user-management.user.search") }}${qryCullingPic}`, 
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
                                // $('.hatchery').val(null).trigger('change');
                                var supplierId = $(this).val();
                                var qryHatchery = supplierId?`?supplier_id=${supplierId}`:'';
                                
                                $('.hatchery').select2({
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

                            var oldValueProduct = "{{ $data->product_id }}";
                            if (oldValueProduct) {
                                var oldNameProduct = "{{ $data->product->name??'' }}";
                                if (oldNameProduct) {
                                    var newOptionProduct = new Option(oldNameProduct, oldValueProduct, true, true);
                                    $('#product_id').append(newOptionProduct).trigger('change');
                                }
                            }

                            var oldValueCompany = "{{ $data->kandang->company_id }}";
                            if (oldValueCompany) {
                                var oldNameCopmany = "{{ $data->kandang->company->name??'' }}";
                                if (oldNameCopmany) {
                                    var newOption = new Option(oldNameCopmany, oldValueCompany, true, true);
                                    $('#company_id').append(newOption).trigger('change');
                                }
                            }

                            var oldValueArea = "{{ $data->kandang->location->area_id }}";
                            if (oldValueArea) {
                                var oldNameArea = "{{ $data->kandang->location->area->name??'' }}";
                                if (oldNameArea) {
                                    var newOptionLoc = new Option(oldNameArea, oldValueArea, true, true);
                                    $('#area_id').append(newOptionLoc).trigger('change');
                                }
                            }

                            var oldValueLoc = "{{ $data->kandang->location_id }}";
                            if (oldValueLoc) {
                                var oldNameLoc = "{{ $data->kandang->location->name??'' }}";
                                if (oldNameLoc) {
                                    var newOptionLoc = new Option(oldNameLoc, oldValueLoc, true, true);
                                    $('#location_id').append(newOptionLoc).trigger('change');
                                }
                            }

                            var oldValue = "{{ $data->supplier_id }}";
                            if (oldValue) {
                                var oldName = "{{ $data->supplier->name??'' }}";
                                if (oldName) {
                                    var newOption = new Option(oldName, oldValue, true, true);
                                    $('#supplier_id').append(newOption).trigger('change');
                                }
                            }

                            var oldValueKandang = "{{ $data->kandang_id }}";
                            if (oldValueKandang) {
                                var oldNameKandang = "{{ $data->kandang->name??'' }}";
                                if (oldNameKandang) {
                                    var newOption = new Option(oldNameKandang, oldValueKandang, true, true);
                                    $('#kandang_id').append(newOption).trigger('change');
                                }
                            }

                            var oldValueCullingPic = "{{ $data->culling_pic }}";
                            if (oldValueCullingPic) {
                                var oldNameCullingPic = "{{ $data->cullingpic->name??'' }}";
                                if (oldNameCullingPic) {
                                    var newOptionCullingPic = new Option(oldNameCullingPic, oldValueCullingPic, true, true);
                                    $('#culling_pic').append(newOptionCullingPic).trigger('change');
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

                            @if ($errors->has('area_id'))
                                $('#area_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('culling_pic'))
                                $('#culling_pic').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('location_id'))
                                $('#location_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('symptoms'))
                                $('#symptoms').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('investigation_date'))
                                $('#investigation_date').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif
                        });
                    </script>
@endsection