<div class="card mb-1">
    <div id="headingCollapse1" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
        <span class="lead collapse-title">DOC BROILER</span>
    </div>
    <div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse show" aria-expanded="true">
        <div class="table-responsive">
            <table id="perhitungan_doc_datatable" class="table" style="margin: 0 !important;">
                <thead>
                    <tr class="text-center">
                        <th>Tanggal</th>
                        <th>No. Referensi</th>
                        <th>QTY Masuk</th>
                        <th>QTY Pakai</th>
                        <th>Kategori Produk</th>
                        <th>Harga Beli/Qty (Rp)</th>
                        <th>Total Harga (Rp)</th>
                        <th>Keterangan</th>
                </thead>
                <tbody>
                    {{-- DATA from AJAX --}}
                    <tr>
                        <td class="text-center" colspan="8">Mengambil data ...</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="font-weight-bolder text-center">
                        <td class="text-left">TOTAL DOC BROILER</td>
                        <td></td>
                        <td class="total_qty_masuk">-</td>
                        <td class="total_qty_pakai">-</td>
                        <td></td>
                        <td class="total_harga_beli">-</td>
                        <td class="grand_total">-</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>


