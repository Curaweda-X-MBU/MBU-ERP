<div class="card">
    <input type="hidden" class="location_perhitungan_sapronak_loaded">
    <input type="hidden" class="kandang_perhitungan_sapronak_loaded">
    <div class="card-body">
        <h4 class="mb-2">Perhitungan Sapronak</h4>
        <section id="collapsible">
            <div class="row">
                <div class="col-sm-12">
                    <div class=" collapse-icon">
                        <div class=" p-0">
                            <div class="collapse-default">
                                @include('closing.sections.perhitungan-sapronak-collapse.doc-broiler')
                                @include('closing.sections.perhitungan-sapronak-collapse.ovk')
                                @include('closing.sections.perhitungan-sapronak-collapse.pakan')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
$(function() {
    const period = getQueryParam('period');

    function populatePerhitunganTable(table_selector, data) {
        $(table_selector).DataTable({
            destroy: true,
            responsive: true,
            ordering: true,
            dom: '<"custom-table-wrapper"t>p',
            data: data,
            columns: [
                { data: 'tanggal' },
                { data: 'no_reference' },
                {
                    data: 'qty_masuk',
                    className: 'qty_masuk text-right',
                },
                {
                    data: 'qty_pakai',
                    className: 'qty_pakai text-right',
                },
                { data: 'product' },
                {
                    data: 'harga_beli',
                    className: 'harga_beli text-right',
                },
                {
                    data: 'total_harga',
                    className: 'total_harga text-right',
                },
                { data: 'notes' },
            ],
            footerCallback: function(row, data) {
                let api = this.api();

                const $footer = $(api.column(0).footer()).closest('tfoot');

                qtyMasuk = (api
                    .column('.qty_masuk')
                    .data() ?? [])
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                $footer.find('.total_qty_masuk').text(parseNumToLocale(qtyMasuk));

                qtyPakai = (api
                    .column('.qty_pakai')
                    .data() ?? [])
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                $footer.find('.total_qty_pakai').text(parseNumToLocale(qtyPakai));

                hargaBeli = (api
                    .column('.harga_beli')
                    .data() ?? [])
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                $footer.find('.total_harga_beli').text(parseNumToLocale(hargaBeli));

                totalHarga = (api
                    .column('.total_harga')
                    .data() ?? [])
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                $footer.find('.grand_total').text(parseNumToLocale(totalHarga));
            },
        });
    }

    function fetchLocationPerhitunganSapronakData() {
        fetchPerhitunganSapronakData("{{ route('closing.detail.location.perhitungan', [ 'location' => $detail->location_id ]) . '?period=' }}" + period);
    }

    function fetchKandangPerhitunganSapronakData() {
        fetchPerhitunganSapronakData("{{ route('closing.detail.kandang.perhitungan', [ 'location' => $detail->location_id, 'project' => $detail->project_id ]) . '?period=' }}" + period);
    }

    function fetchPerhitunganSapronakData(route) {
        $.get(route)
            .then(function(result) {
                if (!result.error) {
                    const doc = result.doc;
                    populatePerhitunganTable('#perhitungan_doc_datatable', doc);

                    const ovk = result.ovk;
                    populatePerhitunganTable('#perhitungan_ovk_datatable', ovk);

                    const pakan = result.pakan;
                    populatePerhitunganTable('#perhitungan_pakan_datatable', pakan);
                }
            });
    }

    $('.location_perhitungan_sapronak_loaded').on('change', fetchLocationPerhitunganSapronakData);
    $('.kandang_perhitungan_sapronak_loaded').on('change', fetchKandangPerhitunganSapronakData);
});
</script>
