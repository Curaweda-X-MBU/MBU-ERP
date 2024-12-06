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
                                    <form class="form form-horizontal" method="post" action="{{ route('data-master.product-component.edit', $data->product_component_id) }}">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="name" class="float-right">Nama</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="name" class="{{$errors->has('name')?'is-invalid':''}} form-control" name="name" placeholder="Nama" value="{{ $data->name }}">
                                                        @if ($errors->has('name'))
                                                            <span class="text-danger small">{{ $errors->first('name') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="supplier_id" class="float-right">Pemasok</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select name="supplier_id" id="supplier_id" class="form-control">
                                                            @if($data->supplier_id && ($data->supplier->name??false))
                                                                <option value="{{ $data->supplier_id }}" selected="selected">{{ $data->supplier->name??'' }}</option>
                                                            @endif
                                                        </select>
                                                        @if ($errors->has('supplier_id'))
                                                            <span class="text-danger small">{{ $errors->first('supplier_id') }}</span>
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
                                                        <input type="text" id="brand" class="{{$errors->has('brand')?'is-invalid':''}} form-control" name="brand" placeholder="Merek" value="{{ $data->brand }}">
                                                        @if ($errors->has('brand'))
                                                            <span class="text-danger small">{{ $errors->first('brand') }}</span>
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
                                                            @if($data->uom_id && ($data->uom->name??false))
                                                                <option value="{{ $data->uom_id }}" selected="selected">{{ $data->uom_name??'' }}</option>
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
                                                        <label for="price" class="float-right">Harga</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1">Rp.</span>
                                                            </div>
                                                            <input type="text" class="{{$errors->has('price')?'is-invalid':''}} form-control numeral-mask" name="price" placeholder="Harga" aria-label="Harga" aria-describedby="basic-addon1" value="{{$data->price}}">
                                                        </div>
                                                        @if ($errors->has('price'))
                                                            <span class="text-danger small">{{ $errors->first('price') }}</span>
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
                                                            <input type="number" class="{{$errors->has('tax')?'is-invalid':''}} form-control" name="tax" placeholder="Periode Kadaluarsa" aria-label="Periode Kadaluarsa" aria-describedby="basic-addon2" value="{{$data->tax}}">
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
                                                            <input type="number" class="{{$errors->has('expiry_period')?'is-invalid':''}} form-control" name="expiry_period" placeholder="Periode Kadaluarsa" aria-label="Periode Kadaluarsa" aria-describedby="basic-addon2" value="{{$data->expiry_period}}">
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
                                            <div class="col-sm-9 offset-sm-3">
                                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Ubah</button>
                                                <a href="{{ route('data-master.product-component.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
                    <script src="{{asset("app-assets/js/scripts/forms/form-input-mask.js")}}"></script>
                    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
                    <script>
                        $(document).ready(function() {
                            $('#supplier_id').select2({
                                placeholder: "Pilih Pemasok",
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

                            var oldValue = "{{ $data->supplier_id }}";
                            if (oldValue) {
                                var oldName = "{{ $data->supplier->name??'' }}";
                                if (oldName) {
                                    var newOption = new Option(oldName, oldValue, true, true);
                                    $('#supplier_id').append(newOption).trigger('change');
                                }
                            }

                            var oldValueUom = "{{ $data->uom_id }}";
                            if (oldValueUom) {
                                var oldNameUom = "{{ $data->uom->name??'' }}";
                                if (oldNameUom) {
                                    var newOption = new Option(oldNameUom, oldValueUom, true, true);
                                    $('#uom_id').append(newOption).trigger('change');
                                }
                            }

                            @if ($errors->has('supplier_id'))
                                $('#supplier_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            @if ($errors->has('uom_id'))
                                $('#uom_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif
                        });
                    </script>

@endsection