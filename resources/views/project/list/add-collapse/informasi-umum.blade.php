@php
    $company_id = old('company_id');
    $company_name = old('company_name');
    $area_id = old('area_id');
    $area_name = old('area_name');
    $location_id = old('location_id');
    $location_name = old('location_name');
    $product_id = old('product_id');
    $product_name = old('product_name');

    if (isset($data)) {
        $company_id = $data->kandang->company_id??'';
        $company_name = $data->kandang->company->name??'';
        $area_id = $data->kandang->location->area_id??'';
        $area_name = $data->kandang->location->area->name??'';
        $location_id = $data->kandang->location_id??'';
        $location_name = $data->kandang->location->name??'';
        $product_id = $data->product_id??'';
        $product_name = $data->product->name??'';
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
                                <select name="company_id" id="company_id" class="form-control">
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
                                <select name="area_id" id="area_id" class="form-control">
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
                                <select name="location_id" id="location_id" class="form-control">
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
                                <label for="product_id" class="float-right">Produk</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="product_id" id="product_id" class="form-control">
                                    <option disabled selected>Pilih Unit Bisnis terlebih dahulu</option>
                                    @if($product_id && $product_name)
                                        <option value="{{ $product_id }}" selected="selected">{{ $product_name }}</option>
                                    @endif
                                </select>
                                @if ($errors->has('product_id'))
                                    <span class="text-danger small">{{ $errors->first('product_id') }}</span>
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
            $('#product_id').val(null).trigger('change');
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

            const qryProduct = companyId?`?company_id=${companyId}`:'';

            $('#product_id').select2({
                placeholder: "Pilih Produk",
                ajax: {
                    url: `{{ route("data-master.product.search") }}${qryProduct}`, 
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

        var oldValueProduct = "{{ $product_id }}";
        if (oldValueProduct) {
            var oldNameProduct = "{{ $product_name }}";
            if (oldNameProduct) {
                var newOptionProduct = new Option(oldNameProduct, oldValueProduct, true, true);
                $('#product_id').append(newOptionProduct).trigger('change');
            }
        }

        @if ($errors->has('product_id'))
            $('#product_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
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