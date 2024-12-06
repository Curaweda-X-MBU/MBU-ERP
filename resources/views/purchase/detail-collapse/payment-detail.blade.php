<table class="table table-bordered table-striped w-100">
    <tr>
        <td style="width: 45%">Tanggal Pembayaran</td>
        <td class="text-center">:</td>
        <td id="payment_date"></td>
    </tr>
    <tr>
        <td>Nomor PO</td>
        <td class="text-center">:</td>
        <td>{{$data->po_number}}</td>
    </tr>
    <tr>
        <td>Nama Vendor</td>
        <td class="text-center">:</td>
        <td>{{$data->supplier->name??''}}</td>
    </tr>
    <tr>
        <td>Metode Pembayaran</td>
        <td class="text-center">:</td>
        <td id="payment_method"></td>
    </tr>
    <tr>
        <td>Akun Bank</td>
        <td class="text-center">:</td>
        <td id="own_bank_id"></td>
    </tr>
    <tr>
        <td>Bank Penerima</td>
        <td class="text-center">:</td>
        <td id="recipient_bank_id"></td>
    </tr>
    <tr>
        <td>Referensi Pembayaran</td>
        <td class="text-center">:</td>
        <td id="ref_number"></td>
    </tr>
    <tr>
        <td>Nomor Transaksi</td>
        <td class="text-center">:</td>
        <td id="transaction_number"></td>
    </tr>
    <tr>
        <td>Nominal Pembelian</td>
        <td class="text-center">:</td>
        <td>Rp. <span class="float-right">{{ number_format($data->grand_total, '0', ',', '.') }}</span></td>
    </tr>
    <tr>
        <td>Nominal Pembayaran</td>
        <td class="text-center">:</td>
        <td>Rp. <span class="float-right" id="amount"></span></td>
    </tr>
    <tr>
        <td>Biaya Admin Bank</td>
        <td class="text-center">:</td>
        <td>Rp. <span class="float-right" id="bank_charge"></span></td>
    </tr>
    <tr>
        <td>Dokumen</td>
        <td class="text-center">:</td>
        <td id="document"></td>
    </tr>
</table>