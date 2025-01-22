@extends('templates.main')
@section('title', $title)
@section('content')
@php
    $dataFcr = '';
    if (isset($data)) {
        if (isset($data->fcr_standard)) {
            $dataFcr = $data->fcr_standard;
        }
    }
@endphp

<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{$title}}</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form form-horizontal" method="post" action="{{ route('data-master.fcr.edit', $data->fcr_id) }}">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group row">
                                                            <div class="col-sm-3 col-form-label">
                                                                <label for="company_id" class="float-right">Unit Bisnis</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <select name="company_id" id="company_id" class="form-control">
                                                                    @if($data->company_id && $data->company->name??false)
                                                                        <option value="{{ $data->company_id }}" selected="selected">{{ $data->company->name??'' }}</option>
                                                                    @endif
                                                                </select>
                                                                @if ($errors->has('company_id'))
                                                                    <span class="text-danger small">{{ $errors->first('company_id') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group row">
                                                            <div class="col-sm-3 col-form-label">
                                                                <label for="name" class="float-right">Nama FCR</label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="text" id="name" class="{{$errors->has('name')?'is-invalid':''}} form-control" name="name" placeholder="Nama FCR" value="{{ $data->name }}">
                                                                @if ($errors->has('name'))
                                                                    <span class="text-danger small">{{ $errors->first('name') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2 mb-4">
                                                <h4><li>Standar FCR (gram)</li></h4>
                                                <div class="table-responsive">
                                                    @if ($errors->has('fcr_standard'))
                                                        <span class="text-danger small">{{ $errors->first('fcr_standard') }}</span>
                                                    @endif
                                                    <table class="table table-bordered w-100 no-wrap text-center" id="fcr-standard">
                                                        <thead>
                                                            <tr>
                                                                <th rowspan="2">Umur<br>(Hari)</th>
                                                                <th rowspan="2">Bobot</th>
                                                                <th colspan="2">Peningkatan</th>
                                                                <th colspan="2">Asupan</th>
                                                                <th rowspan="2">FCR</th>
                                                                <th rowspan="2">
                                                                    <button class="btn btn-sm btn-icon btn-primary" type="button" id="add-btn" data-repeater-create title="Tambah Item">
                                                                        <i data-feather="plus"></i>
                                                                    </button>
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <th>Harian</th>
                                                                <th>Rata - rata</th>
                                                                <th>Harian</th>
                                                                <th>Kumulatif</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody data-repeater-list="fcr_standard">
                                                            <tr data-repeater-item>
                                                                <td><input type="text" class="form-control numeral-mask" name="day" placeholder="1234" required></td>
                                                                <td><input type="text" class="form-control numeral-mask" name="weight" placeholder="1234" required></td>
                                                                <td><input type="text" class="form-control numeral-mask" name="daily_gain" placeholder="1234"></td>
                                                                <td><input type="text" class="form-control numeral-mask" name="avg_daily_gain" placeholder="1234"></td>
                                                                <td><input type="text" class="form-control numeral-mask" name="daily_intake" placeholder="1234"></td>
                                                                <td><input type="text" class="form-control numeral-mask" name="cum_intake" placeholder="1234"></td>
                                                                <td><input type="text" class="form-control numeral-mask" name="fcr" placeholder="1234" required></td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Item">
                                                                        <i data-feather="x"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12">
                                                <div class="text-center">
                                                    <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Ubah</button>
                                                    <a href="{{ route('data-master.fcr.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
                    <script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>

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

                            var oldValuecompany = "{{ old('company_id') }}";
                            if (oldValuecompany) {
                                var oldNamecompany = "{{ old('company_name') }}";
                                if (oldNamecompany) {
                                    var newOptioncompany = new Option(oldNamecompany, oldValuecompany, true, true);
                                    $('#company_id').append(newOptioncompany).trigger('change');
                                }
                            }

                            @if ($errors->has('company_id'))
                                $('#company_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            const optFcr = {
                                initEmpty: true,
                                show: function () {
                                    $this = $(this);
                                    $this.slideDown();
                                    // Feather Icons
                                    if (feather) {
                                        feather.replace({ width: 14, height: 14 });
                                    }

                                    var numeralMask = $('.numeral-mask');
                                    if (numeralMask.length) {
                                        numeralMask.each(function() { 
                                            new Cleave(this, {
                                                numeral: true,
                                                numeralThousandsGroupStyle: 'thousand', 
                                                numeralDecimalMark: ',', 
                                                delimiter: '.',
                                                numeralDecimalScale: 3,
                                                numeralAllowLeadingZero: true,
                                            });
                                        })
                                    }
                                    
                                },
                                hide: function (deleteElement) {
                                    if (confirm('Apakah kamu yakin ingin menghapus data ini?')) {
                                        $(this).slideUp(deleteElement);
                                    }
                                }
                            };

                            const $itemRepeater = $('#fcr-standard').repeater(optFcr);
                            if ('{{ $dataFcr }}'.length) {
                                const dataEdit = @json($dataFcr);
                                console.log(dataEdit);
                                
                                if (dataEdit) {
                                    dataEdit.forEach(element => {
                                        element.fcr = element.fcr.replace('.', ',')
                                    });
                                    $itemRepeater.setList(dataEdit);
                                } 
                            } else {
                                $('#add-btn').trigger('click');
                            }

                        });
                    </script>
@endsection