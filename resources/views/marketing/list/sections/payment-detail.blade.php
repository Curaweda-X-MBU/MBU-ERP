<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/animate/animate.css')}}" />

<style>
    #transparentFileUpload {
        opacity: 0;
        position:absolute;
        inset: 0;
    }
</style>

@if (isset($is_detail))
<div class="row custom-modal-layout" id="detail">
@elseif (isset($is_edit))
<div class="row custom-modal-layout" id="edit">
@else
<div class="row custom-modal-layout">
@endif
    <input type="hidden" name="marketing_payment_id" value="" disabled>
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
                <select name="payment_method" class="form-control" id="payment_method" {{ isset($is_detail) ? 'disabled' : 'required' }}>
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
                <input name="payment_reference" type="text" class="form-control" id="ref_number" placeholder="Masukkan Referensi" {{ isset($is_detail) ? 'disabled' : '' }}>
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="transaction_number">Nomor Transaksi</label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <input name="transaction_number" type="text" class="form-control" id="transaction_number" placeholder="Masukkan No. Transaksi" {{ isset($is_detail) ? 'disabled' : '' }}>
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="payment_amount">Nominal Pembayaran<i class="text-danger">*</i></label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <input name="payment_nominal" type="number" id="payment_nominal" max="{{ $data->grand_total - $data->marketing_payments->sum('payment_nominal') }}" class="position-absolute" style="opacity: 0; pointer-events: none;" tabindex="-1">
                <input name="payment_nominal_mask" type="text" class="form-control numeral-mask" id="payment_nominal_mask" placeholder="0" {{ isset($is_detail) ? 'disabled' : 'required' }}>
                <span id="invalid_payment_nominal" class="text-danger text-right small position-absolute" style="bottom: -1rem; right: 0; font-size: 80%; opacity: 0;">Melebihi sisa belum dibayar</span>
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="payment_at">Tanggal Bayar<i class="text-danger">*</i></label></td>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                @if (isset($is_detail))
                    <input name="payment_at" type="text" class="form-control" id="payment_at" value="{{ now()->format('d-M-Y') }}" disabled>
                @else
                    <input name="payment_at" class="form-control flatpickr-basic" id="payment_at" value="{{ now()->format('d-M-Y') }}" required>
                @endif
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="doc_reference">Upload Dokumen</label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <div class="input-group">
                    <input type="text" id="fileName" placeholder="Upload" class="form-control" {{ isset($is_detail) ? 'disabled' : '' }}>
                    <input type="file" id="transparentFileUpload" name="document_path" {{ isset($is_detail) ? 'disabled' : '' }}>
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
                <textarea name="notes" class="form-control" id="notes" {{ isset($is_detail) ? 'disabled' : '' }}></textarea>
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
    });

    (function() {
        initNumeralMask('.numeral-mask');

        var dateOpt = { dateFormat: 'd-M-Y' };
        $('.flatpickr-basic').flatpickr(dateOpt);

        var $paymentSelect = $('#payment_method');
        var $bankRequiredLabel = $('#bank_required_label');
        var bankIdRoute = '{{ route("data-master.bank.search") }}';
        var $bankSelect = $('#own_bank_id');
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

        var credit = parseFloat("{{ $data->grand_total - $data->marketing_payments->sum('payment_nominal') }}");
        $('#payment_nominal_mask').on('input', function() {
            const val = parseLocaleToNum($(this).val());

            $(this).siblings('#payment_nominal').val(val);
            if (val > credit) {
                $(this).siblings('#invalid_payment_nominal').css('opacity', 1);
            } else {
                $(this).siblings('#invalid_payment_nominal').css('opacity', 0);
            }
        });

        if ('{{ isset($is_detail) }}') {
            const $this = $('#detail');
            var $marketingPayment = $this.find('input[name="marketing_payment_id"]');

            $marketingPayment.on('change', function() {
                const paymentId = $marketingPayment.val();
                const $paymentMethod = $this.find('#payment_method');
                const $ownBank = $this.find('#own_bank_id');
                const $refNumber = $this.find('#ref_number');
                const $transactionNumber = $this.find('#transaction_number');
                const $paymentNominal = $this.find('#payment_nominal_mask');
                const $paymentAt = $this.find('#payment_at');
                const $notes = $this.find('#notes');
                const route = '{{ route('marketing.list.payment.detail', ':id') }}'
                $.ajax({
                    method: 'get',
                    url: route.replace(':id', paymentId),
                }).then(function(result) {
                    $paymentMethod.val(result.payment_method);
                    $ownBank.append(`<option value="${result.bank ? result.bank_id : ''}" selected>${result.bank ? result.bank.name : '-'}</option>`);
                    $refNumber.val(result.payment_reference ?? '-');
                    $transactionNumber.val(result.transaction_number ?? '-');
                    $paymentNominal.val(parseNumToLocale(result.payment_nominal));
                    $paymentAt.val(new Date(result.payment_at).toLocaleDateString('en-GB', { day: '2-digit', year: 'numeric', month: 'short' }).replace(/ /g, '-'));
                    $notes.text(result.notes ?? '-');
                });
            });
        }

        if ('{{ isset($is_edit) }}') {
            const $this = $('#edit');
            var $marketingPayment = $this.find('input[name="marketing_payment_id"]');

            $marketingPayment.on('change', function() {
                const paymentId = $marketingPayment.val();
                const $paymentMethod = $this.find('#payment_method');
                const $ownBank = $this.find('#own_bank_id');
                const $refNumber = $this.find('#ref_number');
                const $transactionNumber = $this.find('#transaction_number');
                const $paymentNominal = $this.find('#payment_nominal_mask');
                const $paymentAt = $this.find('#payment_at');
                const $notes = $this.find('#notes');
                const route = '{{ route('marketing.list.payment.detail', ':id') }}'
                $.ajax({
                    method: 'get',
                    url: route.replace(':id', paymentId),
                }).then(function(result) {
                    $paymentMethod.val(result.payment_method);
                    $ownBank.append(`<option value="${result.bank ? result.bank_id : ''}" selected>${result.bank ? result.bank.name : '-'}</option>`);
                    $refNumber.val(result.payment_reference ?? '-');
                    $transactionNumber.val(result.transaction_number ?? '-');
                    $paymentNominal.val(parseNumToLocale(result.payment_nominal));
                    $paymentAt.val(new Date(result.payment_at).toLocaleDateString('en-GB', { day: '2-digit', year: 'numeric', month: 'short' }).replace(/ /g, '-'));
                    $notes.text(result.notes ?? '-');
                });
            })
        }
    })();
</script>
