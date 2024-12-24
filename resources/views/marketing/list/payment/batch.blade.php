@extends('templates.main')
@section('title', $title)
@section('content')

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

    .color-header {
        color: white;
        background: linear-gradient(118deg, #76A8D8, #c9e5ff);
    }

    .color-header.red {
        color: white;
        background: linear-gradient(118deg, #A87670, #e589a0);
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

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{$title}}</h4>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <form id="paymentBatchForm" action="{{-- route('marketing.list.payment.batch.add') --}}" method="post" enctype="multipart/form-data">
                        <section id="payment-batch-repeater-1">
                            <div class="collapse-default" data-repeater-list="payment_batch_upload">
                                {{-- Repeater --}}
                                <button type="button" id="addButton" data-repeater-create style="position: absolute; opacity: 0;" tabindex="-1"></button>
                                <div class="card rounded-lg mb-1 row-scope" data-repeater-item>
                                    <input type="hidden" name="row" disabled>
                                    <div class="card-header color-header collapsed rounded-lg" role="button">
                                        <span class="lead collapse-title">DO #</span>
                                        <div class="float-right lead">
                                            <span>Sisa Bayar |</span>
                                            <span class="sisa-bayar">0,00</span>
                                        </div>
                                    </div>
                                    <div id="collapsible" role="tabpanel" aria-labelledby="heading" class="collapsible collapse" aria-expanded="false">
                                        <div class="card-body p-2">
                                            <table class="table table-borderless w-100">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <label for="id_marketing">No. DO<i class="text-danger">*</i></label>
                                                            <input type="hidden" name="marketing_id">
                                                            <select name="id_marketing" class="form-control marketing_id_select" required>
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
                                                            <small class="text-danger invalid" style="opacity: 0;">Akun bank tidak ditemukan</small>
                                                        </td>
                                                        <td>
                                                            <label for="transaction_number">Nomor Transaksi</label>
                                                            <input name="transaction_number" type="text" class="form-control" placeholder="Masukkan No. Transaksi" readonly>
                                                        </td>
                                                        <td>
                                                            <label for="payment_nomiinal">Nominal Pembayaran<i class="text-danger">*</i></label>
                                                            <input name="payment_nominal" type="number" id="payment_nominal" class="position-absolute" style="opacity: 0; pointer-events: none;" tabindex="-1">
                                                            <input type="text" class="form-control numeral-mask payment_nominal_mask" placeholder="0" required>
                                                            <small class="text-danger invalid" style="opacity: 0;">Melebihi sisa belum bayar. Alokasikan?</small>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                {{-- Repeater --}}
                            </div>
                        </section>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/js/scripts/components/components-collapse.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('.transparent-file-upload').on('change', function() {
            $(this).siblings('.file-name').val($(this).val().split('\\').pop())
        })
    });

    $(function() {
        function updateIsInvalid(rowIdx) {
            const $row = $(`.row-scope[data-index="${rowIdx}"]`);
            const $invalids = $row.find('.invalid');
            const invalids = $invalids.map(function() {
                return Math.ceil($(this).css('opacity'));
            }).get();

            const is_invalid = invalids.includes(1);

            $row.find('.color-header').toggleClass('red', is_invalid).trigger('validate');
        }

        function updateSisaBayar(rowIdx) {
            const $row = $(`.row-scope[data-index="${rowIdx}"]`);
            const marketing_id = $row.find('input[name*="marketing_id"]').val();
            const marketing = @json($data).find((item) => item.marketing_id == marketing_id);;
            if (marketing) {
                const $payment = $row.find('input[name*="payment_nominal"]');
                const payment = $payment.val();
                const paymentLeft = marketing.grand_total - marketing.is_paid - payment;
                const paymentLeftLocale = parseNumToLocale(paymentLeft);
                $row.find('.sisa-bayar').text(`Rp. ${paymentLeftLocale}`);

                // alokasi dan warning
                if (paymentLeft < 0) {
                    $payment.siblings('.invalid').css('opacity', 1);
                } else {
                    $payment.siblings('.invalid').css('opacity', 0);
                }
            }
        }

        const dateOpt = { dateFormat: 'd-M-Y' };
        const optPaymentBatch = {
            initEmpty: true,
            show: function() {
                const $row = $(this);
                $row.slideDown();

                $row.find('.card-header').on('click', function() {
                    $row.find('.collapsible').collapse('toggle');
                });

                $row.find('.color-header').on('validate', function() {
                    if ($(this).hasClass('red')) {
                        $row.find('.collapsible').collapse('show');
                    }
                });

                const $paymentMethodSelect = $row.find('.payment_method_select');
                initSelect2($paymentMethodSelect, 'Pilih Pembayaran');

                const bankIdRoute = '{{ route("data-master.bank.search") }}';
                const $bankSelect = $row.find('.bank_id_select');
                initSelect2($bankSelect, 'Pilih Bank', bankIdRoute);

                const $marketingSelect = $row.find('.marketing_id_select');
                initSelect2($marketingSelect, 'Pilih DO');

                initNumeralMask('.numeral-mask')
                $('.flatpickr-basic').flatpickr(dateOpt);
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            },
            hide: function(deleteElement) {
                confirmDelete($(this), deleteElement);
            },
        }

        const $repeaterPaymentBatch = $('#payment-batch-repeater-1').repeater(optPaymentBatch);

        function capitalizeFirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        const marketings = @json($data);
        const paymentJson = @json($payments);
        const payments = paymentJson.sort((a, b) => a.do_number.split('.').slice(-1)[0] - b.do_number.split('.').slice(-1)[0]);

        const marketingIds =  marketings.map((marketing) => marketing.id_marketing.toLowerCase());
        payments.forEach((payment, i) => {
            $('#payment-batch-repeater-1').find('button[data-repeater-create]').trigger('click');

            // row-scope and title
            $(`input[name="payment_batch_upload[${i}][row]"]`).closest(".row-scope").attr('data-index', i);
            const $row = $(`.row-scope[data-index="${i}"]`);
            $row.find('.collapse-title').text(`DO #${i + 1} | ${payment.do_number}`);

            // id_marketing
            if (marketingIds.includes(payment.do_number.toLowerCase())) {
                const marketingId = payment.do_number.split('.').splice(-1)[0];
                $row.find('select[name*="id_marketing"]').append(`<option value="${marketingId}" selected>${payment.do_number}</option>`).trigger('change');
                $row.find('select[name*="id_marketing"]').attr('readonly', true);
                $row.find('input[name*="marketing_id"]').val(marketingId);
            } else {
                const selectedMarketings = $('select[name*="id_marketing"]')
                    .map(function() {
                        if ($(this).val()) {
                            return $(this).val().toString();
                        }
                    }).get();
                const availableMarketings = marketings.filter((marketing) => !selectedMarketings.includes(marketing.marketing_id.toString()));
                availableMarketings.forEach(function(marketing) {
                    $row.find('select[name*="id_marketing"]').append(`<option value="${marketing.marketing_id}">${marketing.id_marketing}</option>`).val('');
                })
                $row.find('select[name*="id_marketing"]').siblings('.invalid').css('opacity', 1);
            }

            // payment_method
            if (['transfer', 'cash', 'card', 'cheque'].includes(payment.payment_method.toLowerCase())) {
                $row.find('select[name*="payment_method"]').val(capitalizeFirst(payment.payment_method)).trigger('change');
                $row.find('select[name*="payment_method"]').attr('readonly', true);
            } else {
                $row.find('select[name*="payment_method"]').siblings('.invalid')
                    .css('opacity', 1)
                    .text(`Metode (${payment.payment_method}) tidak valid`);
            }

            // bank_id
            if (payment.bank_account) {
                $.ajax({
                    url: '{{ route("data-master.bank.search") }}',
                    type: 'GET',
                    data: { q: payment.bank_account },
                    success: function(data) {
                        if (data.length > 0) {
                            const bank = data[0];
                            $row.find('select[name*="bank_id"]').append(`<option value="${bank.id}">${bank.text}</option>`);
                            $row.find('select[name*="bank_id"]').attr('readonly', true);
                        } else {
                            $row.find('select[name*="bank_id"]').siblings('.invalid').css('opacity', 1);
                        }
                    }
                });
            } else {
                $row.find('select[name*="bank_id"]').attr('readonly', true);
            }

            // payment_reference
            $row.find('input[name*="payment_reference"]').val(payment.payment_reference);

            // transaction_number
            $row.find('input[name*="transaction_number"]').val(payment.transaction_number);

            // payment_at
            const payment_at = new Date(payment.payment_date);
            const dateOptions = { day: '2-digit', year: 'numeric', month: 'short' };
            if (payment_at.toString() === 'Invalid Date') {
                $row.find('input[name*="payment_at"]').siblings('.invalid').css('opacity', 1);
            } else {
                $row.find('input[name*="payment_at"]').val(payment_at.toLocaleDateString('en-GB', dateOptions).replace(/ /g, '-'));
            }

            // payment_nominal
            $row.find('.payment_nominal_mask').val(payment.payment_nominal).trigger('input');
            initNumeralMask('.numeral-mask')

            // event listeners | validation
            $row.find('.payment_nominal_mask').on('input', function() {
                $row.find('input[name*="payment_nominal"]')
                    .val(parseLocaleToNum($(this).val()))
                    .trigger('input');
                updateSisaBayar(i);
                updateIsInvalid(i);
            });

            $row.on('select2:select', 'select[name*="id_marketing"], select[name*="payment_method"], select[name*="bank_id"]', function() {
                $row.find('.collapse-title').text(`DO #${i + 1} | DO.MBU.${$row.find('select[name*="id_marketing"]').val()}`);
                $row.find('input[name*="marketing_id"]').val($row.find('select[name*="id_marketing"]').val());
                $(this).siblings('.invalid').css('opacity', 0);
                updateSisaBayar(i);
                updateIsInvalid(i);
            });

            updateSisaBayar(i);
            updateIsInvalid(i);
        });
    });
</script>

@endsection
