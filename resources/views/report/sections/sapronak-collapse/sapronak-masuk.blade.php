<div class="card mb-1">
    <div id="headingSapronakMasukCollapse" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#sapronakMasukCollapse" aria-expanded="true" aria-controls="headingSapronakMasukCollapse">
        <span class="lead collapse-title">Sapronak Masuk</span>
    </div>
    <div id="sapronakMasukCollapse" role="tabpanel" aria-labelledby="headingSapronakMasukCollapse" class="collapse show" aria-expanded="true">
        <div class="table-responsive">
            <table id="sapronak_masuk_datatable" class="table" style="margin: 0 !important;">
                <thead>
                    <tr class="text-center">
                        <th>Tanggal</th>
                        <th>No. Referensi</th>
                        <th>Jenis Transaksi</th>
                        <th>Jenis Sapronak</th>
                        <th>Gudang Asal</th>
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
                        <td colspan="5">Total Sapronak Masuk</td>
                        <td class="total_1 text-center">-</td>
                        <td class="total_2 text-center">-</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>


