@extends('templates.main')
@section('title', $title)
@section('content')

<style>
    .color-header {
        color: white;
        background: linear-gradient(118deg, #76A8D8, #c9e5ff);
    }

    .color-header.red {
        background: linear-gradient(118deg, #A87670, #e589a0);
    }

    .color-header.gray {
        background: linear-gradient(118deg, #A8A6A0, #e5e9e0);
    }

    #paymentBatchForm .collapsing {
        -webkit-transition: none;
        transition: none;
        display: none;
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
                        <section>
                            <div class="collapse-default">
                                {{-- Repeater --}}
                                <!-- Invalid DO -->
                                @if (! empty($not_founds))
                                <div class="card rounded-lg mb-1 row-scope">
                                    <div class="card-header color-header rounded-lg gray">
                                        <span class="lead collapse-title">Sebanyak {{ count($not_founds) }} DO tidak ditemukan</span>
                                    </div>
                                    <div id="collapsibleInvalid" role="tabpanel" aria-labelledby="heading" class="collapsible collapse" aria-expanded="false">
                                        <div class="card-body p-2 row row-cols-4">
                                            @foreach ($not_founds as $index => $invalid)
                                            <span>{{ $index + 1 }}. {{ $invalid['id_marketing'] }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <!-- Valid DO -->
                                @foreach ($payments as $index => $payment)
                                    <div class="card rounded-lg mb-1 row-scope">
                                            @include('marketing.list.sections.batch-upload-collapse')
                                    </div>
                                @endforeach
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
    // $('.collapsible').collapse();
    // $('.card-header').on('click', function() {
    //     $(this).siblings('.collapsible').collapse('toggle');
    // });
    $('#collapsibleInvalid').siblings('.card-header').on('click', function() {
        $('#collapsibleInvalid').collapse('toggle');
    });

    function updateSisaBayar($row, $this, notPaid) {
        const $header = $row.siblings('.color-header');
        const $payment = $this;
        const payment = parseLocaleToNum($payment.val());
        const paymentLeft = notPaid - payment;
        const paymentLeftLocale = parseNumToLocale(paymentLeft);

        $header.find('.sisa-bayar').text(paymentLeftLocale);

        if (paymentLeft < 0) {
            $payment.siblings('.invalid').css('opacity', 1);
        } else {
            $payment.siblings('.invalid').css('opacity', 0);
        }
    }

    function updateIsInvalid($row) {
        const $header = $row.siblings('.color-header');
        const $invalids = $row.find('.invalid');
        const invalids = $invalids.map(function() {
            return Math.ceil($(this).css('opacity'));
        }).get();

        const is_invalid = invalids.includes(1);

        $header.toggleClass('red', is_invalid).trigger('validate');
    };

    $(function() {
        $('#allocateButton').on('click', function() {
            const $rows = $('.row-scope');
            const $payments = $('.payment_nominal_mask');

            // get all overpaments
            let totalOver = $payments.get().reduce((cur, i) => {
                const $payment = $(i);
                const payment = parseLocaleToNum($payment.val());
                const $paymentLeft = $(i).closest('.row-scope').find('.sisa-bayar');
                const paymentLeft = parseLocaleToNum($paymentLeft.text());
                let over = 0;

                if (paymentLeft < 0) {
                    // adjust with overpayments
                    over = Math.abs(paymentLeft);
                    $paymentLeft.text(parseNumToLocale(0));
                    $payment.val(parseNumToLocale(payment - over)).trigger('input');
                }

                return cur + over;
            }, 0);

            // allocate from the top
            $payments.each(function() {
                const $payment = $(this);
                const payment = parseLocaleToNum($payment.val());

                const $paymentLeft = $(this).closest('.row-scope').find('.sisa-bayar');
                const paymentLeft = parseLocaleToNum($paymentLeft.text());

                if (paymentLeft > 0 && totalOver > 0) {
                    const add = totalOver > paymentLeft ? payment + Math.abs(paymentLeft) : payment + totalOver;
                    $payment.val(parseNumToLocale(add)).trigger('input');
                    totalOver -= add;
                }
            });

            // if there's overpayment left, allocate to last DO
            if (totalOver > 0) {
                const $lastPayment = $payments.last();
                const lastPayment = parseLocaleToNum($lastPayment.val());
                $lastPayment.val(parseNumToLocale(lastPayment + totalOver)).trigger('input');
            }
        });
    });
</script>

@endsection
