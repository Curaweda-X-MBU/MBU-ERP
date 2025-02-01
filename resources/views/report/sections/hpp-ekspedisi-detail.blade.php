<div class="card">
    <input type="hidden" class="location_hpp_ekspedisi_loaded" value="0">
    <div class="card-body">
        <h4>Perhitungan HPP Ekspedisi</h4>
        <div class="table-responsive mt-2" style="overflow-x: auto;">
            <table id="location_hpp_ekspedisi_datatable" class="table" style="margin: 0 0 !important;">
                <thead>
                    <tr class="text-center">
                        <th>No</th>
                        <th class="text-left">Nama Ekspedisi</th>
                        <th>HPP Ekspedisi</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td class="text-center" colspan="3">Mengambil data ...</td>
                    </tr>
                </tbody>

                <tfoot>
                    <tr class="font-weight-bolder">
                        <td colspan="2">Total HPP Ekspedisi</td>
                        <td class="total_delivery_fee text-right"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
$(function() {
    function intVal (i) {
        return typeof i === 'string'
            ? parseLocaleToNum(i)
            : typeof i === 'number'
            ? i
            : 0;
    };

    function fetchLocationHppEkspedisiData() {
        $.get("{{ route('report.detail.location.ekspedisi', ['location' => $detail->location_id]) . '?period=' . $detail->period }}")
            .then(function(result) {
                if (!result.error) {
                    $('#location_hpp_ekspedisi_datatable').DataTable({
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
});
</script>
