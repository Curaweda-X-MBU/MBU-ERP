<div class="table-responsive mt-2" style="overflow-x: hidden;">
    <div class="row bg-primary d-flex justify-content-center align-items-center py-1 text-white px-2">
        <p class="col-md-6 mb-0">Bukan Biaya Operasional (NBOP)</p>
        <p class="col-md-6 mb-0 text-right">Total: Rp <span id="total-biaya-non-bop">0,00</span></p>
    </div>
    <table id="datatableNonBOP" class="table table-bordered" style="margin: 0 0 !important;">
        <thead>
            <tr class="bg-light text-center">
                <th>No</th>
                <th>Sub Kategori</th>
                <th>Catatan</th>
                <th>QTY</th>
                <th>UOM</th>
                <th>Nominal</th>
                <th>Tanggal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr class="text-center">
                <td>1</td>
                <td>Konsumsi</td>
                <td>Rapat Direktur</td>
                <td>11</td>
                <td>%</td>
                <td class="nominal-non-bop">1.200.000</td>
                <td>12-12-2024</td>
                <td>
                    @php
                        $status = 2;
                    @endphp
                    @switch($status)
                        @case(1)
                            <div class="badge badge-pill badge-primary">Dibayar</div>
                            @break
                        @case(2)
                            <div class="badge badge-pill badge-danger">Belum Dibayar</div>
                            @break
                        @default
                            <div class="badge badge-pill badge-secondary">N/A</div>
                    @endswitch
                </td>
            </tr>
        </tbody>
    </table>
</div>
