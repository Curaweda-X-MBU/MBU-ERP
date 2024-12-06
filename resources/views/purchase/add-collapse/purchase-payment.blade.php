<div class="row">
    <div class="col-12">
        <div class="form-group row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-3 col-form-label">
                        <label class="float-right">Tgl. Pembayaran</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control flatpickr-basic" name="payment_date" placeholder="DD-MM-YYYY" required>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-3 col-form-label">
                        <label class="float-right">No. PO</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" value="{{ $data->po_number }}" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-3 col-form-label">
                        <label class="float-right">Referensi Pembayaran</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" name="ref_number" class="form-control" placeholder="Referensi Pembayaran" required>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-3 col-form-label">
                        <label class="float-right">Nama Vendor</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" value="{{ $data->supplier->name }}" class="form-control" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-3 col-form-label">
                        <label class="float-right">Nomor Transaksi</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" name="transaction_number" class="form-control" placeholder="Nomor Transaksi">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-3 col-form-label">
                        <label class="float-right">Nominal Pembelian</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" value="{{ $data->grand_total }}" class="form-control numeral-mask" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-3 col-form-label">
                        <label class="float-right">Sisa Pembayaran</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" value="{{ $data->total_remaining_payment }}" class="form-control numeral-mask" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-3 col-form-label">
                        <label class="float-right">Metode Pembayaran</label>
                    </div>
                    <div class="col-sm-9">
                        <select name="payment_method" class="form-control" required>
                            <option selected disabled>Pilih Metode Pembayaran</option>
                            @foreach ($payment_method as $key => $item)
                                <option value="{{ $key }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-3 col-form-label">
                        <label class="float-right">Nominal Pembayaran</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control numeral-mask" name="amount" placeholder="Nominal Pembayaran" required>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-3 col-form-label">
                        <label class="float-right">Akun Bank</label>
                    </div>
                    <div class="col-sm-9">
                        <select name="own_bank_id" id="own_bank_id" class="form-control" required>
                            @if(($data->purchase_company->own_bank->name??false))
                                <option value="{{ $data->purchase_company->own_bank->bank_id }}" selected="selected">{{ $data->purchase_company->own_bank->name }}</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-3 col-form-label">
                        <label class="float-right">Biaya Admin Bank</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" name="bank_charge" class="form-control numeral-mask" placeholder="Biaya Admin Bank">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-3 col-form-label">
                        <label class="float-right">Bank Penerima</label>
                    </div>
                    <div class="col-sm-9">
                        <select name="recipient_bank_id" id="recipient_bank_id" class="form-control" required>
                            @if(($data->purchase_company->recipient_bank->name??false))
                                <option value="{{ $data->purchase_company->recipient_bank->bank_id }}" selected="selected">{{ $data->purchase_company->own_bank->name }}</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-3 col-form-label">
                        <label class="float-right">Upload Dokumen</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="file" name="document" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

