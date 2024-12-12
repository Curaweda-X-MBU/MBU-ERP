<style>
    #transparentFileUpload {
        opacity: 0;
        position:absolute;
        inset: 0;
    }
</style>

<div class="table-responsive">
    <div class="form-row">
        <div class="form-section" style="flex: 1; padding: 10px;">
            <div class="form-group">
                <label for="do_number">Nomor DO</label>
                <input type="text" id="do_number" class="form-control" readonly />
            </div>
            <div class="form-group">
                <label for="customer_name">Nama Customer</label>
                <input type="text" id="customer_name" class="form-control" readonly />
            </div>
            <div class="form-group">
                <label for="sales_nominal">Nominal Penjualan</label>
                <input type="text" id="sales_nominal" class="form-control" readonly />
            </div>
            <div class="form-group">
                <label for="payment_method">Metode Pembayaran</label>
                <select id="payment_method" class="form-control">
                    <option value="">Pilih Metode Pembayaran</option>
                    <option value="transfer">Transfer</option>
                    <option value="cash">Cash</option>
                    <option value="credit_card">Kartu Kredit</option>
                </select>
            </div>
            <div class="form-group">
                <label for="own_bank_id">Akun Bank</label>
                <select id="own_bank_id" class="form-control">
                    <option value="">Pilih Akun Bank</option>
                    <option value="bank1">Bank 1</option>
                    <option value="bank2">Bank 2</option>
                </select>
            </div>
        </div>

        <div class="form-section" style="flex: 1; padding: 10px;">
            <div class="form-group">
                <label for="ref_number">Referensi Pembayaran</label>
                <input type="text" id="ref_number" class="form-control" />
            </div>
            <div class="form-group">
                <label for="transaction_number">Nomor Transaksi</label>
                <input type="text" id="transaction_number" class="form-control" />
            </div>
            <div class="form-group">
                <label for="payment_amount">Nominal Pembayaran</label>
                <input type="text" id="payment_amount" class="form-control" />
            </div>
            <div class="form-group">
                <label for="payment_date">Tanggal Bayar</label>
                <input type="date" id="payment_date" class="form-control" />
            </div>
            <div class="form-group">
                <label for="document">Upload Dokumen</label>
                <div class="input-group">
                    <input type="text" id="fileName" placeholder="Upload" class="form-control">
                    <input type="file" id="transparentFileUpload" name="doc_reference">
                    <div class="input-group-append">
                        <span class="input-group-text btn btn-primary">Upload</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="notes">Catatan</label>
                <textarea id="notes" class="form-control" rows="3"></textarea>
            </div>
        </div>
    </div>
</div>
