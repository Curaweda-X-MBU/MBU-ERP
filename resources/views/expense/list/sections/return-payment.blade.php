@php
$paymentLeft = $data->is_paid - $data->is_realized;
$totalReturn = $data->is_paid - $data->expense_realizations->sum('price');

$expense_return = $data->expense_return ?? null;
@endphp

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/animate/animate.css')}}" />

<style>
    .custom-modal-layout {
        gap: 1em;
        justify-content: center;
    }

    .custom-modal-layout > .row {
        justify-content: center;
    }
    #transparentFileUpload {
        opacity: 0;
        position:absolute;
        inset: 0;
    }
</style>

<div class="row custom-modal-layout">
    {{-- Table kiri --}}
    <div class="row col-12 col-sm-6" style="row-gap: 1em;">
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label class="align-baseline" for="id_expense">ID</label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <input type="text" class="form-control" id="id_expense" value="{{ $data->id_expense }}" disabled>
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label class="align-baseline" for="do_number">Lokasi</label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <input type="text" class="form-control" id="location" value="{{ $data->location->name }}" disabled>
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="customer_name">Nama Pengaju</label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <input type="text" class="form-control" id="customer_name" value="{{ $data->created_user->name }}" disabled>
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="return_total">Total Pengembalian</label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <input type="text" class="form-control" id="return_total" value="Rp. {{ \App\Helpers\Parser::toLocale($totalReturn) }}" disabled>
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="payment_method">Metode Pembayaran<i class="text-danger">*</i></label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <select name="payment_method" class="payment_method form-control" required>
                    <option value=""  hidden {{ @$expense_return ? '' : 'selected' }}>Pilih Pembayaran</option>
                    @php
                        $payment_methods = ['Transfer', 'Cash', 'Card', 'Cheque'];
                    @endphp
                    @foreach ($payment_methods as $pm)
                        <option value="{{ $pm }}" {{ @$expense_return->payment_method == $pm ? 'selected' : '' }}>{{ $pm }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="bank_id">Akun Bank</label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <select name="bank_id" class="own_bank_id form-control">
                    @if (@$expense_return->bank_id)
                        <option value="{{ @$expense_return->bank_id }}" selected>{{ @$expense_return->bank->alias . ' - ' . @$expense_return->bank->account_number . ' - ' . @$expense_return->bank->owner }}</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="bank_recipient_id">Bank Penerima</label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <select name="bank_recipient_id" class="bank_recipient_id form-control">
                    @if (@$expense_return->bank_recipient_id)
                        <option value="{{ @$expense_return->bank_recipient_id }}" selected>{{ @$expense_return->recipient_bank->alias . ' - ' . @$expense_return->recipient_bank->account_number . ' - ' . @$expense_return->recipient_bank->owner }}</option>
                    @endif
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
                <input name="payment_reference" type="text" class="ref_number form-control" placeholder="Masukkan Referensi" value="{{ @$expense_return->payment_reference ?: '' }}">
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="transaction_number">Nomor Transaksi</label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <input name="transaction_number" type="text" class="transaction_number form-control" placeholder="Masukkan No. Transaksi" value="{{ @$expense_return->transaction_number ?: '' }}">
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="payment_nominal">Nominal Pembayaran<i class="text-danger">*</i></label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <input name="payment_nominal" type="number" max="{{ $totalReturn }}" class="payment_nominal position-absolute" value="{{ @$expense_return->payment_nominal ?: 0 }}" style="opacity: 0; pointer-events: none;" tabindex="-1">
                <input name="payment_nominal_mask" type="text" class="payment_nominal_mask form-control numeral-mask" placeholder="0" value="{{ @$expense_return->payment_nominal ?: '' }}">
                <span class="invalid text-danger text-right small position-absolute" style="bottom: -1rem; right: 0; font-size: 80%; opacity: 0;">Melebihi sisa belum dibayar</span>
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="bank_admin_fees">Biaya Admin Bank<i class="text-danger">*</i></label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <input name="bank_admin_fees" type="number" max="{{ $totalReturn }}" class="bank_admin_fees position-absolute" value="{{ @$expense_return->bank_admin_fees ?: 0 }}" style="opacity: 0; pointer-events: none;" tabindex="-1">
                <input name="bank_admin_fees_mask" type="text" class="bank_admin_fees_mask form-control numeral-mask" placeholder="0" value="{{ @$expense_return->bank_admin_fees ?: '' }}">
                <span class="invalid text-danger text-right small position-absolute" style="bottom: -1rem; right: 0; font-size: 80%; opacity: 0;">Melebihi nominal pembayaran</span>
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="payment_at">Tanggal Bayar<i class="text-danger">*</i></label></td>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <input name="payment_at" class="payment_at form-control flatpickr-basic" value="{{ @$expense_return->payment_at ?: now() }}" required>
            </div>
        </div>
        <div class="col-12 row">
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <label for="return_docs">Upload Dokumen</label>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center p-0">
                <div class="input-group">
                    @php
                        $path = @$expense_return->return_docs ?? '';
                        $parts = explode('/', $path);
                        $filename = end($parts);
                    @endphp
                    <input type="text" id="fileName" placeholder="Upload" class="form-control" value={{ $filename }}>
                    <input type="file" id="transparentFileUpload" name="return_docs">
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
                <textarea name="notes" class="notes form-control">{{ @$expense_return->notes ?: '' }}</textarea>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>

<script>
    $(function() {
        $(document).on('change', '#transparentFileUpload', function() {
            $(this).siblings('#fileName').val($(this).val().split('\\').pop());
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

        initNumeralMask('.numeral-mask');
        initFlatpickrDate($('.flatpickr-basic'));

        var $paymentSelect = $('.payment_method');
        var bankIdRoute = '{{ route("data-master.bank.search") }}';
        var $ownBankSelect = $('.own_bank_id');
        var $recipientBankSelect = $('.bank_recipient_id');
        initSelect2($ownBankSelect, 'Pilih Bank', bankIdRoute);
        initSelect2($recipientBankSelect, 'Pilih Bank', bankIdRoute);
        initSelect2($paymentSelect, 'Pilih Metode Pembayaran');

        let credit = "{{ $totalReturn }}";
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

        $('.bank_admin_fees_mask').on('input', function() {
            checkBankAdminNominal($(this));
        });
    });
</script>
