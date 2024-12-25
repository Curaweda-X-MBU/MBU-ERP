@extends('templates.main')
@section('title', $title)
@section('content')

<style>
    .color-header {
        color: white;
        background: linear-gradient(118deg, #76A8D8, #c9e5ff);
    }

    .color-header.red {
        color: white;
        background: linear-gradient(118deg, #A87670, #e589a0);
    }
</style>

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/sweetalert2.min.css') }}" />

<div class="row">
    <div class="col-12">
        <div class="card">
            <form id="paymentBatchForm" action="{{ route('marketing.list.payment.batch.add') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
                <div class="card-header">
                    <h4 class="card-title">{{$title}}</h4>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <section id="payment-batch-repeater-1">
                            <div class="collapse-default" data-repeater-list="payment_batch_upload">
                                {{-- Repeater --}}
                                <button type="button" id="addButton" data-repeater-create style="position: absolute; opacity: 0;" tabindex="-1"></button>
                                <div class="card rounded-lg mb-1 row-scope" data-repeater-item>
                                    @include('marketing.list.sections.batch-upload-collapse')
                                </div>
                                {{-- Repeater --}}
                            </div>
                        </section>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="button" id="allocateButton" class="btn btn-warning">Alokasi Otomatis</button>
                    <button type="submit" id="submitButton" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/js/scripts/components/components-collapse.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>

<script>
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
            const marketing_id = $row.find('select[name*="marketing_id"]').val();
            const marketing = @json($data).find((item) => item.marketing_id == marketing_id);;
            if (marketing) {
                // using payment_nominal_mask instead because of race situation
                // const $payment = $row.find('input[name*="payment_nominal"]');
                const $payment = $row.find('.payment_nominal_mask');
                const payment = parseLocaleToNum($payment.val());
                const paymentLeft = marketing.grand_total - marketing.is_paid - payment;
                const paymentLeftLocale = parseNumToLocale(paymentLeft);
                $row.find('.sisa-bayar').text(paymentLeftLocale);

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

                $row.find('.transparent-file-upload').on('change', function() {
                    $(this).siblings('.file-name').val($(this).val().split('\\').pop())
                });

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
                initSelect2($bankSelect, 'Pilih Bank', bankIdRoute, '', { allowClear: true });

                const $marketingSelect = $row.find('.marketing_id_select');
                initSelect2($marketingSelect, 'Pilih DO');

                initNumeralMask('.numeral-mask')
                $('.flatpickr-basic').flatpickr({
                    ...dateOpt,
                    allowInput: true,
                });
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
                $row.find('select[name*="marketing_id"]').append(`<option value="${marketingId}" selected>${payment.do_number}</option>`).trigger('change');
                $row.find('select[name*="marketing_id"]').attr('readonly', true);
            } else {
                const selectedMarketings = $('select[name*="marketing_id"]')
                    .map(function() {
                        if ($(this).val()) {
                            return $(this).val().toString();
                        }
                    }).get();
                const availableMarketings = marketings.filter((marketing) => !selectedMarketings.includes(marketing.marketing_id.toString()));
                availableMarketings.forEach(function(marketing) {
                    $row.find('select[name*="marketing_id"]').append(`<option value="${marketing.marketing_id}">${marketing.id_marketing}</option>`).val('');
                })
                $row.find('select[name*="marketing_id"]').siblings('.invalid').css('opacity', 1);
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
                        } else {
                            $row.find('select[name*="bank_id"]').siblings('.invalid')
                                .css('opacity', 1)
                                .text(`Akun bank (${payment.bank_account}) tidak ditemukan`);
                        }
                    }
                });
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
            $row.find('input[name*="payment_nominal"]')
                .val(payment.payment_nominal)
                .trigger('input');
            initNumeralMask('.numeral-mask')

            // event listeners | validation
            $row.find('.payment_nominal_mask').on('input', function() {
                $row.find('input[name*="payment_nominal"]')
                    .val(parseLocaleToNum($(this).val()))
                    .trigger('input');
                updateSisaBayar(i);
                updateIsInvalid(i);
            });

            $row.find('input[name*="payment_at"]').on('change', function() {
                $(this).siblings('.invalid').css('opacity', 0);
                updateIsInvalid(i);
            });

            $row.on('select2:select', 'select[name*="marketing_id"], select[name*="payment_method"], select[name*="bank_id"]', function() {
                $row.find('.collapse-title').text(`DO #${i + 1} | DO.MBU.${$row.find('select[name*="marketing_id"]').val()}`);
                $(this).siblings('.invalid').css('opacity', 0);
                updateSisaBayar(i);
                updateIsInvalid(i);
            });

            updateSisaBayar(i);
            updateIsInvalid(i);
        });

        $('#submitButton').on('click', function (e) {
            const $form = $('#paymentBatchForm');

            if (!$form[0].checkValidity()) {
                return;
            }

            e.preventDefault();
            const $paymentLefts = $('.sisa-bayar');
            const paymentLefts = $paymentLefts.map(function() {
                return parseLocaleToNum($(this).text());
            }).get();

            const is_invalid = paymentLefts.some((i) => i < 0);

            if (is_invalid) {
                confirmCallback({
                    title: 'Submit',
                    text: 'Masih ada pembayaran berlebih! Tetap lanjutkan?',
                    footer: '<i class="text-center">Data pada DO yang tidak ditemukan akan hangus.</i>',
                    icon: 'warning',
                    confirmText: 'Lanjutkan',
                    confirmClass: 'btn-warning',
                }, function() {
                    $form.trigger('submit');
                });
            } else {
                confirmCallback({
                    title: 'Submit',
                    text: 'Data sudah sesuai.',
                    footer: '<i class="text-center">Data pada DO yang tidak ditemukan akan hangus.</i>',
                    icon: 'info',
                    confirmText: 'Lanjutkan',
                    confirmClass: 'btn-primary',
                }, function() {
                    $form.trigger('submit');
                });
            }
        });

        $('#allocateButton').on('click', function () {
            const $rows = $('.row-scope');

            // Filter rows with valid marketing selections
            const $validRows = $rows.filter(function () {
                return $(this).find('.marketing_id_select').val();
            });

            let overpayment = 0;

            $validRows.each(function (index) {
                const $row = $(this);

                allocateRow($row, index, false);
            });

            // if there's still excess payment, run once again;
            if (overpayment > 0) {
                $validRows.each(function (index) {
                    const $row = $(this);

                    allocateRow($row, index, true);
                });
            }

            function allocateRow($row, index, isLastPass) {
                const $paymentInput = $row.find('input[name*="payment_nominal"]');
                const $paymentDisplay = $row.find('.payment_nominal_mask');
                const $paymentLeftDisplay = $row.find('.sisa-bayar');

                const initialPayment = parseLocaleToNum($paymentDisplay.val());
                const initialPaymentLeft = parseLocaleToNum($paymentLeftDisplay.text());

                let adjustedPayment = initialPayment + overpayment;
                let newPaymentLeft = initialPaymentLeft - overpayment;

                if (isLastPass && $validRows.length === index + 1) {
                } else {
                    if (newPaymentLeft < 0) {
                        overpayment = Math.abs(newPaymentLeft);
                        adjustedPayment = adjustedPayment + newPaymentLeft;
                        newPaymentLeft = 0;
                    } else {
                        overpayment = 0;
                    }
                }

                updatePayment($paymentInput, $paymentDisplay, adjustedPayment);
            }

            function updatePayment($input, $display, value) {
                const formattedValue = parseNumToLocale(value);
                $display.val(formattedValue.split(',')[0]).trigger('input');
                $input.val(value);
            }
        });
    });
</script>

@endsection
