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
    const period = getQueryParam('period');

    function secureReduce(arr) {
        return arr.reduce((a, b) => {
            let add = parseFloat(b);

            if (isNaN(add)) {
                add = 0;
            }
            return intVal(a) + add;
        }, 0);
    }

    function fetchLocationKeuanganData() {
        fetchKeuanganData("{{ route('report.detail.location.keuangan', [ 'location' => $detail->location_id ]) . '?period=' }}" + period);
    }

    function fetchKandangKeuanganData() {
        fetchKeuanganData("{{ route('report.detail.kandang.keuangan', [ 'location' => $detail->location_id, 'project' => $detail->project_id ]) . '?period=' }}" + period);
    }

    function fetchKeuanganData(route) {
        $.get(route)
            .then(function(result) {
                if (!result.error) {
                    const pengeluaran = result.pengeluaran;
                    populateHPPTable(pengeluaran);

                    const labaRugi = result.laba_rugi;
                    console.log((labaRugi.bruto || []))
                    populateLabaRugiTable(labaRugi.bruto ?? [], labaRugi.netto ?? []);
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
                    className: "text-right budget_rp_ekor",
                    render: function(data) {
                        return parseNumToLocale(data);
                    }
                },
                {
                    data: "budget_rp_kg",
                    className: "text-right budget_rp_kg",
                    render: function(data) {
                        return parseNumToLocale(data);
                    }
                },
                {
                    data: "budget_rp",
                    className: "text-right budget_rp",
                    render: function(data) {
                        return parseNumToLocale(data);
                    }
                },
                {
                    data: "realization_rp_ekor",
                    className: "text-right realization_rp_ekor",
                    render: function(data) {
                        return parseNumToLocale(data);
                    }
                },
                {
                    data: "realization_rp_kg",
                    className: "text-right realization_rp_kg",
                    render: function(data) {
                        return parseNumToLocale(data);
                    }
                },
                {
                    data: "realization_rp",
                    className: "text-right realization_rp",
                    render: function(data) {
                        return parseNumToLocale(data);
                    }
                },
            ],
            footerCallback: function(row, data) {
                let api = this.api();

                const $footer = $(api.column(0).footer()).closest('tfoot');

                budgetRpEkor = secureReduce((api
                    .column('.budget_rp_ekor')
                    .data() ?? []));

                $footer.find('.total_budget_rp_ekor').html(`Rp&nbsp;${parseNumToLocale(budgetRpEkor)}`);
                budgetRpKg = secureReduce((api
                    .column('.budget_rp_kg')
                    .data() ?? []));

                $footer.find('.total_budget_rp_kg').html(`Rp&nbsp;${parseNumToLocale(budgetRpKg)}`);
                budgetRp = secureReduce((api
                    .column('.budget_rp')
                    .data() ?? []));

                $footer.find('.total_budget_rp').html(`Rp&nbsp;${parseNumToLocale(budgetRp)}`);

                realizationRpEkor = secureReduce((api
                    .column('.realization_rp_ekor')
                    .data() ?? []));

                $footer.find('.total_realization_rp_ekor').html(`Rp&nbsp;${parseNumToLocale(realizationRpEkor)}`);
                realizationRpKg = secureReduce((api
                    .column('.realization_rp_kg')
                    .data() ?? []));

                $footer.find('.total_realization_rp_kg').html(`Rp&nbsp;${parseNumToLocale(realizationRpKg)}`);
                realizationRp = secureReduce((api
                    .column('.realization_rp')
                    .data() ?? []));

                $footer.find('.total_realization_rp').html(`Rp&nbsp;${parseNumToLocale(realizationRp)}`);
            },
        });
    }

    function populateLabaRugiTable(data1, data2) {
        const $tbody = $('#laba-rugi-tbody');
        $tbody.empty();

        // START :: Laba Rugi Brutto
        data1.forEach((item) => {
            const row1 = document.createElement("tr");
            row1.className = "text-right";

            row1.innerHTML = `
                <td colspan="3" class="text-left">${item.jenis}</td>
                <td>Rp <span>${parseNumToLocale(item.rp_ekor)}</span></td>
                <td>Rp <span>${parseNumToLocale(item.rp_kg)}</span></td>
                <td>Rp <span>${parseNumToLocale(item.rp)}</span></td>
            `;
            $tbody.append(row1);
        });

        // substract
        sumEkor1 = data1[0].rp_ekor - data1[1].rp_ekor;
        sumKg1 = data1[0].rp_kg - data1[1].rp_kg;
        sum1 = data1[0].rp - data1[1].rp;

        const footerRow1 = document.createElement("tr");
        footerRow1.className = "font-weight-bolder text-right";
        footerRow1.innerHTML = `
            <td></td>
            <td colspan="2" class="text-left pt-1"><h4>LABA RUGI BRUTTO</h4></td>
            <td>Rp <span>${parseNumToLocale(sumEkor1)}</span></td>
            <td>Rp <span>${parseNumToLocale(sumKg1)}</span></td>
            <td>Rp <span>${parseNumToLocale(sum1)}</span></td>
        `;
        $tbody.append(footerRow1);
        // END :: Laba Rugi Brutto

        // START :: Laba Rugi Netto
        data2.forEach((item) => {
            const row2 = document.createElement("tr");
            row2.className = "text-right";

            row2.innerHTML = `
                <td colspan="3" class="text-left">${item.name}</td>
                <td>Rp <span>${parseNumToLocale(item.realization_rp_ekor)}</span></td>
                <td>Rp <span>${parseNumToLocale(item.realization_rp_kg)}</span></td>
                <td>Rp <span>${parseNumToLocale(item.realization_rp)}</span></td>
            `;
            $tbody.append(row2);
        });

        // add to substract later
        sumEkor2 = data2.reduce((a, b) => intVal(a) + intVal(b.realization_rp_ekor), 0);
        sumKg2 = data2.reduce((a, b) => intVal(a) + intVal(b.realization_rp_kg), 0);
        sum2 = data2.reduce((a, b) => intVal(a) + intVal(b.realization_rp), 0);

        const footerRow2 = document.createElement("tr");
        footerRow2.className = "font-weight-bolder text-right";
        footerRow2.innerHTML = `
            <td></td>
            <td colspan="2" class="text-left pt-1"><h4>SUB TOTAL</h4></td>
            <td>Rp <span>${parseNumToLocale(sumEkor2)}</span></td>
            <td>Rp <span>${parseNumToLocale(sumKg2)}</span></td>
            <td>Rp <span>${parseNumToLocale(sum2)}</span></td>
        `;
        $tbody.append(footerRow2);
        // END :: Laba Rugi Netto

        $('#laba-rugi-perusahaan').html(`
            <td colspan="3" class="text-left">LABA RUGI NETTO</td>
            <td>Rp <span>${parseNumToLocale(sumEkor1 - sumEkor2)}</span></td>
            <td>Rp <span>${parseNumToLocale(sumKg1 - sumKg2)}</span></td>
            <td>Rp <span>${parseNumToLocale(sum1 - sum2)}</span></td>
        `);

        $('#laba_rugi_bruto').text(parseNumToLocale(sum1));
        $('#laba_rugi_netto').text(parseNumToLocale(sum1 - sum2));
    }

    $('.location_keuangan_loaded').on('change', fetchLocationKeuanganData);
    $('.kandang_keuangan_loaded').on('change', fetchKandangKeuanganData);
});
</script>
