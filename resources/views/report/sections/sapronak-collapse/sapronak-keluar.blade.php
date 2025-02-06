<div class="card mb-1">
    <div id="headingSapronakKeluarCollapse" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#sapronakKeluarCollapse" aria-expanded="true" aria-controls="sapronakKeluarCollapse">
        <span class="lead collapse-title">Sapronak Keluar</span>
    </div>
    <div id="sapronakKeluarCollapse" role="tabpanel" aria-labelledby="headingSapronakKeluarCollapse" class="collapse show" aria-expanded="true">
        <div class="table-responsive">
            <table id="sapronak_keluar_datatable" class="table" style="margin: 0 !important;">
                <thead>
                    <tr class="text-center">
                        <th>Tanggal</th>
                        <th>No. Referensi</th>
                        <th>Jenis Transaksi</th>
                        <th>Jenis Sapronak</th>
                        <th>Gudang Tujuan</th>
                        <th></th>
                        <th>Kuantitas</th>
                        <th>Keterangan</th>
                </thead>
                <tbody>
                    {{-- DATA from AJAX --}}
                    <tr>
                        <td class="text-center" colspan="8">Mengambil data ...</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="font-weight-bolder">
                        <td colspan="5">Total Sapronak Keluar</td>
                        <td class="total_1 text-center">-</td>
                        <td class="total_2 text-center">-</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>


