<div class="card mb-1">
    <div id="headingCollapse2" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
        <span class="lead collapse-title">Pilih Kandang </span>
    </div>
    <div id="collapse2" role="tabpanel" aria-labelledby="headingCollapse2" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-striped table-bordered w-100" id="tbl-kandang">
                    <thead>
                        @if (!isset($data))
                        <th style="width: 6%;">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkAll">
                                <label class="custom-control-label" for="checkAll"></label>
                            </div>
                        </th>
                        @endif
                        <th>Kandang</th>
                        @if (!isset($data))<th>Status Project</th>@endif
                        <th>Kapasitas</th>
                        <th>Penanggung Jawab</th>
                    </thead>
                    <tbody>
                        @isset($data->kandang)
                        <tr>
                            <td>{{ $data->kandang->name??'' }}</td>
                            <td>{{ $data->kandang->capacity??'' }}</td>
                            <td>{{ $data->kandang->user->name??'' }}</td>
                        </tr>
                        @else
                        <tr>
                            <td colspan="{{ !isset($data)?'5':'4' }}"><center>Data tidak tersedia</center></td>
                        </tr>
                        @endisset
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#location_id').on('select2:select', function (e) {
            e.preventDefault();
            const locationId = $(this).val();
            $.ajax({
                type: "get",
                url: `{{ route('data-master.kandang.search') }}?location_id=${locationId}`,
                beforeSend: function () {
                    $('#tbl-kandang tbody').html('');
                    $('#period').val('');
                    $('input[name="period"]').val('');
                },
                success: function (res) {
                    let tblData = ''; 
                    if (res.length === 0) {
                        tblData = `<tr> <td colspan="5"><center>Data tidak tersedia</center></td> </tr>`;
                    }
                    console.log('res', res);
                    
                    let arrPeriod = [];
                    let arrStatusName = [];
                    res.forEach(val => {
                        arrPeriod.push(val.data.latest_period);
                        let disabledCheck = '';
                        let classCheckbox = 'rowCheckbox';
                        if (val.data.project_status_name !== 'Tersedia') {
                            arrStatusName.push(val.data.project_status_name);
                            disabledCheck = 'disabled';
                            classCheckbox = 'rowCheckboxDisabled';
                        }
                        tblData += `<tr>
                                        <td style="padding-left: 2rem !important;">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input ${classCheckbox}" name="kandang_id[]" id="kandangId${ val.id }" value="${ val.id }" ${disabledCheck}>
                                                <label class="custom-control-label" for="kandangId${ val.id }"></label>
                                            </div>
                                        </td>
                                        <td>${val.text}</td>
                                        <td>${val.data.project_status_name}</td>
                                        <td>${val.data.capacity}</td>
                                        <td>${val.data.user.name}</td>
                                    <tr>`;
                    });

                    const isSameStatus = arrStatusName.length === 0 || arrStatusName.every(value => value === arrStatusName[0]);
                    const sortedArray = arrPeriod.length > 0 ? arrPeriod.sort((a, b) => b - a) : [0];
                    const latestPeriod = isSameStatus?sortedArray[0]+1:sortedArray[0];
                    $('#tbl-kandang tbody').html(tblData);
                    $('#period').val(latestPeriod);
                    $('input[name="period"]').val(latestPeriod);
                }
            });

            $('#checkAll').change(function() {
                const isChecked = $(this).is(':checked');
                $('.rowCheckbox').prop('checked', isChecked);
            });

            $('#tbl-kandang').on('change', '.rowCheckbox', function() {
                const allChecked = $('.rowCheckbox').length === $('.rowCheckbox:checked').length;
                $('#checkAll').prop('checked', allChecked);
            });
        });
    });
</script>