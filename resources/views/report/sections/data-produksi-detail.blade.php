<div class="card">
    <input type="hidden" class="location_data_produksi_loaded" value="0">
    <input type="hidden" class="kandang_data_produksi_loaded" value="0">
    <div class="card-body">
        <h4>Data Produksi</h4>
        <div class="row mt-2">
            {{-- left column --}}
            <div class="col-md-6 col-12 border-right">
                {{-- pembelian --}}
                <div class="card-datatable">
                    <div class="table-responsive mb-2">
                        <table id="datatable" class="w-100">
                            <tbody>
                                <tr>
                                    <td class="font-weight-bolder">Pembelian</td>
                                </tr>
                                <tr>
                                    <td class="col-md-6">Populasi Awal</td>
                                    <td id="populasi_awal" class="col-md-3 text-right font-weight-bolder">0</td>
                                    <td class="col-md-3">Ekor</td>
                                </tr>
                                <tr>
                                    <td class="col-md-6">Claim Culling</td>
                                    <td id="claim_culling" class="col-md-3 text-right font-weight-bolder">0</td>
                                    <td class="col-md-3">Ekor</td>
                                </tr>
                                <tr>
                                    <td class="col-md-6">Populasi Akhir</td>
                                    <td id="populasi_akhir" class="col-md-3 text-right font-weight-bolder">0</td>
                                    <td class="col-md-3">Ekor</td>
                                </tr>
                                <tr>
                                    <td class="col-md-6">Pakan Masuk</td>
                                    <td id="pakan_masuk" class="col-md-3 text-right font-weight-bolder">0</td>
                                    <td class="col-md-3">Kg</td>
                                </tr>
                                <tr>
                                    <td class="col-md-6">Pakan Terpakai</td>
                                    <td id="pakan_terpakai" class="col-md-3 text-right font-weight-bolder">0</td>
                                    <td class="col-md-3">Kg</td>
                                </tr>
                                <tr>
                                    <td class="col-md-6">Pakan Terpakai per Ekor</td>
                                    <td id="pakan_terpakai_per_ekor" class="col-md-3 text-right font-weight-bolder">0,00</td>
                                    <td class="col-md-3">Kg</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- penjualan --}}
                <div class="card-datatable">
                    <div class="table-responsive mb-2">
                        <table id="datatable" class="w-100">
                            <tbody>
                                <tr>
                                    <td class="font-weight-bolder">Penjualan</td>
                                </tr>
                                <tr>
                                    <td class="col-md-6">Penjualan (Kg)</td>
                                    <td id="bobot_total" class="col-md-3 text-right font-weight-bolder">0</td>
                                    <td class="col-md-3">Kg</td>
                                </tr>
                                <tr>
                                    <td class="col-md-6">Penjualan (Ekor)</td>
                                    <td id="qty_total" class="col-md-3 text-right font-weight-bolder">0</td>
                                    <td class="col-md-3">Ekor</td>
                                </tr>
                                <tr>
                                    <td class="col-md-6">Bobot Rata-Rata</td>
                                    <td id="bobot_rata" class="col-md-3 text-right font-weight-bolder">0</td>
                                    <td class="col-md-3">Kg/Ekor</td>
                                </tr>
                                <tr>
                                    <td class="col-md-6">Harga Jual Rata-Rata</td>
                                    <td id="harga_jual_rata" class="col-md-3 text-right font-weight-bolder">0</td>
                                    <td class="col-md-3">Rupiah</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- right column --}}
            <div class="col-md-6 col-12 border-left">
                {{-- performance --}}
                <div class="card-datatable">
                    <div class="table-responsive mb-2">
                        <table id="datatable" class="w-100">
                            <tbody>
                                <tr>
                                    <td class="font-weight-bolder">Performance</td>
                                </tr>
                                <tr>
                                    <td class="col-md-5">Deplesi</td>
                                    <td id="deplesi" class="col-md-3 text-right font-weight-bolder">0</td>
                                    <td class="col-md-2">Ekor</td>
                                    <td class="mortalitas_act col-md-1 text-primary">0</td>
                                    <td class="col-md-1">%</td>
                                </tr>
                                <tr>
                                    <td class="col-md-5">Umur</td>
                                    <td id="umur" class="col-md-3 text-right font-weight-bolder">0</td>
                                    <td class="col-md-2">Hari</td>
                                </tr>
                                <tr>
                                    <td class="col-md-5">Mortalitas Std</td>
                                    <td id="mortalitas_std" class="col-md-3 text-right font-weight-bolder">0</td>
                                </tr>
                                <tr>
                                    <td class="col-md-5">Mortalitas Act</td>
                                    <td class="mortalitas_act col-md-3 text-right font-weight-bolder">0</td>
                                </tr>
                                <tr>
                                    <td class="col-md-5">DEFF Mortalitas</td>
                                    <td id="deff_mortalitas" class="col-md-3 text-right font-weight-bolder">0</td>
                                </tr>
                                <tr>
                                    <td class="col-md-5">FCR Std</td>
                                    <td id="fcr_std" class="col-md-3 text-right font-weight-bolder">0</td>
                                </tr>
                                <tr>
                                    <td class="col-md-5">FCR Act</td>
                                    <td id="fcr_act" class="col-md-3 text-right font-weight-bolder">0</td>
                                </tr>
                                <tr>
                                    <td class="col-md-5">DEFF FCR</td>
                                    <td id="deff_fcr" class="col-md-3 text-right font-weight-bolder">0</td>
                                </tr>
                                <tr>
                                    <td class="col-md-5">ADG</td>
                                    <td id="adg" class="col-md-3 text-right font-weight-bolder">0</td>
                                    <td class="col-md-2">Gr/Hari</td>
                                </tr>
                                <tr>
                                    <td class="col-md-5">IP</td>
                                    <td id="ip" class="col-md-3 text-right font-weight-bolder">0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- selisih --}}
                <div class="card-datatable">
                    <div class="table-responsive mb-2">
                        <table id="datatable" class="w-100">
                            <tbody>
                                <tr>
                                    <td class="font-weight-bolder">Selisih</td>
                                </tr>
                                <tr>
                                    <td class="col-md-5">Selisih Ayam</td>
                                    <td class="col-md-3 text-right font-weight-bolder text-danger">-</td>
                                    <td class="col-md-2">Ekor</td>
                                    <td class="col-md-2"></td>
                                </tr>
                                <tr>
                                    <td class="col-md-5">% Selisih Ayam</td>
                                    <td class="col-md-3 text-right font-weight-bolder">-</td>
                                    <td class="col-md-2">%</td>
                                </tr>
                                <tr>
                                    <td class="col-md-5">Selisih Pakan</td>
                                    <td class="col-md-3 text-right font-weight-bolder">-</td>
                                    <td class="col-md-2">Kg</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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

    function fetchLocationDataProduksiData() {
        fetchDataProduksiData("{{ route('report.detail.location.produksi', [ 'location' => $detail->location_id ]) . '?period=' . $detail->period }}");
    }

    function fetchKandangDataProduksiData() {
        fetchDataProduksiData("{{ route('report.detail.kandang.produksi', [ 'location' => $detail->location_id, 'project' => $detail->project_id ]) . '?period=' . $detail->period }}");
    }

    function fetchDataProduksiData(route) {
        $.get(route)
            .then(function(result) {
                if (!result.error) {
                    // PEMBELIAN
                    const pembelian = result.pembelian;

                    $('#populasi_awal').text(trimLocale(pembelian.populasi_awal));
                    $('#claim_culling').text(trimLocale(pembelian.culling));
                    $('#populasi_akhir').text(trimLocale(pembelian.populasi_akhir));
                    $('#pakan_masuk').text(trimLocale(pembelian.pakan_masuk));
                    $('#pakan_terpakai').text(trimLocale(pembelian.pakan_terpakai));
                    $('#pakan_terpakai_per_ekor').text(trimLocale(pembelian.pakan_terpakai_per_ekor));

                    // PENJUALAN
                    const penjualan = result.penjualan;
                    console.log(penjualan);
                    $('#bobot_total').text(trimLocale(penjualan.penjualan_kg));
                    $('#qty_total').text(trimLocale(penjualan.penjualan_ekor));
                    $('#bobot_rata').text(trimLocale(penjualan.bobot_rata));
                    $('#harga_jual_rata').text(trimLocale(penjualan.harga_jual_rata));

                    // PERFORMANCE
                    const performance = result.performance;
                    $('#deplesi').text(trimLocale(performance.deplesi));
                    $('#umur').text(trimLocale(performance.umur));
                    $('#mortalitas_std').text(trimLocale(performance.mortalitas_std));
                    $('.mortalitas_act').text(trimLocale(performance.mortalitas_act));
                    $('#deff_mortalitas').text(trimLocale(performance.deff_mortalitas));
                    $('#fcr_std').text(trimLocale(performance.fcr_std));
                    $('#fcr_act').text(trimLocale(performance.fcr_act));
                    $('#deff_fcr').text(trimLocale(performance.deff_fcr));
                    $('#adg').text(trimLocale(performance.adg));
                    $('#ip').text(trimLocale(performance.ip));
                }
            });
    }

    $('.location_data_produksi_loaded').on('change', fetchLocationDataProduksiData);
    $('.kandang_data_produksi_loaded').on('change', fetchKandangDataProduksiData);
});
</script>
