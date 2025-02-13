{{-- <div class="table-responsive">
    <table class="table table-striped table-bordered w-100" id="tbl-kandang">
        <thead>
            <tr>
                <th colspan="5">Project Aktif</th>
            </tr>
            <tr>
                <th>Kandang</th>
                <th>Kapasitas</th>
                <th>Penanggung Jawab</th>
                <th>Status</th>
                <th>Estimasi Anggaran</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="5"><center>Data tidak tersedia</center></td>
            </tr>
        </tbody>
    </table>
</div><br>

<div class="modal fade text-left" id="est-budget" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Estimasi Anggaran</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-bordered w-100" id="tbl-budget">
                            <thead>
                                <th>Nama Produk</th>
                                <th class="text-right">QTY</th>
                                <th class="text-right">Harga Satuan (Rp)</th>
                                <th class="text-right">Total Anggaran (Rp)</th>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">
                                        Grand Total Anggaran
                                    </th>
                                    <td class="text-right" style="background-color: #f3f2f7;" id="total-budget">
                                        
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Kembali</button>
            </div>
        </div>
    </div>
</div> --}}

<script>
    $(function () {
        function formatMoney(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        $('#est-budget').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var id = button.data('id')
            var modal = $(this)
            $.ajax({
                type: "get",
                url: `{{ route('project.list.search-budget') }}?project_id=${id}`,
                beforeSend: function() {
                    modal.find('#tbl-budget tbody').html();
                },
                success: function (res) {
                    console.log(res);
                    const tblBudget = modal.find('#tbl-budget tbody');
                    let row = '';
                    let totalBudget = 0;
                    res.forEach(val => {
                        row += `<tr>
                                    <td>${val.product?.name ?? val.nonstock?.name ?? ''}</td>
                                    <td class="text-right">${formatMoney(val.qty)}</td>
                                    <td class="text-right">${formatMoney(val.price)}</td>
                                    <td class="text-right">${formatMoney(val.total)}</td>
                                </tr>`;
                        totalBudget += parseInt(val.total);
                    });
                    tblBudget.html(row);
                    $('#total-budget').html(formatMoney(totalBudget));
                }
            });
        });

        $('#location_id').on('select2:select', function (e) {
            e.preventDefault();
            const locationId = $(this).val();
            
            $('#warehouse_id').val(null).trigger('change');
            $('#warehouse_id').select2({
                placeholder: "Pilih Gudang",
                ajax: {
                    url: `{{ route("data-master.warehouse.search") }}`, 
                    dataType: 'json',
                    delay: 250, 
                    data: function(params) {
                        return {
                            q: params.term,
                            location_ids: locationId
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
                url: `{{ route('project.list.search') }}?location_id=${locationId}&project_status_not=3&project_status_not=4`,
                beforeSend: function () {
                    $('#tbl-kandang tbody').html('');
                    $('#period').val('');
                    $('input[name="period"]').val('');
                },
                success: function (res) {
                    let tblData = ''; 
                    let latestPeriod;
                    if (res.length === 0) {
                        tblData = `<tr> <td colspan="5"><center>Data tidak tersedia</center></td> </tr>`;
                    }
                    console.log(res);
                    if (feather) {
                        feather.replace({ width: 14, height: 14 });
                    }
                    
                    res.forEach(val => {
                        let projectStatus = '';
                        let estBudget = '';
                        switch (val.data.project_status) {
                            case 1:
                                projectStatus = '<div class="badge badge-warning">Pengajuan</div>';
                                break;
                            case 2:
                                projectStatus = '<div class="badge badge-primary">Aktif</div>';
                                estBudget = `<a href="javascript:void(0)" data-id="${val.data.project_id}" data-toggle="modal" data-target="#est-budget">
                                                <span>Detail</span>
                                            </a>`;
                                break;
                            default:
                                projectStatus = '<div class="badge badge-secondary">N/A</div>';
                                break;
                        }
                        // const projectBudget = val.data.project_status?
                        tblData += `<tr>
                                        <td>${val.text.replace('( Aktif )', '')}</td>
                                        <td>${val.data.kandang.capacity ?? ''}</td>
                                        <td>${val.data.kandang.user.name ?? ''}</td>
                                        <td>${projectStatus}</td>
                                        <td>${estBudget}</td>
                                    <tr>`;

                    });
                    $('#tbl-kandang tbody').html(tblData);
                }
            });
    }
</script>