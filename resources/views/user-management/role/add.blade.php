@extends('templates.main')
@section('title', $title)
@section('content')
<link rel="stylesheet" href="{{ asset('app-assets/js/scripts/checkbox-tree/checkbox-tree.css') }}">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{$title}}</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form form-horizontal" method="post" action="{{ route('user-management.role.add') }}">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="name" class="float-right">Nama Role</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="name" class="{{$errors->has('name')?'is-invalid':''}} form-control" name="name" placeholder="Nama Role" value="{{ old('name') }}">
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
                                                        <label for="cakupan" class="float-right">Cakupan</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" name="all_area" id="customCheck1" {{ old('all_area')?'checked':'' }}>
                                                            <label class="custom-control-label" for="customCheck1">Semua Area</label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox mt-1">
                                                            <input type="checkbox" class="custom-control-input" name="all_location" id="customCheck2" {{ old('all_location')?'checked':'' }}>
                                                            <label class="custom-control-label" for="customCheck2">Semua Lokasi</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="company_id" class="float-right">Modul</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        @php
                                                        if (!function_exists('generateHtml')) {
                                                            function generateHtml($tree, $prefix = '') {
                                                                $html = '<ul class="tree">';
                                                                $oldModul = old('permission')?array_keys(old('permission')):[];
                                                                foreach ($tree as $key => $subtree) {
                                                                    $currentName = $prefix ? $prefix . '.' . $key : $key;
                                                                    $html .= '<li>';
                                                                    $checked = in_array($currentName, $oldModul) ? 'checked' : '';
                                                                    $label = str_replace(['-', '.'], ' ', $key);
                                                                    $label = ucwords($label);
                                                                    $label = $label==="Index"?"List":$label;
                                                                    $label = $label==="Ph"?"Poultry Health":$label;
                                                                    $label = $label==="Symptom"?"Gejala Klinis":$label;
                                                                    $label = $label==="Warehouse"?"Gudang":$label;
                                                                    if (!empty($subtree)) {
                                                                        $html .= "<label>{$label}</label> <input type='checkbox'>";
                                                                        $html .= generateHtml($subtree, $currentName);
                                                                    } else {
                                                                        $html .= "{$label} <input type='checkbox' name='permission[{$currentName}]' {$checked}>";
                                                                    }
    
                                                                    $html .= '</li>';
                                                                }
    
                                                                $html .= '</ul>';
                                                                return $html;
                                                            }
                                                        }
                                                        @endphp

                                                        {!! generateHtml($modul) !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 offset-sm-3">
                                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Simpan</button>
                                                <a href="{{ route('user-management.role.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
                    <script src="{{asset('app-assets/js/scripts/checkbox-tree/checkbox-tree.js')}}"></script>
                    <script>
                        $(document).ready(function() {
                            $('ul.tree').checkTree();
                            $('input[type="checkbox"]').each(function() {
                                var $checkboxInput = $(this);
                                var $checkboxDiv = $checkboxInput.prev('.checkbox'); 

                                if ($checkboxInput.is(':checked')) {
                                    $checkboxDiv.trigger('click');
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

                            var oldValue = "{{ old('company_id') }}";
                            if (oldValue) {
                                var oldName = "{{ old('company_name') }}";
                                if (oldName) {
                                    var newOption = new Option(oldName, oldValue, true, true);
                                    $('#company_id').append(newOption).trigger('change');
                                }
                            }

                            @if ($errors->has('company_id'))
                                $('#company_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif
                        });
                    </script>
@endsection