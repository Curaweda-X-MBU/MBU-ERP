<div class="card">
    <input type="hidden" class="location_overhead_loaded" value="0">
    <div class="card-body">
        <h4>Pengeluaran Overhead</h4>
        <div class="table-responsive mt-2" style="overflow-x: auto;">
            <table id="location_overhead_datatable" class="table" style="margin: 0 0 !important;">
                <thead>
                    <tr class="text-center">
                        <th rowspan="2" style="vertical-align: middle">No</th>
                        <th rowspan="2" style="vertical-align: middle">Jenis</th>
                        <th colspan="3" class="border-right">Budget Pengajuan</th>
                        <th colspan="5">Realisasi</th>
                        <th rowspan="2" style="vertical-align: middle">Rp/QTY</th>
                    </tr>
                    <tr class="text-center">
                        <th>QTY</th>
                        <th>Harga Satuan</th>
                        <th class="border-right">Total (RP)</th>
                        <th>Tanggal</th>
                        <th>Noref</th>
                        <th>QTY</th>
                        <th>RP/Kandang</th>
                        <th>Total (RP)</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- DATA from AJAX --}}
                </tbody>
                <tfoot>
                    <tr class="font-weight-bolder">
                        <td>Total</td>
                        <td></td>
                        <td class="total_budget_qty text-right"></td>
                        <td></td>
                        <td class="budget_grand_total text-right"></td>
                        <td></td>
                        <td></td>
                        <td class="total_realization_qty text-right"></td>
                        <td></td>
                        <td class="grand_total text-right"></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
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

    function fetchLocationOverheadData() {
        $.get("{{ route('report.detail.location.overhead', [ 'location' => $detail->location_id ]) . '?period=' . $detail->period }}")
            .then(function(result) {
                if (!result.error) {
                    populateBudgetTable(result);
                }
            });
    }

    function populateBudgetTable(data) {
        // Flatten the data to match DataTable structure
        let formattedData = [];

        let index = 1;
        $.each(data, function(_, item) {
            formattedData.push({
                index: "",
                nama: "<b>" + item.kategori + "</b>",
                qtyPengajuan: "",
                hargaSatuanPengajuan: "",
                totalPengajuan: "",
                tanggal: "",
                noRef: "",
                qtyRealisasi: "",
                hargaSatuanRealisasi: "",
                totalRealisasi: "",
                rpPerQty: "",
            });
            $.each(item.subkategori, function(_, sub) {
                formattedData.push({
                    index: index,
                    nama: sub.produk,
                    qtyPengajuan: sub.budget_qty,
                    hargaSatuanPengajuan: item.kategori.toLowerCase() == 'pengeluaran operasional' ? sub.budget_price : '-',
                    totalPengajuan: sub.budget_total,
                    tanggal: sub.tanggal,
                    noRef: sub.no_ref,
                    qtyRealisasi: sub.realization_qty,
                    uom: sub.uom,
                    hargaSatuanRealisasi: item.kategori.toLowerCase() == 'pengeluaran operasional' ? sub.realization_price : '-',
                    totalRealisasi: sub.realization_total,
                    rpPerQty: sub.price_per_qty,
                });
                index += 1;
            });
        });

        // Destroy existing DataTable if initialized
        if ($.fn.DataTable.isDataTable("#location_overhead_datatable")) {
            $("#location_overhead_datatable").DataTable().destroy();
        }

        // Initialize DataTable
        $("#location_overhead_datatable").DataTable({
            destroy: true,  // Allows reloading data dynamically
            responsive: true,
            ordering: false,
            dom: '<"custom-table-wrapper"t>',
            data: formattedData,
            columns: [
                {
                    data: "index",
                    className: "text-right",
                },
                { data: "nama" },
                {
                    data: "qtyPengajuan",
                    className: "budget_qty",
                },
                {
                    data: "hargaSatuanPengajuan",
                    className: "text-right",
                },
                {
                    data: "totalPengajuan",
                    className: "text-right budget_total",
                },
                { data: "tanggal" },
                { data: "noRef" },
                {
                    data: "qtyRealisasi",
                    className: "realization_qty",
                    render: function(data, type, row) {
                        if (type === 'display') {
                            data = data ? `${trimLocale(data)} ${row.uom}` : "";
                        }

                        return data;
                    }
                },
                {
                    data: "hargaSatuanRealisasi",
                    className: "text-right",
                },
                {
                    data: "totalRealisasi",
                    className: "text-right realization_total",
                },
                {
                    data: "rpPerQty",
                    className: "text-right",
                }
            ],
            footerCallback: function(row, data) {
                let api = this.api();

                let intVal = function (i) {
                    return typeof i === 'string'
                        ? parseLocaleToNum(i)
                        : typeof i === 'number'
                        ? i
                        : 0;
                };

                const $footer = $(api.column(0).footer()).closest('tfoot');

                totalBudgetQty = data.reduce((a, b) => intVal(a) + intVal(b.qtyPengajuan ?? 0), 0);

                $footer.find('.total_budget_qty').html(trimLocale(totalBudgetQty));

                budgetGrandTotal = (api
                    .column('.budget_total')
                    .data() ?? [])
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                $footer.find('.budget_grand_total').html(parseNumToLocale(budgetGrandTotal));

                totalRealizationQty = data.reduce((a, b) => intVal(a) + intVal(b.qtyRealisasi ?? 0), 0);

                $footer.find('.total_realization_qty').html(trimLocale(totalRealizationQty));

                grandTotal = (api
                    .column('.realization_total')
                    .data() ?? [])
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                $footer.find('.grand_total').html(parseNumToLocale(grandTotal));
            },
        });
    }

    $('.location_overhead_loaded').on('change', fetchLocationOverheadData);
});
</script>
