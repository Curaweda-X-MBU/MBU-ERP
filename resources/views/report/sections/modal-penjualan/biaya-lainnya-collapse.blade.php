<div class="card mb-1">
    <div id="headingPenjualanLainnyaCollapse" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#penjualanLainnyaCollapse" aria-expanded="true" aria-controls="penjualanLainnyaCollapse">
        <span class="lead collapse-title">Biaya Lainnya</span>
    </div>
    <div id="penjualanLainnyaCollapse" role="tabpanel" aria-labelledby="headingPenjualanLainnyaCollapse" class="collapse show" aria-expanded="true">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="location_penjualan_lainnya_datatable" class="table" style="margin: 0 !important;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Item</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- DATA from AJAX --}}
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bolder">
                            <td>TOTAL BIAYA LAINNYA</td>
                            <td colspan="2" class="text-right grand_total"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
