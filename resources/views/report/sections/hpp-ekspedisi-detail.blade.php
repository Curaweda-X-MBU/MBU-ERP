<div class="card">
    <input type="hidden" class="location_hpp_ekspedisi_loaded" value="0">
    <input type="hidden" class="kandang_hpp_ekspedisi_loaded" value="0">
    <div class="card-body">
        <h4>Perhitungan HPP Ekspedisi</h4>
        <div class="table-responsive mt-2" style="overflow-x: auto;">
            <table id="hpp_ekspedisi_datatable" class="table" style="margin: 0 0 !important;">
                <thead>
                    <tr class="text-center">
                        <th>No</th>
                        @if (@$detail->project_id !== 'nothing')
                        <th class="text-left">No. DO</th>
                        @endif
                        <th class="text-left">Nama Ekspedisi</th>
                        <th>HPP Ekspedisi</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        @if (@$detail->project_id === 'nothing')
                        <td class="text-center" colspan="3">Mengambil data ...</td>
                        @else
                        <td class="text-center" colspan="4">Mengambil data ...</td>
                        @endif
                    </tr>
                </tbody>

                <tfoot>
                    <tr class="font-weight-bolder">
                        <td colspan="2">Total HPP Ekspedisi</td>
                        @if (@$detail->project_id !== 'nothing')
                        <td></td>
                        @endif
                        <td class="total_delivery_fee text-right"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
$(function() {
    function fetchLocationHppEkspedisiData() {
        fetchHppEkspedisiData("{{ route('report.detail.location.ekspedisi', [ 'location' => $detail->location_id ]) . '?period=' . $detail->period }}");
    }

    function fetchKandangHppEkspedisiData() {
        fetchHppEkspedisiData("{{ route('report.detail.kandang.ekspedisi', [ 'location' => $detail->location_id, 'project' => $detail->project_id ]) . '?period=' . $detail->period }}");
    }

    function fetchHppEkspedisiData(route) {
        $.get(route)
            .then(function(result) {
                if (!result.error) {
                    $('#hpp_ekspedisi_datatable').DataTable({
                        destroy: true,
                        responsive: true,
                        ordering: true,
                        dom: '<"custom-table-wrapper"t>',
                        data: result,
                        columns: [
                            {
                                data: null,
                                render: function(data, type, _, meta) {
                                    return meta.row + 1;
                                }
                            },
                            @if (@$detail->project_id !== 'nothing')
                            {data: 'id_marketing'},
                            @endif
                            {data: 'supplier_name'},
                            {
                                data: 'total_delivery_fee',
                                className: 'delivery_fee text-right',
                                render: function(data, type) {
                                    return parseNumToLocale(data);
                                }
                            },
                        ],
                        footerCallback: function(row, data) {
                            let api = this.api();

                            const $footer = $(api.column(0).footer()).closest('tfoot');

                            totalDeliveryFee = (api
                                .column('.delivery_fee')
                                .data() ?? [])
                                .reduce((a, b) => intVal(a) + intVal(b), 0);

                            $footer.find('.total_delivery_fee').html(`Rp&nbsp;${parseNumToLocale(totalDeliveryFee)}`);
                        }
                    })
                }
            });
    }

    $('.location_hpp_ekspedisi_loaded').on('change', fetchLocationHppEkspedisiData);
    $('.kandang_hpp_ekspedisi_loaded').on('change', fetchKandangHppEkspedisiData);
});
</script>
