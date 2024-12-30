@php
$paymentLeft = $data->marketing_return->total_return - $data->is_returned;
@endphp

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

@if (@$is_detail)
<div class="row custom-modal-layout" id="detail">
@elseif (@$is_edit)
<div class="row custom-modal-layout" id="edit">
@else
<div class="row custom-modal-layout">
@endif
    <input type="hidden" name="marketing_return_payment_id" value="" disabled>
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
                <label for="return_nominal">Nominal Retur</label>
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
                <select name="payment_method" class="payment_method form-control" {{ isset($is_detail) ? 'disabled' : 'required' }}>
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
                <label for="bank_id">Akun Bank<i id="bank_required_label" class="text-danger"></i></label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <select name="bank_id" class="own_bank_id form-control" {{ isset($is_detail) ? 'disabled' : '' }}>
                </select>
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="recipient_bank_id">Bank Penerima<i id="recipient_bank_required_label" class="text-danger"></i></label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <select name="recipient_bank_id" class="recipient_bank_id form-control" {{ isset($is_detail) ? 'disabled' : '' }}>
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
                <input name="payment_reference" type="text" class="ref_number form-control" placeholder="Masukkan Referensi" {{ isset($is_detail) ? 'disabled' : '' }}>
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="transaction_number">Nomor Transaksi</label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <input name="transaction_number" type="text" class="transaction_number form-control" placeholder="Masukkan No. Transaksi" {{ isset($is_detail) ? 'disabled' : '' }}>
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="payment_amount">Nominal Pembayaran<i class="text-danger">*</i></label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <input name="payment_nominal" type="number" max="{{ $paymentLeft }}" class="payment_nominal position-absolute" style="opacity: 0; pointer-events: none;" tabindex="-1">
                <input name="payment_nominal_mask" type="text" class="payment_nominal_mask form-control numeral-mask" placeholder="0" {{ isset($is_detail) ? 'disabled' : 'required' }}>
                @if (empty($is_detail))
                    <span class="invalid text-danger text-right small position-absolute" style="bottom: -1rem; right: 0; font-size: 80%; opacity: 0;">Melebihi sisa belum dibayar</span>
                @endif
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="bank_admin_fees">Biaya Admin Bank<i class="text-danger">*</i></label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <input name="bank_admin_fees" type="number" max="{{ $paymentLeft }}" class="bank_admin_fees position-absolute" style="opacity: 0; pointer-events: none;" tabindex="-1">
                <input name="bank_admin_fees_mask" type="text" class="bank_admin_fees_mask form-control numeral-mask" placeholder="0" {{ isset($is_detail) ? 'disabled' : 'required' }}>
                <span class="invalid text-danger text-right small position-absolute" style="bottom: -1rem; right: 0; font-size: 80%; opacity: 0;">Melebihi nominal pembayaran</span>
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="payment_at">Tanggal Bayar<i class="text-danger">*</i></label></td>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                @if (@$is_detail)
                    <input name="payment_at" type="text" class="payment_at form-control" value="{{ now()->format('d-M-Y') }}" disabled>
                @else
                    <input name="payment_at" class="payment_at form-control flatpickr-basic" value="{{ now()->format('d-M-Y') }}" required>
                @endif
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="document_path">Upload Dokumen</label>
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
                <textarea name="notes" class="notes form-control" {{ isset($is_detail) ? 'disabled' : '' }}></textarea>
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

        var $paymentSelect = $('.payment_method');
        var bankIdRoute = '{{ route("data-master.bank.search") }}';
        var $bankSelect = $('.own_bank_id');
        var $recipientBankSelect = $('.recipient_bank_id');
        initSelect2($bankSelect, 'Pilih Bank', bankIdRoute);
        initSelect2($paymentSelect, 'Pilih Metode Pembayaran');
        initSelect2($recipientBankSelect, 'Pilih Bank', bankIdRoute);

        let credit = "{{ $paymentLeft }}";
        $('.payment_nominal_mask').on('input', function() {
            const val = parseLocaleToNum($(this).val());

            $(this).siblings('.payment_nominal').val(val);
            if (val > credit) {
                $(this).siblings('.invalid').css('opacity', 1);
            } else {
                $(this).siblings('.invalid').css('opacity', 0);
            }

            checkBankAdminNominal($(this).closest('form').find('.bank_admin_fees_mask'));
        });

        function checkBankAdminNominal($this) {
            const val = parseLocaleToNum($this.val());

            const nominal = parseLocaleToNum($this.closest('form').find('.payment_nominal_mask').val());

            $this.siblings('.bank_admin_fees').val(val).attr('max', nominal);
            if (val > nominal) {
                $this.siblings('.invalid').css('opacity', 1);
            } else {
                $this.siblings('.invalid').css('opacity', 0);
            }
        }

        $('.bank_admin_fees_mask').on('input', function() {
            checkBankAdminNominal($(this));
        });

        if ('{{ @$is_detail }}') {
            const $this = $('#detail');
            var $marketingPayment = $this.find('input[name="marketing_return_payment_id"]');

            $marketingPayment.on('change', function() {
                const paymentId = $marketingPayment.val();
                const $paymentMethod = $this.find('.payment_method');
                const $ownBank = $this.find('.own_bank_id');
                const $recipientBank = $this.find('.recipient_bank_id');
                const $refNumber = $this.find('.ref_number');
                const $transactionNumber = $this.find('.transaction_number');
                const $paymentNominalMask = $this.find('.payment_nominal_mask');
                const $paymentNominal = $this.find('.payment_nominal');
                const $bankAdminNominalMask = $this.find('.bank_admin_fees_mask');
                const $bankAdminNominal = $this.find('.bank_admin_fees');
                const $paymentAt = $this.find('.payment_at');
                const $notes = $this.find('.notes');
                const route = '{{ route('marketing.return.payment.detail', ':id') }}'
                $.ajax({
                    method: 'get',
                    url: route.replace(':id', paymentId),
                }).then(function(result) {
                    $paymentMethod.val(result.payment_method).trigger('change');
                    $ownBank.append(`<option value="${result.bank ? result.bank_id : ''}" selected>${result.bank ? [result.bank.alias, result.bank.account_number, result.bank.owner].join(' - ') : '-'}</option>`);
                    $recipientBank.append(`<option value="${result.recipient_bank ? result.recipient_bank_id : ''}" selected>${result.recipient_bank ? [result.recipient_bank.alias, result.recipient_bank.account_number, result.recipient_bank.owner].join(' - ') : '-'}</option>`);
                    $refNumber.val(result.payment_reference ?? '-');
                    $transactionNumber.val(result.transaction_number ?? '-');
                    $paymentNominalMask.val(parseNumToLocale(result.payment_nominal)).trigger('input');
                    $paymentNominal.val(result.payment_nominal).trigger('input');
                    $bankAdminNominalMask.val(parseNumToLocale(result.bank_admin_fees)).trigger('input');
                    $bankAdminNominal.val(result.bank_admin_fees).trigger('input');
                    $paymentAt.val(new Date(result.payment_at).toLocaleDateString('en-GB', { day: '2-digit', year: 'numeric', month: 'short' }).replace(/ /g, '-'));
                    $notes.text(result.notes ?? '-');
                });
            });
        }

        if ('{{ @$is_edit }}') {
            const $this = $('#edit');
            var $marketingPayment = $this.find('input[name="marketing_return_payment_id"]');

            $marketingPayment.on('change', function() {
                const paymentId = $marketingPayment.val();
                const $paymentMethod = $this.find('.payment_method');
                const $ownBank = $this.find('.own_bank_id');
                const $recipientBank = $this.find('.recipient_bank_id');
                const $refNumber = $this.find('.ref_number');
                const $transactionNumber = $this.find('.transaction_number');
                const $paymentNominalMask = $this.find('.payment_nominal_mask');
                const $paymentNominal = $this.find('.payment_nominal');
                const $bankAdminNominalMask = $this.find('.bank_admin_fees_mask');
                const $bankAdminNominal = $this.find('.bank_admin_fees');
                const $paymentAt = $this.find('.payment_at');
                const $notes = $this.find('.notes');
                const route = '{{ route('marketing.return.payment.detail', ':id') }}'
                $.ajax({
                    method: 'get',
                    url: route.replace(':id', paymentId),
                }).then(function(result) {
                    credit = (@js($paymentLeft) > 0) ? @js($paymentLeft) : result.payment_nominal - Math.abs(@js($paymentLeft));
                    if (result.verify_status == 2) {
                        credit += result.payment_nominal;
                        $paymentNominal.attr('max', credit);
                    }
                    $paymentMethod.val(result.payment_method).trigger('change');
                    $ownBank.append(`<option value="${result.bank ? result.bank_id : ''}" selected>${result.bank ? [result.bank.alias, result.bank.account_number, result.bank.owner].join(' - ') : '-'}</option>`);
                    $recipientBank.append(`<option value="${result.recipient_bank ? result.recipient_bank_id : ''}" selected>${result.recipient_bank ? [result.recipient_bank.alias, result.recipient_bank.account_number, result.recipient_bank.owner].join(' - ') : '-'}</option>`);
                    $refNumber.val(result.payment_reference ?? '-');
                    $transactionNumber.val(result.transaction_number ?? '-');
                    $paymentNominalMask.val(parseNumToLocale(result.payment_nominal)).trigger('input');
                    $paymentNominal.val(result.payment_nominal).trigger('input');
                    $bankAdminNominalMask.val(parseNumToLocale(result.bank_admin_fees)).trigger('input');
                    $bankAdminNominal.val(result.bank_admin_fees).trigger('input');
                    $paymentAt.val(new Date(result.payment_at).toLocaleDateString('en-GB', { day: '2-digit', year: 'numeric', month: 'short' }).replace(/ /g, '-'));
                    $notes.text(result.notes ?? '-');
                });
            })
        }
    })();
</script>
