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
                                @include('closing.sections.sapronak-collapse.sapronak-masuk')
                                @include('closing.sections.sapronak-collapse.sapronak-keluar')
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

    function fetchLocationSapronakData() {
        fetchSapronakData("{{ route('closing.detail.location.sapronak', [ 'location' => $detail->location_id ]) . '?period=' }}" + period);
    }

    function fetchKandangSapronakData() {
        fetchSapronakData("{{ route('closing.detail.kandang.sapronak', [ 'location' => $detail->location_id, 'project' => $detail->project_id ]) . '?period=' }}" + period);
    }

    function fetchSapronakData(route) {
        $.get(route)
            .then(function(result) {
                if (!result.error) {
                    $('#sapronak_masuk_datatable').DataTable({
                        destroy: true,
                        responsive: true,
                        ordering: true,
                        dom: '<"custom-table-wrapper"t>p',
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

                            const uomArr = qtyColumn.map((q) => q.split(' ').slice(1).join(' ').trim()).unique();

                            const qtyArr = qtyColumn.map((q) => q.split(' '));

                            const setFooter = (column, text) => {
                                $(api.column(column).footer()).text(text);
                            }

                            for (let i = 0; i < uomArr.length; i++) {
                                const filteredSum = qtyArr
                                    .filter((q) => q.slice(1).join(' ').trim() === uomArr[i])
                                    .reduce((a, b) => intVal(a) + intVal(b[0]), 0);

                                if (i < 6) {
                                    setFooter(6 - i, [trimLocale(filteredSum), uomArr[i]].join(' '));
                                }
                            }
                        }
                    });

                    $('#sapronak_keluar_datatable').DataTable({
                        destroy: true,
                        responsive: true,
                        ordering: true,
                        dom: '<"custom-table-wrapper"t>p',
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

                            const setFooter = (column, text) => {
                                $(api.column(column).footer()).text(text);
                            }

                            for (let i = 0; i < uomArr.length; i++) {
                                const filteredSum = qtyArr
                                    .filter((q) => q.slice(1).join(' ').trim() === uomArr[i])
                                    .reduce((a, b) => intVal(a) + intVal(b[0]), 0);

                                if (i < 6) {
                                    setFooter(6 - i, [trimLocale(filteredSum), uomArr[i]].join(' '));
                                }
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
