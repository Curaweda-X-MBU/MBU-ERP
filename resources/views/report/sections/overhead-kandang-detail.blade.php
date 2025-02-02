<div class="card">
    <input type="hidden" class="kandang_overhead_loaded" value="0">
    <div class="card-body">
        <h4>Pengeluaran Overhead</h4>
        {{-- table --}}
        <div class="table-responsive mt-2">
            <table id="kandang_overhead_datatable" class="table">
                <thead>
                    <tr class="text-center">
                        <th>No</th>
                        <th>Tanggal</th>
                        <th class="text-left">Produk</th>
                        <th>QTY</th>
                        <th>Rp/QTY</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    {{-- DATA from AJAX --}}
                    <tr>
                        <td class="text-center" colspan="6">Mengambil data ...</td>
                    </tr>
                </tbody>

                <tfoot>
                    <tr class="font-weight-bolder text-center border-bottom">
                        <td colspan="3" class="text-left">Total Overhead</td>
                        <td class="total_qty">-</td>
                        <td class="total_price_per_qty">-</td>
                        <td class="grand_total">-</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- formula --}}
        <div class="card mt-2">
            <div class="card-body border d-flex align-items-center justify-content-center">
                <div class="table-responsive">
                    <div style="min-width: 1000px;">
                        <div class="row w-100 font-weight-bolder">
                            <div class="col-2 text-center d-flex align-items-center">Pembelian Kandang <span class="pl-2">=</span></div>
                            <div class="col-4">
                                <div class="row text-center d-flex align-items-center justify-content-center">
                                    Populasi Akhir KANDANG X Pemakaian Di FARM
                                </div>
                                <div><hr class="p-0 border-dark" style="border-width: 3px;"></div>
                                <div class="row d-flex align-items-center justify-content-center">
                                    Populasi Akhir Proyek
                                </div>
                            </div>
                            <div class="col-1 d-flex align-items-center justify-content-center">=</div>
                            <div class="col-3">
                                <div class="row text-center d-flex align-items-center justify-content-center">
                                    50.000 X 512.447.584
                                </div>
                                <div><hr class="p-0 border-dark" style="border-width: 3px;"></div>
                                <div class="row d-flex align-items-center justify-content-center">
                                    387.200
                                </div>
                            </div>
                            <div class="col-2 text-center d-flex align-items-center"><span class="pl-2 pr-3">=</span>66.173.153.23</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- total --}}
        <div class="row mt-1 mx-0 font-weight-bolder d-flex justify-content-between">
            <div class="">TOTAL PEMBEBANAN KANDANG</div>
            <div class="text-right ">66.173.501.29</div>
        </div>
    </div>
</div>

<script>
$(function() {
    function trimLocale(num) {
        const locale = parseNumToLocale(num);
        return locale.split(',')[1] === '00'
            ? locale.split(',')[0]
            : locale;
    }

    function intVal (i) {
        return typeof i === 'string'
            ? parseLocaleToNum(i)
            : typeof i === 'number'
            ? i
            : 0;
    };

    function fetchKandangOverheadData() {
        $.get("{{ route('report.detail.kandang.overhead', [ 'location' => $detail->location_id, 'project' => $detail->project_id ]) . '?period=' . $detail->period }}")
            .then(function(result) {
                if (!result.error) {
                    $('#kandang_overhead_datatable').DataTable({
                        destroy: true,
                        responsive: true,
                        ordering: true,
                        dom: '<"custom-table-wrapper"t>',
                        data: result,
                        columns: [
                            {
                                data: null,
                                render: function(data, type, _, meta) {
                                    if (type === 'display') {
                                        data = meta.row + 1;
                                    }
                                    return data;
                                }
                            },
                            { data: 'tanggal' },
                            { data: 'produk' },
                            {
                                data: 'qty',
                                className: 'qty text-right',
                            },
                            {
                                data: 'price',
                                className: 'price text-right',
                            },
                            {
                                data: 'total',
                                className: 'total text-right',
                            },
                        ],
                        footerCallback: function(row, data) {
                            let api = this.api();

                            const $footer = $(api.column(0).footer()).closest('tfoot');

                            totalQty = (api
                                .column('.qty')
                                .data() ?? [])
                                .reduce((a, b) => intVal(a) + intVal(b), 0);

                            $footer.find('.total_qty').html(trimLocale(totalQty));

                            totalPricePerQty = (api
                                .column('.price')
                                .data() ?? [])
                                .reduce((a, b) => intVal(a) + intVal(b), 0);

                            $footer.find('.total_price_per_qty').html(`Rp&nbsp;${parseNumToLocale(totalPricePerQty)}`);

                            grandTotal = (api
                                .column('.total')
                                .data() ?? [])
                                .reduce((a, b) => intVal(a) + intVal(b), 0);

                            $footer.find('.grand_total').html(`Rp&nbsp;${parseNumToLocale(grandTotal)}`);
                        }
                    });
                }
            });
    }

    $('.kandang_overhead_loaded').on('change', fetchKandangOverheadData);
});
</script>
