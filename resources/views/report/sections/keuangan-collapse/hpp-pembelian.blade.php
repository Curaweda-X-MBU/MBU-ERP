<h4 class="mt-3">HPP Pembelian</h4>
<div class="card mb-1">
    <div id="headingCollapse1" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
        <span class="lead collapse-title">Pembelian HPP Budgeting dan HPP Realisasi </span>
    </div>
    <div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse show" aria-expanded="true">
        <div class="table-responsive">
            <table class="table w-100">
                <thead>
                    <tr class="text-center">
                        <th rowspan="2" style="vertical-align: middle">No</th>
                        <th rowspan="2" style="vertical-align: middle">Jenis</th>
                        <th colspan="3" class="border-right">Budgeting</th>
                        <th colspan="3">Realisasi</th>
                    </tr>
                    <tr class="text-center">
                        <th>Rp/Ekor</th>
                        <th>Rp/Kg</th>
                        <th class="border-right">Jumlah(Rp)</th>
                        <th>Rp/Ekor</th>
                        <th>Rp/Kg</th>
                        <th class="border-right">Jumlah(Rp)</th>
                    </tr>
                </thead>

                <tbody id="hpp-tbody"></tbody>

                <tfoot>
                    <tr class="font-weight-bolder text-center">
                        <td class="text-left">HPP</td>
                        <td></td>
                        <td>Rp <span>274.235.400</span></td>
                        <td>Rp <span>274.235.400</span></td>
                        <td>Rp <span>274.235.400</span></td>
                        <td>Rp <span>274.235.400</span></td>
                        <td>Rp <span>274.235.400</span></td>
                        <td>Rp <span>265.678.820</span></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    const hppPengeluaranData = [
        {
            id: 1,
            jenis: "Pembelian DOC BROILER",
            budgeting: {
                ekor: 68214,
                kg: 427160,
                jumlah: 2541580000
            },
            realisasi: {
                ekor: 68214,
                kg: 427160,
                jumlah: 2541580000
            }
        },
    ];
    const hppBahanBakuData = [
        {
            id: 1,
            jenis: "Pembelian DOC BROILER",
            budgeting: {
                ekor: 68214,
                kg: 427160,
                jumlah: 2541580000
            },
            realisasi: {
                ekor: 68214,
                kg: 427160,
                jumlah: 2541580000
            }
        },
    ];

    function generateTable(data1, data2) {
        const tbody = document.getElementById("hpp-tbody");
        tbody.innerHTML = "";

        // START :: HPP dan Pengeluaran
        const titleRow1 = document.createElement("tr");
        titleRow1.innerHTML = `<td colspan="8" class="pt-1"><h4>HPP dan Pengeluaran</h4></td>`;
        tbody.appendChild(titleRow1);

        data1.forEach((item) => {
            const row1 = document.createElement("tr");
            row1.className = "text-center";

            row1.innerHTML = `
                <td>${item.id}</td>
                <td class="text-left">${item.jenis}</td>
                <td>Rp <span>${item.budgeting.ekor.toLocaleString("id-ID")}</span></td>
                <td>Rp <span>${item.budgeting.kg.toLocaleString("id-ID")}</span></td>
                <td>Rp <span>${item.budgeting.jumlah.toLocaleString("id-ID")}</span></td>
                <td>Rp <span>${item.realisasi.ekor.toLocaleString("id-ID")}</span></td>
                <td>Rp <span>${item.realisasi.kg.toLocaleString("id-ID")}</span></td>
                <td>Rp <span>${item.realisasi.jumlah.toLocaleString("id-ID")}</span></td>
            `;
            tbody.appendChild(row1);
        });
        // END :: HPP dan Pengeluaran

        // START :: HPP dan Bahan Baku
        const titleRow2 = document.createElement("tr");
        titleRow2.innerHTML = `<td colspan="8" class="pt-1"><h4>HPP dan Bahan Baku</h4></td>`;
        tbody.appendChild(titleRow2);

        data2.forEach((item) => {
            const secondRow = document.createElement("tr");
            secondRow.className = "text-center";

            secondRow.innerHTML = `
                <td>${item.id}</td>
                <td class="text-left">${item.jenis}</td>
                <td>Rp <span>${item.budgeting.ekor.toLocaleString("id-ID")}</span></td>
                <td>Rp <span>${item.budgeting.kg.toLocaleString("id-ID")}</span></td>
                <td>Rp <span>${item.budgeting.jumlah.toLocaleString("id-ID")}</span></td>
                <td>Rp <span>${item.realisasi.ekor.toLocaleString("id-ID")}</span></td>
                <td>Rp <span>${item.realisasi.kg.toLocaleString("id-ID")}</span></td>
                <td>Rp <span>${item.realisasi.jumlah.toLocaleString("id-ID")}</span></td>
            `;
            tbody.appendChild(secondRow);
        });
        // END :: HPP dan Bahan Baku
    }

    generateTable(hppPengeluaranData, hppBahanBakuData);
</script>


