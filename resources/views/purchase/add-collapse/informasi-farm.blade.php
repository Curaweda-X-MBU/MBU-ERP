<div class="table-responsive">
    <table class="table table-striped table-bordered w-100" id="tbl-kandang">
        <thead>
            <tr>
                <th colspan="4">Project Aktif</th>
            </tr>
            <tr>
                <th>Kandang</th>
                <th>Kapasitas</th>
                <th>Penanggung Jawab</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4"><center>Data tidak tersedia</center></td>
            </tr>
        </tbody>
    </table>
</div><br>

<script>
    $(function () {
        $('#location_id').on('select2:select', function (e) {
            e.preventDefault();
            const locationId = $(this).val();
            $('#warehouse_id').val(null).trigger('change');
            $('#warehouse_id').select2({
                placeholder: "Pilih Gudang",
                ajax: {
                    url: `{{ route("data-master.warehouse.search") }}?location_id=${locationId}`, 
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

            getKandangByLocationId(locationId);

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

    function getKandangByLocationId(locationId) {
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
                    let latestPeriod;
                    if (res.length === 0) {
                        tblData = `<tr> <td colspan="4"><center>Data tidak tersedia</center></td> </tr>`;
                    }
                    console.log(res);
                    
                    res.forEach(val => {
                        latestPeriod = val.data.latest_period;
                        const projectStatus = val.data.project_status?
                            '<div class="badge badge-success">Aktif</div>':
                            '<div class="badge badge-secondary">Nonaktif</div>'
                        tblData += `<tr>
                                        <td>${val.text.replace('( Aktif )', '')}</td>
                                        <td>${val.data.capacity}</td>
                                        <td>${val.data.user.name}</td>
                                        <td>
                                            ${projectStatus}    
                                        </td>
                                    <tr>`;
                        const arrProject = val.data.project;
                        arrProject.forEach(v => {
                            if (![1,4].includes(v.project_status)) {
                            }
                        });

                    });
                    $('#tbl-kandang tbody').html(tblData);
                    $('#period').val(latestPeriod);
                }
            });
    }
</script>