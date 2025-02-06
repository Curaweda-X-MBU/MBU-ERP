<div class="card">
    <input type="hidden" name="location_sapronak_loaded" value="1">
    <input type="hidden" name="kandang_sapronak_loaded" value="1">
    <div class="card-body">
        <h4 class="mb-2">List Keluar Masuk Sapronak</h4>
        <section id="collapsible">
            <div class="row">
                <div class="col-sm-12">
                    <div class=" collapse-icon">
                        <div class=" p-0">
                            <div class="collapse-default">
                                @include('report.sections.sapronak-collapse.sapronak-masuk')
                                @include('report.sections.sapronak-collapse.sapronak-keluar')
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
    function intVal (i) {
        return typeof i === 'string'
            ? parseLocaleToNum(i)
            : typeof i === 'number'
            ? i
            : 0;
    }

    function trimLocale(num) {
        const locale = parseNumToLocale(num);
        return locale.split(',')[1] === '00'
            ? locale.split(',')[0]
            : locale;
    }

    function fetchLocationSapronakData() {
        fetchSapronakData("{{ route('report.detail.location.sapronak', [ 'location' => $detail->location_id ]) . '?period=' . $detail->period }}");
    }

    function fetchKandangSapronakData() {
        fetchSapronakData("{{ route('report.detail.kandang.sapronak', [ 'location' => $detail->location_id, 'project' => $detail->project_id ]) . '?period=' . $detail->period }}");
    }

    function fetchSapronakData(route) {
        $.get(route)
            .then(function(result) {
                if (!result.error) {
                    $('#sapronak_masuk_datatable').DataTable({
                        destroy: true,
                        responsive: true,
                        ordering: true,
                        dom: '<"custom-table-wrapper"t>',
                        data: result.sapronak_masuk,
                        columns: [
                            { data: 'tanggal' },
                            {
                                data: 'no_referensi',
                            },
                            { data: 'transaksi' },
                            { data: 'produk' },
                            { data: 'gudang_asal' },
                            {
                                data: null,
                                render: () => '',
                            },
                            {
                                data: 'qty',
                                className: 'qty',
                            },
                            { data: 'notes' },
                        ],
                        footerCallback: function(row, data) {
                            let api = this.api();

                            const $footer = $(api.column(0).footer()).closest('tfoot');

                            const qtyColumn = (api
                                .column('.qty')
                                .data() ?? []);

                            const uomArr = qtyColumn.map((q) => q.split(' ')[1].trim()).unique();

                            const qtyArr = qtyColumn.map((q, i) => q.split(' '));

                            if (uomArr.length) {
                                totalQty1 = qtyArr.filter((q) => q[1] === uomArr[0]).reduce((a, b) => intVal(a) + intVal(b[0]), 0);
                                $footer.find('.total_2').html(`${trimLocale(totalQty1)} ${uomArr[0]}`);
                            }

                            if (uomArr.length > 1) {
                                totalQty2 = qtyArr.filter((q) => q[1] === uomArr[1]).reduce((a, b) => intVal(a) + intVal(b[0]), 0);
                                $footer.find('.total_1').html(`${trimLocale(totalQty2)} ${uomArr[1]}`);
                            }
                        }
                    });

                    $('#sapronak_keluar_datatable').DataTable({
                        destroy: true,
                        responsive: true,
                        ordering: true,
                        dom: '<"custom-table-wrapper"t>',
                        data: result.sapronak_keluar,
                        columns: [
                            { data: 'tanggal' },
                            {
                                data: 'no_referensi',
                            },
                            { data: 'transaksi' },
                            { data: 'produk' },
                            { data: 'gudang_tujuan' },
                            {
                                data: null,
                                render: () => '',
                            },
                            {
                                data: 'qty',
                                className: 'qty',
                            },
                            { data: 'notes' },
                        ],
                        footerCallback: function(row, data) {
                            let api = this.api();

                            const $footer = $(api.column(0).footer()).closest('tfoot');

                            const qtyColumn = (api
                                .column('.qty')
                                .data() ?? []);

                            const uomArr = qtyColumn.map((q) => q.split(' ')[1].trim()).unique();

                            const qtyArr = qtyColumn.map((q, i) => q.split(' '));

                            if (uomArr.length) {
                                totalQty1 = qtyArr.filter((q) => q[1] === uomArr[0]).reduce((a, b) => intVal(a) + intVal(b[0]), 0);
                                $footer.find('.total_2').html(`${trimLocale(totalQty1)} ${uomArr[0]}`);
                            }

                            if (uomArr.length > 1) {
                                totalQty2 = qtyArr.filter((q) => q[1] === uomArr[1]).reduce((a, b) => intVal(a) + intVal(b[0]), 0);
                                $footer.find('.total_1').html(`${trimLocale(totalQty2)} ${uomArr[1]}`);
                            }
                        }
                    });
                }
            });
    }

    @if (@$detail->project_id === 'nothing')
    fetchLocationSapronakData();
    @else
    fetchKandangSapronakData();
    @endif
});
</script>
