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
                                    <span id="populasi_akhir_kandang">0</span>&nbsp;X&nbsp;<span id="pemakaian_farm">0</span>
                                </div>
                                <div><hr class="p-0 border-dark" style="border-width: 3px;"></div>
                                <div class="row d-flex align-items-center justify-content-center">
                                    <span id="populasi_akhir_proyek">0</span>
                                </div>
                            </div>
                            <div class="col-2 text-center d-flex align-items-center"><span class="pl-2 pr-3">=</span><span class="result">0</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- total --}}
        <div class="row mt-1 mx-0 font-weight-bolder d-flex justify-content-between">
            <div class="">TOTAL PEMBEBANAN KANDANG</div>
            <div class="text-right "><span class="result">0</span></div>
        </div>
    </div>
</div>

<script>
$(function() {
    const period = getQueryParam('period');

    function fetchKandangOverheadData() {
        $.get("{{ route('report.detail.kandang.overhead', [ 'location' => $detail->location_id, 'project' => $detail->project_id ]) . '?period=' }}" + period)
            .then(function(result) {
                if (!result.error) {
                    const perhitungan = result.perhitungan;
                    $('#populasi_akhir_kandang').text(trimLocale(perhitungan.populasi_akhir_kandang));
                    $('#pemakaian_farm').text(trimLocale(perhitungan.pemakaian_farm));
                    $('#populasi_akhir_proyek').text(trimLocale(perhitungan.populasi_akhir_proyek));
                    $('.result').text(trimLocale(perhitungan.result));

                    const expense = result.expense;
                    $('#kandang_overhead_datatable').DataTable({
                        destroy: true,
                        responsive: true,
                        ordering: true,
                        dom: '<"custom-table-wrapper"t>p',
                        data: expense,
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
