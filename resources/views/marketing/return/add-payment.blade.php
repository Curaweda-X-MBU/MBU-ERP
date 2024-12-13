<style>
    #transparentFileUpload {
        opacity: 0;
        position:absolute;
        inset: 0;
    }
</style>

<div class="modal fade" id="returnPayment" tabindex="-1" role="dialog" aria-labelledby="returnPaymentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="returnPaymentLabel">Form Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; top: 16px; right: 30px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        {{-- Table kiri --}}
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td><label for="noDO">No. DO</label></td>
                                        <td><input type="text" class="form-control" id="noDO" value="DO-MBU-19289" disabled></td>
                                    </tr>
                                    <tr>
                                        <td><label for="namaCustomer">Nama Customer</label></td>
                                        <td><input type="text" class="form-control" id="namaCustomer" value="Abd. Muis" disabled></td>
                                    </tr>
                                    <tr>
                                        <td><label for="nominalRetur">Nominal Retur</label></td>
                                        <td><input type="text" class="form-control" id="nominalRetur" value="8,020,000.00" disabled></td>
                                    </tr>
                                    <tr>
                                        <td><label for="metodePembayaran">Metode Pembayaran*</label></td>
                                        <td>
                                            <select class="form-control" id="metodePembayaran">
                                                <option>Transfer</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label for="akunBank">Akun Bank*</label></td>
                                        <td>
                                            <select class="form-control" id="akunBank">
                                                <option>Mandiri - 012345678 - Mitra Berlian</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label for="bankPenerima">Bank Penerima*</label></td>
                                        <td>
                                            <select class="form-control" id="bankPenerima">
                                                <option>Mandiri - 85462220 - Abd. Muis</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Table kanan --}}
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td><label for="refPembayaran">Referensi Pembayaran</label></td>
                                        <td><input type="text" class="form-control" id="refPembayaran" value="INV-0929"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="noTransaksi">Nomor Transaksi</label></td>
                                        <td><input type="text" class="form-control" id="noTransaksi" value="12345678"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="nominalPembayaran">Nominal Pembayaran*</label></td>
                                        <td><input type="text" class="form-control" id="nominalPembayaran" value="8,020,000.00"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="biayaAdmin">Biaya Admin Bank*</label></td>
                                        <td><input type="text" class="form-control" id="biayaAdmin" value="0"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="tanggalBayar">Tanggal Bayar*</label></td>
                                        <td><input type="date" class="form-control" id="tanggalBayar" value="0"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="uploadDokumen">Upload Dokumen</label></td>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" id="uploadDokumen" class="form-control">
                                                <input type="file" id="transparentFileUpload" name="doc_reference">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-primary text-white">Upload</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label for="notes">Catatan</label></td>
                                        <td colspan="3"><textarea class="form-control" id="notes"></textarea></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- button --}}
                    <center class="mt-2">
                        <a href="{{ route('marketing.return.payment') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                        <button id="submitForm" type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Submit</button>
                    </center>
                </form>
            </div>
        </div>
    </div>
</div>

