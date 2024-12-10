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
                                    <form class="form form-horizontal" method="post" action="{{ route('inventory.adjustment.add') }}">
                                        {{ csrf_field() }}
                                        <div class="row">
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
                                                        @if ($errors->has('product_id'))
                                                            <span class="text-danger small">{{ $errors->first('product_category_id') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="product_id" class="float-right">Produk</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select name="product_id" id="product_id" class="form-control">
                                                            <option disabled selected>Pilih kategorti produk terlebih dahulu</option>
                                                            @if(old('product_id') && old('product_name'))
                                                                <option value="{{ old('product_id') }}" selected="selected">{{ old('product_name') }}</option>
                                                            @endif
                                                        </select>
                                                        @if ($errors->has('product_id'))
                                                            <span class="text-danger small">{{ $errors->first('product_id') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="warehouse_id" class="float-right">Gudang / Tempat Penyimpanan</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select name="warehouse_id" id="warehouse_id" class="form-control">
                                                            @if(old('warehouse_id') && old('warehouse_name'))
                                                                <option value="{{ old('warehouse_id') }}" selected="selected">{{ old('warehouse_name') }}</option>
                                                            @endif
                                                        </select>
                                                        @if ($errors->has('warehouse_id'))
                                                            <span class="text-danger small">{{ $errors->first('warehouse_id') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="increase" class="float-right">Penambahan Stok</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="increase" class="{{$errors->has('increase')?'is-invalid':''}} form-control numeral-mask" name="increase" placeholder="Penambahan Stok" value="{{ old('increase')??0 }}">
                                                        @if ($errors->has('increase'))
                                                            <span class="text-danger small">{{ $errors->first('increase') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="decrease" class="float-right">Pengurangan Stok</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="decrease" class="{{$errors->has('decrease')?'is-invalid':''}} form-control numeral-mask" name="decrease" placeholder="Pengurangan Stok" value="{{ old('decrease')??0 }}">
                                                        @if ($errors->has('decrease'))
                                                            <span class="text-danger small">{{ $errors->first('decrease') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="notes" class="float-right">Catatan</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <textarea name="notes" class="{{$errors->has('notes')?'is-invalid':''}} form-control" placeholder="Optional">{{ old('notes') }}</textarea>
                                                        @if ($errors->has('notes'))
                                                            <span class="text-danger small">{{ $errors->first('notes') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 offset-sm-3">
                                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Simpan</button>
                                                <a href="{{ route('inventory.adjustment.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
                    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
                    <script>
                        $(document).ready(function() {
                            var numeralMask = $('.numeral-mask');
                            if (numeralMask.length) {
                                numeralMask.each(function() { 
                                    new Cleave(this, {
                                        numeral: true,
                                        numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                                    });
                                })
                            }

                            $('#product_category_id').select2({
                                placeholder: "Pilih Kategoti Produk",
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
                                const prodCatId = $(this).val();
                                $('#product_id').val(null).trigger('change');

                                $('#product_id').select2({
                                    placeholder: "Pilih Produk",
                                    ajax: {
                                        url: `{{ route("data-master.product.search") }}?product_category_id=${prodCatId}`, 
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


                            $('#warehouse_id').select2({
                                placeholder: "Pilih Gudang",
                                ajax: {
                                    url: '{{ route("data-master.warehouse.search") }}', 
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

                            var oldValueproduct = "{{ old('product_id') }}";
                            if (oldValueproduct) {
                                var oldNameCopmany = "{{ old('product_name') }}";
                                if (oldNameCopmany) {
                                    var newOption = new Option(oldNameCopmany, oldValueproduct, true, true);
                                    $('#product_id').append(newOption).trigger('change');
                                }
                            }

                            var oldValuewarehouse = "{{ old('warehouse_id') }}";
                            if (oldValuewarehouse) {
                                var oldNamewarehouse = "{{ old('warehouse_name') }}";
                                if (oldNamewarehouse) {
                                    var newOption = new Option(oldNamewarehouse, oldValuewarehouse, true, true);
                                    $('#warehouse_id').append(newOption).trigger('change');
                                }
                            }

                            @if ($errors->has('product_id'))
                                $('#product_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('warehouse_id'))
                                $('#warehouse_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif
                        });
                    </script>
@endsection