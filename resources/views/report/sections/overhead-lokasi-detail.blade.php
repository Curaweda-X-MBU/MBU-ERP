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
                        <th colspan="4">Realisasi</th>
                        <th rowspan="2" style="vertical-align: middle">Rp/Kandang</th>
                    </tr>
                    <tr class="text-center">
                        <th>QTY</th>
                        <th>RP/QTY</th>
                        <th class="border-right">Total (RP)</th>
                        <th>Tanggal</th>
                        <th>QTY</th>
                        <th>RP/QTY</th>
                        <th>Total (RP)</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- DATA from AJAX --}}
                    <tr>
                        <td class="text-center" colspan="10">Mengambil data ...</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="font-weight-bolder">
                        <td>Total</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="total_realization_qty">-</td>
                        <td></td>
                        <td class="grand_total text-right">-</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
$(function() {
    const period = getQueryParam('period');

    function sumValues (arr, column) {
        const sum = arr.reduce((a, b) => intVal(a) + intVal(b[column]), 0);
    }

    function fetchLocationOverheadData() {
        $.get("{{ route('report.detail.location.overhead', [ 'location' => $detail->location_id ]) . '?period=' }}" + period)
            .then(function(result) {
                if (!result.error) {
                    populateBudgetTable(result);
                }
            });
    }

    function populateBudgetTable(data) {
        // Flatten the data to match DataTable structure
        let formattedData = [];

        console.log(data);

        $.each(data, function(_, item) {
            let index = 1;
            formattedData.push({
                index: "",
                nama: "<b>" + item.kategori + "</b>",
                qtyPengajuan: "",
                hargaSatuanPengajuan: "",
                totalPengajuan: "",
                tanggal: "",
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
                    uom: sub.uom,
                    qtyRealisasi: sub.realization_qty,
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
            dom: '<"custom-table-wrapper"t>p',
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
                    render: function(data, type, row) {
                        if (type === 'display') {
                            data = data ? `${trimLocale(data)} ${row.uom}` : "";
                        }

                        return data;
                    }
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
                    data: "rpPerQty",
                    className: "text-right",
                },
                {
                    data: "totalRealisasi",
                    className: "text-right realization_total",
                },
                {
                    data: "hargaSatuanRealisasi",
                    className: "text-right",
                },
            ],
            footerCallback: function(row, data) {
                let api = this.api();

                const $footer = $(api.column(0).footer()).closest('tfoot');

                totalRealizationQty = data.reduce((a, b) => intVal(a) + intVal(b.qtyRealisasi ?? 0), 0);

                $footer.find('.total_realization_qty').html(trimLocale(totalRealizationQty));

                grandTotal = (api
                    .column('.realization_total')
                    .data() ?? [])
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                $footer.find('.grand_total').html(`Rp&nbsp;${parseNumToLocale(grandTotal)}`);
            },
        });
    }

    $('.location_overhead_loaded').on('change', fetchLocationOverheadData);
});
</script>
