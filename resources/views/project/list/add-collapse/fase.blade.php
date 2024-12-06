@php
    $fcr_id = old('fcr_id');
    $fcr_name = old('fcr_name');
    $target_depletion = old('target_depletion');
    $dataPhase = '';
    if (isset($data)) {
        $fcr_id = $data->fcr->fcr_id??"";
        $fcr_name = $data->fcr->name.' - '.$data->fcr->value.' '.$data->fcr->uom->name??"";
        $target_depletion = $data->target_depletion??"";
    
        if (isset($data->project_phase)) {
            $dataPhase = $data->project_phase;
        }
    }
@endphp

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">

<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>

<div class="card mb-1">
    <div id="headingCollapse3" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
        <span class="lead collapse-title"> Fase </span>
    </div>
    <div id="collapse3" role="tabpanel" aria-labelledby="headingCollapse3" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-3 col-form-label">
                                <label for="fcr_id" class="float-right">Target FCR</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="fcr_id" id="fcr_id" class="form-control {{$errors->has('fcr_id')?'is-invalid':''}}">
                                    <option disabled selected>Pilih Produk terlebih dahulu</option>
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
                                <label for="target_depletion" class="float-right">Target Deplesi</label>
                            </div>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="number" id="target_depletion" class="{{$errors->has('target_depletion')?'is-invalid':''}} form-control" name="target_depletion" placeholder="Target Deplesi" value="{{ $target_depletion }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                </div>
                                @if ($errors->has('target_depletion'))
                                    <span class="text-danger small">{{ $errors->first('target_depletion') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered w-100 no-wrap text-center" id="fase-repeater-1">
                        <thead>
                            <th>Fase</th>
                            <th>Estimasi Tgl. Mulai</th>
                            <th>Estimasi Tgl. Selesai</th>
                            <th>Status Fase</th>
                            <th colspan="2">
                                <button class="btn btn-sm btn-icon btn-primary" type="button" data-repeater-create title="Tambah Fase">
                                    <i data-feather="plus"></i>
                                </button>
                            </th>
                        </thead>
                        <tbody data-repeater-list="phase">
                            <tr data-repeater-item>
                                <td><input type="text" name="name" class="form-control" aria-describedby="phase" placeholder="Fase" required/></td>
                                <td><input type="text" name="start_date_estimate" class="form-control flatpickr-basic" aria-describedby="start_date_estimate" placeholder="Estimasi Tanggal Mulai" required/></td>
                                <td><input type="text" name="end_date_estimate" class="form-control flatpickr-basic" aria-describedby="end_date_estimate" placeholder="Estimasi Tanggal Selesai" required/></td>
                                <td><div class="badge badge-pill badge-warning">Belum Mulai</div></td>
                                <td>
                                    <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Fase">
                                        <i data-feather="x"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#product_id').change(function (e) { 
            $('#fcr_id').val(null).trigger('change');
            var productId = $('#product_id').val();
            var qryParam = productId?`?product_id=${productId}`:'';

            $('#fcr_id').select2({
                placeholder: "Pilih FCR",
                ajax: {
                    url: `{{ route("data-master.fcr.search") }}${qryParam}`, 
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
        $('#product_id').trigger('change');

        var oldValueFcr = "{{ $fcr_id }}";
        if (oldValueFcr) {
            var oldNameFcr = "{{ $fcr_name }}";
            if (oldNameFcr) {
                var newOption = new Option(oldNameFcr, oldValueFcr, true, true);
                $('#fcr_id').append(newOption).trigger('change');
            }
        }

        @if ($errors->has('fcr_id'))
            $('#fcr_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
        @endif

        const dateOpt = { dateFormat: 'd-M-Y' }
        $('.flatpickr-basic').flatpickr(dateOpt);

        const optFase = {
            show: function () {
                $(this).slideDown();
                // Feather Icons
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }

                const dateOpt = { dateFormat: 'd-M-Y' }
                $('.flatpickr-basic').flatpickr(dateOpt);
            },
            hide: function (deleteElement) {
                if (confirm('Apakah kamu yakin ingin menghapus data ini?')) {
                    $(this).slideUp(deleteElement);
                }
            }
        };

        const $faseRepeater = $('#fase-repeater-1').repeater(optFase);
        const oldPhase = @json(old("phase"));
        if (oldPhase) {
            $faseRepeater.setList(oldPhase);
        } 

        if ('{{ $dataPhase }}'.length) {
            const dataPhase = @json($dataPhase);
            if (dataPhase) {
                $faseRepeater.setList(dataPhase);
            }
        } 
    });
</script>