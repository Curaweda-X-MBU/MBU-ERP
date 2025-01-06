<div class="table-responsive mt-2">
    <div class="row bg-primary d-flex justify-content-center align-items-center py-1 text-white px-2">
        <p class="col-md-6 mb-0">Biaya Operasional (BOP)</p>
        <p class="col-md-6 mb-0 text-right">Total: <span id="total-biaya-bop">0,00</span></p>
    </div>
    <table id="datatableBOP" class="table table-bordered">
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
                <td>Listrik</td>
                <td>Listrik Bulanan</td>
                <td>1.000</td>
                <td>kWh</td>
                <td class="nominal-bop">1.200.000</td>
                <td>12-12-2024</td>
                <td>
                    @php
                        $status = 1;
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

{{-- <script>
    $(function() {
        function calculateTotalBOP() {
            let total = 0;

            $('.table tbody tr').each(function() {
                const nominal = $(this).find('.nominal-bop').text();
                total += parseLocaleToNum(nominal);
            });

            $('#total-biaya-bop').text(parseNumToLocale(total));
        }

        calculateTotalBOP();
    });
</script> --}}
