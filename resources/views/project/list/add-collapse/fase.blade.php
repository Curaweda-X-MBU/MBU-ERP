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
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#fcr_id').select2({
            placeholder: "Pilih FCR",
            ajax: {
                url: `{{ route("data-master.fcr.search") }}`, 
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
    });
</script>