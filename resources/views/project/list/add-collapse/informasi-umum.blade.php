@php
    $company_id = old('company_id');
    $company_name = old('company_name');
    $area_id = old('area_id');
    $area_name = old('area_name');
    $location_id = old('location_id');
    $location_name = old('location_name');
    $product_category_id = old('product_category_id');
    $product_category_name = old('product_category_name');
    $period = old('period');
    $farm_type = old('farm_type');
    $fcr_id = old('fcr_id');
    $fcr_name = old('fcr_name');
    $mortality = old('standard_mortality');

    if (isset($data)) {
        $company_id = $data->kandang->company_id??'';
        $company_name = $data->kandang->company->name??'';
        $area_id = $data->kandang->location->area_id??'';
        $area_name = $data->kandang->location->area->name??'';
        $location_id = $data->kandang->location_id??'';
        $location_name = $data->kandang->location->name??'';
        $product_category_id = $data->product_category_id??'';
        $product_category_name = $data->product_category->name??'';
        $period = $data->period??"";
        $farm_type = $data->farm_type??"";
        $fcr_id = $data->fcr_id;
        $fcr_name = $data->fcr->name??"";
        $mortality = $data->standard_mortality;
    } 

@endphp

<div class="card mb-1">
    <div id="headingCollapse1" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
        <span class="lead collapse-title"> Informasi  Umum </span>
    </div>
    <div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-3 col-form-label">
                                <label for="company_id" class="float-right">Unit Bisnis</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="company_id" id="company_id" class="form-control" required>
                                    @if($company_id && $company_name)
                                        <option value="{{ $company_id }}" selected="selected">{{ $company_name }}</option>
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
                                <select name="area_id" id="area_id" class="form-control" required>
                                    <option disabled selected>Pilih Unit Bisnis terlebih dahulu</option>
                                    @if($area_id && $area_name)
                                        <option value="{{ $area_id }}" selected="selected">{{ $area_name }}</option>
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
                                <select name="location_id" id="location_id" class="form-control" required>
                                    <option disabled selected>Pilih Area terlebih dahulu</option>
                                    @if($location_id && $location_name)
                                    <option value="{{ $location_id }}" selected="selected">{{ $location_name }}</option>
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
                                <label for="product_category_id" class="float-right">Kategori Produk</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="product_category_id" id="product_category_id" class="form-control" required>
                                    <option disabled selected>Pilih Unit Bisnis terlebih dahulu</option>
                                    @if($product_category_id && $product_category_name)
                                    <option value="{{ $product_category_id }}" selected="selected">{{ $product_category_name }}</option>
                                    @endif
                                </select>
                                @if ($errors->has('product_category_id'))
                                <span class="text-danger small">{{ $errors->first('product_category_id') }}</span>
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
                                <label for="period" class="float-right">Periode</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="number" id="period" class="{{$errors->has('period')?'is-invalid':''}} form-control" placeholder="Periode" value="{{ $period }}" readonly>
                                <input type="hidden" name="period" value="{{ $period }}">
                                @if ($errors->has('period'))
                                    <span class="text-danger small">{{ $errors->first('period') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-3 col-form-label">
                                <label for="farm_type" class="float-right">Jenis Farm</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="farm_type" id="farm_type" class="{{$errors->has('farm_type')?'is-invalid':''}} form-control" required>
                                    @foreach ($type as $key => $item)
                                        <option value="{{$key}}" {{$farm_type==$key?'selected':''}}>{{$item}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('farm_type'))
                                    <span class="text-danger small">{{ $errors->first('farm_type') }}</span>
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
                                <label for="fcr_id" class="float-right">Standar FCR</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="fcr_id" id="fcr_id" class="form-control" required>
                                    <option disabled selected>Pilih Unit Bisnis terlebih dahulu</option>
                                    @if($fcr_id && $fcr_name)
                                    <option value="{{ $fcr_id }}" selected="selected">{{ $fcr_name }}</option>
                                    @endif
                                </select>
                                @if ($errors->has('fcr_id'))
                                <span class="text-danger small">{{ $errors->first('fcr_id') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-3 col-form-label">
                                <label for="standard_mortality" class="float-right">Standar Mortalitas</label>
                            </div>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" name="standard_mortality" class="form-control numeral-mask" value="{{ $mortality }}" placeholder="1234" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                </div>
                                @if ($errors->has('standard_mortality'))
                                <span class="text-danger small">{{ $errors->first('standard_mortality') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
            $('#fcr_id').val(null).trigger('change');
            $('#product_category_id').val(null).trigger('change');
            var companyId = $('#company_id').val();

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

            $('#fcr_id').select2({
                placeholder: "Pilih Standar FCR",
                ajax: {
                    url: `{{ route("data-master.fcr.search") }}?company_id=${companyId}`, 
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

            const qryProduct = companyId?`?company_id=${companyId}`:'';

            $('#area_id').change(function (e) { 
                e.preventDefault();
                $('#location_id').val(null).trigger('change');
                var areaId = $('#area_id').val();
                var qryLocation = areaId&&companyId?`?company_id=${companyId}&area_id=${areaId}`:'';

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
            });

        });

        $('#product_category_id').select2({
            placeholder: "Pilih Kategori Produk",
            ajax: {
                url: `{{ route("data-master.product-category.search") }}`, 
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

        var oldValueCompany = "{{ $company_id }}";
        if (oldValueCompany) {
            var oldNameCopmany = "{{ $company_name }}";
            if (oldNameCopmany) {
                var newOption = new Option(oldNameCopmany, oldValueCompany, true, true);
                $('#company_id').append(newOption).trigger('change');
            }
        }

        var oldValueArea = "{{ $area_id }}";
        if (oldValueArea) {
            var oldNameArea = "{{ $area_name }}";
            if (oldNameArea) {
                var newOptionLoc = new Option(oldNameArea, oldValueArea, true, true);
                $('#area_id').append(newOptionLoc).trigger('change');
            }
        }

        var oldValueLoc = "{{ $location_id }}";
        if (oldValueLoc) {
            var oldNameLoc = "{{ $location_name }}";
            if (oldNameLoc) {
                var newOptionLoc = new Option(oldNameLoc, oldValueLoc, true, true);
                $('#location_id').append(newOptionLoc).trigger('change');
            }
        }

        var oldValueProduct = "{{ $product_category_id }}";
        if (oldValueProduct) {
            var oldNameProduct = "{{ $product_category_name }}";
            if (oldNameProduct) {
                var newOptionProduct = new Option(oldNameProduct, oldValueProduct, true, true);
                $('#product_category_id').append(newOptionProduct).trigger('change');
            }
        }

        @if ($errors->has('product_category_id'))
            $('#product_category_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
        @endif

        @if ($errors->has('company_id'))
            $('#company_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
        @endif

        @if ($errors->has('area_id'))
            $('#area_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
        @endif

        @if ($errors->has('location_id'))
            $('#location_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
        @endif

    });
</script>