@php
$paymentLeft = $data->grand_total - $data->is_paid;
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
                <label for="marketing_nominal">Nominal Penjualan</label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <input type="text" class="form-control" id="marketing_nominal" value="Rp. {{ \App\Helpers\Parser::toLocale($data->grand_total) }}" disabled>
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
                <label for="payment_nominal">Nominal Pembayaran<i class="text-danger">*</i></label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <input name="payment_nominal" type="number" max="{{ $paymentLeft }}" class="payment_nominal position-absolute" style="opacity: 0; pointer-events: none;" tabindex="-1">
                <input name="payment_nominal_mask" type="text" class="payment_nominal_mask form-control numeral-mask" placeholder="0" {{ isset($is_detail) ? 'disabled' : 'required' }}>
                <span class="invalid text-danger text-right small position-absolute" style="bottom: -1rem; right: 0; font-size: 80%; opacity: 0;">Melebihi sisa belum dibayar</span>
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

        var $paymentSelect = $('.payment_method');
        var bankIdRoute = '{{ route("data-master.bank.search") }}';
        var $bankSelect = $('.own_bank_id');
        initSelect2($bankSelect, 'Pilih Bank', bankIdRoute);
        initSelect2($paymentSelect, 'Pilih Metode Pembayaran');

        let credit = "{{ $paymentLeft }}";
        $('.payment_nominal_mask').on('input', function() {
            const val = parseLocaleToNum($(this).val());
            console.log(val);

            $(this).siblings('.payment_nominal').val(val);
            if (val > credit) {
                $(this).siblings('.invalid').css('opacity', 1);
            } else {
                $(this).siblings('.invalid').css('opacity', 0);
            }
        });

        if ('{{ isset($is_detail) }}') {
            const $this = $('#detail');
            var $marketingPayment = $this.find('input[name="marketing_payment_id"]');

            $marketingPayment.on('change', function() {
                const paymentId = $marketingPayment.val();
                const $paymentMethod = $this.find('.payment_method');
                const $ownBank = $this.find('.own_bank_id');
                const $refNumber = $this.find('.ref_number');
                const $transactionNumber = $this.find('.transaction_number');
                const $paymentNominal = $this.find('.payment_nominal_mask');
                const $paymentAt = $this.find('.payment_at');
                const $notes = $this.find('#notes');
                const route = '{{ route('marketing.list.payment.detail', ':id') }}'
                $.ajax({
                    method: 'get',
                    url: route.replace(':id', paymentId),
                }).then(function(result) {
                    $paymentMethod.val(result.payment_method).trigger('change');
                    $ownBank.append(`<option value="${result.bank ? result.bank_id : ''}" selected>${result.bank ? [result.bank.alias, result.bank.account_number, result.bank.owner].join(' - ') : '-'}</option>`);
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
                const $paymentMethod = $this.find('.payment_method');
                const $ownBank = $this.find('.own_bank_id');
                const $refNumber = $this.find('.ref_number');
                const $transactionNumber = $this.find('.transaction_number');
                const $paymentNominal = $this.find('.payment_nominal_mask');
                const $paymentAt = $this.find('.payment_at');
                const $notes = $this.find('#notes');
                const route = '{{ route('marketing.list.payment.detail', ':id') }}'
                $.ajax({
                    method: 'get',
                    url: route.replace(':id', paymentId),
                }).then(function(result) {
                    credit = (@js($paymentLeft) > 0) ? @js($paymentLeft) : result.payment_nominal - Math.abs(@js($paymentLeft));
                    $paymentMethod.val(result.payment_method).trigger('change');
                    $ownBank.append(`<option value="${result.bank ? result.bank_id : ''}" selected>${result.bank ? result.bank.name : '-'}</option>`);
                    $refNumber.val(result.payment_reference ?? '-');
                    $transactionNumber.val(result.transaction_number ?? '-');
                    $paymentNominal.siblings('.payment_nominal').attr('max', credit);
                    $paymentNominal.val(parseNumToLocale(result.payment_nominal));
                    $paymentAt.val(new Date(result.payment_at).toLocaleDateString('en-GB', { day: '2-digit', year: 'numeric', month: 'short' }).replace(/ /g, '-'));
                    $notes.text(result.notes ?? '-');
                });
            })
        }
    })();
</script>
