<div class="card mb-1">
    <div id="headingPenjualanProdukCollapse" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#penjualanProdukCollapse" aria-expanded="true" aria-controls="penjualanProdukCollapse">
        <span class="lead collapse-title">Produk Penjualan</span>
    </div>
    <div id="penjualanProdukCollapse" role="tabpanel" aria-labelledby="headingPenjualanProdukCollapse" class="collapse show" aria-expanded="true">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="penjualan_produk_datatable" class="table" style="margin: 0 !important;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kandang/Hatcher</th>
                            <th>Nama Produk</th>
                            <th>Harga Satuan</th>
                            <th>Bobot AVG</th>
                            <th>UOM</th>
                            <th>QTY</th>
                            <th>Total Bobot</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- DATA from AJAX --}}
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bolder">
                            <td colspan="6">TOTAL PENJUALAN</td>
                            <td colspan="3" class="text-right grand_total"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
