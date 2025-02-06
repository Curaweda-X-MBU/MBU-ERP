<style>
    .color-header {
        color: white;
        background: linear-gradient(118deg, #76A8D8, #c9e5ff);
    }
</style>

<div class="card">
    <input type="hidden" class="location_keuangan_loaded" value="0">
    <input type="hidden" class="kandang_keuangan_loaded" value="0">
    <div class="card-body">
        @include('report.sections.keuangan-collapse.data-keuangan')
        <section id="collapsible">
            <div class="row">
                <div class="col-sm-12">
                    <div class=" collapse-icon">
                        <div class=" p-0">
                            <div class="collapse-default">
                                @include('report.sections.keuangan-collapse.hpp-pembelian')
                                @include('report.sections.keuangan-collapse.laba-rugi')
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
    function fetchLocationKeuanganData() {
        fetchKeuanganData("{{ route('report.detail.location.keuangan', [ 'location' => $detail->location_id ]) . '?period=' . $detail->period }}");
    }

    function fetchKandangKeuanganData() {
        fetchKeuanganData("{{ route('report.detail.location.keuangan', [ 'location' => $detail->location_id, 'project' => $detail->project_id ]) . '?period=' . $detail->period }}");
    }

    function fetchKeuanganData(route) {
        $.get(route)
            .then(function(result) {
                console.log(result);
                if (!result.error) {
                    const pengeluaran = result.pengeluaran;
                    populateHPPTable(pengeluaran);
                }
            });
    }

    function populateHPPTable(data) {
        // Flatten the data to match DataTable structure
        let formattedData = [];

        $.each(data, function(_, item) {
            let index = 1;
            formattedData.push({
                index: "",
                nama: "<b>" + item.kategori + "</b>",
                budget_rp_ekor: "",
                budget_rp_kg: "",
                budget_rp: "",
                realization_rp_ekor: "",
                realization_rp_kg: "",
                realization_rp: "",
            });
            $.each(item.subkategori, function(_, sub) {
                formattedData.push({
                    index: index,
                    nama: sub.name,
                    budget_rp_ekor: sub.budget_rp_ekor,
                    budget_rp_kg: sub.budget_rp_kg,
                    budget_rp: sub.budget_rp,
                    realization_rp_ekor: sub.realization_rp_ekor,
                    realization_rp_kg: sub.realization_rp_kg,
                    realization_rp: sub.realization_rp,
                });
                index += 1;
            });
        });

        // Destroy existing DataTable if initialized
        if ($.fn.DataTable.isDataTable("#hpp_pembelian_datatable")) {
            $("#hpp_pembelian_datatable").DataTable().destroy();
        }

        // Initialize DataTable
        $("#hpp_pembelian_datatable").DataTable({
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
                    data: "budget_rp_ekor",
                    render: function(data) {
                        return parseNumToLocale(data);
                    }
                },
                {
                    data: "budget_rp_kg",
                    render: function(data) {
                        return parseNumToLocale(data);
                    }
                },
                {
                    data: "budget_rp",
                    render: function(data) {
                        return parseNumToLocale(data);
                    }
                },
                {
                    data: "realization_rp_ekor",
                    render: function(data) {
                        return parseNumToLocale(data);
                    }
                },
                {
                    data: "realization_rp_kg",
                    render: function(data) {
                        return parseNumToLocale(data);
                    }
                },
                {
                    data: "realization_rp",
                    render: function(data) {
                        return parseNumToLocale(data);
                    }
                },
            ],
            // footerCallback: function(row, data) {
                // let api = this.api();

                // const $footer = $(api.column(0).footer()).closest('tfoot');

                // totalRealizationQty = data.reduce((a, b) => intVal(a) + intVal(b.qtyRealisasi ?? 0), 0);

                // $footer.find('.total_realization_qty').html(trimLocale(totalRealizationQty));

                // grandTotal = (api
                //     .column('.realization_total')
                //     .data() ?? [])
                //     .reduce((a, b) => intVal(a) + intVal(b), 0);

                // $footer.find('.grand_total').html(`Rp&nbsp;${parseNumToLocale(grandTotal)}`);
            // },
        });
    }

    $('.location_keuangan_loaded').on('change', fetchLocationKeuanganData);
    $('.kandang_keuangan_loaded').on('change', fetchKandangKeuanganData);
});
</script>
