<h4 class="mt-2">Pengeluaran Overhead</h4>
<div class="table-responsive mt-2" style="overflow-x: auto;">
    <table id="datatable" class="table" style="margin: 0 0 !important;">
        <thead>
            <tr class="text-center">
                <th rowspan="2" style="vertical-align: middle">No</th>
                <th rowspan="2" style="vertical-align: middle">Jenis</th>
                <th colspan="3">Budget Pengajuan</th>
                <th colspan="5">Realisasi</th>
            </tr>
            <tr class="text-center">
                <th>QTY</th>
                <th>Harga Satuan</th>
                <th>Total</th>
                <th>Tanggal</th>
                <th>Noref</th>
                <th>QTY</th>
                <th>Harga Satuan</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody id="overhead-tbody"></tbody>

        <tfoot>
            <tr class="font-weight-bolder">
                <td>Total Penjualan</td>
                <td></td>
                <td class="text-center">8</td>
                <td></td>
                <td class="text-center">Rp <span>274.235.400</span></td>
                <td></td>
                <td></td>
                <td class="text-center">2</td>
                <td></td>
                <td class="text-center">Rp <span>265.678.820</span></td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    const overheadData = [
        {
            id: 1,
            kategori: "Uang Makan",
            subkategori: []
        },
        {
            id: 2,
            kategori: "Pengeluaran Operasional",
            subkategori: [
                {
                    nama: "Gaji Karyawan",
                    qtyPengajuan: 8,
                    hargaSatuanPengajuan: 34279400,
                    totalPengajuan: 274235400,
                    tanggal: "29-11-2024",
                    noRef: "-",
                    qtyRealisasi: 2,
                    hargaSatuanRealisasi: 132839410,
                    totalRealisasi: 265678820
                }
            ]
        },
    ];


    function generateBudgetTable(data) {
        const tbody = document.getElementById("overhead-tbody");
        tbody.innerHTML = "";
        const titleRow = document.createElement("tr")
        titleRow.innerHTML = `<td colspan="9"><h4>Budget</h4></td>`
        tbody.appendChild(titleRow)

        data.forEach((item) => {
            const mainRow = document.createElement("tr");
            mainRow.className = "text-center";

            mainRow.innerHTML = `
                <td>${item.id}</td>
                <td class="text-left">${item.kategori}</td>
                <td colspan="8"></td>
            `;

            tbody.appendChild(mainRow);

            if (item.subkategori && item.subkategori.length > 0) {
                item.subkategori.forEach((sub) => {
                    const subRow = document.createElement("tr");
                    subRow.className = "text-center";

                    subRow.innerHTML = `
                        <td></td>
                        <td class="text-left">${sub.nama}</td>
                        <td>${sub.qtyPengajuan}</td>
                        <td>Rp <span>${sub.hargaSatuanPengajuan.toLocaleString("id-ID")}</span></td>
                        <td>Rp <span>${sub.totalPengajuan.toLocaleString("id-ID")}</span></td>
                        <td>${sub.tanggal}</td>
                        <td>${sub.noRef}</td>
                        <td>${sub.qtyRealisasi}</td>
                        <td>Rp <span>${sub.hargaSatuanRealisasi.toLocaleString("id-ID")}</span></td>
                        <td>Rp <span>${sub.totalRealisasi.toLocaleString("id-ID")}</span></td>
                    `;

                    tbody.appendChild(subRow);
                });
            }
        });
    }

    generateBudgetTable(overheadData);

</script>
