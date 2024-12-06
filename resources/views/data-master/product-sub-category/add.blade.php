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
                                    <form class="form form-horizontal" method="post" action="{{ route('data-master.product-sub-category.add') }}">
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
                                                        <label for="product_category_id" class="float-right">Kategori Produk</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select name="product_category_id" id="product_category_id" class="form-control">
                                                            @if(old('product_category_id') && old('product_category_name'))
                                                                <option value="{{ old('product_category_id') }}" selected="selected">{{ old('product_category_code').' - '.old('product_category_name') }}</option>
                                                            @endif
                                                        </select>
                                                        @if ($errors->has('product_category_id'))
                                                            <span class="text-danger small">{{ $errors->first('product_category_id') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 offset-sm-3">
                                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Simpan</button>
                                                <a href="{{ route('data-master.product-sub-category.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
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

                            var oldValue = "{{ old('product_category_id') }}";
                            if (oldValue) {
                                var oldName = "{{ old('product_category_code').' - '.old('product_category_name') }}";
                                if (oldName) {
                                    var newOption = new Option(oldName, oldValue, true, true);
                                    $('#product_category_id').append(newOption).trigger('change');
                                }
                            }

                            @if ($errors->has('product_category_id'))
                                $('#product_category_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif
                        });
                    </script>
@endsection