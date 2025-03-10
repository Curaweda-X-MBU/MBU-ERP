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

@php
$initialPaymentLeft = $payment['not_paid'] - $payment['payment_nominal'];
$initialPaymentLeftLocale = \App\Helpers\Parser::toLocale($initialPaymentLeft);
@endphp

<div class="card-header color-header collapsed rounded-lg {{ $payment['has_invalid'] ? 'red' : '' }}" role="button">
    <span class="lead collapse-title">DO # {{ $index + 1 }} | {{ strtoupper($payment['do_number']) }}</span>
    <div class="float-right lead">
        <span>Sisa Bayar | Rp.</span>
        <span class="sisa-bayar">{{ $initialPaymentLeftLocale }}</span>
    </div>
</div>
<div id="collapsible" role="tabpanel" aria-labelledby="heading" class="collapsible collapse" aria-expanded="false" data-index="{{ $index }}">
    <div class="card-body p-2">
        <table class="table table-borderless w-100">
            <tbody>
                <tr>
                    <td>
                        <label for="payment_batch_upload[{{ $index }}][id_marketing]">No. DO<i class="text-danger">*</i></label>
                        <input type="hidden" name="payment_batch_upload[{{ $index }}][marketing_id]" value="{{ $payment['marketing_id'] }}">
                        <input type="text" name="payment_batch_upload[{{ $index }}][id_marketing]" class="form-control" value="{{ $payment['do_number'] }}" readonly>
                    </td>
                    <td>
                        <label for="payment_method">Metode Pembayaran<i class="text-danger">*</i></label>
                        @if (isset($payment['payment_method_invalid']))
                        <select name="payment_batch_upload[{{ $index }}][payment_method]" class="form-control payment_method_select" required>
                            <option value="" selected hidden>Pilih Pembayaran</option>
                            <option value="Transfer">Transfer</option>
                            <option value="Cash">Cash</option>
                            <option value="Card">Card</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                        <small class="text-danger invalid">{{ $payment['payment_method_invalid'] }}</small>
                        @else
                        <input type="text" name="payment_batch_upload[{{ $index }}][payment_method]" class="form-control" value="{{ ucfirst($payment['payment_method']) }}" readonly>
                        @endif
                    </td>
                    <td>
                        <label for="payment_reference">Referensi Pembayaran</label>
                        <input name="payment_batch_upload[{{ $index }}][payment_reference]" type="text" class="form-control" value="{{ $payment['payment_reference'] }}" readonly>
                    </td>
                    <td>
                        <label for="payment_at">Tanggal Bayar<i class="text-danger">*</i></label>
                        @if (isset($payment['payment_date_invalid']))
                        <input name="payment_batch_upload[{{ $index }}][payment_at]" class="form-control flatpickr-basic" required>
                        <small class="text-danger invalid">{{ $payment['payment_date_invalid'] }}</small>
                        @else
                        <input name="payment_batch_upload[{{ $index }}][payment_at]" class="form-control" value="{{ $payment['payment_date'] }}" readonly>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="document_path">Upload Dokumen</label>
                        <div class="input-group">
                            <input type="text" placeholder="Upload" class="file-name form-control">
                            <input type="file" name="payment_batch_upload[{{ $index }}][document_path]" class="transparent-file-upload">
                            <div class="input-group-append" style="pointer-events: none;">
                                <span class="input-group-text btn btn-primary">Upload</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <label for="bank_id">Akun Bank</label>
                        @if (isset($payment['bank_account_invalid']))
                        <select name="payment_batch_upload[{{ $index }}][bank_id]" class="form-control bank_id_select">
                            <option value="" selected></option>
                            @foreach ($banks as $bank)
                                <option value="{{ $bank['bank_id'] }}">{{ $bank['text'] }}</option>
                            @endforeach
                        </select>
                        <small class="text-danger invalid">{{ $payment['bank_account_invalid'] }}</small>
                        @else
                        <input type="text" class="form-control" value="{{ @$banks[$payment['bank_id']]['text'] ?: '-' }}" readonly>
                        @endif
                    </td>
                    <td>
                        <label for="transaction_number">Nomor Transaksi</label>
                        <input name="payment_batch_upload[{{ $index }}][transaction_number]" type="text" class="form-control" value="{{ $payment['transaction_number'] }}" readonly>
                    </td>
                    <td>
                        <label for="payment_nominal">Nominal Pembayaran<i class="text-danger">*</i></label>
                        <input type="text" name="payment_batch_upload[{{ $index }}][payment_nominal_mask]" class="payment_nominal_mask form-control numeral-mask" placeholder="0" value="{{ \App\Helpers\Parser::toLocale($payment['payment_nominal']) }}" required>
                        <small class="text-danger invalid" style="opacity: {{ $initialPaymentLeft < 0 ? '1' : '0' }};">Melebihi sisa belum bayar</small>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(function() {
        const $row = $('.collapsible[data-index="{{ $index }}"]');
        initSelect2($row.find('.bank_id_select'), 'Pilih Bank');
        initSelect2($row.find('.payment_method_select'), 'Pilih Pembayaran');
        initFlatpickrDate($row.find('.flatpickr-basic'));
        initNumeralMask('input[name="payment_batch_upload[{{ $index }}][payment_nominal_mask]"]');

        $(`select[name="${$row.find('.bank_id_select').attr('name')}"], select[name="${$row.find('.payment_method_select').attr('name')}"]`).on('select2:select', function() {
            $(this).siblings('.invalid').css('opacity', 0);
            updateIsInvalid($row);
        });

        // calculate sisa-bayar
        $row.find('.payment_nominal_mask').on('input', function() {
            updateSisaBayar($row, $(this), parseFloat('{{ $payment['not_paid']}}'));
            updateIsInvalid($row);
        });

        if ('{{ $payment['has_invalid'] }}') {
            $row.collapse('show');
        }

        $row.siblings('.card-header').on('click', function() {
            $row.collapse('toggle');
        });
    });
</script>
