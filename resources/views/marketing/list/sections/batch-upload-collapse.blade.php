<style>
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #efefef !important;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection--single .select2-selection__rendered {
        color: #6A6B7B;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow,
    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
        display: none;
    }

    .transparent-file-upload {
        opacity: 0;
        position:absolute;
        inset: 0;
    }

    #paymentBatchForm .select2-selection {
        overflow: hidden;
    }

    #paymentBatchForm .select2-selection__rendered {
        white-space: normal;
        word-break: break-all;
    }

    #paymentBatchForm table tr {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    #paymentBatchForm table td {
         vertical-align: top;
         min-height: 6em;
    }

    @media (max-width: 767.98px) {
        #paymentBatchForm table tr {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>

<input type="hidden" name="row" disabled>
<input type="hidden" name="invalid">
<div class="card-header color-header collapsed rounded-lg" role="button">
    <span class="lead collapse-title">DO #</span>
    <div class="float-right lead">
        <span>Sisa Bayar | Rp.</span>
        <span class="sisa-bayar">0,00</span>
    </div>
</div>
<div id="collapsible" role="tabpanel" aria-labelledby="heading" class="collapsible collapse" aria-expanded="false">
    <div class="card-body p-2">
        <table class="table table-borderless w-100">
            <tbody>
                <tr>
                    <td>
                        <label for="marketing_id">No. DO<i class="text-danger">*</i></label>
                        <select name="marketing_id" class="form-control marketing_id_select">
                        </select>
                        <small class="text-danger invalid" style="opacity: 0;">DO tidak ditemukan</small>
                    </td>
                    <td>
                        <label for="payment_method">Metode Pembayaran<i class="text-danger">*</i></label>
                        <select name="payment_method" class="form-control payment_method_select" required>
                            <option value="" selected hidden>Pilih Pembayaran</option>
                            <option value="Transfer">Transfer</option>
                            <option value="Cash">Cash</option>
                            <option value="Card">Card</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                        <small class="text-danger invalid" style="opacity: 0;"></small>
                    </td>
                    <td>
                        <label for="payment_reference">Referensi Pembayaran</label>
                        <input name="payment_reference" type="text" class="form-control" placeholder="Masukkan Referensi" readonly>
                    </td>
                    <td>
                        <label for="payment_at">Tanggal Bayar<i class="text-danger">*</i></label>
                        <input name="payment_at" class="form-control flatpickr-basic" id="payment_at" required>
                        <small class="text-danger invalid" style="opacity: 0;">Format tanggal salah</small>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="document_path">Upload Dokumen</label>
                        <div class="input-group">
                            <input type="text" placeholder="Upload" class="file-name form-control">
                            <input type="file" name="document_path" class="transparent-file-upload">
                            <div class="input-group-append" style="pointer-events: none;">
                                <span class="input-group-text btn btn-primary">Upload</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <label for="bank_id">Akun Bank</label>
                        <select name="bank_id" class="form-control bank_id_select">
                        </select>
                        <small class="text-danger invalid" style="opacity: 0;"></small>
                    </td>
                    <td>
                        <label for="transaction_number">Nomor Transaksi</label>
                        <input name="transaction_number" type="text" class="form-control" placeholder="Masukkan No. Transaksi" readonly>
                    </td>
                    <td>
                        <label for="payment_nominal">Nominal Pembayaran<i class="text-danger">*</i></label>
                        <input name="payment_nominal" type="number" id="payment_nominal" class="position-absolute" style="opacity: 0; pointer-events: none;" tabindex="-1">
                        <input type="text" class="form-control numeral-mask payment_nominal_mask" placeholder="0" required>
                        <small class="text-danger invalid" style="opacity: 0;">Melebihi sisa belum bayar</small>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
