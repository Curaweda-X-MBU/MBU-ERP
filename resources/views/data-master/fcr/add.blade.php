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
                                    <form class="form form-horizontal" method="post" action="{{ route('data-master.fcr.add') }}">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="name" class="float-right">Nama FCR</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="name" class="{{$errors->has('name')?'is-invalid':''}} form-control" name="name" placeholder="Nama FCR" value="{{ old('name') }}">
                                                        @if ($errors->has('name'))
                                                            <span class="text-danger small">{{ $errors->first('name') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="value" class="float-right">Nilai</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="number" step="0.01" id="value" class="{{$errors->has('value')?'is-invalid':''}} form-control" name="value" placeholder="Nilai" value="{{ old('value') }}">
                                                        @if ($errors->has('value'))
                                                            <span class="text-danger small">{{ $errors->first('value') }}</span>
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
                                            <div class="col-sm-9 offset-sm-3">
                                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Simpan</button>
                                                <a href="{{ route('data-master.fcr.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
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

                            var oldValueProduct = "{{ old('product_id') }}";
                            if (oldValueProduct) {
                                var oldNameProduct = "{{ old('product_name') }}";
                                if (oldNameProduct) {
                                    var newOptionProduct = new Option(oldNameProduct, oldValueProduct, true, true);
                                    $('#product_id').append(newOptionProduct).trigger('change');
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

                            @if ($errors->has('product_id'))
                                $('#product_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('uom_id'))
                                $('#uom_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif
                        });
                    </script>
@endsection