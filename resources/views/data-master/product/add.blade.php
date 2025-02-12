@extends('templates.main')
@section('title', $title)
@section('content')
                    <style>
                        .custom-supplier-repeater-grid {
                            display: grid;
                            grid-template-columns: repeat(5, 1fr);
                            grid-template-areas:
                                "select select select select delete"
                                "input input input input delete";
                            column-gap: 1rem;
                            align-items: start;
                            margin-bottom: 0.5rem;
                            border-bottom: solid 1px lightgray;
                        }
                        .custom-supplier-repeater-grid + .custom-supplier-repeater-grid {
                            margin-left: 25%;
                        }
                        .custom-supplier-repeater-grid .supplier-select-group {
                            grid-area: select;
                        }
                        .custom-supplier-repeater-grid .product-price-group {
                            grid-area: input;
                        }
                        .custom-supplier-repeater-grid .delete-button {
                            grid-area: delete;
                            justify-self: center;
                        }
                        .custom-supplier-repeater-grid .transparent {
                            visibility: hidden;
                        }
                    </style>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{$title}}</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form form-horizontal" method="post" action="{{ route('data-master.product.add') }}">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="name" class="float-right">Nama</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="name" class="{{$errors->has('name')?'is-invalid':''}} form-control" name="name" placeholder="Nama" value="{{ old('name') }}">
                                                        @if ($errors->has('name'))
                                                            <span class="text-danger small">{{ $errors->first('name') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="brand" class="float-right">Merek</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="brand" class="{{$errors->has('brand')?'is-invalid':''}} form-control" name="brand" placeholder="Merek" value="{{ old('brand') }}">
                                                        @if ($errors->has('brand'))
                                                            <span class="text-danger small">{{ $errors->first('brand') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="sku" class="float-right">SKU</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="sku" class="{{$errors->has('sku')?'is-invalid':''}} form-control" name="sku" placeholder="SKU" value="{{ old('sku') }}">
                                                        @if ($errors->has('sku'))
                                                            <span class="text-danger small">{{ $errors->first('sku') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="uom_id" class="float-right">UOM</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select name="uom_id" id="uom_id" class="form-control">
                                                            @if(old('uom_id') && old('uom_name'))
                                                                <option value="{{ old('uom_id') }}" selected="selected">{{ old('uom_name') }}</option>
                                                            @endif
                                                        </select>
                                                        @if ($errors->has('uom_id'))
                                                            <span class="text-danger small">{{ $errors->first('uom_id') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="product_category_id" class="float-right">Kategori Produk</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select name="product_category_id" id="product_category_id" class="form-control">
                                                            @if(old('product_category_id') && old('product_category_name'))
                                                                <option value="{{ old('product_category_id') }}" selected="selected">{{ old('product_category_name') }}</option>
                                                            @endif
                                                        </select>
                                                        @if ($errors->has('product_category_id'))
                                                            <span class="text-danger small">{{ $errors->first('product_category_id') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="product_sub_category_id" class="float-right">Kategori Sub Produk</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select name="product_sub_category_id" id="product_sub_category_id" class="form-control">
                                                            <option disabled selected>Pilih kategori produk terlebih dahulu</option>
                                                            @if(old('product_sub_category_id') && old('product_sub_category_name'))
                                                                <option value="{{ old('product_sub_category_id') }}" selected="selected">{{ old('product_sub_category_name') }}</option>
                                                            @endif
                                                        </select>
                                                        @if ($errors->has('product_sub_category_id'))
                                                            <span class="text-danger small">{{ $errors->first('product_sub_category_id') }}</span>
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
                                                        <label for="product_price" class="float-right">Harga Produk</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="product_price">Rp.</span>
                                                            </div>
                                                            <input type="text" class="{{$errors->has('product_price')?'is-invalid':''}} form-control numeral-mask" placeholder="Harga Produk" name="product_price" aria-label="Harga Produk" aria-describedby="product_price" value="{{old('product_price')}}">
                                                        </div>
                                                        @if ($errors->has('product_price'))
                                                            <span class="text-danger small">{{ $errors->first('product_price') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="selling_price" class="float-right">Harga Jual</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="selling_price">Rp.</span>
                                                            </div>
                                                            <input type="text" class="{{$errors->has('selling_price')?'is-invalid':''}} form-control numeral-mask" placeholder="Harga Jual" name="selling_price" aria-label="Harga Jual" aria-describedby="selling_price" value="{{old('selling_price')}}">
                                                        </div>
                                                        @if ($errors->has('selling_price'))
                                                            <span class="text-danger small">{{ $errors->first('selling_price') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="tax" class="float-right">Pajak</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <div class="input-group">
                                                            <input type="number" class="{{$errors->has('tax')?'is-invalid':''}} form-control" name="tax" placeholder="Pajak" aria-label="Pajak" aria-describedby="basic-addon2" value="{{old('tax')}}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text" id="basic-addon2">%</span>
                                                            </div>
                                                        </div>
                                                        @if ($errors->has('tax'))
                                                            <span class="text-danger small">{{ $errors->first('tax') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="expiry_period" class="float-right">Periode Kadaluarsa</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <div class="input-group">
                                                            <input type="number" class="{{$errors->has('expiry_period')?'is-invalid':''}} form-control" name="expiry_period" placeholder="Periode Kadaluarsa" aria-label="Periode Kadaluarsa" aria-describedby="basic-addon2" value="{{old('expiry_period')}}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text" id="basic-addon2">Hari</span>
                                                            </div>
                                                        </div>
                                                        @if ($errors->has('expiry_period'))
                                                            <span class="text-danger small">{{ $errors->first('expiry_period') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-1" id="product-supplier-repeater">
                                                <div class="row" data-repeater-list="product_supplier">
                                                    <div class="col-sm-3">
                                                        <button class="btn btn-sm btn-primary float-right" type="button" data-repeater-create title="Tambah Vendor" disabled>
                                                            Masukan Harga Produk
                                                        </button>
                                                    </div>
                                                    <div class="col-sm-5 custom-supplier-repeater-grid" data-repeater-item>
                                                        <div class="form-group supplier-select-group">
                                                            <div><label for="supplier_id">Supplier</label></div>
                                                            <select name="supplier_id" class="form-control"></select>
                                                        </div>
                                                        <div class="form-group product-price-group">
                                                            <div><label for="product_price">Harga Produk</label></div>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="product_price">Rp.</span>
                                                                </div>
                                                                <input type="text" name="product_price" class="form-control numeral-mask">
                                                            </div>
                                                        </div>
                                                        <div class="delete-button">
                                                            <div><label class="transparent">a</label></div>
                                                            <button class="btn btn-sm btn-icon btn-danger vertical-align-bottom" data-repeater-delete type="button" title="Hapus Vendor">
                                                                <i data-feather="x"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="can_be_sold" class="float-right">Bisa Dijual</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <div class="custom-control custom-switch custom-switch-primary" style="margin-top: 5px;">
                                                            <input type="checkbox" class="custom-control-input" id="canBeSold" name="can_be_sold" checked>
                                                            <label class="custom-control-label" for="canBeSold">
                                                                <span class="switch-icon-left"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
                                                                <span class="switch-icon-right"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span>
                                                            </label>
                                                        </div>
                                                        @if ($errors->has('can_be_sold'))
                                                            <span class="text-danger small">{{ $errors->first('can_be_sold') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="can_be_purchased" class="float-right">Bisa Dibeli</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <div class="custom-control custom-switch custom-switch-primary" style="margin-top: 5px;">
                                                            <input type="checkbox" class="custom-control-input" id="canBePurchased" name="can_be_purchased" checked>
                                                            <label class="custom-control-label" for="canBePurchased">
                                                                <span class="switch-icon-left"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
                                                                <span class="switch-icon-right"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span>
                                                            </label>
                                                        </div>
                                                        @if ($errors->has('can_be_purchased'))
                                                            <span class="text-danger small">{{ $errors->first('can_be_purchased') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="is_active" class="float-right">Aktif</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <div class="custom-control custom-switch custom-switch-primary" style="margin-top: 5px;">
                                                            <input type="checkbox" class="custom-control-input" id="customSwitch10" name="is_active" checked>
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
                                            <div class="col-sm-9 offset-sm-3">
                                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Simpan</button>
                                                <a href="{{ route('data-master.product.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
                    {{-- <script src="{{asset("app-assets/js/scripts/forms/form-input-mask.js")}}"></script> --}}
                    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
                    <script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
                    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
                    <script>
                        $(document).ready(function() {
                            $('input[name="product_price"]').on('change', function() {
                                if ($(this).val().length > 0) {
                                    $('[data-repeater-create]').text('Tambah Vendor').attr('disabled', false);
                                } else {
                                    $('[data-repeater-create]').text('Masukan Harga Produk').attr('disabled', true);
                                }
                            });
                            const supplierIdRoute = "{{ route('data-master.supplier.search') }}";
                            $('#product-supplier-repeater').repeater({
                                initEmpty: true,
                                show: function() {
                                    const $row = $(this);
                                    $row.slideDown();

                                    const defaultProductPrice = $('input[name="product_price"]').val()
                                    $row.find('input').val(defaultProductPrice);

                                    if (feather) {
                                        feather.replace({ width: 14, height: 14 });
                                    }
                                    initSelect2($row.find('select'), 'Pilih Vendor', supplierIdRoute);
                                    initNumeralMask('.numeral-mask');
                                },
                                hide: function(deleteElement) {
                                    confirmDelete($(this), deleteElement);
                                }
                            });

                            var numeralMask = $('.numeral-mask');
                            if (numeralMask.length) {
                                numeralMask.each(function() {
                                new Cleave(this, {
                                    numeral: true,
                                    numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                                });
                                })
                            }

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

                            $('#product_category_id').select2({
                                placeholder: "Pilih Kategori Produk",
                                ajax: {
                                    url: '{{ route("data-master.product-category.search") }}',
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

                            $('#product_category_id').change(function (e) {
                                e.preventDefault();
                                $('#product_sub_category_id').val(null).trigger('change');

                                var productCategoryId = $(this).val();
                                var qryParam = productCategoryId?`?product_category_id=${productCategoryId}`:'';

                                $('#product_sub_category_id').select2({
                                    placeholder: "Pilih Kategori Sub Produk",
                                    ajax: {
                                        url: `{{ route("data-master.product-sub-category.search") }}${qryParam}`,
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

                            $('#uom_id').select2({
                                placeholder: "Pilih UOM",
                                ajax: {
                                    url: '{{ route("data-master.uom.search") }}',
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

                            var oldValue = "{{ old('company_id') }}";
                            if (oldValue) {
                                var oldName = "{{ old('company_name') }}";
                                if (oldName) {
                                    var newOption = new Option(oldName, oldValue, true, true);
                                    $('#company_id').append(newOption).trigger('change');
                                }
                            }

                            var oldValueProductCategory = "{{ old('product_category_id') }}";
                            if (oldValueProductCategory) {
                                var oldNameProductCategory = "{{ old('product_category_name') }}";
                                if (oldNameProductCategory) {
                                    var newOption = new Option(oldNameProductCategory, oldValueProductCategory, true, true);
                                    $('#product_category_id').append(newOption).trigger('change');
                                }
                            }

                            var oldValueProductSubCategory = "{{ old('product_sub_category_id') }}";
                            if (oldValueProductSubCategory) {
                                var oldNameProductSubCategory = "{{ old('product_sub_category_name') }}";
                                if (oldNameProductSubCategory) {
                                    var newOption = new Option(oldNameProductSubCategory, oldValueProductSubCategory, true, true);
                                    $('#product_sub_category_id').append(newOption).trigger('change');
                                }
                            }

                            var oldValueUom = "{{ old('uom_id') }}";
                            if (oldValueUom) {
                                var oldNameUom = "{{ old('uom_name') }}";
                                if (oldNameUom) {
                                    var newOption = new Option(oldNameUom, oldValueUom, true, true);
                                    $('#uom_id').append(newOption).trigger('change');
                                }
                            }

                            @if ($errors->has('company_id'))
                                $('#company_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('product_category_id'))
                                $('#product_category_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('product_sub_category_id'))
                                $('#product_sub_category_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('uom_id'))
                                $('#uom_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif
                        });
                    </script>
@endsection
