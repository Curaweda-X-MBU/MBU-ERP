<h4 class="mt-2">Penjualan Ayam Besar</h4>
<div class="table-responsive mt-2" style="overflow-x: auto;">
    <table id="datatable" class="table" style="margin: 0 0 !important;">
        <thead>
            <tr class="text-center">
              <th rowspan="2" style="vertical-align: middle">Tanggal</th>
              <th rowspan="2" style="vertical-align: middle">Umur</th>
              <th rowspan="2" style="vertical-align: middle">No. DTPS</th>
              <th rowspan="2" style="vertical-align: middle">Costumer</th>
              <th colspan="2">Jumlah</th>
              <th rowspan="2" style="vertical-align: middle">Harga</th>
              <th rowspan="2" style="vertical-align: middle">CN</th>
              <th rowspan="2" style="vertical-align: middle">Total</th>
              <th rowspan="2" style="vertical-align: middle">Kandang</th>
              <th rowspan="2" style="vertical-align: middle">Status</th>
            </tr>
            <tr class="text-center">
              <th>Ekor</th>
              <th>Kg</th>
            </tr>
          </thead>
        <tbody>
            <tr class="text-center">
                <td>1-10-2024</td>
                <td>19</td>
                <td class="text-primary">PND.MBU00912</td>
                <td>Ariyanti</td>
                <td>1.575</td>
                <td>1.706,20</td>
                <td>21.000</td>
                <td>0</td>
                <td>35.830.200</td>
                <td>Pandeglang 8</td>
                <td>
                    @php
                        $status = 1;
                    @endphp
                    @switch($status)
                        @case(1)
                            <div class="badge badge-pill badge-success">Sudah Bayar</div>
                            @break
                        @default
                            <div class="badge badge-pill badge-secondary">N/A</div>
                    @endswitch
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="font-weight-bolder">
                <td>Total Penjualan</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-center">6.300</td>
                <td class="text-center">6.824,8</td>
                <td class="text-center">84.000</td>
                <td class="text-center">0</td>
                <td class="text-center">143.320.000</td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

