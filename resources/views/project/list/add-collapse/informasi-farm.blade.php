@php
    $kandang_id = old('kandang_id');
    $kandang_name = old('kandang_name');
    $capacity = old('capacity');
    $farm_type = old('farm_type');
    $period = old('period');
    $pic = old('pic');

    if (isset($data)) {
        $kandang_id = $data->kandang->kandang_id??"";
        $kandang_name = $data->kandang->name??"";
        $capacity = $data->capacity??"";
        $farm_type = $data->farm_type??"";
        $period = $data->period??"";
        $pic = $data->pic??"";
    }

    if (isset($copy)) {
        $kandang_id = "";
        $kandang_name = "";
        $capacity = "";
        $farm_type = "";
        $period = "";
        $pic = "";
    }
@endphp

<div class="card mb-1">
    <div id="headingCollapse2" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
        <span class="lead collapse-title"> Informasi  Farm </span>
    </div>
    <div id="collapse2" role="tabpanel" aria-labelledby="headingCollapse2" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-3 col-form-label">
                                <label for="kandang_id" class="float-right">Kandang</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="kandang_id" id="kandang_id" class="form-control {{$errors->has('kandang_id')?'is-invalid':''}}">
                                    <option disabled selected>Pilih Lokasi terlebih dahulu</option>
                                    @if($kandang_id && $kandang_name)
                                        <option value="{{ $kandang_id }}" selected="selected">{{ $kandang_name }}</option>
                                    @endif
                                </select>
                                <span class="text-danger small" id="error-kandang"></span>
                                @if ($errors->has('kandang_id'))
                                    <span class="text-danger small">{{ $errors->first('kandang_id') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-3 col-form-label">
                                <label for="capacity" class="float-right">Kapasitas</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="number" id="capacity" class="{{$errors->has('capacity')?'is-invalid':''}} form-control" name="capacity" placeholder="Kapasitas" value="{{ $capacity }}">
                                @if ($errors->has('capacity'))
                                    <span class="text-danger small">{{ $errors->first('capacity') }}</span>
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
                                <label for="farm_type" class="float-right">Jenis Farm</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="farm_type" id="farm_type" class="{{$errors->has('farm_type')?'is-invalid':''}} form-control">
                                    <option disabled {{ !$farm_type?'selected': '' }}>Pilih Tipe</option>
                                    @foreach ($type as $key => $item)
                                        <option value="{{$key}}" {{$farm_type==$key?'selected':''}}>{{$item}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('farm_type'))
                                    <span class="text-danger small">{{ $errors->first('farm_type') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-3 col-form-label">
                                <label for="period" class="float-right">Periode</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="number" id="period" class="{{$errors->has('period')?'is-invalid':''}} form-control" name="period" placeholder="Periode" value="{{ $period }}">
                                @if ($errors->has('period'))
                                    <span class="text-danger small">{{ $errors->first('period') }}</span>
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
                            <div class="col-sm-3">
                                <label for="pic" style="text-align: end">Penanggung Jawab</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" id="pic" class="{{$errors->has('pic')?'is-invalid':''}} form-control" name="pic" placeholder="Penanggung Jawab" value="{{ $pic }}">
                                @if ($errors->has('pic'))
                                    <span class="text-danger small">{{ $errors->first('pic') }}</span>
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
    $(document).ready(function () {
        $('#location_id').change(function (e) { 
            $('#kandang_id').val(null).trigger('change');
            var companyId = $('#company_id').val();
            var locationId = $('#location_id').val();
            var qryParam = locationId&&companyId?`?location_id=${locationId}&company_id=${companyId}`:'';

            $('#kandang_id').select2({
                placeholder: "Pilih Kandang",
                ajax: {
                    url: `{{ route("data-master.kandang.search") }}${qryParam}`, 
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

        $('#location_id').trigger('change');

        $('#kandang_id').on('select2:select', function (e) {
            var res = e.params.data;

            if (res.data.project_status) {
                $(this).next('.select2-container').find('.select2-selection').addClass('is-invalid');
                $('#error-kandang').html('tidak bisa dipilih, kandang sedang aktif.');
                $('#kandang_id').val(null).trigger('change');
                $('#capacity').val('');
                $('#farm_type option').first().prop('selected', true);
                $('#pic').val('');
            } else {
                $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
                $('#error-kandang').html('');
                $('#capacity').val(res.data.capacity);
                $('#farm_type').val(res.data.type);
                $('#pic').val(res.data.user.name);
            }

        });

        var oldValueKandang = "{{ $kandang_id }}";
        if (oldValueKandang) {
            var oldNameKandang = "{{ $kandang_name }}";
            if (oldNameKandang) {
                var newOption = new Option(oldNameKandang, oldValueKandang, true, true);
                $('#kandang_id').append(newOption).trigger('change');
            }
        }

        @if ($errors->has('kandang_id'))
            $('#kandang_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
        @endif
    });
</script>