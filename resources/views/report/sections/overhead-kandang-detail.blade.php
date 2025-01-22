<div class="card">
    <div class="card-body">
        <h4>Pengeluaran Overhead</h4>
        {{-- table --}}
        <div class="table-responsive mt-2">
            <table id="datatable" class="table">
                <thead>
                    <tr class="text-center">
                        <th>No</th>
                        <th>Tanggal</th>
                        <th class="text-left">Produk</th>
                        <th>Kuantitas</th>
                        <th>Rp/Kuantitas</th>
                        <th>Total</th>
                        <th>Rp/Ekor</th>
                    </tr>
                </thead>

                <tbody>
                    <tr class="text-center">
                        <td>1</td>
                        <td>3-12-2024</td>
                        <td class="text-left">Sewa Kandang</td>
                        <td>1</td>
                        <td>Rp <span>150.000.000</span></td>
                        <td>Rp <span>150.000.000</span></td>
                        <td>3.000</td>
                    </tr>
                </tbody>

                <tfoot>
                    <tr class="font-weight-bolder text-center border-bottom">
                        <td colspan="3" class="text-left">Total HPP Ekspedisi</td>
                        <td>1</td>
                        <td>Rp <span>150.000.000</span></td>
                        <td>Rp <span>150.000.000</span></td>
                        <td>3.000</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- formula --}}
        <div class="card mt-2">
            <div class="card-body border d-flex align-items-center justify-content-center">
                <div class="table-responsive">
                    <div style="min-width: 1000px;">
                        <div class="row w-100 font-weight-bolder">
                            <div class="col-2 text-center d-flex align-items-center">Pembelian Kandang <span class="pl-2">=</span></div>
                            <div class="col-4">
                                <div class="row text-center d-flex align-items-center justify-content-center">
                                    Populasi Akhir KANDANG X Pemakaian Di FARM
                                </div>
                                <div><hr class="p-0 border-dark" style="border-width: 3px;"></div>
                                <div class="row d-flex align-items-center justify-content-center">
                                    Populasi Akhir Proyek
                                </div>
                            </div>
                            <div class="col-1 d-flex align-items-center justify-content-center">=</div>
                            <div class="col-3">
                                <div class="row text-center d-flex align-items-center justify-content-center">
                                    50.000 X 512.447.584
                                </div>
                                <div><hr class="p-0 border-dark" style="border-width: 3px;"></div>
                                <div class="row d-flex align-items-center justify-content-center">
                                    387.200
                                </div>
                            </div>
                            <div class="col-2 text-center d-flex align-items-center"><span class="pl-2 pr-3">=</span>66.173.153.23</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- total --}}
        <div class="row mt-1 mx-0 font-weight-bolder d-flex justify-content-between">
            <div class="">TOTAL PEMBEBANAN KANDANG</div>
            <div class="text-right ">66.173.501.29</div>
        </div>
    </div>
</div>
