<h4 class="mt-3">Laba Rugi</h4>
<div class="card mb-1">
    <div id="headingCollapse2" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
        <span class="lead collapse-title">Laba Rugi Perusahaan</span>
    </div>
    <div id="collapse2" role="tabpanel" aria-labelledby="headingCollapse2" class="collapse show" aria-expanded="true">
        <div class="table-responsive">
            <table class="table w-100">
                <thead>
                    <tr class="text-center">
                        <th colspan="3" class="text-left">Jenis</th>
                        <th>Rp/Ekor</th>
                        <th>Rp/Kg</th>
                        <th>Jumlah(Rp)</th>
                    </tr>
                </thead>

                <tbody id="laba-rugi-tbody"></tbody>

                <tfoot>
                    <tr class="text-center font-weight-bolder">
                        <td colspan="3" class="text-left">LABA RUGI PERUSAHAAN</td>
                        <td>Rp <span>750,94</span></td>
                        <td>Rp <span>750,94</span></td>
                        <td>Rp <span>274.235.400</span></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    const rugiBruttoData = [
        {
            id: 1,
            jenis: "Penjualan Ayam Besar",
            ekor: 68214,
            kg: 427160,
            jumlah: 2541580000
        },
        {
            id: 2,
            jenis: "Penjualan Ayam Besar",
            ekor: 68214,
            kg: 427160,
            jumlah: 2541580000
        },
    ];

    const rugiNettoData = [
        {
            id: 1,
            jenis: "Pengeluaran Overhead",
            ekor: 68214,
            kg: 427160,
            jumlah: 2541580000
        },
    ];

    function generateTable(data1, data2) {
        const tbody = document.getElementById("laba-rugi-tbody");
        tbody.innerHTML = "";

        // START :: Laba Rugi Brutto
        data1.forEach((item) => {
            const row1 = document.createElement("tr");
            row1.className = "text-center";

            row1.innerHTML = `
                <td colspan="3" class="text-left">${item.jenis}</td>
                <td>Rp <span>${item.ekor.toLocaleString("id-ID")}</span></td>
                <td>Rp <span>${item.kg.toLocaleString("id-ID")}</span></td>
                <td>Rp <span>${item.jumlah.toLocaleString("id-ID")}</span></td>
            `;
            tbody.appendChild(row1);
        });

        const footerRow1 = document.createElement("tr");
        footerRow1.className = "font-weight-bolder text-center";
        footerRow1.innerHTML = `
            <td></td>
            <td colspan="2" class="text-left pt-1"><h4>LABA RUGI BRUTTO</h4></td>
            <td>Rp <span>750,94</span></td>
            <td>Rp <span>750,94</span></td>
            <td>Rp <span>274.235.400</span></td>
        `;
        tbody.appendChild(footerRow1);
        // END :: Laba Rugi Brutto

        // START :: Laba Rugi Netto
        data2.forEach((item) => {
            const secondRow = document.createElement("tr");
            secondRow.className = "text-center";

            secondRow.innerHTML = `
                <td colspan="3" class="text-left">${item.jenis}</td>
                <td>Rp <span>${item.ekor.toLocaleString("id-ID")}</span></td>
                <td>Rp <span>${item.kg.toLocaleString("id-ID")}</span></td>
                <td>Rp <span>${item.jumlah.toLocaleString("id-ID")}</span></td>
            `;
            tbody.appendChild(secondRow);
        });

        const footerRow2 = document.createElement("tr");
        footerRow2.className = "font-weight-bolder text-center";
        footerRow2.innerHTML = `
            <td></td>
            <td colspan="2" class="text-left pt-1"><h4>LABA RUGI NETTO</h4></td>
            <td>Rp <span>750,94</span></td>
            <td>Rp <span>750,94</span></td>
            <td>Rp <span>174.235.400</span></td>
        `;
        tbody.appendChild(footerRow2);
        // END :: Laba Rugi Netto
    }

    generateTable(rugiBruttoData, rugiNettoData);
</script>



