<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/animate/animate.css')}}" />

<style>
    #transparentFileUpload {
        opacity: 0;
        position:absolute;
        inset: 0;
    }

    .custom-modal-layout {
        gap: 1em;
        justify-content: center;
    }

    .custom-modal-layout > .row {
        justify-content: center;
    }
</style>

<div class="modal fade" id="paymentDetail" tabindex="-1" role="dialog" aria-labelledby="paymentDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="paymentDetailLabel">Form Pembayaran</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 16px; right: 30px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <div class="row custom-modal-layout">
                        {{-- Table kiri --}}
                        <div class="row col-12 col-sm-6" style="row-gap: 1em;">
                            <div class="col-12 row">
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <label class="align-baseline" for="do_number">No. DO</label>
                                </div>
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <input type="text" class="form-control" id="do_number" value="{{ $data->id_marketing }}" disabled>
                                </div>
                            </div>
                            <div class="col-12 row">
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <label for="customer_name">Nama Customer</label>
                                </div>
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <input type="text" class="form-control" id="customer_name" value="{{ $data->customer->name }}" disabled>
                                </div>
                            </div>
                            <div class="col-12 row">
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <label for="return_nominal">Nominal Penjualan</label>
                                </div>
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <input type="text" class="form-control" id="return_nominal" value="Rp. {{ number_format($data->grand_total, 2, '.', ',') }}" disabled>
                                </div>
                            </div>
                            <div class="col-12 row">
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <label for="payment_method">Metode Pembayaran<i class="text-danger">*</i></label>
                                </div>
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <select name="payment_method" class="form-control" id="payment_method" required>
                                        <option value="" selected hidden>Pilih Pembayaran</option>
                                        <option value="Transfer">Transfer</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Card">Card</option>
                                        <option value="Cheque">Cheque</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 row">
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <label for="own_bank_id" id="own_bank_label">Akun Bank<i id="bank_required_label" class="text-danger"></i></label>
                                </div>
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <select name="bank_id" class="form-control" id="own_bank_id" disabled>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Table kanan --}}
                        <div class="row col-12 col-sm-6" style="row-gap: 1em;">
                            <div class="col-12 row">
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <label for="ref_number">Referensi Pembayaran</label>
                                </div>
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <input name="payment_reference" type="text" class="form-control" id="ref_number" placeholder="Masukkan Referensi">
                                </div>
                            </div>
                            <div class="col-12 row">
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <label for="transaction_number">Nomor Transaksi</label>
                                </div>
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <input name="transaction_number" type="text" class="form-control" id="transaction_number" placeholder="Masukkan No. Transaksi">
                                </div>
                            </div>
                            <div class="col-12 row">
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <label for="payment_amount">Nominal Pembayaran<i class="text-danger">*</i></label>
                                </div>
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <input name="payment_nominal" type="text" class="form-control numeral-mask" id="payment_amount" placeholder="0" required>
                                </div>
                            </div>
                            <div class="col-12 row">
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <label for="payment_at">Tanggal Bayar<i class="text-danger">*</i></label></td>
                                </div>
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <input name="payment_at" class="form-control flatpickr-basic" id="payment_at" value="{{ now()->format('d-M-Y') }}" required>
                                </div>
                            </div>
                            <div class="col-12 row">
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <label for="doc_reference">Upload Dokumen</label>
                                </div>
                                <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                                    <div class="input-group">
                                        <input type="text" id="fileName" placeholder="Upload" class="form-control">
                                        <input type="file" id="transparentFileUpload" name="document_path">
                                        <div class="input-group-append" style="pointer-events: none;">
                                            <span class="input-group-text btn btn-primary">Upload</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row col-12">
                            <div class="col-12 row">
                                <div class="col-12 col-lg-6 d-flex flex-column align-items-start p-0 offset-lg-6">
                                    <label for="notes">Catatan</label>
                                    <textarea name="notes" class="form-control" id="notes"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button id="submitForm" type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Submit</button>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#transparentFileUpload').on('change', function() {
            $('#fileName').val($('#transparentFileUpload').val().split('\\').pop())
        })
    })

    initNumeralMask('.numeral-mask');

    const dateOpt = { dateFormat: 'd-M-Y' };
    $('.flatpickr-basic').flatpickr(dateOpt);

    const $paymentSelect = $('#payment_method');
    const $bankRequiredLabel = $('#bank_required_label');
    const bankIdRoute = '{{ route("data-master.bank.search") }}';
    const $bankSelect = $('#own_bank_id');
    initSelect2($bankSelect, 'Pilih Bank', bankIdRoute);
    initSelect2($paymentSelect, 'Pilih Metode Pembayaran');
    initSelect2($('#recipient_bank_id'), 'Pilih Bank');

    $paymentSelect.on('select2:select', function() {
        switch (this.value.toLowerCase()) {
            case 'transfer':
                $bankSelect.attr('required', true);
                $bankSelect.attr('disabled', false);
                $bankRequiredLabel.text('*');
                break;
            case 'card':
                $bankSelect.attr('required', true);
                $bankSelect.attr('disabled', false);
                $bankRequiredLabel.text('*');
                break;
            default:
                $bankSelect.attr('required', false);
                $bankSelect.attr('disabled', true);
                $bankRequiredLabel.text('');
                break;
        }
    });
</script>
